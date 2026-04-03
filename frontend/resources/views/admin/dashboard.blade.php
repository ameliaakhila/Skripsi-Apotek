@extends('layouts.app')

@section('title', 'Dashboard - Sistem Apotek Sena Farma')

@section('content')
    <div class="dashboard-container">
        {{-- Dashboard Header --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card dashboard-header">
                    <div class="card-header">
                        <h4 class="card-title">Admin Dashboard</h4>
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
                <div class="card stat-card bg-success text-white">
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
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Master Data</h6>
                                <h3 class="mb-0">{{ $statusCards['master'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-database fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Total Users</h6>
                                <h3 class="mb-0">{{ $statusCards['users'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart Section --}}
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h4 class="card-title">📊 Grafik Penjualan (Stok Keluar) - Tahun Ini</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <canvas id="monthlySalesChart" height="80"></canvas>
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
                // Monthly Sales Chart
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
            });
        </script>
    @endpush
@endsection
