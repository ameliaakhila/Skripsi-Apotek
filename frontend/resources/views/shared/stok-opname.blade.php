<x-app :title="'Stok Opname'">
    <div class="content-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Pengingat:</strong> Lakukan stok opname berkala untuk memastikan stok sistem dan stok fisik tetap sinkron.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
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

                            @if($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row g-3 mb-4">
                                <div class="col-lg-6">
                                    <h4 class="mb-3">Buat Sesi Opname</h4>
                                    <form method="POST" action="{{ url('/stok-opname-tambah') }}" class="row g-2">
                                        @csrf
                                        <div class="col-md-6">
                                            <label for="tanggal_opname" class="form-label">Tanggal Opname</label>
                                            <input
                                                type="date"
                                                id="tanggal_opname"
                                                name="tanggal_opname"
                                                class="form-control form-control-sm"
                                                value="{{ old('tanggal_opname', now()->format('Y-m-d')) }}"
                                                required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="stok_fisik" class="form-label">Stok Fisik</label>
                                            <input
                                                type="number"
                                                id="stok_fisik"
                                                name="stok_fisik"
                                                class="form-control form-control-sm"
                                                min="0"
                                                value="{{ old('stok_fisik') }}"
                                                placeholder="Masukkan hasil hitung fisik"
                                                required>
                                        </div>

                                        <div class="col-12">
                                            <label for="id_stok_batch" class="form-label">Pilih Batch yang Diaudit</label>
                                            <select id="id_stok_batch" name="id_stok_batch" class="form-control form-control-sm js-example-data-array" required>
                                                <option value="">-- Pilih batch --</option>
                                                @foreach($dataStokBatch as $batch)
                                                    <option value="{{ $batch->id_stok_batch }}" @selected((string) old('id_stok_batch') === (string) $batch->id_stok_batch)>
                                                        {{ $batch->DataCabang->nama_cabang ?? '-' }} | {{ $batch->Obat->nama_obat ?? '-' }} |
                                                        Batch {{ $batch->kode_batch }} | Sisa Sistem: {{ $batch->stok_sisa }} |
                                                        ED: {{ \Carbon\Carbon::parse($batch->tanggal_kedaluwarsa)->locale('id')->isoFormat('D MMMM Y') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Penyesuaian hanya akan mengubah stok_sisa pada batch ini dan mencatat histori opname.</small>
                                        </div>

                                        <div class="col-12 d-grid d-md-flex justify-content-md-end mt-2">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-save me-1"></i>Simpan Opname
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-lg-6">
                                    <h4 class="mb-3">Filter Histori</h4>
                                    <form method="GET" action="{{ url('/stok-opname') }}" class="row g-2">
                                        <div class="col-md-5">
                                            <label for="id_cabang_filter" class="form-label">Cabang</label>
                                            <select id="id_cabang_filter" name="id_cabang" class="form-control form-control-sm js-example-data-array">
                                                <option value="">Semua Cabang</option>
                                                @foreach ($dataCabang as $cabang)
                                                    <option value="{{ $cabang->id_cabang }}" @selected((string) request('id_cabang') === (string) $cabang->id_cabang)>
                                                        {{ $cabang->nama_cabang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <h4 class="mb-3">Histori Stok Opname</h4>
                            <div class="table-responsive">
                                <table id="example3" class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px">No</th>
                                            <th>Kode Opname</th>
                                            <th>Cabang</th>
                                            <th>Obat</th>
                                            <th>Batch</th>
                                            <th class="text-center">Sistem</th>
                                            <th class="text-center">Fisik</th>
                                            <th class="text-center">Selisih</th>
                                            <th class="text-center">Keterangan</th>
                                            <th class="text-center">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data as $opname)
                                            <tr>
                                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                                <td>
                                                    <i class="{{ $opname->keterangan_icon }} me-1"></i>
                                                    {{ $opname->kode_opname }}
                                                </td>
                                                <td>{{ $opname->DataCabang->nama_cabang ?? '-' }}</td>
                                                <td>{{ $opname->Obat->nama_obat ?? '-' }}</td>
                                                <td>{{ $opname->StokBatch->kode_batch ?? '-' }}</td>
                                                <td class="text-center">{{ number_format($opname->stok_sistem) }}</td>
                                                <td class="text-center">{{ number_format($opname->stok_fisik) }}</td>
                                                <td class="text-center">
                                                    @if($opname->selisih > 0)
                                                        <span class="text-warning fw-bold">+{{ number_format($opname->selisih) }}</span>
                                                    @elseif($opname->selisih < 0)
                                                        <span class="text-danger fw-bold">{{ number_format($opname->selisih) }}</span>
                                                    @else
                                                        <span class="text-success fw-bold">0</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($opname->keterangan === 'Sesuai')
                                                        <span class="badge badge-success">Sesuai</span>
                                                    @elseif($opname->keterangan === 'Kurang')
                                                        <span class="badge badge-danger">Kurang</span>
                                                    @else
                                                        <span class="badge badge-warning">Lebih</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $opname->tanggal_opname_formatted }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">Belum ada histori stok opname.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-2">
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app>
