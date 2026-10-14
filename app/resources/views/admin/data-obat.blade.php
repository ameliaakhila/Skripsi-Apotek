<x-app :title="'Kelola Data Obat'">
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title text-primary">
                        {{ $title ?? 'Data Obat' }}
                    </div>
                </div>
                <div class="card-body">
                        @if(session('success'))
                            <div class="d-flex align-items-center alert alert-success solid alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                    <polyline points="9 11 12 14 22 4"></polyline>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                                <strong>{{ session('success') }}!</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                                </button>
                                </div>
                        @endif

                        @if(session('error'))
                            <div class="d-flex align-items-center alert alert-danger solid alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24 " height="24" stroke="currentColor" stroke-width="2" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                    <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2">
                                    </polygon>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                                </button>
                                </div>
                        @endif
                        <button data-bs-toggle="modal" data-bs-target="#tambah" class="btn btn-primary btn-xs mb-2">
                            <i class="fas fa-plus me-2"></i>
                            Tambah</button>

                        <div class="table-responsive">
                        <table id="example3" class="table table-borderd">
                            <thead>
                                <tr>
                                    <th style="width: 4%">No</th>
                                    <th style="width: 25%">Nama Obat</th>
                                    <th style="width: 12%">Safety Stock</th>
                                    <th style="width: 15%">Satuan</th>
                                    <th style="width: 18%">Supplier</th>
                                    <th style="width: 14%">Kategori</th>
                                    <th style="width: 12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $d)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $d->nama_obat }}</td>
                                            <td class="text-center"><span class="badge badge-primary">{{ $d->safety_stock }}</span>
                                            </td>
                                            <td>{{ $d->DataSatuan->nama_satuan ?? '-' }}</td>
                                            <td>{{ $d->DataSupplier->nama_supplier ?? '-' }}</td>
                                            <td>{{ $d->DataKategori->nama_kategori ?? '-' }}</td>
                                            <td class="action-cell">
                                                <div class="btn-group-table">
                                                    {{-- ! EDIT --}}
                                                    <button type="button" class="btn btn-primary shadow btn-xs sharp me-1" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditObat"
                                                        data-id="{{ $d->id_obat }}" data-nama-obat="{{ $d->nama_obat }}"
                                                        data-safety-stock="{{ $d->safety_stock }}" data-satuan="{{ $d->id_satuan }}"
                                                        data-supplier="{{ $d->id_supplier }}" data-kategori="{{ $d->id_kategori }}"
                                                        data-action="{{ Route::has('admin.data-obat.edit-legacy') ? route('admin.data-obat.edit-legacy', $d->id_obat) : url('/data-obat-edit/' . $d->id_obat) }}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    {{-- ! DELETE --}}
                                                    <form
                                                        action="{{ Route::has('admin.data-obat.hapus-legacy') ? route('admin.data-obat.hapus-legacy', $d->id_obat) : url('data-obat-hapus/' . $d->id_obat) }}"
                                                        method="POST"
                                                                id="delete-form-{{ $d->id_obat }}" class="d-inline-block">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    class="btn btn-danger shadow btn-xs sharp btn-delete"
                                                                    data-id="{{ $d->id_obat }}" data-nama="{{ $d->nama_obat }}"
                                                                    data-entity="obat">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                @endforeach
                                    </tbody>
                                </table>
                                {{-- ! Modal Tambah & Edit --}}
                                <x-admin.modals.modal-data-obat judul="Tambah Data Obat" type="tambah" :satuan="$satuan" :supplier="$supplier"
                                    :kategori="$kategori" />
                                <x-admin.modals.modal-data-obat id="modalEditObat" judul="Edit Data Obat" type="edit"
                                    :satuan="$satuan" :supplier="$supplier" :kategori="$kategori" />
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $data->links() }}
                    </div>
                    </div>
                    </div>
                    </div>
    </div>
</x-app>
