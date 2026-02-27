{{-- ? Admin Header Start--}}
<div class="nav-header">
    <a href="/" class="brand-logo">
        <img src="./assets/images/logosf.png" alt="logo-apotek" class="w-25">
        <div class="brand-title">
            <h2 class="">Sistem</h2>
            <span class="brand-sub-title fs-4">Apotek Sena Farma</span>
        </div>
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span>
            <span class="line"></span>
            <span class="line"></span>
        </div>
    </div>
</div>
{{-- ? Admin Header End --}}


{{-- ? Header Dashboard Start --}}
<div class="header border-bottom">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar text-primary">
                        {{ $title }}
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <img src="./assets/images/logosf.png" width="20" alt="logo-apotek" />
                            <div class="header-info ms-3">
                                <span class="fs-18 font-w500 mb-2">
                                    Admin
                                </span>
                                <small class="fs-12 font-w400">
                                    admin@gmail.com
                                </small>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="/" class="dropdown-item ai-icon">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span class="ms-2">Logout </span>
                            </a>
                        </div>
                    </li>
                </ul>

            </div>
        </nav>
    </div>
</div>
{{-- ? Header Dashboard End --}}

{{-- ? Admin Menu --}}
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">

            <x-link.nav-link route="dashboard" icon="fas fa-home" label="Dashboard" />

            <x-link.nav-group icon="fas fa-database" label="Master Data">
                <x-link.nav-link route="data-obat" label="Data Obat" />
                <x-link.nav-link route="data-satuan" label="Data Satuan" />
                <x-link.nav-link route="data-kategori" label="Data Kategori" />
                <x-link.nav-link route="data-supplier" label="Data Supplier" />
                <x-link.nav-link route="data-cabang" label="Data Cabang" />
            </x-link.nav-group>

            <x-link.nav-group icon="fas fa-boxes" label="Manajemen Stok">
                <x-link.nav-link route="obat-masuk" label="Obat Masuk" />
                <x-link.nav-link route="stok-opname" label="Stok Opname" />
                <x-link.nav-link route="mutasi" label="Mutasi Cabang" />
                <x-link.nav-link route="stok-cabang" label="Stok Cabang" />
                <x-link.nav-link route="stok-batch-kedaluwarsa" label="Batch & Kedaluwarsa" />
                <x-link.nav-link route="minimum-stok" label="Minimum Stok" />
            </x-link.nav-group>
            <x-link.nav-group icon="fas fa-shopping-cart" label="Transaksi">
                <x-link.nav-link route="pembelian-obat" label="Pembelian Obat" />
                <x-link.nav-link route="detail-pembelian-obat" label="Detail Pembelian Obat" />
                <x-link.nav-link route="penjualan-obat" label="Penjualan Obat" />
                <x-link.nav-link route="detail-penjualan-obat" label="Detail Penjualan Obat" />
            </x-link.nav-group>

            <x-link.nav-link route="prediksi" icon="fas fa-location-arrow" label="Prediksi Obat" />

            <x-link.nav-link route="obat-kedaluwarsa" icon="fas fa-exclamation-circle" label="Obat Kedaluwarsa" />

            <x-link.nav-group icon="fas fa-file-medical" label="Laporan">
                <x-link.nav-link route="laporan" label="Laporan Full" />
                <x-link.nav-link route="laporan-obat" label="Detail Laporan Obat" />
                <x-link.nav-link route="laporan-penjualan" label="Laporan Penjualan" />
                <x-link.nav-link route="laporan-prediksi" label="Laporan Prediksi" />
            </x-link.nav-group>

            <x-link.nav-link route="/kelola-akun" icon="fas fa-users" label="Kelola Akun" />
        </ul>
    </div>
</div>
{{-- ? Admin Menu End --}}