@if ($type === 'tambah')
    {{-- ! Modal Tambah --}}
    <div class="modal fade" id="tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">{{ $judul }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form action="{{ url('/data-supplier-tambah') }}" method="POST">
                        @csrf
                        <table class="table">
                            <tr>
                                <td>Nama Suplier</td>
                                <td>
                                    <input type="text" name="nama_supplier" value="" placeholder="Masukan Nama Supplier"
                                        class="form-control" required>
                                </td>
                            </tr>
                            <tr>
                                <td>Alamat Suplier</td>
                                <td>
                                    <input type="text" name="alamat_supplier" value="" placeholder="Masukan Alamat Supplier"
                                        class="form-control" required>
                                </td>
                            </tr>
                            <tr>
                                <td>No Telphone Suplier</td>
                                <td>
                                    <input type="text" name="no_telp" value="" placeholder="Masukan No Telp Supplier"
                                        class="form-control" required>
                                </td>
                            </tr>
                        </table>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
                    <h5 class="modal-title">{{ $judul }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? BODY --}}
                <div class="modal-body modal-body-custom">
                    <form method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table">
                            <tr>
                                <td>Nama Suplier</td>
                                <td>
                                    <input type="text" data-field="nama" placeholder="Masukan Nama Supplier"
                                        name="nama_supplier" class="form-control" required>
                                </td>
                            </tr>
                            <tr>
                                <td>Alamat Suplier</td>
                                <td>
                                    <input type="text" data-field="alamat" placeholder="Masukan Alamat Supplier"
                                        name="alamat_supplier" class="form-control" required>
                                </td>
                            </tr>
                            <tr>
                                <td>No Telphone Suplier</td>
                                <td>
                                    <input type="text" data-field="telp" placeholder="Masukan No Telp Supplier"
                                        name="no_telp" class="form-control" required>
                                </td>
                            </tr>
                        </table>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif