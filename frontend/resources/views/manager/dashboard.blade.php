<x-app :title="'Dashboard Manajer - Sistem Apotek Sena Farma'">
    <div class="container-fluid">
        {{-- Dashboard Header --}}
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="font-weight-bold">Dashboard Manajer</h2>
                    <p class="text-muted">Analisis kinerja dan pemantauan stok terkini.</p>
                </div>
                <button id="refreshDashboard" class="btn btn-outline-primary shadow-sm">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row" id="statsRow">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-8">Total Obat</h6>
                        <h3 class="mb-0 font-weight-bold" id="statObat">{{ $statusCards['obat'] ?? 0 }}</h3>
                        <i class="fas fa-pills"></i>
                        </div>
                </div>
                </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-8">Total Cabang</h6>
                        <h3 class="mb-0 font-weight-bold" id="statCabang">{{ $statusCards['cabang'] ?? 0 }}</h3>
                        <i class="fas fa-hospital"></i>
                        </div>
                </div>
                </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-8">Prediksi Stok Rendah</h6>
                        <h3 class="mb-0 font-weight-bold" id="statPrediksi">{{ $statusCards['prediksi'] ?? 0 }}</h3>
                        <i class="fas fa-chart-line"></i>
                        </div>
                </div>
                </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-8">Laporan Tersedia</h6>
                        <h3 class="mb-0 font-weight-bold" id="statLaporan">{{ $statusCards['laporan'] ?? 0 }}</h3>
                        <i class="fas fa-file-medical"></i>
                        </div>
                </div>
                </div>
                </div>
        <!-- ... -->
        @push('extra_js')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                let trendChart, pieChart, topSellingChart;

                function updateCharts(data) {
                    // Update Line Chart
                    trendChart.data.labels = data.trendChart.labels;
                    trendChart.data.datasets[0].data = data.trendChart.masuk;
                    trendChart.data.datasets[1].data = data.trendChart.keluar;
                    trendChart.update();

                    // Update Doughnut Chart
                    pieChart.data.datasets[0].data = data.distributionChart.data;
                    pieChart.update();

                    // Update Bar Chart
                    topSellingChart.data.labels = data.topSellingDrugs.labels;
                    topSellingChart.data.datasets[0].data = data.topSellingDrugs.data;
                    topSellingChart.update();

                    // Update Stats
                    $('#statObat').text(data.statusCards.obat);
                    $('#statCabang').text(data.statusCards.cabang);
                    $('#statPrediksi').text(data.statusCards.prediksi);
                    $('#statLaporan').text(data.statusCards.laporan);
                }

                $('#refreshDashboard').click(function () {
                    window.showLoader();
                    $.ajax({
                        url: '{{ route('dashboard.refresh') }}',
                        method: 'GET',
                        success: function (data) {
                            updateCharts(data);
                            window.hideLoader();
                        },
                        error: function () {
                            window.hideLoader();
                            alert('Gagal memperbarui data.');
                        }
                    });
                });

                document.addEventListener('DOMContentLoaded', function () {
                    // ... (Chart initialization code)
                    // Assign to global variables for refresh function
                    trendChart = new Chart(...);
                    pieChart = new Chart(...);
                    topSellingChart = new Chart(...);
                });
            </script>
        @endpush
        {{-- Quick Actions --}}
        <div class="row">
            <div class="col-12">
                <div class="card chart-card mb-4">
                    <div class="card-header border-0 pt-4">
                        <h4 class="card-title font-weight-bold">Akses Analisis</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="{{ url('/laporan') }}" class="action-card"
                                    style="background: linear-gradient(135deg, #4ba2ff 0%, #357ae8 100%);">
                                    <i class="fas fa-file-alt mb-2" style="font-size: 1.5rem;"></i><br>Laporan Full
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ url('/prediksi') }}" class="action-card"
                                    style="background: linear-gradient(135deg, #28a745 0%, #21863a 100%);">
                                    <i class="fas fa-chart-line mb-2" style="font-size: 1.5rem;"></i><br>Prediksi Stok
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ url('/stok-cabang') }}" class="action-card"
                                    style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                    <i class="fas fa-warehouse mb-2" style="font-size: 1.5rem;"></i><br>Monitoring Stok
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ url('/obat-kedaluwarsa') }}" class="action-card"
                                    style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                                    <i class="fas fa-calendar-times mb-2" style="font-size: 1.5rem;"></i><br>Obat Kadaluarsa
                                </a>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row">
            <div class="col-lg-8">
                <div class="card chart-card mb-4">
                    <div class="card-header border-0 pt-4">
                        <h4 class="card-title font-weight-bold">Trend Stok Obat</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper" style="height: 300px;">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card chart-card mb-4">
                    <div class="card-header border-0 pt-4">
                        <h4 class="card-title font-weight-bold">Status Stok</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper" style="height: 300px;">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{-- Top Selling Section --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card chart-card mb-4">
            <div class="card-header border-0 pt-4 d-flex justify-content-between align-items-center">
                <h4 class="card-title font-weight-bold">Top 5 Obat Paling Laku</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper" style="height: 300px;">
                            <canvas id="topSellingChart"></canvas>
                        </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    <!-- Scripts ... -->


    @push('extra_js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Global Chart Config
                Chart.defaults.font.family = "'Poppins', sans-serif";
                Chart.defaults.color = '#6e6e6e';

                // ========== Trend Chart (Line) ==========
                const trendCtx = document.getElementById('trendChart').getContext('2d');

                // Create Gradients
                const gradMasuk = trendCtx.createLinearGradient(0, 0, 0, 400);
                gradMasuk.addColorStop(0, 'rgba(40, 167, 69, 0.4)');
                gradMasuk.addColorStop(1, 'rgba(40, 167, 69, 0.0)');

                const gradKeluar = trendCtx.createLinearGradient(0, 0, 0, 400);
                gradKeluar.addColorStop(0, 'rgba(255, 193, 7, 0.4)');
                gradKeluar.addColorStop(1, 'rgba(255, 193, 7, 0.0)');

                trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($trendChart['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
                        datasets: [{
                            label: 'Stok Masuk (Unit)',
                            data: {!! json_encode($trendChart['masuk'] ?? []) !!},
                            borderColor: '#28a745',
                            backgroundColor: gradMasuk,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#28a745',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }, {
                            label: 'Stok Keluar (Unit)',
                            data: {!! json_encode($trendChart['keluar'] ?? []) !!},
                            borderColor: '#ffc107',
                            backgroundColor: gradKeluar,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffc107',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
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
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: { size: 12, weight: '600' }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#333',
                                bodyColor: '#666',
                                bodySpacing: 4,
                                padding: 12,
                                borderColor: '#e2e2e2',
                                borderWidth: 1,
                                titleFont: { size: 14, weight: 'bold' },
                                callbacks: {
                                    label: function (context) {
                                        return ' ' + context.dataset.label + ': ' + context.parsed.y + ' unit';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { borderDash: [5, 5], color: '#f0f0f0' },
                                ticks: { stepSize: 50 }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });

                // ========== Distribution Pie Chart (Doughnut) ==========
                const pieCtx = document.getElementById('pieChart').getContext('2d');
                pieChart = new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($distributionChart['labels'] ?? ['Aman', 'Menipis', 'Kritis']) !!},
                        datasets: [{
                            data: {!! json_encode($distributionChart['data'] ?? [0, 0, 0]) !!},
                            backgroundColor: {!! json_encode($distributionChart['backgroundColor'] ?? ['#28a745', '#ffc107', '#dc3545']) !!},
                            hoverOffset: 15,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: { size: 12, weight: '600' }
                                }
                            }
                        }
                    }
                });

                // ========== Top Selling Bar Chart ==========
                const barCtx = document.getElementById('topSellingChart').getContext('2d');

                const gradBar = barCtx.createLinearGradient(0, 0, 400, 0);
                gradBar.addColorStop(0, '#4ba2ff');
                gradBar.addColorStop(1, '#357ae8');

                topSellingChart = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($topSellingDrugs['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Total Terjual (Unit)',
                            data: {!! json_encode($topSellingDrugs['data'] ?? []) !!},
                            backgroundColor: gradBar,
                            borderRadius: 8,
                            barThickness: 35,
                            maxBarThickness: 50
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { display: false }
                            },
                            y: {
                                grid: { display: false },
                                ticks: {
                                    font: { weight: 'bold' }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app>
