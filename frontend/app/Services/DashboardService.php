<?php

namespace App\Services;

use App\Models\Admin\{Obat, DataCabang, DataKategori, DataSatuan, DataSupplier, StokBatch, ObatMasuk, ObatKeluar};
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get Admin Dashboard Data
     *
     * @return array
     */
    public function getAdminDashboardData(): array
    {
        return [
            'totalDataObat' => Obat::count(),
            'totalDataCabang' => DataCabang::count(),
            'totalDataMaster' => $this->countMasterData(),
            'totalUsers' => User::count(),
            'monthlySalesChart' => $this->getMonthlySalesData(),
            'statusCards' => [
                'obat' => Obat::count(),
                'cabang' => DataCabang::count(),
                'users' => User::count(),
                'master' => $this->countMasterData(),
            ]
        ];
    }

    /**
     * Get Pharmacist Dashboard Data
     *
     * @return array
     */
    public function getPharmacistDashboardData(): array
    {
        return [
            'totalDataObat' => Obat::count(),
            'totalDataCabang' => DataCabang::count(),
            'transactionToday' => $this->getTodayTransactionCount(),
            'expiredMedicines' => $this->getExpiredMedicinesCount(),
            'weeklyTransactionChart' => $this->getWeeklyTransactionData(),
            'statusCards' => [
                'obat' => Obat::count(),
                'transaksi' => $this->getTodayTransactionCount(),
                'cabang' => DataCabang::count(),
                'kedaluwarsa' => $this->getExpiredMedicinesCount(),
            ]
        ];
    }

    /**
     * Get Manager Dashboard Data
     *
     * @return array
     */
    public function getManagerDashboardData(): array
    {
        return [
            'totalDataObat' => Obat::count(),
            'totalDataCabang' => DataCabang::count(),
            'predictedStockIssue' => $this->getPredictedStockIssueCount(),
            'availableReports' => 4, // Based on reports available in system
            'trendChart' => $this->getMonthlyTrendData(),
            'distributionChart' => $this->getStockDistributionData(),
            'statusCards' => [
                'obat' => Obat::count(),
                'cabang' => DataCabang::count(),
                'prediksi' => $this->getPredictedStockIssueCount(),
                'laporan' => 4,
            ]
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
     * Get Upcoming Expired Medicines (7-30 days)
     * Useful for pharmacist early warning
     *
     * @return int
     */
    public function getUpcomingExpiredCount(): int
    {
        $today = Carbon::today();
        $thirtyDaysLater = $today->copy()->addDays(30);

        return StokBatch::whereBetween('tanggal_kedaluwarsa', [$today, $thirtyDaysLater])
            ->where('stok_sisa', '>', 0)
            ->count();
    }
}
