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
 * Extracts 12 features dari database untuk prediksi stok obat.
 * Menggunakan DATA TRANSAKSI 3 BULAN TERAKHIR sebagai dasar kalkulasi fitur.
 *
 * Filosofi: Prediksi kebutuhan stok = kebutuhan 1 bulan ke depan
 * berdasarkan rata-rata penjualan 3 bulan terakhir (lebih stabil dari 1 bulan).
 */
class StockFeatureService
{
    /**
     * Periode history untuk fitur frekuensi & rata-rata penjualan: 3 bulan
     */
    private const SALES_PERIOD_MONTHS = 3;

    /**
     * Extract all features untuk satu obat
     *
     * @param int $id_obat
     * @param int $months Historical period untuk total masuk/keluar (default: 12)
     * @return array|null
     */
    public function extractFeatures(int $id_obat, int $months = 12): ?array
    {
        try {
            $drug = Obat::find($id_obat);
            if (!$drug) {
                return null;
            }

            // -- Periode untuk total masuk/keluar historis (12 bulan) --
            $endDate   = now()->endOfDay();
            $startDate = $endDate->copy()->subMonths($months)->startOfDay();

            // -- Periode 3 bulan terakhir untuk fitur transaksi (konsisten) --
            $periodStart = now()->subMonths(self::SALES_PERIOD_MONTHS)->startOfDay();
            $periodEnd   = now()->endOfDay();
            $periodDays  = max(1, $periodStart->diffInDays($periodEnd));

            // Get all batches for this drug
            $batches = StokBatch::where('id_obat', $id_obat)->get();

            if ($batches->isEmpty()) {
                return null;
            }

            $batchIds = $batches->pluck('id_stok_batch');

            // Feature 1: Total incoming stock (historical 12 bulan)
            $totalMasuk = (int) $batches->sum('stok_masuk');

            // Feature 2: Total outgoing stock (historical 12 bulan)
            $totalKeluar = (int) ObatKeluar::whereIn('id_stok_batch', $batchIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('stok_keluar');

            // Feature 3: Current stock level
            $stokSaatIni = (int) $batches->sum('stok_sisa');

            // Feature 4 & 5: Frekuensi & rata-rata transaksi — 3 BULAN TERAKHIR
            $periodSales = ObatKeluar::whereIn('id_stok_batch', $batchIds)
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->get(['stok_keluar', 'created_at']);

            $frekuensiTransaksi = $periodSales->count();
            $periodSalesTotal   = (int) $periodSales->sum('stok_keluar');

            $avgTransaksi = $frekuensiTransaksi > 0
                ? round($periodSalesTotal / $frekuensiTransaksi, 2)
                : 0;

            // Feature 6: Expiry risk (batches expiring within 30 days)
            $kedaluwarsaRisk = (int) $batches
                ->filter(fn($b) => Carbon::parse($b->tanggal_kedaluwarsa)
                    ->diffInDays(Carbon::now(), false) <= 30)
                ->count();

            // Feature 7: Inventory accuracy from opname
            $opnameData = StokOpname::whereIn('id_stok_batch', $batchIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $accuracyDeviation = $opnameData->isNotEmpty()
                ? round(abs($opnameData->avg('selisih') ?? 0), 2)
                : 0;

            // Feature 8: Safety stock ratio
            $safetyStockRatio = $drug->safety_stock > 0
                ? round($stokSaatIni / $drug->safety_stock, 2)
                : 0;

            // Feature 9: Demand volatility (std dev daily sales — 3 bulan terakhir)
            // FIX: gunakan $periodStart/$periodEnd yang sudah didefinisikan
            $demandVolatility = $this->calculateVolatilityFromCollection(
                $periodSales,
                $periodStart,
                $periodEnd
            );

            // Feature 10: Trend indicator (7 hari terakhir vs avg 3 bulan)
            $sevenDaysAgo = now()->subDays(7)->startOfDay();
            $recentSales  = $periodSales->filter(fn($s) => $s->created_at >= $sevenDaysAgo);
            $recentAvg    = round($recentSales->sum('stok_keluar') / 7, 2);

            $overallDailyAvg  = $periodDays > 0 ? $periodSalesTotal / $periodDays : 0;
            $trendIndicator   = $overallDailyAvg > 0
                ? round($recentAvg / $overallDailyAvg, 2)
                : 0;

            // Feature 11: Safety stock threshold
            $safetyStock = (int) ($drug->safety_stock ?? 10);

            // Feature 12: Average daily sales — 3 BULAN TERAKHIR
            $avgDailySales = round($periodSalesTotal / $periodDays, 2);

            return [
                'id_obat'                => $id_obat,
                'nama_obat'              => $drug->nama_obat,
                'total_masuk'            => $totalMasuk,
                'total_keluar'           => $totalKeluar,
                'stok_saat_ini'          => $stokSaatIni,
                'frekuensi_transaksi'    => $frekuensiTransaksi,
                'avg_transaksi'          => (float) $avgTransaksi,
                'kedaluwarsa_risk'       => $kedaluwarsaRisk,
                'accuracy_deviation'     => (float) $accuracyDeviation,
                'safety_stock_ratio'     => (float) $safetyStockRatio,
                'demand_volatility'      => (float) $demandVolatility,
                'trend_indicator'        => (float) $trendIndicator,
                'safety_stock'           => $safetyStock,
                'avg_daily_sales'        => (float) $avgDailySales,
                // Informasi tambahan untuk view (tidak dikirim ke ML API)
                'last_3month_total_sales' => $periodSalesTotal,
                'last_month_total_sales'  => $periodSalesTotal, // alias agar kompatibel
                'periode_data'           => self::SALES_PERIOD_MONTHS . ' bulan terakhir',
            ];
        } catch (\Exception $e) {
            \Log::error("Feature extraction error for drug {$id_obat}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Extract features untuk banyak obat sekaligus (bulk, dipakai production)
     */
    public function extractFeaturesForDrugs(Collection $drugs, int $months = 12): Collection
    {
        if ($drugs->isEmpty()) {
            return collect();
        }

        $drugIds = $drugs->pluck('id_obat')->filter()->toArray();

        // -- Periode untuk total masuk/keluar historis --
        $endDate   = now()->endOfDay();
        $startDate = $endDate->copy()->subMonths($months)->startOfDay();

        // -- Periode 3 bulan terakhir untuk fitur transaksi (SERAGAM) --
        $periodStart = now()->subMonths(self::SALES_PERIOD_MONTHS)->startOfDay();
        $periodEnd   = now()->endOfDay();

        // 1. Bulk fetch batches
        $allBatches = StokBatch::whereIn('id_obat', $drugIds)
            ->select('id_stok_batch', 'id_obat', 'stok_masuk', 'stok_sisa', 'tanggal_kedaluwarsa')
            ->get();

        if ($allBatches->isEmpty()) {
            return collect();
        }

        $batchToDrugMap = $allBatches->pluck('id_obat', 'id_stok_batch')->toArray();
        $batchIds       = array_keys($batchToDrugMap);
        $groupedBatches = $allBatches->groupBy('id_obat');

        // 2. Bulk fetch sales — GUNAKAN 3 BULAN TERAKHIR (bukan subMonths(12))
        $allSales = ObatKeluar::whereIn('id_stok_batch', $batchIds)
            ->where('created_at', '>=', $periodStart)   // << 3 bulan terakhir
            ->select('id_stok_batch', 'stok_keluar', 'created_at')
            ->get();

        $salesByDrug = $allSales->groupBy(function ($sale) use ($batchToDrugMap) {
            return $batchToDrugMap[$sale->id_stok_batch] ?? 0;
        });

        // 3. Bulk fetch total keluar historis 12 bulan (untuk feature total_keluar)
        $allSalesHistorical = ObatKeluar::whereIn('id_stok_batch', $batchIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('id_stok_batch', 'stok_keluar')
            ->get();

        $historicalSalesByDrug = $allSalesHistorical->groupBy(function ($sale) use ($batchToDrugMap) {
            return $batchToDrugMap[$sale->id_stok_batch] ?? 0;
        });

        // 4. Bulk fetch opnames
        $allOpnames = StokOpname::whereIn('id_stok_batch', $batchIds)
            ->select('id_stok_batch', 'selisih')
            ->get()
            ->groupBy('id_stok_batch');

        $features = collect();
        foreach ($drugs as $drug) {
            $drugId      = (int) $drug->id_obat;
            $drugBatches = $groupedBatches->get($drugId) ?? collect();

            if ($drugBatches->isEmpty()) {
                continue;
            }

            $drugSales      = $salesByDrug->get($drugId) ?? collect();
            $historicalSales = $historicalSalesByDrug->get($drugId) ?? collect();

            $drugBatchIds = $drugBatches->pluck('id_stok_batch');
            $drugOpnames  = collect();
            foreach ($drugBatchIds as $bid) {
                if ($allOpnames->has($bid)) {
                    $drugOpnames = $drugOpnames->concat($allOpnames->get($bid));
                }
            }

            $drugFeatures = $this->extractFeaturesWithPreFetchedData(
                $drug,
                $drugBatches,
                $drugSales,
                $historicalSales,
                $drugOpnames,
                $periodStart,
                $periodEnd
            );

            if ($drugFeatures) {
                $features->push($drugFeatures);
            }
        }

        return $features;
    }

    /**
     * Versi optimized menggunakan data yang sudah di-prefetch (tanpa DB query di dalam)
     *
     * @param Obat       $drug             Data obat
     * @param Collection $batches          Batches milik obat ini
     * @param Collection $sales            Sales 3 bulan terakhir
     * @param Collection $historicalSales  Sales 12 bulan (untuk total_keluar)
     * @param Collection $opnames          Data opname
     * @param Carbon     $periodStart      Start 3 bulan terakhir
     * @param Carbon     $periodEnd        End (hari ini)
     */
    private function extractFeaturesWithPreFetchedData(
        Obat       $drug,
        Collection $batches,
        Collection $sales,
        Collection $historicalSales,
        Collection $opnames,
        $periodStart,
        $periodEnd
    ): ?array {
        try {
            $id_obat    = $drug->id_obat;
            $periodDays = max(1, $periodStart->diffInDays($periodEnd));

            // Feature 1: Total incoming stock
            $totalMasuk = (int) $batches->sum('stok_masuk');

            // Feature 2: Total outgoing stock — 12 bulan historis
            $totalKeluar = (int) $historicalSales->sum('stok_keluar');

            // Feature 3: Current stock level
            $stokSaatIni = (int) $batches->sum('stok_sisa');

            // Feature 4 & 5: Frekuensi & avg transaksi — 3 BULAN TERAKHIR
            $frekuensiTransaksi = $sales->count();
            $periodSalesTotal   = (int) $sales->sum('stok_keluar');

            $avgTransaksi = $frekuensiTransaksi > 0
                ? round($periodSalesTotal / $frekuensiTransaksi, 2)
                : 0;

            // Feature 6: Expiry risk
            $kedaluwarsaRisk = (int) $batches
                ->filter(fn($b) => Carbon::parse($b->tanggal_kedaluwarsa)
                    ->diffInDays(Carbon::now(), false) <= 30)
                ->count();

            // Feature 7: Accuracy deviation dari opname
            $accuracyDeviation = $opnames->isNotEmpty()
                ? round(abs($opnames->avg('selisih') ?? 0), 2)
                : 0;

            // Feature 8: Safety stock ratio
            $safetyStockRatio = $drug->safety_stock > 0
                ? round($stokSaatIni / $drug->safety_stock, 2)
                : 0;

            // Feature 9: Demand volatility — 3 BULAN TERAKHIR
            $demandVolatility = $this->calculateVolatilityFromCollection(
                $sales,
                $periodStart,
                $periodEnd
            );

            // Feature 10: Trend indicator (7 hari vs rata-rata harian 3 bulan)
            $sevenDaysAgo = now()->subDays(7)->startOfDay();
            $recentSales  = $sales->filter(fn($s) => $s->created_at >= $sevenDaysAgo);
            $recentAvg    = round($recentSales->sum('stok_keluar') / 7, 2);

            $overallDailyAvg = $periodDays > 0 ? $periodSalesTotal / $periodDays : 0;
            $trendIndicator  = $overallDailyAvg > 0
                ? round($recentAvg / $overallDailyAvg, 2)
                : 0;

            // Feature 11: Safety stock threshold
            $safetyStock = (int) ($drug->safety_stock ?? 10);

            // Feature 12: Average daily sales — 3 BULAN TERAKHIR
            $avgDailySales = round($periodSalesTotal / $periodDays, 2);

            return [
                'id_obat'                 => $id_obat,
                'nama_obat'               => $drug->nama_obat,
                'total_masuk'             => $totalMasuk,
                'total_keluar'            => $totalKeluar,
                'stok_saat_ini'           => $stokSaatIni,
                'frekuensi_transaksi'     => $frekuensiTransaksi,
                'avg_transaksi'           => (float) $avgTransaksi,
                'kedaluwarsa_risk'        => $kedaluwarsaRisk,
                'accuracy_deviation'      => (float) $accuracyDeviation,
                'safety_stock_ratio'      => (float) $safetyStockRatio,
                'demand_volatility'       => (float) $demandVolatility,
                'trend_indicator'         => (float) $trendIndicator,
                'safety_stock'            => $safetyStock,
                'avg_daily_sales'         => (float) $avgDailySales,
                // Informasi tambahan untuk view
                'last_3month_total_sales' => $periodSalesTotal,
                'last_month_total_sales'  => $periodSalesTotal,  // alias kompatibilitas
                'periode_data'            => self::SALES_PERIOD_MONTHS . ' bulan terakhir',
            ];
        } catch (\Exception $e) {
            \Log::error("Feature extraction error for drug {$drug->id_obat}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Hitung demand volatility (std deviasi penjualan harian) dari collection
     */
    private function calculateVolatilityFromCollection(Collection $sales, $startDate, $endDate): float
    {
        try {
            if ($sales->isEmpty()) {
                return 0;
            }

            // Kelompokkan penjualan per hari
            $salesByDate = [];
            foreach ($sales as $sale) {
                $date = $sale->created_at->format('Y-m-d');
                $salesByDate[$date] = ($salesByDate[$date] ?? 0) + $sale->stok_keluar;
            }

            // Bangun array penjualan harian (termasuk hari dengan 0 penjualan)
            $dailySales  = [];
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $dateStr      = $currentDate->format('Y-m-d');
                $dailySales[] = (int) ($salesByDate[$dateStr] ?? 0);
                $currentDate->addDay();
            }

            $count = count($dailySales);
            if ($count <= 1) {
                return 0;
            }

            $mean      = array_sum($dailySales) / $count;
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
     * Extract features untuk semua obat
     */
    public function extractAllFeatures(int $months = 12): Collection
    {
        $drugs = Obat::all();
        return $this->extractFeaturesForDrugs($drugs, $months);
    }

    /**
     * Get ringkasan penjualan 3 bulan terakhir untuk satu obat
     */
    public function getLastMonthSalesSummary(int $id_obat): ?array
    {
        try {
            $periodStart = now()->subMonths(self::SALES_PERIOD_MONTHS)->startOfDay();
            $periodEnd   = now()->endOfDay();
            $periodDays  = max(1, $periodStart->diffInDays($periodEnd));

            $batches = StokBatch::where('id_obat', $id_obat)->get();

            if ($batches->isEmpty()) {
                return null;
            }

            $sales = ObatKeluar::whereIn('id_stok_batch', $batches->pluck('id_stok_batch'))
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->get();

            $totalSales       = (int) $sales->sum('stok_keluar');
            $avgDailySales    = round($totalSales / $periodDays, 2);
            $maxDailySales    = (int) ($sales->max('stok_keluar') ?? 0);
            $transactionCount = $sales->count();

            return [
                'total_sales'       => $totalSales,
                'avg_daily_sales'   => $avgDailySales,
                'max_daily_sales'   => $maxDailySales,
                'transaction_count' => $transactionCount,
                'period_months'     => self::SALES_PERIOD_MONTHS,
                'days_active'       => $sales->groupBy(fn($s) => $s->created_at->format('Y-m-d'))->count(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
