<?php

namespace App\Services;

use App\Models\Admin\{Obat, DataCabang, DataKategori, DataSatuan, DataSupplier, StokBatch, ObatMasuk, ObatKeluar};
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    /**
     * Get Admin Dashboard Data
     *
     * @return array
     */
    public function getAdminDashboardData(): array
    {
        try {
            return [
                'totalDataObat' => Obat::count(),
                'totalDataCabang' => DataCabang::count(),
                'totalDataMaster' => $this->countMasterData(),
                'totalUsers' => User::count(),
                'monthlySalesChart' => $this->getMonthlySalesData(),
                'expiringMedicines' => $this->getExpiredMedicinesList(),
                'statusCards' => [
                    'obat' => Obat::count(),
                    'cabang' => DataCabang::count(),
                    'users' => User::count(),
                    'master' => $this->countMasterData(),
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in getAdminDashboardData: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get Apoteker Dashboard Data
     *
     * @return array
     */
    public function getApotekerDashboardData(): array
    {
        try {
            return [
                'totalDataObat' => Obat::count(),
                'totalDataCabang' => DataCabang::count(),
                'transactionToday' => $this->getTodayTransactionCount(),
                'expiredMedicines' => $this->getExpiredMedicinesCount(),
                'upcomingExpired' => $this->getUpcomingExpiredCount(),
                'lowStockCount' => $this->getLowStockCount(),
                'weeklyTransactionChart' => $this->getWeeklyTransactionData(),
                'stockDistribution' => $this->getStockDistributionData(),
                'recentActivities' => $this->getRecentActivities(),
                'statusCards' => [
                    'obat' => Obat::count(),
                    'transaksi' => $this->getTodayTransactionCount(),
                    'cabang' => DataCabang::count(),
                    'kedaluwarsa' => $this->getExpiredMedicinesCount(),
                    'stok_rendah' => $this->getLowStockCount(),
                    'hampir_kedaluwarsa' => $this->getUpcomingExpiredCount(),
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in getApotekerDashboardData: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get Count of Medicines with Low Stock
     *
     * @return int
     */
    private function getLowStockCount(): int
    {
        return StokBatch::whereRaw('stok_sisa <= (SELECT safety_stock FROM tb_obat WHERE tb_obat.id_obat = tb_stok_batch.id_obat)')
            ->where('stok_sisa', '>', 0)
            ->count();
    }

    /**
     * Get Recent Activities from activity_logs table
     *
     * @return \Illuminate\Support\Collection
     */
    private function getRecentActivities(): \Illuminate\Support\Collection
    {
        return \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(function($item) {
                return [
                    'title' => $item->user->name ?? 'System',
                    'description' => $item->action,
                    'time' => $item->created_at->diffForHumans(),
                    'color' => 'info' // Default color
                ];
            });
    }

    /**
     * Get Manager Dashboard Data
     *
     * @return array
     */
    public function getManagerDashboardData(): array
    {
        try {
            return [
                'totalDataObat' => Obat::count(),
                'totalDataCabang' => DataCabang::count(),
                'predictedStockIssue' => $this->getPredictedStockIssueCount(),
                'availableReports' => 4, // Based on reports available in system
                'trendChart' => $this->getMonthlyTrendData(),
                'distributionChart' => $this->getStockDistributionData(),
                'topSellingDrugs' => $this->getTopSellingDrugs(),
                'statusCards' => [
                    'obat' => Obat::count(),
                    'cabang' => DataCabang::count(),
                    'prediksi' => $this->getPredictedStockIssueCount(),
                    'laporan' => 4,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in getManagerDashboardData: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get Top Selling Drugs for Bar Chart
     *
     * @return array
     */
    private function getTopSellingDrugs(): array
    {
        $topDrugs = ObatKeluar::join('tb_obat_masuk', 'tb_obat_keluar.id_obat_masuk', '=', 'tb_obat_masuk.id_obat_masuk')
            ->join('tb_obat', 'tb_obat_masuk.id_obat', '=', 'tb_obat.id_obat')
            ->select('tb_obat.nama_obat', DB::raw('SUM(tb_obat_keluar.stok_keluar) as total_keluar'))
            ->groupBy('tb_obat.id_obat', 'tb_obat.nama_obat')
            ->orderBy('total_keluar', 'desc')
            ->limit(5)
            ->get();

        return [
            'labels' => $topDrugs->map(fn($item) => $item->nama_obat ?? 'Unknown')->toArray(),
            'data' => $topDrugs->pluck('total_keluar')->map(fn($val) => (int)$val)->toArray(),
        ];
    }

    /**
     * Count total master data (kategori + satuan + supplier)
     *
     * @return int
     */
    private function countMasterData(): int
    {
        return Obat::count() + DataKategori::count() + DataSatuan::count() + DataSupplier::count() + DataCabang::count();
    }

    /**
     * Get Today's Transaction Count
     *
     * @return int
     */
    private function getTodayTransactionCount(): int
    {
        return ObatKeluar::whereDate('tanggal_keluar', Carbon::today())->count();
    }

    /**
     * Get Count of Medicines expiring within the next 30 days
     *
     * @return int
     */
    private function getUpcomingExpiredCount(): int
    {
        return StokBatch::where('tanggal_kedaluwarsa', '>', Carbon::today())
            ->where('tanggal_kedaluwarsa', '<=', Carbon::today()->addDays(30))
            ->where('stok_sisa', '>', 0)
            ->count();
    }

    /**
     * Get Count of Expired Medicines
     *
     * @return int
     */
    private function getExpiredMedicinesCount(): int
    {
        return StokBatch::where('tanggal_kedaluwarsa', '<=', Carbon::today())
            ->where('stok_sisa', '>', 0)
            ->count();
    }

    /**
     * Get Count of Predicted Stock Issues (items below safety stock)
     *
     * @return int
     */
    private function getPredictedStockIssueCount(): int
    {
        return StokBatch::whereRaw('stok_sisa <= (SELECT safety_stock FROM tb_obat WHERE tb_obat.id_obat = tb_stok_batch.id_obat)')
            ->where('stok_sisa', '>', 0)
            ->count();
    }

    /**
     * Get Monthly Sales Data for Line Chart
     * Format: [Jan, Feb, Mar, ..., Dec] with actual outgoing stock counts
     *
     * @return array
     */
    private function getMonthlySalesData(): array
    {
        $data = [];
        $currentYear = Carbon::now()->year;

        for ($month = 1; $month <= 12; $month++) {
            $count = ObatKeluar::whereYear('tanggal_keluar', $currentYear)
                ->whereMonth('tanggal_keluar', $month)
                ->sum('stok_keluar');

            $data[] = (int) $count;
        }

        return $data;
    }

    /**
     * Get Weekly Transaction Data for Bar Chart
     *
     * @return array
     */
    private function getWeeklyTransactionData(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $masuk = [];
        $keluar = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);

            $masuk[] = ObatMasuk::whereDate('tanggal_masuk', $date)->sum('stok_masuk');
            $keluar[] = ObatKeluar::whereDate('tanggal_keluar', $date)->sum('stok_keluar');
        }

        return [
            'labels' => $days,
            'incoming' => $masuk,
            'outgoing' => $keluar,
        ];
    }

    /**
     * Get Monthly Trend Data (last 12 months)
     * Shows stok masuk vs stok keluar trend
     *
     * @return array
     */
    private function getMonthlyTrendData(): array
    {
        $months = [];
        $masuk = [];
        $keluar = [];
        $currentYear = Carbon::now()->year;
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($month = 1; $month <= 12; $month++) {
            $months[] = $monthLabels[$month - 1];

            $masuk[] = ObatMasuk::whereYear('tanggal_masuk', $currentYear)
                ->whereMonth('tanggal_masuk', $month)
                ->sum('stok_masuk');

            $keluar[] = ObatKeluar::whereYear('tanggal_keluar', $currentYear)
                ->whereMonth('tanggal_keluar', $month)
                ->sum('stok_keluar');
        }

        return [
            'labels' => $months,
            'masuk' => $masuk,
            'keluar' => $keluar,
        ];
    }

    /**
     * Get Stock Distribution by Branch/Category
     * For pie chart visualization
     *
     * @return array
     */
    private function getStockDistributionData(): array
    {
        // Get stock status distribution: Aman, Menipis, Kritis
        $aman = StokBatch::whereRaw('stok_sisa > (SELECT safety_stock * 1.5 FROM tb_obat WHERE tb_obat.id_obat = tb_stok_batch.id_obat)')
            ->count();

        $menipis = StokBatch::whereRaw('stok_sisa BETWEEN (SELECT safety_stock FROM tb_obat WHERE tb_obat.id_obat = tb_stok_batch.id_obat) AND (SELECT safety_stock * 1.5 FROM tb_obat WHERE tb_obat.id_obat = tb_stok_batch.id_obat)')
            ->count();

        $kritis = StokBatch::whereRaw('stok_sisa < (SELECT safety_stock FROM tb_obat WHERE tb_obat.id_obat = tb_stok_batch.id_obat)')
            ->where('stok_sisa', '>', 0)
            ->count();

        return [
            'labels' => ['Aman', 'Menipis', 'Kritis'],
            'data' => [$aman, $menipis, $kritis],
            'backgroundColor' => ['#28a745', '#ffc107', '#dc3545']
        ];
    }

    /**
     * Get Expired/Soon-to-Expire Medicines List
     *
     * @return \Illuminate\Support\Collection
     */
    public function getExpiredMedicinesList(): \Illuminate\Support\Collection
    {
        return StokBatch::with(['Obat', 'DataCabang'])
            ->where('tanggal_kedaluwarsa', '<=', Carbon::now()->addDays(30))
            ->where('stok_sisa', '>', 0)
            ->orderBy('tanggal_kedaluwarsa', 'asc')
            ->limit(5)
            ->get();
    }

}
