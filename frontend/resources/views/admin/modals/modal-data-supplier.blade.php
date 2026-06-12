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
                    <form action="{{ route('admin.data-supplier.store') }}" method="POST">
                        @csrf
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong class="required-field">Nama Suplier</strong></td>
                                <td>
                                    <input type="text" name="nama_supplier" value="" placeholder="Masukan Nama Supplier"
                                        class="form-control form-control-sm" required>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">Alamat Suplier</strong></td>
                                <td>
                                    <input type="text" name="alamat_supplier" value="" placeholder="Masukan Alamat Supplier"
                                        class="form-control form-control-sm" required>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">No Telphone Suplier</strong></td>
                                <td>
                                    <input type="text" name="no_telp" value="" placeholder="Masukan No Telp Supplier"
                                        class="form-control form-control-sm" required>
                                </td>
                            </tr>
                        </table>
                        <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><i
                                    class="fas fa-times me-2"></i>Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@elseif ($type === 'edit')
    {{-- ! Modal Edit --}}
    <div class="modal fade modal-dynamic" id="modalEditSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>{{ $judul }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? BODY --}}
                <div class="modal-body modal-body-custom">
                    <form method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong class="required-field">Nama Suplier</strong></td>
                                <td>
                                    <input type="text" data-field="nama" placeholder="Masukan Nama Supplier"
                                        name="nama_supplier" class="form-control form-control-sm" required>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">Alamat Suplier</strong></td>
                                <td>
                                    <input type="text" data-field="alamat" placeholder="Masukan Alamat Supplier"
                                        name="alamat_supplier" class="form-control form-control-sm" required>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">No Telphone Suplier</strong></td>
                                <td>
                                    <input type="text" data-field="telp" placeholder="Masukan No Telp Supplier"
                                        name="no_telp" class="form-control form-control-sm" required>
                                </td>
                            </tr>
                        </table>
                        <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><i
                                    class="fas fa-times me-2"></i>Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
