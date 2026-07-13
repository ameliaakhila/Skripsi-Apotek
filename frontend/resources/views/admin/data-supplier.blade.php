<x-app title="Kelola Data Supplier">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title text-primary">
                                {{ $title ?? 'Data Supplier' }}
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div
                                    class="d-flex align-items-center alert alert-success solid alert-dismissible fade show">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
                                        stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                        class="me-2">
                                        <polyline points="9 11 12 14 22 4"></polyline>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                    <strong>{{ session('success') }}!</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="btn-close">
                                    </button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div
                                    class="d-flex align-items-center alert alert-danger solid alert-dismissible fade show">
                                    <svg viewBox="0 0 24 24" width="24 " height="24" stroke="currentColor"
                                        stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                        class="me-2">
                                        <polygon
                                            points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2">
                                        </polygon>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="btn-close">
                                    </button>
                                </div>
                            @endif

                            <button data-bs-toggle="modal" data-bs-target="#tambah"
                                class="btn btn-primary btn-xs mb-2">
                                <i class="fas fa-plus me-2"></i> Tambah
                            </button>

                            <div class="table-responsive">
                                <table id="example3" class="table table-borderd">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No</th>
                                            <th style="width: 35%">Nama Supplier</th>
                                            <th style="width: 30%">Alamat</th>
                                            <th style="width: 20%">No Telpon</th>
                                            <th style="width: 10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $s)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $s->nama_supplier }}</td>
                                                <td>{{ $s->alamat_supplier }}</td>
                                                <td>{{ $s->no_telp }}</td>
                                                <td class="action-cell">
                                                    <div class="btn-group-table">
                                                        {{-- ! EDIT --}}
                                                        <button type="button" class="btn btn-primary shadow btn-xs sharp me-1" data-bs-toggle="modal"
                                                            data-bs-target="#modalEditSupplier" data-id="{{ $s->id_supplier }}" data-nama="{{ $s->nama_supplier }}"
                                                            data-alamat="{{ $s->alamat_supplier }}" data-telp="{{ $s->no_telp }}"
                                                            data-action="{{ Route::has('admin.data-supplier.edit-legacy') ? route('admin.data-supplier.edit-legacy', $s->id_supplier) : url('/data-supplier-edit/' . $s->id_supplier) }}">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        {{-- ! DELETE --}}
                                                        <form action="{{ url('data-supplier-hapus/' . $s->id_supplier) }}" method="POST"
                                                            id="delete-form-{{ $s->id_supplier }}" class="d-inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-danger shadow btn-xs sharp btn-delete"
                                                                data-id="{{ $s->id_supplier }}" data-nama="{{ $s->nama_supplier }}"
                                                                data-entity="supplier">
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
                                <x-admin.modals.modal-data-supplier type="tambah" id="modalTambahSupplier" judul="Tambah Data Supplier" />
                                <x-admin.modals.modal-data-supplier type="edit" id="modalEditSupplier" judul="Edit Data Supplier" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app>
