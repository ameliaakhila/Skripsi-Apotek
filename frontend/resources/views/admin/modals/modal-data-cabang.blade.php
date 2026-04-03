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
                    <form action="{{ url('/data-cabang-tambah') }}" method="POST">
                        @csrf
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Kode Cabang</strong></td>
                                <td>
                                    <input type="text" name="kode_cabang" value="{{ $kodeCabang }}"
                                        class="form-control form-control-sm opacity-75" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">Nama Cabang</strong></td>
                                <td>
                                    <input type="text" name="nama_cabang" value="" placeholder="Masukan nama cabang baru"
                                        class="form-control form-control-sm" required>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">Alamat Cabang</strong></td>
                                <td>
                                    <input type="text" name="alamat_cabang" value=""
                                        placeholder="Masukan alamat cabang baru" class="form-control form-control-sm"
                                        required>
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

@elseif(($type === 'edit'))
    {{-- ! Modal Edit / Detail --}}
    <div class="modal fade modal-dynamic" id="modalEditCabang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class=" modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>{{ $judul }}
                    </h5> <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? Body --}}
                <div class="modal-body modal-body-custom">
                    <form method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Kode Cabang</strong></td>
                                <td>
                                    <input type="text" name="kode_cabang" data-field="kodeCabang"
                                        class="form-control form-control-sm text-opacity-75" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">Nama Cabang</strong></td>
                                <td>
                                    <input type="text" name="nama_cabang" data-field="namaCabang"
                                        placeholder="Masukan nama cabang baru" class="form-control form-control-sm"
                                        required>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">Alamat Cabang</strong></td>
                                <td>
                                    <input type="text" name="alamat_cabang" data-field="alamatCabang"
                                        placeholder="Masukan alamat cabang baru" class="form-control form-control-sm"
                                        required>
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
