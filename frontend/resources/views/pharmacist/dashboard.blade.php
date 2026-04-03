@extends('layouts.app')

@section('title', 'Dashboard Apoteker - Sistem Apotek Sena Farma')

@section('content')
    <div class="dashboard-container">
        {{-- Dashboard Header --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card dashboard-header">
                    <div class="card-header">
                        <h4 class="card-title">Dashboard Apoteker</h4>
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
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Transaksi Hari Ini</h6>
                                <h3 class="mb-0">{{ $statusCards['transaksi'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-exchange-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Stok Cabang</h6>
                                <h3 class="mb-0">{{ $statusCards['cabang'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-warehouse fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card stat-card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Obat Kadaluarsa</h6>
                                <h3 class="mb-0">{{ $statusCards['kedaluwarsa'] ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-exclamation-circle fa-3x opacity-50"></i>
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
                        <h4 class="card-title">⚡ Akses Cepat</h4>
                    </div>
                    <div class="card-body">
                        <div class="action-grid">
                            <a href="{{ url('/obat-masuk') }}" class="action-link"
                                style="background: linear-gradient(135deg, #4ba2ff 0%, #357ae8 100%);">
                                <i class="fas fa-arrow-down"></i> Obat Masuk
                            </a>
                            <a href="{{ url('/obat-keluar') }}" class="action-link"
                                style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
                                <i class="fas fa-arrow-up"></i> Obat Keluar
                            </a>
                            <a href="{{ url('/stok-cabang') }}" class="action-link"
                                style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                <i class="fas fa-warehouse"></i> Stok Cabang
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

        {{-- Chart Section --}}
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h4 class="card-title">📊 Grafik Transaksi Obat (Minggu Ini)</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <canvas id="transactionChart" height="80"></canvas>
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
                const ctx = document.getElementById('transactionChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {{ json_encode($weeklyTransactionChart['labels'] ?? ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']) }},
                        datasets: [{
                            label: 'Obat Masuk (Unit)',
                            data: {{ json_encode($weeklyTransactionChart['incoming'] ?? []) }},
                            backgroundColor: 'rgba(40, 167, 69, 0.8)',
                            borderColor: '#28a745',
                            borderWidth: 2,
                            borderRadius: 6
                        }, {
                            label: 'Obat Keluar (Unit)',
                            data: {{ json_encode($weeklyTransactionChart['outgoing'] ?? []) }},
                            backgroundColor: 'rgba(255, 193, 7, 0.8)',
                            borderColor: '#ffc107',
                            borderWidth: 2,
                            borderRadius: 6
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
