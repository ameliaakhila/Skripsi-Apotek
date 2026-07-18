<x-app :title="$title">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title text-primary">
                        {{ $title ?? 'Data Stok Cabang' }}
                    </div>
                </div>
                <div class="card-body">
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

                        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
                            <div>
                                <h4 class="mb-1">Monitoring Global Stok Cabang</h4>
                                <small class="text-muted">Ringkasan stok per obat per cabang (agregasi total stok_sisa).</small>
                            </div>

                            <form method="GET"
                            action="{{ Route::has('admin.stok-cabang.index') ? route('admin.stok-cabang.index') : url('/stok-cabang') }}"
                            class="d-flex align-items-end gap-2">
                            <div>
                                <label for="id_cabang" class="form-label mb-1">Filter Cabang</label>
                                    <select id="single-select-cabang" name="id_cabang" class="form-control form-control-sm js-example-data-array"
                                        onchange="this.form.submit()">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($dataCabang as $cabang)
                                            <option value="{{ $cabang->id_cabang }}" @selected((string) request('id_cabang') === (string) $cabang->id_cabang)>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                        <table id="example3" class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 60px">No</th>
                                    <th>Cabang</th>
                                    <th>Nama Obat</th>
                                    <th class="text-center">Total Batch</th>
                                    <th class="text-center">Total Stok Sisa</th>
                                    <th class="text-center">Safety Stock</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 120px">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stok as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nama_cabang }}</td>
                                        <td>{{ $item->nama_obat }}</td>
                                        <td class="text-center">{{ $item->total_batch }}</td>
                                        <td class="text-center fw-bold">{{ number_format($item->total_stok_sisa) }}</td>
                                        <td class="text-center">{{ number_format($item->safety_stock) }}</td>
                                        <td class="text-center">
                                            @if($item->status_stok === 'Aman')
                                                <span class="badge badge-success">Aman</span>
                                            @elseif($item->status_stok === 'Menipis')
                                                <span class="badge badge-warning">Menipis</span>
                                            @else
                                                <span class="badge badge-danger">Habis</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ Route::has('admin.stok-batch-kedaluwarsa.index') ? route('admin.stok-batch-kedaluwarsa.index', ['id_cabang' => $item->id_cabang, 'id_obat' => $item->id_obat]) : url('/stok-batch-kedaluwarsa?id_cabang=' . $item->id_cabang . '&id_obat=' . $item->id_obat) }}"
                                                class="btn btn-info btn-xs">
                                                <i class="fas fa-eye me-1"></i>Batch
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Tidak ada data stok untuk filter yang dipilih.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $stok->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app>
