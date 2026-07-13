<x-app>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title text-primary">
                        {{ $title ?? 'Data Mutasi Stok' }}
                    </div>
                </div>
                <div class="card-body">
                    {{-- ! SUCCESS FLASH MESSAGE --}}
                        @if(session('success'))
                            <div class="d-flex align-items-center alert alert-success solid alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                    <polyline points="9 11 12 14 22 4"></polyline>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                        </svg>
                                        <strong>{{ session('success') }}!</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                                        </div>
                        @endif

                        {{-- ! ERROR FLASH MESSAGE --}}
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                                </div>
                        @endif

                        {{-- ! TAMBAH BUTTON --}}
                        <button type="button" data-bs-toggle="modal" data-bs-target="#tambah" class="btn btn-primary btn-xs mb-2"><i
                                class="fas fa-plus me-2"></i>
                            Tambah Mutasi
                        </button>

                        {{-- ! TABLE MUTASI STOK --}}
                        <div class="table-responsive">
                        <table id="example3" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Batch</th>
                                    <th style="width: 250px">Nama Obat</th>
                                    <th class="text-center">Cabang Asal</th>
                                    <th class="text-center">Cabang Tujuan</th>
                                    <th class="text-center">Tanggal Mutasi</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $d)
                                    <tr>
                                        <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                                        </td>
                                        <td><span class="badge badge-primary">{{ $d->stokBatch->kode_batch ?? '-' }}</span>
                                        </td>
                                        <td>{{ $d->stokBatch->Obat->nama_obat ?? '-' }}
                                        </td>
                                        <td class="text-center">{{ $d->cabangAsal->nama_cabang ?? '-' }}</td>
                                        <td class="text-center">{{ $d->cabangTujuan->nama_cabang ?? '-' }}</td>
                                        <td class="text-center">
                                            {{ $d->tanggal_mutasi_formatted }}
                                        </td>
                                        <td class="text-center"><strong>{{ $d->jumlah_mutasi}}</strong></td>
                                        <td class="text-center d-flex justify-content-center align-items-center gap-1">
                                            {{-- ! VIEW BUTTON --}}
                                            <button type="button" class="btn btn-info shadow btn-xs sharp" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailMutasi" data-id="{{ $d->id_mutasi }}"
                                                data-batch="{{ $d->stokBatch->kode_batch ?? '-' }}"
                                                data-nama-obat="{{ $d->stokBatch->Obat->nama_obat ?? '-' }}"
                                                data-nama-satuan="{{ $d->stokBatch->Obat->DataSatuan->nama_satuan ?? '-' }}"
                                                data-nama-kategori=" {{ $d->stokBatch->Obat->DataKategori->nama_kategori ?? '-' }}"
                                                data-cabang-asal="{{ $d->cabangAsal->nama_cabang ?? '-' }}"
                                                data-cabang-tujuan="{{ $d->cabangTujuan->nama_cabang ?? '-' }}"
                                                            data-tanggal="{{ $d->tanggal_mutasi_formatted }}" data-jumlah="{{ $d->jumlah_mutasi }}"
                                                            data-keterangan="{{ $d->keterangan ?? '-' }}" title="Detail Mutasi">
                                                            <i class="fas fa-eye"></i>
                                                        </button>

                                                        {{-- ! EDIT BUTTON --}}
                                                        <button type="button" class="btn btn-primary shadow btn-xs sharp" data-bs-toggle="modal"
                                                            data-bs-target="#modalEditMutasi" data-id="{{ $d->id_mutasi }}"
                                                            data-id-stok-batch="{{ $d->id_stok_batch }}" data-id-cabang-asal="{{ $d->id_cabang_asal }}"
                                                            data-id-cabang-tujuan="{{ $d->id_cabang_tujuan }}"
                                                            data-batch-read="({{ $d->stokBatch->kode_batch ?? '-' }}) {{ $d->stokBatch->Obat->nama_obat ?? '-' }}"
                                                            data-tanggal="{{ $d->tanggal_mutasi }}" data-jumlah="{{ $d->jumlah_mutasi }}"
                                                            data-keterangan="{{ $d->keterangan ?? '' }}"
                                                            data-action="{{ Route::has('admin.mutasi-stok.update-legacy') ? route('admin.mutasi-stok.update-legacy', $d->id_mutasi) : url('/mutasi-stok-edit/' . $d->id_mutasi) }}"
                                                            title="Edit Mutasi">
                                                            <i class="fas fa-pencil-alt"></i>

                                                                            {{-- ! DELETE BUTTON --}}
                                                                            <form
                                                                                action="{{ Route::has('admin.mutasi-stok.destroy-legacy') ? route('admin.mutasi-stok.destroy-legacy', $d->id_mutasi) : url('/mutasi-stok-hapus/' . $d->id_mutasi) }}"
                                                                                method="POST" id="delete-form-{{ $d->id_mutasi }}" class="d-inline-block">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="button" class="btn btn-danger shadow btn-xs sharp btn-delete"
                                                                                    data-id="{{ $d->id_mutasi }}" data-keterangan="{{ $d->keterangan ?? 'Mutasi Stok' }}"
                                                                                    title="Hapus Mutasi">
                                                                                    <i class="fas fa-trash-alt"></i>
                                                                                </button>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <p class="text-muted">Belum ada data mutasi stok</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ! Modal Tambah Mutasi --}}
    <x-admin.modals.modal-mutasi-stok judul="Tambah Mutasi Stok" type="tambah" :data-stok-batch="$dataStokBatch"
        :data-cabang-asal="$dataCabangAsal" :data-cabang-tujuan="$dataCabangTujuan" />

    {{-- ! Modal Edit Mutasi --}}
    <x-admin.modals.modal-mutasi-stok id="modalEditMutasi" judul="Edit Mutasi Stok" type="edit"
        :data-stok-batch="$dataStokBatch" :data-cabang-asal="$dataCabangAsal" :data-cabang-tujuan="$dataCabangTujuan" />

    {{-- ! Modal Detail Mutasi --}}
        <x-admin.modals.modal-mutasi-stok id="modalDetailMutasi" judul="Detail Mutasi Stok" type="detail" />

</x-app>
