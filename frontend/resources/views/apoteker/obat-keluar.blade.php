<x-app>
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
                                    <th style="width: 300px">Nama Obat</th>
                                    <th class="text-center">Tanggal Keluar</th>
                                    <th class="text-center">Stok Keluar</th>
                                    <th class="text-center">Opsi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $d)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $d->StokBatch->kode_batch ?? '-' }}</td>
                                        <td>
                                            {{ $d->ObatMasuk->Obat->nama_obat ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $d->tanggal_keluar_formatted ?? '-' }}<br>
                                        </td>
                                        <td class="text-center">{{ $d->stok_keluar }}</td>
                                        <td class="text-center d-flex justify-content-center align-items-center">
                                            {{-- ! READ --}}
                                            <button type="button" class="btn btn-info shadow btn-xs sharp me-1"
                                                data-bs-toggle="modal" data-bs-target="#modalDetailObatKeluar"
                                                data-id="{{ $d->id_obat_keluar }}"
                                                data-batch="{{ $d->StokBatch->kode_batch ?? '-' }}"
                                                data-nama_obat="{{ $d->ObatMasuk->Obat->nama_obat ?? '-' }}"
                                                data-tanggal_keluar="{{ $d->tanggal_keluar_formatted ?? '-' }}"
                                                data-tanggal_kedaluwarsa="{{ $d->ObatMasuk->tanggal_kedaluwarsa_formatted ?? '-' }}"
                                                data-nama_cabang="{{ $d->DataCabang->nama_cabang ?? '-' }}"
                                                data-stok_keluar="{{ $d->stok_keluar }}"> <i class="fas fa-eye"></i>
                                            </button>

                                            {{-- ! UPDATE --}}
                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                data-bs-toggle="modal" data-bs-target="#modalEditObatKeluar"
                                                data-id="{{ $d->id_obat_keluar }}"
                                                data-id_stok_batch="{{ $d->id_stok_batch }}"
                                                data-id_cabang="{{ $d->id_cabang }}"
                                                data-batch="{{ $d->StokBatch->kode_batch ?? '-' }}"
                                                data-stok_keluar="{{ $d->stok_keluar }}"
                                                data-stok_sisa="{{ $d->StokBatch->stok_sisa ?? '-' }}"
                                                data-nama_cabang="{{ $d->DataCabang->nama_cabang ?? '-' }}"
                                                data-tanggal_keluar="{{ $d->tanggal_keluar ?? '-' }}"
                                                data-tanggal_kedaluwarsa="{{ $d->ObatMasuk->tanggal_kedaluwarsa ?? '-' }}"
                                                data-action="{{ Route::has('obat-keluar.update-legacy') ? route('obat-keluar.update-legacy', $d->id_obat_keluar) : url('/obat-keluar-edit/' . $d->id_obat_keluar) }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>

                                            <form action="{{ Route::has('obat-keluar.destroy-legacy') ? route('obat-keluar.destroy-legacy', $d->id_obat_keluar) : url('obat-keluar-hapus/' . $d->id_obat_keluar) }}" method="POST"
                                                style="display:inline-block;"
                                                onsubmit="return confirm('Anda yakin hapus data obat keluar ini ?')">
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
                        <x-admin.modals.modal-obat-keluar judul="Tambah Obat Keluar" type="tambah"
                            :obatList="$obatList" :kodeBatch="$kodeBatch" :cabang="$cabang" />
                        {{-- ! Modal Edit --}}
                        <x-admin.modals.modal-obat-keluar id="modalEditObatKeluar" judul="Edit Obat Keluar"
                            type="edit" :kodeBatch="$kodeBatch" :cabang="$cabang" :obatList="$obatList" />
                        {{-- ! Modal Detail --}}
                        <x-admin.modals.modal-obat-keluar id="modalDetailObatKeluar" judul="Detail Obat Keluar"
                            type="detail" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Filter logic for Medicine selection based on Branch
            function filterObatByCabang(modalId, idCabang) {
                let $modal = $(`#${modalId}`);
                let $obatSelect = $modal.find('select[name="id_stok_batch"]');

                // Reset medicine selection if branch changes
                $obatSelect.val('').trigger('change').prop('disabled', !idCabang);

                if (idCabang) {
                    $obatSelect.find('option').addClass('d-none');
                    $obatSelect.find('option[value=""]').removeClass('d-none').text('Pilih Obat - Batch');
                    $obatSelect.find(`option[data-id-cabang="${idCabang}"]`).removeClass('d-none');
                } else {
                    $obatSelect.find('option[value=""]').text('Pilih Cabang Terlebih Dahulu');
                }

                // Re-init Select2 if it's being used
                if ($obatSelect.hasClass("select2-hidden-accessible")) {
                    $obatSelect.select2('destroy').select2({
                        dropdownParent: $modal,
                        width: '100%'
                    });
                }
            }

            // Handler for Branch Selection in TAMBAH Modal
            $(document).on('change', '#single-select-cabang-tambah', function() {
                filterObatByCabang('tambah', $(this).val());
            });

            // Handler for Branch Selection in EDIT Modal
            $(document).on('change', '#single-select-cabang-edit', function() {
                filterObatByCabang('modalEditObatKeluar', $(this).val());
            });

            // Global Handler for Medicine Selection (Both modals)
            $(document).on('change', 'select[name="id_stok_batch"]', function() {
                let option = $(this).find(':selected');
                let modal = $(this).closest('.modal');

                let exp = option.data('exp');
                let stok = option.data('stok');

                // Populate auto-filled fields
                modal.find('[data-field="tanggal_kedaluwarsa"]').val(exp);
                modal.find('[data-field="stok_sisa"]').val(stok);
                modal.find('[data-field="stok_keluar"]').attr('max', stok);
            });

            // Handle EDIT modal data initialization
            document.addEventListener('DOMContentLoaded', function() {
                const editModal = document.getElementById('modalEditObatKeluar');
                if (editModal) {
                    editModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const idCabang = button.dataset.id_cabang;
                        const idStokBatch = button.dataset.id_stok_batch;
                        const $modal = $(editModal);

                        // First, set the branch
                        const $branchSelect = $modal.find('select[name="id_cabang"]');
                        $branchSelect.val(idCabang).trigger('change');

                        // Small delay to ensure filter logic finished and select2 is ready
                        setTimeout(() => {
                            let $obatSelect = $modal.find('select[name="id_stok_batch"]');
                            $obatSelect.val(idStokBatch).trigger('change');
                        }, 100);
                    });
                }
            });
        </script>
    @endpush
</x-app>
