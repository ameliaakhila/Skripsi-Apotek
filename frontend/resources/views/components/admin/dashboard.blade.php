<x-layout title="Dashboard">
    {{-- @include('components.admin.dashboard') --}}
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <h3 class="text-primary fw-bold">Smart Pharmacy Inventory System</h3>
                                    <p>Pantau stok, kelola batch & kadaluarsa, serta optimalkan persediaan dengan sistem
                                        otomatis berbasis data.</p>
                                </div>
                                <div class="col-6">
                                    <img class="w-100" src="{{ asset('./assets/images/dashboard-animate.svg') }}"
                                        alt="logo-dashboard">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header bg-primary py-2">
                            <h4 class="card-title text-white">Grafik penjualan bulanan</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="barChart_1" style="max-heightt: 100vh;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="widget-stat card bg-primary">
                        <div class="card-body  p-4">
                            <div class="media">
                                <span class="me-3">
                                    <i class="fas fa-pills"></i>
                                </span>
                                <div class="media-body text-white text-end">
                                    <p class="mb-1">Total Stok Aktif</p>
                                    <h3 class="text-white">{{ $totalDataObat }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="widget-stat card bg-success">
                        <div class="card-body  p-4">
                            <div class="media">
                                <span class="me-3">
                                    <i class="fas fa-hospital"></i>
                                </span>
                                <div class="media-body text-white text-end">
                                    <p class="mb-1">Total Cabang</p>
                                    <h3 class="text-white">{{ $totalDataCabang }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="widget-stat card bg-info">
                        <div class="card-body  p-4">
                            <div class="media">
                                <span class="me-3">
                                    <i class="fas fa-comment-medical"></i>
                                </span>
                                <div class="media-body text-white text-end">
                                    <p class="mb-1">Stok Hampir Habis</p>
                                    <h3 class="text-white">10</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card bg-danger">
                        <div class="card-body  p-4">
                            <div class="media">
                                <span class="me-3">
                                    <i class="fas fa-file-medical"></i>
                                </span>
                                <div class="media-body text-white text-end">
                                    <p class="mb-1">Obat Hampir Kedaluwarsa</p>
                                    <h3 class="text-white">10</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Kirim data chart dari route ke JS global --}}
    <script>
        window.chartData = @json($chartData);
    </script>
</x-layout>