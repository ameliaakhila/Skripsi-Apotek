<x-app :title="'Kelola Data Obat'">
    <div class="row">
        <div class="col-12">
            <div class="card">
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
                            class="btn btn-primary btn-xs mb-2"><i class="fas fa-plus me-2"></i>
                            Tambah
                        </button>

                        <table id="example3" class="table table-borderd">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Batch</th>
                                    <th style="width: 300px">Nama obat</th>
                                    <th class="text-center">Tanggal Masuk</th>
                                    <th class="text-center">Stok Masuk</th>
                                    <th class="text-center">Opsi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $d)
                                    <tr class="align-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $d->kode_batch }}</td>
                                        <td>
                                            {{ $d->Obat->nama_obat ?? '-' }}
                                            {{ $d->Obat->DataSatuan->nama_satuan ?? '-' }}
                                        </td>
                                        <td class="text-center">{{ $d->tanggal_masuk_formatted }}</td>
                                        <td class="text-center">{{ $d->stok_masuk }}</td>
                                        <td class="text-center d-flex justify-content-center align-items-center">
                                            {{-- ! READ --}}
                                            <button type="button" class="btn btn-info shadow btn-xs sharp me-1"
                                                data-bs-toggle="modal" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailObat" data-id="{{ $d->id_obat_masuk }}"
                                                data-id_obat="{{ $d->id_obat }}"
                                                data-nama_obat="{{ $d->Obat->nama_obat ?? '-'}}"
                                                data-nama_satuan="{{ $d->Obat->DataSatuan->nama_satuan ?? '-' }}"
                                                data-nama_supplier="{{ $d->Obat->DataSupplier->nama_supplier ?? '-' }}"
                                                data-nama_kategori="{{ $d->Obat->DataKategori->nama_kategori ?? '-' }}"
                                                data-tanggal_masuk="{{ $d->tanggal_masuk_formatted }}"
                                                data-tanggal_kedaluwarsa="{{ $d->tanggal_kedaluwarsa_formatted }}"
                                                data-kode_batch="{{ $d->kode_batch }}"
                                                data-stok_masuk="{{ $d->stok_masuk }}"
                                                data-stok_keluar="{{ $d->stok_keluar }}"
                                                data-stok_sisa="{{ $d->stok_sisa }}">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            {{-- ! UPDATE --}}
                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                data-bs-toggle="modal" data-bs-target="#modalEditObat"
                                                data-id="{{ $d->id_obat_masuk }}"
                                                data-kode-batch="{{ $d->kode_batch }}"
                                                data-tanggal-masuk="{{ $d->tanggal_masuk }}"
                                                data-tanggal-kedaluwarsa="{{ $d->tanggal_kedaluwarsa }}"
                                                data-stok-masuk="{{ $d->stok_masuk }}"
                                                data-stok-keluar="{{ $d->stok_keluar }}"
                                                data-stok-sisa="{{ $d->stok_sisa }}"
                                                data-id-obat="{{ $d->id_obat }}"
                                                data-action="{{ Route::has('obat-masuk.update-legacy') ? route('obat-masuk.update-legacy', $d->id_obat_masuk) : url('/obat-masuk-edit/' . $d->id_obat_masuk) }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>

                                            {{-- ! DELETE --}}
                                            <form action="{{ Route::has('obat-masuk.destroy-legacy') ? route('obat-masuk.destroy-legacy', $d->id_obat_masuk) : url('obat-masuk-hapus/' . $d->id_obat_masuk) }}" method="POST"
                                                id="delete-form-{{ $d->id_obat_masuk }}" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-danger shadow btn-xs sharp btn-delete"
                                                    data-id="{{ $d->id_obat_masuk }}" data-nama="{{ $d->Obat->nama_obat }}"
                                                    data-entity="obat">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- ! Modal Tambah --}}
                        <x-admin.modals.modal-obat-masuk judul="Tambah Obat Masuk" type="tambah" :obat="$obat"
                        :kodeBatch="$kodeBatch" />

                        {{-- ! Modal Edit --}}
                        <x-admin.modals.modal-obat-masuk id="modalEditObat" judul="Edit Obat Masuk" type="edit"
                            :kodeBatch="$kodeBatch" :obat="$obat" />
                        {{-- ! Modal Detail --}}
                        <x-admin.modals.modal-obat-masuk id="modalDetailObat" judul="Detail Obat Masuk"
                            type="detail" :kodeBatch="$kodeBatch" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app>
