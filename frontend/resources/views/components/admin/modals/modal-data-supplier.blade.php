@if ($type === 'tambah')
    {{-- ! Modal Tambah --}}
    <div class="modal fade modal-dynamic" id="tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>{{ $judul }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="formTambahSupplier" action="{{ url('/data-supplier-tambah') }}" method="POST"
                        class="modal-form-container">
                        @csrf

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Nama Supplier</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="nama_supplier" value="" placeholder="Masukan nama supplier"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Alamat Supplier</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="alamat_supplier" value="" placeholder="Masukan alamat supplier"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">No Telepon Supplier</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="no_telp" value="" placeholder="Masukan nomor telepon supplier"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formTambahSupplier" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@elseif ($type === 'edit')
    {{-- ! Modal Edit --}}
    <div class="modal fade modal-dynamic" id="modalEditSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>{{ $judul }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="formEditSupplier" method="POST" class="modal-form-container">
                        @csrf
                        @method('PUT')

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Nama Supplier</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" data-field="nama" placeholder="Masukan nama supplier"
                                    name="nama_supplier" class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Alamat Supplier</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" data-field="alamat" placeholder="Masukan alamat supplier"
                                    name="alamat_supplier" class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">No Telepon Supplier</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" data-field="telp" placeholder="Masukan nomor telepon supplier"
                                    name="no_telp" class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formEditSupplier" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif