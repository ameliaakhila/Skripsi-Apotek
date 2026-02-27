<x-layout title="Penjualan Obat">
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card ">
                        <div class="card-header">
                            <h4 class="card-title">Penjualan Obat</h4>
                        </div>

                        <div class="mx-4">
                            <form id="formObat" action="{{ url('/penjualan-obat-tambah') }}" method="POST">
                                @csrf
                                <select id="id_obat_masuk" name="id_obat" class="js-example-data-array" required>
                                    <option value="" placeholder="Masukan nama obat" selected disabled hidden>
                                        Masukan Nama obat | kode obat
                                    </option>
                                    @foreach ($data as $d)
                                        <option value="{{ $d->id_obat_masuk }}">
                                            {{ $d->Obat->nama_obat }} |
                                            {{ $d->kode_obat }} |
                                            {{ $d->satuan_obat }} |
                                            {{ $d->DataKategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
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

                                <table id="example3" class="display">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama obat</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            {{-- <th>Keterangan Keluar</th> --}}
                                            <th>Harga</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penjualan as $d)
                                            {{-- @dd($d) --}}
                                            <tr>
                                                <td>{{ $loop->iteration }}
                                                </td>
                                                <td> {{ $d->Obat->nama_obat }} </td>
                                                <td> {{ $d->stok_keluar }} </td>
                                                <td> {{ $d->satuan_obat }} </td>
                                                {{-- <td>Terju</td> --}}
                                                <td>Rp {{ $d->harga_obat_formatted }}</td>
                                                <td class="text-center">
                                                    <button penjualan-bs-toggle="modal"
                                                        data-bs-target="#add {{-- <?= $d['id_obat']; ?> --}}"
                                                        id=".$d['id_obat']."
                                                        class="btn btn-success shadow btn-xs sharp me-1"><i
                                                            class="fas fa-plus"></i></button>
                                                    <button penjualan-bs-toggle="modal"
                                                        data-bs-target="#edit {{-- <?= $d['id_obat']; ?> --}}"
                                                        id=".$d['id_obat']."
                                                        class="btn btn-primary shadow btn-xs sharp me-1"><i
                                                            class="fas fa-pen"></i></button>
                                                    <button penjualan-bs-toggle="modal"
                                                        data-bs-target="#delete {{-- <?= $d['id_obat']; ?> --}}"
                                                        id=".$d['id_obat']."
                                                        class="btn btn-danger shadow btn-xs sharp me-1"><i
                                                            class="fas fa-trash"></i></button>
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