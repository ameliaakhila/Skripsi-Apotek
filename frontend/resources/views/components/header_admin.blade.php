{{-- ? Admin Header Start--}}
<div class="nav-header">
    <a href="/" class="brand-logo">
        <img src="./assets/images/logo1.png" alt="logo-apotek" class="w-25">
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


{{-- ? Header Dashboard End --}}
<div class="header border-bottom">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar text-primary">
                        Dashboard
                    </div>
                </div>
                <ul class="navbar-nav header-right">

                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="#javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <img src="./assets/images/logo1.png" width="20" alt="logo-apotek" />
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
                            <a href="#../admin/logout.php" class="dropdown-item ai-icon">
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

            <li>
                <a href="/dashboard" aria-expanded="false">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <li>
                <a class="has-arrow" href="#javascript:void(0)" aria-expanded="false">
                    <i class="fas fa-pills"></i>
                    <span class="nav-text">Kelola Obat</span>
                </a>
                <ul>
                    <li><a href="/data-obat">Data Obat</a></li>
                    <li><a href="/kategori-obat">Kategori Obat</a></li>
                    <li><a href="/obat-masuk">Stok Obat Masuk</a></li>
                </ul>
            </li>

            <li>
                <a href="/penjualan-obat">
                    <i class="fas fa-wallet"></i>
                    <span class="nav-text">Penjualan Obat</span>
                </a>
            </li>

            <li>
                <a href="/prediksi">
                    <i class="fas fa-location-arrow"></i>
                    <span class="nav-text">Prediksi Obat</span>
                </a>
            </li>

            <li>
                <a href="/obat-kadaluarsa">
                    <i class="fa fa-exclamation-circle"></i>
                    <span class="nav-text">Obat Kadaluarsa</span>
                </a>
            </li>

            <li>
                <a class="has-arrow" href="#javascript:void(0)" aria-expanded="false">
                    <i class="fas fa-file-medical"></i>
                    <span class="nav-text">Laporan</span>
                </a>
                <ul>
                    <li><a href="/laporan">Laporan</a></li>
                    <li><a href="/laporan-obat">Laporan Obat</a></li>
                    <li><a href="/laporan-penjualan">Laporan penjualan</a></li>
                    <li><a href="/laporan-prediksi">Laporan Prediksi</a></li>
                </ul>
            </li>


            <li>
                <a href="/kelola-akun">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Kelola Akun</span>
                </a>
            </li>

        </ul>
    </div>
</div>
{{-- ? Admin Menu End --}}
