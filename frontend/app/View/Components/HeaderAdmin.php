<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderAdmin extends Component
{
    public function __construct(
        public $title = 'Apotek Sena Farma',
        public $nav = [
            [
                'type' => 'link',
                'route' => 'dashboard',
                'icon' => 'fas fa-home',
                'label' => 'Dashboard',
            ],
            [
                'type' => 'group',
                'icon' => 'fas fa-database',
                'label' => 'Master Data',
                'children' => [
                    ['route' => 'data-obat', 'label' => 'Data Obat'],
                    ['route' => 'data-satuan', 'label' => 'Data Satuan'],
                    ['route' => 'data-kategori', 'label' => 'Data Kategori'],
                    ['route' => 'data-supplier', 'label' => 'Data Supplier'],
                    ['route' => 'data-cabang', 'label' => 'Data Cabang'],
                ],
            ],
            [
                'type' => 'group',
                'icon' => 'fas fa-boxes',
                'label' => 'Manajemen Stok',
                'children' => [
                    ['route' => 'obat-masuk', 'label' => 'Obat Masuk'],
                    ['route' => 'stok-opname', 'label' => 'Stok Opname'],
                    ['route' => 'mutasi', 'label' => 'Mutasi Cabang'],
                    ['route' => 'stok-cabang', 'label' => 'Stok Cabang'],
                    ['route' => 'stok-batch-kedaluwarsa', 'label' => 'Batch & Kedaluwarsa'],
                    ['route' => 'minimum-stok', 'label' => 'Minimum Stok'],
                ],
            ],
            [
                'type' => 'group',
                'icon' => 'fas fa-shopping-cart',
                'label' => 'Transaksi',
                'children' => [
                    ['route' => 'pembelian-obat', 'label' => 'Pembelian Obat'],
                    ['route' => 'detail-pembelian-obat', 'label' => 'Detail Pembelian Obat'],
                    ['route' => 'penjualan-obat', 'label' => 'Penjualan Obat'],
                    ['route' => 'detail-penjualan-obat', 'label' => 'Detail Penjualan Obat'],
                ],
            ],
            [
                'type' => 'link',
                'route' => 'prediksi',
                'icon' => 'fas fa-location-arrow',
                'label' => 'Prediksi Obat',
            ],
            [
                'type' => 'link',
                'route' => 'obat-kedaluwarsa',
                'icon' => 'fas fa-exclamation-circle',
                'label' => 'Obat Kedaluwarsa',
            ],
            [
                'type' => 'group',
                'icon' => 'fas fa-file-medical',
                'label' => 'Laporan',
                'children' => [
                    ['route' => 'laporan', 'label' => 'Laporan Full'],
                    ['route' => 'laporan-obat', 'label' => 'Detail Laporan Obat'],
                    ['route' => 'laporan-penjualan', 'label' => 'Laporan Penjualan'],
                    ['route' => 'laporan-prediksi', 'label' => 'Laporan Prediksi'],
                ],
            ],
            [
                'type' => 'link',
                'route' => 'kelola-akun',
                'icon' => 'fas fa-users',
                'label' => 'Kelola Akun',
            ],
        ]
    ) {
        $this->title = $title;
        $this->nav = $nav;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.navbar');
    }
}
