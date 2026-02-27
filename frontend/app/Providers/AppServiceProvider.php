<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // membuat auto totle untuk tiap component
        View::composer('*', function ($view) {

            $titles = [
                'dashboard' => 'Dashboard',

                // Master Data
                'data-obat' => 'Data Obat',
                'data-satuan' => 'Data Satuan',
                'data-kategori' => 'Data Kategori',
                'data-cabang' => 'Data Cabang',
                'data-supplier' => 'Data Supplier',

                // Manajemen Stok
                'stok-cabang' => 'Stok Cabang',
                'stok-batch-kedaluwarsa' => 'Batch & Kedaluwarsa',
                'obat-masuk' => 'Obat Masuk',
                'stok-opname' => 'Stok Opname',

                // Transaksi
                'penjualan-obat' => 'Penjualan Obat',

                // AI & Monitoring
                'prediksi' => 'Prediksi Obat',
                'obat-kedaluwarsa' => 'Obat Kedaluwarsa',

                // Laporan
                'laporan' => 'Laporan Full',
                'laporan-obat' => 'Detail Laporan Obat',
                'laporan-penjualan' => 'Laporan Penjualan',
                'laporan-prediksi' => 'Laporan Prediksi',

                // Akun
                'kelola-akun' => 'Kelola Akun',
            ];

            $segment = Request::segment(1);

            $title = $titles[$segment] ?? 'Sistem Informasi Manajemen Obat';

            $view->with('title', $title);
        });
    }
}
