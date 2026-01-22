<x-layout title="Kelola Data Obat">
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Kategori Obat</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                @if(session('success'))
                                    <div class="alert alert-success solid alert-dismissible fade show">
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
                                    <div class="alert alert-danger solid alert-dismissible fade show">
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

                                {{--
                                <?php include('data_obat_modal_tambah.php') ?> --}}
                                <table id="example3" class="table table-borderd">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kategori obat</th>
                                            <th class="text-center">Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kategori as $k)
                                            <tr>
                                                <td>{{ $loop->iteration }}
                                                </td>
                                                <td> {{ $k->nama_kategori_obat }} </td>
                                                <td class="d-flex justify-content-center align-items-center">
                                                    <a href="data_obat_hapus.php?id_obat= {{-- <?= $d['id_obat']; ?> --}}"
                                                        onclick="return confirm('Anda yakin Hapus data Kategori obat {{-- <?php    echo $d['nama_obat']; ?> --}} ?')"
                                                        class="btn btn-danger shadow btn-xs sharp me-1">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                    <button data-bs-toggle="modal"
                                                        data-bs-target="#edit {{-- <?= $d['id_obat']; ?> --}}"
                                                        id=".$d['id_obat']."
                                                        class="btn btn-primary shadow btn-xs sharp me-1">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout>