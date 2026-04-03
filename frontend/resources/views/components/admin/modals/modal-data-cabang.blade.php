@if($type === 'tambah')
    {{-- ! Modal Tambah --}}
    <div class="modal fade modal-dynamic" id="tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>{{ $judul }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form action="{{ url('/data-cabang-tambah') }}" method="POST" id="formTambahCabang"
                        class="modal-form-container">
                        @csrf

                        <div class="modal-form-group">
                            <label class="modal-form-label">Kode Cabang</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="kode_cabang" value="{{ $kodeCabang }}"
                                    class="form-control form-control-sm modal-form-control" readonly>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Nama Cabang</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="nama_cabang" value="" placeholder="Masukan nama cabang"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Alamat Cabang</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="alamat_cabang" value="" placeholder="Masukan alamat cabang"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formTambahCabang" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

@elseif(($type === 'edit'))
    {{-- ! Modal Edit / Detail --}}
    <div class="modal fade modal-dynamic" id="modalEditCabang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>{{ $judul }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="formEditCabang" method="POST" class="modal-form-container">
                        @csrf
                        @method('PUT')

                        <div class="modal-form-group">
                            <label class="modal-form-label">Kode Cabang</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="kode_cabang" data-field="kodeCabang"
                                    class="form-control form-control-sm modal-form-control" readonly>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Nama Cabang</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="nama_cabang" data-field="namaCabang"
                                    placeholder="Masukan nama cabang"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Alamat Cabang</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="alamat_cabang" data-field="alamatCabang"
                                    placeholder="Masukan alamat cabang"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formEditCabang" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
