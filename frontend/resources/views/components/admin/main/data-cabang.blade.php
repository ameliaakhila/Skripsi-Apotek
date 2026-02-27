<x-layout title="Kelola Data Cabang">
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ $title }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
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

                                <table id="example3" class="table table-borderd">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Cabang</th>
                                            <th>Alamat</th>
                                            <th class="text-center">Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}
                                                </td>
                                                <td> {{ $d->kode_cabang }} </td>
                                                <td> {{ $d->nama_cabang }} </td>
                                                <td> {{ $d->alamat_cabang }} </td>
                                                <td class="d-flex justify-content-center align-items-center">
                                                    {{-- ! UPDATE --}}
                                                    <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditCabang"
                                                        data-id="{{ $d->id_cabang }}"
                                                        data-kode-cabang="{{ $d->kode_cabang }}"
                                                        data-nama-cabang="{{ $d->nama_cabang }}"
                                                        data-alamat-cabang="{{ $d->alamat_cabang }}"
                                                        data-action="{{ url('data-cabang-edit/' . $d->id_cabang) }}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    {{-- ! DELETE --}}
                                                    <form action="{{ url('data-cabang-hapus/' . $d->id_cabang) }}"
                                                        method="POST" id="delete-form-{{ $d->id_cabang }}"
                                                        class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-danger shadow btn-xs sharp me-1 btn-delete"
                                                            data-id="{{ $d->id_cabang }}" data-nama="{{ $d->nama_cabang }}"
                                                            data-entity="Cabang">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- ! Modal Tambah & Edit --}}
                                <x-admin.modals.modal-data-cabang judul="Tambah Data Cabang" type="tambah"
                                    :kodeCabang="$kodeCabang" />
                                <x-admin.modals.modal-data-cabang judul="Edit Data Cabang" type="edit"
                                    id="modalEditCabang" />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout>