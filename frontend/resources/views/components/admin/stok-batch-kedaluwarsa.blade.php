<x-layout title="Kelola Stok Cabang">
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="card-title text-white">{{ $title }}</h4>
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

                                <div id="select2-modal"></div>

                                <table id="example3" class="table table-borderd">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Batch</th>
                                            <th>Nama Obat</th>
                                            <th>Tanggal Kedaluwarsa</th>
                                            <th>Sisa stok</th>
                                            <th>Status</th>
                                            <th class="text-center">Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stok as $s)
                                            <tr>
                                                <td>{{ $loop->iteration }}
                                                </td>
                                                <td style="width: 120px">
                                                    @if ($s->status_stok === "Aman")
                                                        <i class="fas fa-circle text-success"></i>
                                                    @elseif ($s->status_stok === "Menipis")
                                                        <i class="fas fa-circle text-warning"></i>
                                                    @elseif ($s->status_stok === "Habis")
                                                        <i class="fas fa-circle text-danger"></i>
                                                    @endif
                                                    {{ $s->kode_batch }}
                                                </td>
                                                <td>
                                                    {{ $s->Obat->nama_obat }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $s->tanggal_kedaluwarsa_format }}
                                                </td>
                                                <td class="text-center"> {{ $s->jumlah_sisa }} </td>
                                                <td class="text-center">
                                                    @if($s->status_stok === "Aman")
                                                        <span class="badge badge-success">{{ $s->status_stok }}</span>
                                                    @elseif($s->status_stok === "Menipis")
                                                        <span class="badge badge-warning">{{ $s->status_stok }}</span>
                                                    @elseif($s->status_stok === "Habis")
                                                        <span class="badge badge-danger">{{ $s->status_stok }}</span>
                                                    @endif
                                                </td>
                                                <td class="d-flex justify-content-center align-items-center">
                                                    {{-- ! DETAIL --}}
                                                    <button type="button" class="btn btn-info shadow btn-xs sharp me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detail{{ $s->id_stok_batch }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    {{-- ! UPDATE --}}
                                                    <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#edit{{ $s->id_stok_batch }}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    {{-- ! DELETE --}}
                                                    <form action="{{ url('data-satuan-hapus/' . $s->id_satuan) }}"
                                                        method="POST" id="delete-form-{{ $s->id_satuan }}"
                                                        class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-danger shadow btn-xs sharp me-1 btn-delete"
                                                            data-id="{{ $s->id_satuan }}" data-nama="{{ $s->nama_satuan }}"
                                                            data-entity="satuan">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- ! Modal Tambah & Edit --}}
                                @foreach($stok as $s)
                                    <x-admin.modals.modal-stok-cabang type="tambah" />
                                    <x-admin.modals.modal-stok-cabang type="edit" :stok="$stok" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout>