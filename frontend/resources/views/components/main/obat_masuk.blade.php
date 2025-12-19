<x-layout title="Obat Masuk | Apotek Sena Farma">
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Obat</h4>
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
                                    class="btn btn-primary btn-xs mb-2"><i class="fas fa-plus me-2"></i>
                                    Tambah
                                </button>
                                <table id="example3" class="table table-borderd">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Batch</th>
                                            <th>Nama obat</th>
                                            <th>Satuan</th>
                                            <th class="text-center">Tanggal Masuk</th>
                                            <th class="text-center">Tanggal Kadaluarsa</th>
                                            <th class="text-center">Stok Masuk</th>
                                            <th class="text-center">Opsi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($data as $d)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d->kode_obat }}</td>
                                                <td>{{ $d->Obat->nama_obat ?? '-' }}</td>
                                                <td>{{ $d->satuan_obat }}</td>
                                                <td>{{ $d->tanggal_masuk_formatted }}</td>
                                                <td>{{ $d->tanggal_kadaluarsa_formatted }}</td>
                                                <td>{{ $d->stok_masuk }}</td>
                                                <td class="text-center d-flex justify-content-center align-items-center">
                                                    <!-- Tombol Detail -->
                                                    <button type="button" class="btn btn-info shadow btn-xs sharp me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detail{{ $d->id_obat_masuk }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    <!-- Tombol Edit -->
                                                    <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#edit{{ $d->id_obat_masuk }}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    <form action="obat-masuk-hapus/{{ $d->id_obat_masuk }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('Anda yakin hapus data obat {{ $d->nama_obat }} ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-danger shadow btn-xs sharp me-1">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- ! Loop Edit / Detail --}}
                                @foreach($data as $d)
                                    <x-modal-obat-masuk type="tambah" :d="$d" :obat="$obat"
                                        :satuanList="$satuanList" :kategori="$kategori" :kodeObat="$kodeObat" />
                                    <x-modal-obat-masuk type="detail" :d="$d" :obatMasukList="$obatMasukList"
                                        :satuanList="$satuanList" :kategori="$kategori" />
                                    <x-modal-obat-masuk type="edit" :d="$d" :obatMasukList="$obatMasukList"
                                        :satuanList="$satuanList" :kategori="$kategori" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</x-layout>
