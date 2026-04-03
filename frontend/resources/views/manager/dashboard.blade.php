@extends('layouts.app')

@section('title', 'Dashboard Manajer - Sistem Apotek Sena Farma')

@section('content')
    <div class="dashboard-container">
        {{-- Dashboard Header --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card dashboard-header">
                    <div class="card-header">
                        <h4 class="card-title">Dashboard Manajer</h4>
                        <p class="card-subtitle">Selamat datang, {{ Auth::user()->username }}! 👋</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Total Obat</h6>
                                <h3 class="mb-0">{{ $statusCards['obat'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-pills fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Total Cabang</h6>
                                <h3 class="mb-0">{{ $statusCards['cabang'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-hospital fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Prediksi Stok Rendah</h6>
                                <h3 class="mb-0">{{ $statusCards['prediksi'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-chart-line fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Laporan Tersedia</h6>
                                <h3 class="mb-0">{{ $statusCards['laporan'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-file-medical fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">📋 Laporan & Analisis</h4>
                    </div>
                    <div class="card-body">
                        <div class="action-grid">
                            <a href="{{ url('/laporan') }}" class="action-link"
                                style="background: linear-gradient(135deg, #4ba2ff 0%, #357ae8 100%);">
                                <i class="fas fa-file-alt"></i> Laporan Full
                            </a>
                            <a href="{{ url('/prediksi') }}" class="action-link"
                                style="background: linear-gradient(135deg, #28a745 0%, #21863a 100%);">
                                <i class="fas fa-chart-line"></i> Prediksi Stok
                            </a>
                            <a href="{{ url('/stok-cabang') }}" class="action-link"
                                style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                <i class="fas fa-warehouse"></i> Monitoring Stok
                            </a>
                            <a href="{{ url('/obat-kedaluwarsa') }}" class="action-link"
                                style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                                <i class="fas fa-calendar-times"></i> Obat Kadaluarsa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="card chart-card">
                    <div class="card-header">
                        <h4 class="card-title">📈 Trend Stok Obat (Tahun Ini)</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <canvas id="trendChart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card chart-card">
                    <div class="card-header">
                        <h4 class="card-title">🎯 Status Stok</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('extra_js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // ========== Trend Chart ==========
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {{ json_encode($trendChart['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) }},
                        datasets: [{
                            label: 'Stok Masuk (Unit)',
                            data: {{ json_encode($trendChart['masuk'] ?? []) }},
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#28a745',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }, {
                            label: 'Stok Keluar (Unit)',
                            data: {{ json_encode($trendChart['keluar'] ?? []) }},
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#ffc107',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 13,
                                        weight: 'bold'
                                    },
                                    padding: 15,
                                    usePointStyle: true
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // ========== Distribution/Status Pie Chart ==========
                const pieCtx = document.getElementById('pieChart').getContext('2d');
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {{ json_encode($distributionChart['labels'] ?? ['Aman', 'Menipis', 'Kritis']) }},
                        datasets: [{
                            data: {{ json_encode($distributionChart['data'] ?? [0, 0, 0]) }},
                            backgroundColor: {{ json_encode($distributionChart['backgroundColor'] ?? ['#28a745', '#ffc107', '#dc3545']) }},
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    padding: 12,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
