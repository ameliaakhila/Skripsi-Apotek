<x-app :title="'Dashboard Apoteker - Sistem Apotek Sena Farma'">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="font-weight-bold text-dark">Dashboard Apoteker</h2>
            </div>
            <div class="text-right">
                <p class="mb-0 font-weight-bold text-dark">{{ now()->translatedFormat('l, d F Y') }}</p>
                <div id="live-clock" class="text-primary font-weight-bold" style="font-size: 1.1rem;">--:--:--</div>
            </div>
        </div>

        <div class="row">
            @php
                $cards = [
                    ['title' => 'Total Jenis Obat', 'value' => $statusCards['obat'] ?? 0, 'icon' => 'fas fa-pills', 'class' => 'bg-primary'],
                    ['title' => 'Transaksi Hari Ini', 'value' => $statusCards['transaksi'] ?? 0, 'icon' => 'fas fa-exchange-alt', 'class' => 'bg-success'],
                    ['title' => 'Stok Rendah', 'value' => $statusCards['stok_rendah'] ?? 0, 'icon' => 'fas fa-layer-group', 'class' => 'bg-warning'],
                    ['title' => 'Mendekati Expired', 'value' => $statusCards['hampir_kedaluwarsa'] ?? 0, 'icon' => 'fas fa-hourglass-half', 'class' => 'bg-info'],
                ];
            @endphp
            @foreach($cards as $card)
                <div class="col-lg-3">
                    <div class="card stat-card {{ $card['class'] }} text-white mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-0 small font-weight-bold">{{ $card['title'] }}</h6>
                                <h3 class="mb-0 font-weight-bolder">{{ $card['value'] }}</h3>
                            </div>
                            <i class="{{ $card['icon'] }} opacity-50" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-7 card chart-card mb-4">
                        <div class="card-header">Grafik Transaksi Minggu Ini</div>
                        <div class="card-body">
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="transactionChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card chart-card mb-4">
                            <div class="card-header">Status Ketersediaan Stok</div>
                            <div class="card-body">
                                <div style="height: 200px;">
                                    <canvas id="stockDistChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <style>
                    /* ... existing styles ... */

                    /* Timeline styling */
                    .activity-timeline {
                        position: relative;
                        padding-left: 15px;
                        border-left: 2px solid #e3e6f0;
                    }

                    .activity-item {
                        position: relative;
                        margin-bottom: 1.5rem;
                    }

                    .activity-item::before {
                        content: '';
                        position: absolute;
                        left: -22px;
                        top: 0;
                        width: 12px;
                        height: 12px;
                        background: #fff;
                        border: 2px solid var(--primary);
                        border-radius: 50%;
                    }

                    .activity-item:last-child {
                        margin-bottom: 0;
                    }
                </style>

                {{-- Recent Activities --}}
                <div class="card chart-card mb-4">
                    <div class="card-header">Aktivitas Terakhir</div>
                    <div class="card-body">
                        <div id="activity-container" style="max-height: 250px; overflow-y: hidden; transition: max-height 0.3s ease;">
                            <div class="activity-timeline">
                                @forelse($recentActivities as $index => $activity)
                                    <div class="activity-item {{ $index >= 5 ? 'd-none' : '' }} activity-row">
                                        <p class="mb-0 font-weight-bold text-dark small">{{ $activity['title'] }}</p>
                                        <p class="mb-0 text-muted small">{{ $activity['description'] }}</p>
                                        <span class="text-primary small font-italic">{{ $activity['time'] }}</span>
                                    </div>
                                @empty
                                    <p class="text-muted small">Tidak ada aktivitas.</p>
                                @endforelse
                            </div>
                        </div>
                        @if(count($recentActivities) > 5)
                            <button id="see-more-btn" class="btn btn-sm btn-link btn-block mt-2" onclick="toggleActivities()">Lihat Semua</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Fixed Live Clock
                    function updateClock() {
                        const now = new Date();
                        document.getElementById('live-clock').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
                    }
                    setInterval(updateClock, 1000);
                    updateClock();

                    // Main Transaction Chart
                    const transCtx = document.getElementById('transactionChart').getContext('2d');
                    new Chart(transCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($weeklyTransactionChart['labels'] ?? []) !!},
                            datasets: [{
                                label: 'Obat Masuk',
                                data: {!! json_encode($weeklyTransactionChart['incoming'] ?? []) !!},
                                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                borderColor: '#28a745',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'Obat Keluar',
                                    data: {!! json_encode($weeklyTransactionChart['outgoing'] ?? []) !!},
                                    backgroundColor: 'rgba(253, 126, 20, 0.1)',
                                    borderColor: '#fd7e14',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true
                                }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                    });

                    // Recent Activities Toggle
                    window.toggleActivities = function() {
                        const container = document.getElementById('activity-container');
                        const btn = document.getElementById('see-more-btn');
                        const hiddenItems = document.querySelectorAll('.activity-row.d-none');

                        if (hiddenItems.length > 0) {
                            // Show all
                            container.style.maxHeight = '350px';
                            container.style.overflowY = 'auto';
                            hiddenItems.forEach(item => item.classList.remove('d-none'));
                            btn.textContent = 'Sembunyikan';
                        } else {
                            // Hide extra
                            container.style.maxHeight = '250px';
                            container.style.overflowY = 'hidden';
                            document.querySelectorAll('.activity-row').forEach((item, index) => {
                                if (index >= 5) item.classList.add('d-none');
                            });
                            btn.textContent = 'Lihat Semua';
                        }
                    };

                    // Stock Distribution Pie Chart
                    const distCtx = document.getElementById('stockDistChart').getContext('2d');
                    new Chart(distCtx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($stockDistribution['labels'] ?? []) !!},
                            datasets: [{
                                data: {!! json_encode($stockDistribution['data'] ?? []) !!},
                                backgroundColor: {!! json_encode($stockDistribution['backgroundColor'] ?? ['#28a745', '#ffc107', '#dc3545']) !!},
                                borderWidth: 0
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
                    });
                });
            </script>
        @endpush
</x-app>
