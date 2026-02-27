<x-layout :title="'Kelola Data Obat'">
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">
                {{-- Alert Notivikasi cek stok opname --}}
                @if (request()->is('stok-opname'))
                    <div class="d-flex align-items-center alert alert-warning solid alert-dismissible fade show">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <strong>Pengingat:</strong> Wajib melakukan pengecekan Stok Opname gudang dan cabang
                        minimal 1 bulan sekali untuk
                        menjaga akurasi persediaan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

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


                                <div class="row mb-4">
                                    <h4>Cek Stok Cabang</h4>
                                    <form method="GET" action="{{ url('/stok-opname') }}">
                                        @csrf
                                        <select id="dropdown-placement" style="width: 100px" name="id_cabang"
                                            onchange="this.form.submit()">
                                            {{-- Kalau belum pilih --}}
                                            @if(!request('id_cabang'))
                                                <option value="" selected>Semua Stok</option>
                                            @else
                                                @php
    $selectedCabang = $dataCabang->firstWhere('id_cabang', request('id_cabang'));
                                                @endphp

                                                <option value="{{ $selectedCabang->id_cabang }}" selected>
                                                    {{ $selectedCabang->nama_cabang }}
                                                </option>
                                            @endif

                                            {{-- Daftar semua cabang --}}
                                            @foreach ($dataCabang as $d)
                                                @if(request('id_cabang') != $d->id_cabang)
                                                    <option value="{{ $d->id_cabang }}">
                                                        {{ $d->nama_cabang }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                                <div id="select2-modal"></div>

                                <table id="example3" class="table table-borderd"
                                    style="table-layout: fixed; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%">No</th>
                                            <th class="text-center" style="width: 15%">Kode Opname</th>
                                            <th style="width: 24%">Nama Obat</th>
                                            <th class="text-center" style="width: 9%">Sistem</th>
                                            <th class="text-center" style="width: 9%">Fisik</th>
                                            <th class="text-center" style="width: 9%">Selisih</th>
                                            <th class="text-center" style="width: 17%">Tanggal</th>
                                            <th class="text-center" style="width: 9%" class="text-center">Opsi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($data as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><i class="{{ $d->keterangan_icon }}"></i> {{ $d->kode_opname }}</td>
                                                <td>{{ $d->Obat->nama_obat ?? '-' }}</td>
                                                <td class="text-center">{{ $d->stok_sistem }}</td>
                                                <td class="text-center">{{ $d->stok_fisik }}</td>
                                                <td class="text-center">{{ $d->selisih }}</td>
                                                <td class="text-center">{{ $d->tanggal_opname_formatted }}</td>
                                                <td class="text-center d-flex justify-content-center align-items-center">
                                                    {{-- ! READ
                                                    <button type="button" class="btn btn-info shadow btn-xs sharp me-1"
                                                        data-bs-toggle="modal" data-bs-toggle="modal"
                                                        data-bs-target="#modalDetailObatMasuk"
                                                        data-id="{{ $d->id_obat_masuk }}" data-id_obat="{{ $d->id_obat }}"
                                                        data-nama_obat="{{ $d->Obat->nama_obat ?? '-'}}"
                                                        data-kode_obat="{{ $d->kode_obat }}"
                                                        data-satuan_obat="{{ $d->DataSatuan->nama_satuan ?? '-' }}"
                                                        data-kategori_obat="{{ $d->DataKategori->nama_kategori ?? '-' }}"
                                                        data-tanggal_masuk="{{ $d->tanggal_masuk_formatted }}"
                                                        data-tanggal_kedaluwarsa="{{ $d->tanggal_kedaluwarsa_formatted }}"
                                                        data-stok_masuk="{{ $d->stok_masuk }}"
                                                        data-kode_batch="{{ $d->kode_batch }}"
                                                        data-stok_masuk="{{ $d->stok_masuk }}"
                                                        data-stok_keluar="{{ $d->stok_keluar }}"
                                                        data-harga_obat="{{ $d->harga_obat }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button> --}}

                                                    {{-- ! UPDATE --}}
                                                    <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditObatMasuk"
                                                        data-id="{{ $d->id_obat_masuk }}"
                                                        data-kode-obat="{{ $d->kode_obat }}"
                                                        data-kode-batch="{{ $d->kode_batch }}"
                                                        data-tanggal-masuk="{{ $d->tanggal_masuk }}"
                                                        data-tanggal-kedaluwarsa="{{ $d->tanggal_kedaluwarsa }}"
                                                        data-stok-masuk="{{ $d->stok_masuk }}"
                                                        data-stok-keluar="{{ $d->stok_keluar }}"
                                                        data-stok-sisa="{{ $d->stok_sisa }}"
                                                        data-harga-obat="{{ $d->harga_obat }}" data-obat="{{ $d->id_obat }}"
                                                        data-satuan="{{ $d->id_satuan }}"
                                                        data-kategori="{{ $d->id_kategori }}"
                                                        data-action="{{ url('/obat-masuk-edit/' . $d->id_obat_masuk) }}">
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

                                {{-- ! Modal Tambah --}}
                                {{-- // <x-admin.modals.modal-obat-masuk judul="Tambah Obat Masuk" type="tambah" :d="$d"
                                    // :obat="$obat" :satuan="$satuan" :kategori="$kategori" :kodeObat="$kodeObat" //
                                    :kodeBatch="$kodeBatch" />
                                {{-- ! Modal Edit --}}
                                {{-- // <x-admin.modals.modal-obat-masuk id="modalEditObat" judul="Edit Obat Masuk" //
                                    type="edit" :kodeObat="$kodeObat" :kodeBatch="$kodeBatch" :satuan="$satuan" //
                                    :kategori="$kategori" :obat="$obat" />
                                {{-- ! Modal Detail --}}
                                {{-- // <x-admin.modals.modal-obat-masuk id="modalDetailObat" judul="Detail Obat Masuk"
                                    - // type="detail" :kodeObat="$kodeObat" :kodeBatch="$kodeBatch" :satuan="$satuan"
                                    // :kategori="$kategori" /> --}}
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</x-layout>