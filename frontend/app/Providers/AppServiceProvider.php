<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

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
        if ($this->app->environment('production') || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // membuat auto totle untuk tiap component
        View::composer('*', function ($view) {

            $titles = [
                'dashboard' => 'Dashboard',

                // Master Data
                'data-obat' => 'Data Obat',
                'data-satuan' => 'Data Satuan',
                'data-kategori' => 'Data Kategori',
                'data-supplier' => 'Data Supplier',
                'data-cabang' => 'Data Cabang',

                // Transaksi Stok
                'obat-masuk' => 'Obat Masuk',
                'obat-keluar' => 'Obat Keluar',
                'mutasi-stok' => 'Mutasi Stok',

                // Monitoring Stok
                'stok-cabang' => 'Stok Cabang',
                'stok-batch-kedaluwarsa' => 'Batch & Kedaluwarsa',
                'minimum-stok' => 'Minimum Stok',
                'stok-opname' => 'Stok Opname',

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
