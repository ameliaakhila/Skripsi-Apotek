<x-app :title="'Dashboard Manajer - Sistem Apotek Sena Farma'">
    <div class="container-fluid">
        {{-- Dashboard Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="font-weight-bold text-dark">Dashboard Manajer</h2>
                <p class="text-muted mb-0">Analisis kinerja dan pemantauan stok</p>
            </div>
            <button id="refreshDashboard" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt mr-1"></i> Refresh Data
            </button>
        </div>

        {{-- Statistics Cards --}}
        <div class="row">
            @php
                $cards = [
                    ['title' => 'Total Obat', 'value' => $statusCards['obat'] ?? 0, 'icon' => 'fas fa-pills', 'class' => 'bg-primary'],
                    ['title' => 'Total Cabang', 'value' => $statusCards['cabang'] ?? 0, 'icon' => 'fas fa-hospital', 'class' => 'bg-info'],
                    ['title' => 'Prediksi Stok Rendah', 'value' => $statusCards['prediksi'] ?? 0, 'icon' => 'fas fa-chart-line', 'class' => 'bg-success'],
                    ['title' => 'Laporan Tersedia', 'value' => $statusCards['laporan'] ?? 0, 'icon' => 'fas fa-file-medical', 'class' => 'bg-warning'],
                ];
            @endphp
            @foreach($cards as $card)
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card {{ $card['class'] }} text-white mb-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 small font-weight-bold">{{ $card['title'] }}</h6>
                                <h3 class="mb-0 font-weight-bolder">{{ $card['value'] }}</h3>
                            </div>
                            <i class="{{ $card['icon'] }} opacity-50" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Quick Actions --}}
        <div class="card chart-card mb-4">
            <div class="card-header">Akses Analisis</div>
            <div class="card-body py-3">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ url('/laporan') }}" class="action-card bg-primary">
                            <i class="fas fa-file-alt mr-2"></i> Laporan Full
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ url('/prediksi') }}" class="action-card bg-success">
                            <i class="fas fa-chart-line mr-2"></i> Prediksi Stok
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ url('/stok-cabang') }}" class="action-card bg-info">
                            <i class="fas fa-warehouse mr-2"></i> Monitoring Stok
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ url('/obat-kedaluwarsa') }}" class="action-card bg-danger">
                            <i class="fas fa-calendar-times mr-2"></i> Obat Kadaluarsa
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row">
            <div class="col-lg-8">
                <div class="card chart-card mb-4">
                    <div class="card-header">Trend Stok Obat</div>
                    <div class="card-body">
                        <div class="chart-wrapper" style="height: 300px;">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card chart-card mb-4">
                    <div class="card-header">Status Stok</div>
                    <div class="card-body">
                        <div class="chart-wrapper" style="height: 300px;">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Selling Section --}}
        <div class="card chart-card mb-4">
            <div class="card-header">Top 5 Obat Paling Laku</div>
            <div class="card-body">
                <div class="chart-wrapper" style="height: 300px;">
                    <canvas id="topSellingChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('extra_js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let trendChart, pieChart, topSellingChart;

            function updateCharts(data) {
                trendChart.data.labels = data.trendChart.labels;
                trendChart.data.datasets[0].data = data.trendChart.masuk;
                trendChart.data.datasets[1].data = data.trendChart.keluar;
                trendChart.update();

                pieChart.data.datasets[0].data = data.distributionChart.data;
                pieChart.update();

                topSellingChart.data.labels = data.topSellingDrugs.labels;
                topSellingChart.data.datasets[0].data = data.topSellingDrugs.data;
                topSellingChart.update();

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
                // Charts initialization
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($trendChart['labels'] ?? []) !!},
                        datasets: [
                            { label: 'Stok Masuk', data: {!! json_encode($trendChart['masuk'] ?? []) !!}, borderColor: '#1cc88a', tension: 0.4 },
                            { label: 'Stok Keluar', data: {!! json_encode($trendChart['keluar'] ?? []) !!}, borderColor: '#f6c23e', tension: 0.4 }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                const pieCtx = document.getElementById('pieChart').getContext('2d');
                pieChart = new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($distributionChart['labels'] ?? []) !!},
                        datasets: [{ data: {!! json_encode($distributionChart['data'] ?? []) !!}, backgroundColor: {!! json_encode($distributionChart['backgroundColor'] ?? []) !!} }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                const barCtx = document.getElementById('topSellingChart').getContext('2d');
                topSellingChart = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($topSellingDrugs['labels'] ?? []) !!},
                        datasets: [{ label: 'Total Terjual', data: {!! json_encode($topSellingDrugs['data'] ?? []) !!}, backgroundColor: '#4e73df' }]
                    },
                    options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false }
                });
            });
        </script>
    @endpush
</x-app>
