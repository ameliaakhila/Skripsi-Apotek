<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\Obat;
use App\Models\Admin\StokBatch;
use App\Models\Admin\ObatKeluar;
use App\Models\Admin\StokOpname;
use Carbon\Carbon;

class ExportPrediksiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prediksi:export-data {--months=12 : Number of months to export}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Export historical stock data for ML model training';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = $this->option('months');
        $this->info("Exporting {$months} months of stock prediction data...");

        try {
            // Get date range
            $endDate = Carbon::now()->endOfDay();
            $startDate = $endDate->copy()->subMonths($months)->startOfDay();

            $this->info("Period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

            // Extract features from database
            $data = $this->extractFeatures($startDate, $endDate);

            if ($data->isEmpty()) {
                $this->error('❌ No data found for the specified period');
                return 1;
            }

            $this->info("📊 Extracted {$data->count()} records");

            // Generate CSV file
            $filename = $this->generateCSV($data, $months);

            $this->info("✅ Export completed: {$filename}");
            $this->info("📁 File size: " . number_format(filesize($filename) / 1024, 2) . " KB");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Extract features from database for ML training
     */
    private function extractFeatures($startDate, $endDate)
    {
        // Main query: Get all drugs with historical metrics
        $drugs = Obat::select('id_obat', 'nama_obat', 'safety_stock')
            ->with(['ObatMasuk' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get();

        $data = collect();

        foreach ($drugs as $drug) {
            // Get batches for this drug (remove created_at filter to include all active/historical batches)
            $batches = StokBatch::where('id_obat', $drug->id_obat)
                ->get();

            if ($batches->isEmpty()) {
                continue;
            }

            // Calculate features
            $features = $this->calculateFeatures(
                $drug,
                $batches,
                $startDate,
                $endDate
            );

            $data->push($features);
        }

        return $data;
    }

    /**
     * Calculate all features for a single drug
     */
    private function calculateFeatures($drug, $batches, $startDate, $endDate)
    {
        // Get last month range for specific features
        $lastMonthStart = $endDate->copy()->subMonth()->startOfDay();
        $lastMonthEnd = $endDate->copy()->endOfDay();

        // 1. Historical sales (outgoing stock) - 12 Months
        $totalKeluar = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('stok_keluar');

        // 2. Historical incoming stock - 12 Months
        $totalMasuk = $batches->sum('stok_masuk');

        // 3. Current stock level (latest stok_sisa)
        $stokSaatIni = $batches->sum('stok_sisa');

        // 4. Transaction frequency - LAST MONTH (Match StockFeatureService)
        $frekuensiTransaksi = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();

        // 5. Average transaction value - LAST MONTH
        $lastMonthSales = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('stok_keluar');

        $avgTransaksi = $frekuensiTransaksi > 0
            ? round($lastMonthSales / $frekuensiTransaksi, 2)
            : 0;

        // 6. Expiry risk (batches expiring within 30 days)
        $kedaluwarsaRisk = $batches
            ->filter(fn($b) => Carbon::parse($b->tanggal_kedaluwarsa)
                ->diffInDays(Carbon::now(), false) <= 30)
            ->count();

        // 7. Inventory accuracy from opname (average difference)
        $opnameData = StokOpname::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $accuracyDeviation = $opnameData->isNotEmpty()
            ? round(abs($opnameData->avg('selisih')), 2)
            : 0;

        // 8. Safety stock ratio
        $safetyStockRatio = $drug->safety_stock > 0
            ? round($stokSaatIni / $drug->safety_stock, 2)
            : 0;

        // 9. Demand volatility - LAST MONTH (Match StockFeatureService)
        $dailySales = $this->getDailySales($batches, $lastMonthStart, $lastMonthEnd);
        $demandVolatility = $this->calculateVolatility($dailySales);

        // 10. Recent sales trend (last 7 days average vs overall average)
        $recentAvg = $this->getRecentAverage($batches, 7);
        $overallAvg = $frekuensiTransaksi > 0 ? $lastMonthSales / $frekuensiTransaksi : 0;
        $trendIndicator = $overallAvg > 0
            ? round($recentAvg / $overallAvg, 2)
            : 0;

        // 11. Safety stock threshold
        $safetyStock = $drug->safety_stock ?? 10;

        // 12. Target: Predicted required stock for next month (Optimized for 1 month sufficiency)
        // Use last month average for daily sales
        $lastMonthDays = $lastMonthStart->diffInDays($lastMonthEnd) ?: 30;
        $avgDailySales = round($lastMonthSales / $lastMonthDays, 2);

        // Use a safety factor for volatility (e.g., 1.5 to cover peaks)
        $volatilityBuffer = $demandVolatility * 1.5;

        // Final prediction must cover at least the average demand plus buffers
        $predictedRequired = round(($avgDailySales * 30) + $safetyStock + $volatilityBuffer, 0);

        // If recent trend is upward (trend > 1), increase prediction by trend factor
        if ($trendIndicator > 1.1) {
            $predictedRequired = round($predictedRequired * min(1.5, $trendIndicator), 0);
        }

        return [
            'id_obat' => $drug->id_obat,
            'nama_obat' => $drug->nama_obat,
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'stok_saat_ini' => $stokSaatIni,
            'frekuensi_transaksi' => $frekuensiTransaksi,
            'avg_transaksi' => (float)$avgTransaksi,
            'kedaluwarsa_risk' => $kedaluwarsaRisk,
            'accuracy_deviation' => (float)$accuracyDeviation,
            'safety_stock_ratio' => (float)$safetyStockRatio,
            'demand_volatility' => (float)$demandVolatility,
            'trend_indicator' => (float)$trendIndicator,
            'safety_stock' => (int)$safetyStock,
            'avg_daily_sales' => (float)$avgDailySales,
            'predicted_required_stock' => (float)$predictedRequired,
        ];
    }

    /**
     * Calculate daily sales for volatility measurement (Optimized)
     */
    private function getDailySales($batches, $startDate, $endDate)
    {
        $batchIds = $batches->pluck('id_stok_batch');

        // Use single query with grouping by date
        $salesData = ObatKeluar::whereIn('id_stok_batch', $batchIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(stok_keluar) as total_sales')
            ->groupBy('date')
            ->pluck('total_sales', 'date')
            ->toArray();

        $dailySales = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dailySales[] = (int)($salesData[$dateStr] ?? 0);
            $currentDate->addDay();
        }

        return $dailySales;
    }

    /**
     * Calculate demand volatility (standard deviation)
     */
    private function calculateVolatility($dailySales)
    {
        if (empty($dailySales)) {
            return 0;
        }

        $mean = array_sum($dailySales) / count($dailySales);
        $variance = array_sum(array_map(
            fn($x) => pow($x - $mean, 2),
            $dailySales
        )) / count($dailySales);

        return round(sqrt($variance), 2);
    }

    /**
     * Get recent average (last N days)
     */
    private function getRecentAverage($batches, $days)
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $totalSales = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('stok_keluar');

        return round($totalSales / $days, 2);
    }

    /**
     * Generate CSV file from data
     */
    private function generateCSV($data, $months)
    {
        $storagePath = storage_path('training_data_prediksi_stok');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filename = "{$storagePath}/training_data_{$months}m_" . now()->format('Y-m-d_His') . '.csv';

        $handle = fopen($filename, 'w');

        // Write CSV header
        $headers = [
            'id_obat',
            'nama_obat',
            'total_masuk',
            'total_keluar',
            'stok_saat_ini',
            'frekuensi_transaksi',
            'avg_transaksi',
            'kedaluwarsa_risk',
            'accuracy_deviation',
            'safety_stock_ratio',
            'demand_volatility',
            'trend_indicator',
            'safety_stock',
            'avg_daily_sales',
            'predicted_required_stock',
        ];

        fputcsv($handle, $headers);

        // Write data rows
        foreach ($data as $row) {
            fputcsv($handle, [
                $row['id_obat'],
                $row['nama_obat'],
                $row['total_masuk'],
                $row['total_keluar'],
                $row['stok_saat_ini'],
                $row['frekuensi_transaksi'],
                $row['avg_transaksi'],
                $row['kedaluwarsa_risk'],
                $row['accuracy_deviation'],
                $row['safety_stock_ratio'],
                $row['demand_volatility'],
                $row['trend_indicator'],
                $row['safety_stock'],
                $row['avg_daily_sales'],
                $row['predicted_required_stock'],
            ]);
        }

        fclose($handle);

        return $filename;
    }
}
