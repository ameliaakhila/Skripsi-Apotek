<x-app title="Prediksi Obat">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="mx-4 d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h5 class="card-title mb-0">
                            Prediksi Stok Obat (Total: {{ count($data) }})
                            @if($api_available ?? false)
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> ML Model Aktif</span>
                            @else
                                <span class="badge badge-warning"><i class="fas fa-database"></i> Mode Database</span>
                            @endif
                        </h5>
                        <small class="text-muted">
                            Update terakhir: {{ now()->format('d/m/Y H:i:s') }}
                            &nbsp;|&nbsp;
                            <i class="fas fa-calendar-alt"></i>
                            Berdasarkan riwayat transaksi <strong>3 bulan terakhir</strong>
                        </small>
                    </div>

                    <div>
                        <form action="{{ route('prediksi.generate') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary" id="btnGeneratePrediction">
                                <i class="fas fa-redo-alt"></i> Hitung Prediksi
                            </button>
                        </form>
                        <button id="btnCetakDropdown" type="button" class="btn btn-success">
                            <a href="{{ route('prediksi.export-excel') }}" class="text-white">
                                <i class="fas fa-file-excel"></i>
                                Cetak Laporan
                            </a>
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error') || isset($error))
                    <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') ?? $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Info Box Penjelasan Prediksi --}}
                <div class="mx-4 mb-2">
                    <div class="alert alert-info py-2 mb-0" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle"></i>
                        <strong>Cara Baca:</strong>
                        <b>Kebutuhan 1 Bulan</b> = Prediksi stok yang harus tersedia 1 bulan ke depan
                        (berdasarkan rata-rata penjualan 3 bulan terakhir + safety stock).
                        <b>Rekomendasi Order</b> = Jumlah unit yang perlu dipesan sekarang.
                    </div>
                </div>

                <div class="card-body">
                    @if($data && count($data) > 0)
                        <div class="table-responsive">
                            <table id="tablePrediksi" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Obat</th>
                                        <th style="text-align: center;">Stok Saat Ini</th>
                                        <th style="text-align: center;">Terjual</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">
                                            Kebutuhan 1 Bulan
                                        </th>
                                        <th style="text-align: center;">
                                            Rekomendasi Order
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($data as $d)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>
                                                    {{ $d['nama_obat'] ?? 'Unknown' }}
                                                    @if(isset($d['is_prediction']) && $d['is_prediction'])
                                                        <br><small class="text-muted">
                                                            <i class="fas fa-clock fa-xs"></i>
                                                            {{ $d['periode_data'] ?? '3 bulan terakhir' }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ number_format($d['stok_sisa'] ?? 0, 0) }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ number_format($d['stok_terjual'] ?? 0, 0) }}
                                                </td>
                                                <td>
                                                    @if (($d['status'] ?? '') === 'menipis')
                                                        <span class="badge badge-sm badge-warning"><i class="fas fa-exclamation-triangle"></i> Menipis</span>
                                                    @elseif (($d['status'] ?? '') === 'aman')
                                                        <span class="badge badge-sm badge-success"><i class="fas fa-check"></i> Aman</span>
                                                    @elseif (($d['status'] ?? '') === 'habis')
                                                        <span class="badge badge-sm badge-danger"><i class="fas fa-times"></i> Habis</span>
                                                    @else
                                                        <span class="badge badge-sm badge-secondary">-</span>
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    <strong class="text-primary">
                                                        {{ number_format($d['stok_prediksi'] ?? 0, 0) }}
                                                    </strong>
                                                    @if(isset($d['source']))
                                                        <br>
                                                        @if($d['source'] === 'ml')
                                                            <small class="text-muted">(ML)</small>
                                                        @else
                                                            <small class="text-muted">(Formula)</small>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    @php $order = $d['rekomendasi_order'] ?? 0; @endphp
                                                    @if($order > 0)
                                                        <strong class="text-danger">
                                                            {{ number_format($order, 0) }}
                                                        </strong>
                                                        <br><small class="text-muted">unit</small>
                                                    @else
                                                        <span class="text-success fw-bold">✓ Cukup</span>
                                                        <br><small class="text-muted">tidak perlu pesan</small>
                                                    @endif
                                                </td>
                                                {{-- <td style="text-align: center;">
                                                    @if(isset($d['confidence']))
                                                        @if($d['confidence'] === 'high')
                                                            <span class="badge badge-success"><i class="fas fa-star"></i> Tinggi</span>
                                                        @elseif($d['confidence'] === 'medium')
                                                            <span class="badge badge-warning"><i class="fas fa-star-half-alt"></i> Sedang</span>
                                                        @elseif($d['confidence'] === 'low')
                                                            <span class="badge badge-danger"><i class="far fa-star"></i> Rendah</span>
                                                        @else
                                                            <span class="badge badge-secondary">-</span>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary">-</span>
                                                    @endif
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
                                    </div>

                        {{-- Summary Stats --}}
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Obat</h6>
                                        <h3 class="text-primary">{{ $stats['total_drugs'] ?? 0 }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Status Aman</h6>
                                        <h3 class="text-success">{{ $stats['status_aman'] ?? 0 }}</h3>
                                        </div>
                                        </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title">Status Menipis</h6>
                                        <h3 class="text-warning">{{ $stats['status_menipis'] ?? 0 }}</h3>
                                        </div>
                                        </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title">Status Habis</h6>
                                        <h3 class="text-danger">{{ $stats['status_habis'] ?? 0 }}</h3>
                                        </div>
                                        </div>
                                        </div>
                                        </div>

                                        {{-- Keterangan Tambahan --}}
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body py-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle text-info"></i>
                                                            <strong>Catatan:</strong>
                                                            Prediksi menggunakan metode <em>Decision Tree Regressor (CARD)</em>.
                                                            Semakin banyak data transaksi obat keluar yang tersedia,
                                                            semakin akurat hasil prediksi model.
                                                            Periode data: <strong>3 bulan terakhir</strong>
                                                            ({{ now()->subMonths(3)->format('d M Y') }} –
                                                            {{ now()->format('d M Y') }}).
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                    @else
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span><i class="fas fa-info-circle"></i> Tidak ada data prediksi yang tersedia.</span>
                                <a href="javascript:void(0);"
                                    onclick="event.preventDefault(); document.getElementById('form-generate-prediksi').submit();"
                                    class="alert-link">Klik di sini untuk menghitung prediksi</a>
                            </div>
                            <form id="form-generate-prediksi" action="{{ route('prediksi.generate') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    $(document).ready(function () {
        initStandardDataTable('#tablePrediksi');
    });

    document.getElementById('btnGeneratePrediction')?.addEventListener('click', function (e) {
        const form = this.closest('form');
        if (form) {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghitung...';
            form.submit();
        }
    });
</script>
@endpush
</x-app>
