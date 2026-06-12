<x-app :title="'Dashboard Apoteker - Sistem Apotek Sena Farma'">
    <div class="container-fluid">
        {{-- Dashboard Header --}}
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="font-weight-bold">Selamat Datang, Apoteker! 👋</h2>
                    <p class="text-muted">Pantau stok obat dan aktivitas transaksi harian.</p>
                </div>
                <div class="text-right">
                    <p class="mb-0 font-weight-bold">{{ now()->translatedFormat('l, d F Y') }}</p>
                    <span id="live-clock" class="badge badge-primary">00:00:00</span>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row">
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-8">Total Jenis Obat</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $statusCards['obat'] ?? 0 }}</h2>
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-8">Transaksi Hari Ini</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $statusCards['transaksi'] ?? 0 }}</h2>
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-8">Stok Rendah</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $statusCards['stok_rendah'] ?? 0 }}</h2>
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card stat-card bg-danger text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-8">Telah Kedaluwarsa</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $statusCards['kedaluwarsa'] ?? 0 }}</h2>
                        <i class="fas fa-calendar-times"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Quick Actions --}}
            <div class="col-lg-8">
                <div class="card chart-card mb-4">
                    <div class="card-header border-0 pt-4">
                        <h4 class="card-title font-weight-bold">⚡ Akses Cepat Operasional</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 col-md-3 mb-3">
                                <a href="{{ route('obat-masuk.index') }}" class="action-card" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);">
                                    <i class="fas fa-file-import mb-2" style="font-size: 1.5rem;"></i><br>Obat Masuk
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <a href="{{ route('obat-keluar.index') }}" class="action-card" style="background: linear-gradient(135deg, #fd7e14 0%, #d96b01 100%);">
                                    <i class="fas fa-file-export mb-2" style="font-size: 1.5rem;"></i><br>Obat Keluar
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <a href="{{ route('stok-cabang.index') }}" class="action-card" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
                                    <i class="fas fa-boxes mb-2" style="font-size: 1.5rem;"></i><br>Stok Cabang
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <a href="{{ route('obat-kedaluwarsa.index') }}" class="action-card" style="background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);">
                                    <i class="fas fa-history mb-2" style="font-size: 1.5rem;"></i><br>Data Expired
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Chart --}}
                <div class="card chart-card mb-4">
                    <div class="card-header border-0 pt-4 d-flex justify-content-between align-items-center">
                        <h4 class="card-title font-weight-bold">📊 Grafik Transaksi Minggu Ini</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="transactionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Side Panel --}}
            <div class="col-lg-4">
                {{-- Stock Distribution Chart --}}
                <div class="card chart-card mb-4">
                    <div class="card-header border-0 pt-4">
                        <h4 class="card-title font-weight-bold">📦 Status Ketersediaan Stok</h4>
                    </div>
                    <div class="card-body">
                        <div style="height: 200px;">
                            <canvas id="stockDistChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Recent Activities --}}
                <div class="card chart-card mb-4">
                    <div class="card-header border-0 pt-4">
                        <h4 class="card-title font-weight-bold">🕒 Aktivitas Terakhir</h4>
                    </div>
                    <div class="card-body">
                        {{-- Activity timeline markup ... --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Live Clock
                function updateClock() {
                    const now = new Date();
                    const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                                     now.getMinutes().toString().padStart(2, '0') + ':' + 
                                     now.getSeconds().toString().padStart(2, '0');
                    document.getElementById('live-clock').textContent = timeString;
                }
                setInterval(updateClock, 1000);
                updateClock();

                // Main Transaction Chart
                const transCtx = document.getElementById('transactionChart').getContext('2d');
                new Chart(transCtx, {
                    type: 'line',
                    data: {
                        labels: {{ json_encode($weeklyTransactionChart['labels'] ?? []) }},
                        datasets: [{
                            label: 'Obat Masuk',
                            data: {{ json_encode($weeklyTransactionChart['incoming'] ?? []) }},
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderColor: '#28a745',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#28a745',
                            pointRadius: 4
                        }, {
                            label: 'Obat Keluar',
                            data: {{ json_encode($weeklyTransactionChart['outgoing'] ?? []) }},
                            backgroundColor: 'rgba(253, 126, 20, 0.1)',
                            borderColor: '#fd7e14',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#fd7e14',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: { family: "'Nunito', sans-serif", size: 12, weight: '600' }
                                }
                            },
                            tooltip: {
                                padding: 10,
                                cornerRadius: 8,
                                bodyFont: { family: "'Nunito', sans-serif" },
                                titleFont: { family: "'Nunito', sans-serif", weight: 'bold' }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                                ticks: { font: { family: "'Nunito', sans-serif" } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: "'Nunito', sans-serif" } }
                            }
                        }
                    }
                });

                // Stock Distribution Pie Chart
                const distCtx = document.getElementById('stockDistChart').getContext('2d');
                new Chart(distCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {{ json_encode($stockDistribution['labels'] ?? []) }},
                        datasets: [{
                            data: {{ json_encode($stockDistribution['data'] ?? []) }},
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app>
