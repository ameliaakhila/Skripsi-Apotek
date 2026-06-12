<?php

namespace App\Services\ML;

use App\Models\Admin\Obat;
use App\Models\Admin\ObatKeluar;
use App\Models\Admin\StokBatch;
use App\Models\Admin\StokOpname;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Stock Feature Extraction Service
 *
 * Extracts 12 features from database for ML model training/prediction
 * Focuses on last month sales data as primary predictor
 */
class StockFeatureService
{
    /**
     * Extract all features for a single drug
     *
     * @param int $id_obat
     * @param int $months Historical period in months (default: 12)
     * @return array|null
     */
    public function extractFeatures(int $id_obat, int $months = 12): ?array
    {
        try {
            $drug = Obat::find($id_obat);
            if (!$drug) {
                return null;
            }

            $endDate = now()->endOfDay();
            $startDate = $endDate->copy()->subMonths($months)->startOfDay();

            // Get all batches for this drug (remove created_at filter)
            $batches = StokBatch::where('id_obat', $id_obat)->get();

            if ($batches->isEmpty()) {
                return null;
            }

            // Feature 1: Total incoming stock (historical)
            $totalMasuk = (int)$batches->sum('stok_masuk');

            // Feature 2: Total outgoing stock (sales)
            $totalKeluar = (int)ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('stok_keluar');

            // Feature 3: Current stock level
            $stokSaatIni = (int)$batches->sum('stok_sisa');

            // Feature 4: Transaction frequency (last month focus)
            $lastMonthStart = now()->subMonth()->startOfDay();
            $lastMonthEnd = now()->endOfDay();

            $frekuensiTransaksi = (int)ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->count();

            // Feature 5: Average transaction value
            $avgTransaksi = $frekuensiTransaksi > 0
                ? round($totalKeluar / $frekuensiTransaksi, 2)
                : 0;

            // Feature 6: Expiry risk (batches expiring within 30 days)
            $kedaluwarsaRisk = (int)$batches
                ->filter(fn($b) => Carbon::parse($b->tanggal_kedaluwarsa)
                    ->diffInDays(Carbon::now(), false) <= 30)
                ->count();

            // Feature 7: Inventory accuracy from opname
            $opnameData = StokOpname::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $accuracyDeviation = $opnameData->isNotEmpty()
                ? round(abs($opnameData->avg('selisih') ?? 0), 2)
                : 0;

            // Feature 8: Safety stock ratio
            $safetyStockRatio = $drug->safety_stock > 0
                ? round($stokSaatIni / $drug->safety_stock, 2)
                : 0;

            // Feature 9: Demand volatility (standard deviation of last month daily sales)
            $demandVolatility = $this->calculateMonthlyVolatility($batches, $lastMonthStart, $lastMonthEnd);

            // Feature 10: Trend indicator (recent vs overall average)
            $recentAvg = $this->getRecentAverage($batches, 7);
            $lastMonthSalesTotal = (int)ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->sum('stok_keluar');
            $overallAvg = $frekuensiTransaksi > 0 ? $lastMonthSalesTotal / $frekuensiTransaksi : 0;
            $trendIndicator = $overallAvg > 0
                ? round($recentAvg / $overallAvg, 2)
                : 0;

            // Feature 11: Safety stock threshold
            $safetyStock = (int)($drug->safety_stock ?? 10);

            // Feature 12: Average daily sales (last month)
            $lastMonthDays = $lastMonthStart->diffInDays($lastMonthEnd) ?: 1;
            $lastMonthSales = (int)ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->sum('stok_keluar');

            $avgDailySales = round($lastMonthSales / $lastMonthDays, 2);

            return [
                'id_obat' => $id_obat,
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
                'safety_stock' => $safetyStock,
                'avg_daily_sales' => (float)$avgDailySales,
                'last_month_total_sales' => $lastMonthSales,
            ];
        } catch (\Exception $e) {
            \Log::error("Feature extraction error for drug {$id_obat}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Extract features for multiple drugs efficiently
     */
    public function extractFeaturesForDrugs(Collection $drugs, int $months = 12): Collection
    {
        if ($drugs->isEmpty()) {
            return collect();
        }

        $drugIds = $drugs->pluck('id_obat')->filter()->toArray();
        $endDate = now()->endOfDay();
        $startDate = $endDate->copy()->subMonths($months)->startOfDay();
        $lastMonthStart = now()->subMonth()->startOfDay();

        // 1. Bulk fetch batches (Optimized: Select specific columns)
        $allBatches = StokBatch::whereIn('id_obat', $drugIds)
            ->select('id_stok_batch', 'id_obat', 'stok_masuk', 'stok_sisa', 'tanggal_kedaluwarsa')
            ->get();
        
        if ($allBatches->isEmpty()) {
            return collect();
        }

        // Mapping batch ID to drug ID for fast lookup
        $batchToDrugMap = $allBatches->pluck('id_obat', 'id_stok_batch')->toArray();
        $batchIds = array_keys($batchToDrugMap);
        $groupedBatches = $allBatches->groupBy('id_obat');

        // 2. Bulk fetch sales (Optimized: Filter by date and select specific columns)
        $allSales = ObatKeluar::whereIn('id_stok_batch', $batchIds)
            ->where('created_at', '>=', $startDate)
            ->select('id_stok_batch', 'stok_keluar', 'created_at')
            ->get();

        $salesByDrug = $allSales->groupBy(function($sale) use ($batchToDrugMap) {
            return $batchToDrugMap[$sale->id_stok_batch] ?? 0;
        });

        // 3. Bulk fetch opnames (Optimized: Select specific columns)
        $allOpnames = StokOpname::whereIn('id_stok_batch', $batchIds)
            ->select('id_stok_batch', 'selisih')
            ->get()
            ->groupBy('id_stok_batch');
        
        $features = collect();
        foreach ($drugs as $drug) {
            $drugId = (int)$drug->id_obat;
            $drugBatches = $groupedBatches->get($drugId) ?? collect();
            if ($drugBatches->isEmpty()) {
                continue;
            }

            // O(1) lookup instead of O(M) filtering
            $drugSales = $salesByDrug->get($drugId) ?? collect();
            
            // Opnames still need some aggregation but it's smaller
            $drugBatchIds = $drugBatches->pluck('id_stok_batch');
            $drugOpnames = collect();
            foreach ($drugBatchIds as $bid) {
                if ($allOpnames->has($bid)) {
                    $drugOpnames = $drugOpnames->concat($allOpnames->get($bid));
                }
            }

            $drugFeatures = $this->extractFeaturesWithPreFetchedData($drug, $drugBatches, $drugSales, $drugOpnames, $startDate, $endDate);
            if ($drugFeatures) {
                $features->push($drugFeatures);
            }
        }

        return $features;
    }

    /**
     * Optimized version using pre-fetched data (No DB queries inside)
     */
    private function extractFeaturesWithPreFetchedData(Obat $drug, Collection $batches, Collection $sales, Collection $opnames, $startDate, $endDate): ?array
    {
        try {
            $id_obat = $drug->id_obat;
            $lastMonthStart = now()->subMonth()->startOfDay();
            $lastMonthEnd = now()->endOfDay();

            // Feature 1: Total incoming stock (historical)
            $totalMasuk = (int)$batches->sum('stok_masuk');

            // Feature 2: Total outgoing stock (sales)
            $totalKeluar = (int)$sales->sum('stok_keluar');
            
            $lastMonthSales = $sales->filter(fn($s) => $s->created_at >= $lastMonthStart);
            $frekuensiTransaksi = $lastMonthSales->count();
            $lastMonthSalesTotal = (int)$lastMonthSales->sum('stok_keluar');

            // Feature 3: Current stock level
            $stokSaatIni = (int)$batches->sum('stok_sisa');

            // Feature 5: Average transaction value
            $avgTransaksi = $frekuensiTransaksi > 0
                ? round($lastMonthSalesTotal / $frekuensiTransaksi, 2)
                : 0;

            // Feature 6: Expiry risk (batches expiring within 30 days)
            $kedaluwarsaRisk = (int)$batches
                ->filter(fn($b) => Carbon::parse($b->tanggal_kedaluwarsa)
                    ->diffInDays(Carbon::now(), false) <= 30)
                ->count();

            // Feature 7: Inventory accuracy from opname
            $accuracyDeviation = $opnames->isNotEmpty()
                ? round(abs($opnames->avg('selisih') ?? 0), 2)
                : 0;

            // Feature 8: Safety stock ratio
            $safetyStockRatio = $drug->safety_stock > 0
                ? round($stokSaatIni / $drug->safety_stock, 2)
                : 0;

            // Feature 9: Demand volatility
            $demandVolatility = $this->calculateVolatilityFromCollection($lastMonthSales, $lastMonthStart, $lastMonthEnd);

            // Feature 10: Trend indicator
            $sevenDaysAgo = now()->subDays(7)->startOfDay();
            $recentSales = $lastMonthSales->filter(fn($s) => $s->created_at >= $sevenDaysAgo);
            $recentAvg = round($recentSales->sum('stok_keluar') / 7, 2);
            
            $overallAvg = $frekuensiTransaksi > 0 ? $lastMonthSalesTotal / $frekuensiTransaksi : 0;
            $trendIndicator = $overallAvg > 0
                ? round($recentAvg / $overallAvg, 2)
                : 0;

            // Feature 11: Safety stock threshold
            $safetyStock = (int)($drug->safety_stock ?? 10);

            // Feature 12: Average daily sales (last month)
            $lastMonthDays = $lastMonthStart->diffInDays($lastMonthEnd) ?: 1;
            $avgDailySales = round($lastMonthSalesTotal / $lastMonthDays, 2);

            return [
                'id_obat' => $id_obat,
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
                'safety_stock' => $safetyStock,
                'avg_daily_sales' => (float)$avgDailySales,
                'last_month_total_sales' => $lastMonthSalesTotal,
            ];
        } catch (\Exception $e) {
            \Log::error("Feature extraction error for drug {$drug->id_obat}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Calculate volatility from a collection of sales (No DB queries)
     */
    private function calculateVolatilityFromCollection(Collection $sales, $startDate, $endDate): float
    {
        try {
            if ($sales->isEmpty()) {
                return 0;
            }

            // Group sales by date more efficiently
            $salesByDate = [];
            foreach ($sales as $sale) {
                $date = $sale->created_at->format('Y-m-d');
                $salesByDate[$date] = ($salesByDate[$date] ?? 0) + $sale->stok_keluar;
            }

            $dailySales = [];
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $dailySales[] = (int)($salesByDate[$dateStr] ?? 0);
                $currentDate->addDay();
            }

            $count = count($dailySales);
            if ($count <= 1) {
                return 0;
            }

            $mean = array_sum($dailySales) / $count;
            $sumSqDiff = 0;
            foreach ($dailySales as $val) {
                $sumSqDiff += pow($val - $mean, 2);
            }
            
            return round(sqrt($sumSqDiff / $count), 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Extract features for all drugs
     */
    public function extractAllFeatures(int $months = 12): Collection
    {
        $drugs = Obat::all();
        return $this->extractFeaturesForDrugs($drugs, $months);
    }

    /**
     * Calculate demand volatility (std dev of daily sales in last month)
     */
    private function calculateMonthlyVolatility($batches, $startDate, $endDate): float
    {
        try {
            $batchIds = $batches->pluck('id_stok_batch');
            
            // ! OPTIMIZATION: Use a single query with groupBy to get all daily sales in one go
            $salesData = ObatKeluar::whereIn('id_stok_batch', $batchIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, SUM(stok_keluar) as total_sales')
                ->groupBy('date')
                ->pluck('total_sales', 'date')
                ->toArray();

            $dailySales = [];
            $dateRange = $startDate->copy();
            
            while ($dateRange <= $endDate) {
                $dateStr = $dateRange->format('Y-m-d');
                $dailySales[] = (int)($salesData[$dateStr] ?? 0);
                $dateRange->addDay();
            }

            if (count($dailySales) <= 1) {
                return 0;
            }

            $mean = array_sum($dailySales) / count($dailySales);
            $variance = array_sum(array_map(
                fn($x) => pow($x - $mean, 2),
                $dailySales
            )) / count($dailySales);

            return round(sqrt($variance), 2);
        } catch (\Exception $e) {
            \Log::error("Volatility calculation error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get recent average (last N days)
     */
    private function getRecentAverage($batches, int $days): float
    {
        try {
            $startDate = Carbon::now()->subDays($days)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            $totalSales = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('stok_keluar');

            return round($totalSales / $days, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get last month sales summary for a drug
     */
    public function getLastMonthSalesSummary(int $id_obat): ?array
    {
        try {
            $lastMonthStart = now()->subMonth()->startOfDay();
            $lastMonthEnd = now()->endOfDay();

            $batches = StokBatch::where('id_obat', $id_obat)->get();

            if ($batches->isEmpty()) {
                return null;
            }

            $sales = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->get();

            $totalSales = (int)$sales->sum('stok_keluar');
            $avgDailySales = round($totalSales / 30, 2);
            $maxDailySales = (int)$sales->max('stok_keluar') ?? 0;
            $transactionCount = $sales->count();

            return [
                'total_sales' => $totalSales,
                'avg_daily_sales' => $avgDailySales,
                'max_daily_sales' => $maxDailySales,
                'transaction_count' => $transactionCount,
                'days_active' => $sales->groupBy('tanggal_keluar')->count(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
