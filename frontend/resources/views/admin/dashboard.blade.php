<x-app :title="'Dashboard Admin - Sistem Apotek Sena Farma'">
    <div class="container-fluid">
        {{-- Dashboard Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="font-weight-bold">Dashboard Admin</h2>
                <p class="text-muted">Kelola sistem, pengguna, dan pantau aktivitas apotek secara menyeluruh.</p>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="text-white font-weight-bolder text-uppercase mb-2">Total Obat</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $statusCards['obat'] ?? 0 }}</h3>
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-white font-weight-bolder text-uppercase mb-2">Total Cabang</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $statusCards['cabang'] ?? 0 }}</h3>
                        <i class="fas fa-hospital"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="text-white font-weight-bolder text-uppercase mb-2">Master Data</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $statusCards['master'] ?? 0 }}</h3>
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-danger text-white">
                    <div class="card-body">
                        <h6 class="text-white font-weight-bolder text-uppercase mb-2">Total Users</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $statusCards['users'] ?? 0 }}</h3>
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Analysis & Logs --}}
        <div class="row">
            {{-- Main Chart --}}
            <div class="col-lg-8">
                <div class="card chart-card mb-4">
                    <div class="card-header border-0 pt-4">
                        <h4 class="card-title font-weight-bold">Grafik Penjualan Tahun Ini</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper" style="height: 350px;">
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expiring Medicines Alert --}}
            <div class="col-lg-4">
                <div class="card chart-card mb-4 border-top border-bottom border-danger">
                    <div class="card-header border-0 pt-4 pb-0">
                        <h4 class="card-title font-weight-bold text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>Obat Mendekati Kedaluwarsa
                        </h4>
                    </div>
                    <div class="card-body p-0 pt-3">
                        <ul class="list-group list-group-flush">
                            @forelse($expiringMedicines ?? [] as $medicine)
                                <li class="list-group-item px-4 py-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 font-weight-bold">{{ optional($medicine->Obat)->nama_obat ?? 'Unknown' }}</h6>
                                        <span
                                            class="badge {{ ($medicine->tanggal_kedaluwarsa && \Carbon\Carbon::parse($medicine->tanggal_kedaluwarsa)->isPast()) ? 'bg-danger' : 'bg-warning' }} text-white">
                                            {{ $medicine->tanggal_kedaluwarsa ? \Carbon\Carbon::parse($medicine->tanggal_kedaluwarsa)->format('d M Y') : '-' }}
                                        </span>
                                    </div>
                                    <p class="mb-0 text-muted small">Batch: {{ $medicine->kode_batch }} | Cabang:
                                        {{ optional($medicine->DataCabang)->nama_cabang ?? '-' }}
                                    </p>
                                </li>
                            @empty
                                <li class="list-group-item px-4 py-4 text-center text-muted">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <p class="mb-0">Tidak ada obat mendekati kedaluwarsa.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
                    </div>
                    </div>
                    </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const montlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
                new Chart(montlyCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Stok Keluar (Unit)',
                            data: [{{ implode(',', $monthlySalesChart ?? []) }}],
                            borderColor: '#4ba2ff',
                            backgroundColor: 'rgba(75, 162, 255, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#4ba2ff',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app>
