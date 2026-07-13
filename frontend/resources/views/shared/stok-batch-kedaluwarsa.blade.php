<x-app :title="$title">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <div class="card-title text-primary">
                            {{ $title ?? 'Data Stok Batch Kedaluwarsa' }}
                        </div>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="GET"
                        action="{{ Route::has('admin.stok-batch-kedaluwarsa.index') ? route('admin.stok-batch-kedaluwarsa.index') : url('/stok-batch-kedaluwarsa') }}"
                        class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label for="id_cabang" class="form-label mb-1">Cabang</label>
                            <select id="id_cabang" name="id_cabang" class="form-control form-control-sm js-example-data-array">
                                <option value="">Semua Cabang</option>
                                @foreach ($dataCabang as $cabang)
                                    <option value="{{ $cabang->id_cabang }}" @selected((string) request('id_cabang') === (string) $cabang->id_cabang)>
                                        {{ $cabang->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="id_obat" class="form-label mb-1">Obat</label>
                            <select id="id_obat" name="id_obat" class="form-control form-control-sm js-example-data-array">
                                <option value="">Semua Obat</option>
                                @foreach ($dataObat as $obat)
                                    <option value="{{ $obat->id_obat }}" @selected((string) request('id_obat') === (string) $obat->id_obat)>
                                        {{ $obat->nama_obat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status_expiry" class="form-label mb-1">Status Kedaluwarsa</label>
                            <select id="status_expiry" name="status_expiry" class="form-control form-control-sm js-example-data-array">
                                <option value="">Semua</option>
                                <option value="expired" @selected(request('status_expiry') === 'expired')>Kedaluwarsa</option>.
                                <option value="near" @selected(request('status_expiry') === 'near')>Mendekati (&lt;= 30 hari)</option>
                                <option value="safe" @selected(request('status_expiry') === 'safe')>Aman (&gt; 30 hari)</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-grid">
                            <label class="form-label mb-1">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        </div>
                    </form>


                        <div class="d-flex flex-wrap justify-content-between gap-3">
                            <div>
                                <h4 class="mb-1">Detail Batch</h4>
                                <small class="text-muted">Daftar batch diurutkan berdasarkan tanggal kedaluwarsa paling dekat.</small>
                            </div>

                            <a href="{{ Route::has('admin.stok-cabang.index') ? route('admin.stok-cabang.index') : url('/stok-cabang') }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali ke Stok Cabang
                            </a>
                        </div>

                    <div class="table-responsive my-3">
                        <table id="example3" class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Cabang</th>
                                    <th>Nama Obat</th>
                                    <th>Kode Batch</th>
                                    <th class="text-center">Kedaluwarsa</th>
                                    <th class="text-center">Sisa Hari</th>
                                    <th class="text-center">Stok Sisa</th>
                                    <th class="text-center">Status Stok</th>
                                    <th class="text-center">Indikator ED</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stok as $batch)
                                    <tr>
                                        <td>{{ ($stok->currentPage() - 1) * $stok->perPage() + $loop->iteration }}</td>
                                        <td>{{ $batch->DataCabang->nama_cabang ?? '-' }}</td>
                                        <td>{{ $batch->Obat->nama_obat ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $batch->kode_batch }}</span>
                                        </td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($batch->tanggal_kedaluwarsa)->locale('id')->isoFormat('D MMMM Y') }}
                                        </td>
                                        <td class="text-center">
                                            @if($batch->sisa_hari < 0)
                                                <span class="text-danger fw-bold">{{ abs($batch->sisa_hari) }} hari lalu</span>
                                            @else
                                                <span class="fw-bold">{{ $batch->sisa_hari }} hari</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ number_format($batch->stok_sisa) }}</td>
                                        <td class="text-center">
                                            @if($batch->status_stok === 'Aman')
                                                <span class="badge badge-success">Aman</span>
                                            @elseif($batch->status_stok === 'Menipis')
                                                <span class="badge badge-warning">Menipis</span>
                                            @else
                                                <span class="badge badge-danger">Habis</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $batch->indikator_kedaluwarsa['badge'] }}">
                                                {{ $batch->indikator_kedaluwarsa['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Tidak ada data batch untuk filter yang dipilih.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-2">
                            {{ $stok->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app>
