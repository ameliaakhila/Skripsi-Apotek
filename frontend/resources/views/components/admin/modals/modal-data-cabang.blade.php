@if($type === 'tambah')
    {{-- ! Modal Tambah --}}
    <div class="modal fade" id="tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">{{ $judul }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form action="{{ url('/data-cabang-tambah') }}" method="POST">
                        @csrf
                        <table class="table">
                            <tr>
                                <td>Kode Cabang</td>
                                <td>
                                    <input type="text" name="kode_cabang" value="{{ $kodeCabang }}"
                                        class="form-control text-opacity-75" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td>Nama Cabang</td>
                                <td>
                                    <input type="text" name="nama_cabang" value="" placeholder="Masukan nama cabang baru"
                                        class="form-control" required>
                                </td>
                            </tr>
                            <tr>
                                <td>Alamat Cabang</td>
                                <td>
                                    <input type="text" name="alamat_cabang" value=""
                                        placeholder="Masukan alamat cabang baru" class="form-control" required>
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

@elseif(($type === 'edit'))
    {{-- ! Modal Edit / Detail --}}
    <div class="modal fade modal-dynamic" id="modalEditCabang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class=" modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">
                        {{ $judul }}
                    </h5> <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? Body --}}
                <div class="modal-body modal-body-custom">
                    <form method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table">
                            <tr>
                                <td>Kode Cabang</td>
                                <td>
                                    <input type="text" name="kode_cabang" data-field="kodeCabang"
                                        class="form-control text-opacity-75" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td>Nama Cabang</td>
                                <td>
                                    <input type="text" name="nama_cabang" data-field="namaCabang"
                                        placeholder="Masukan nama cabang baru" class="form-control" required>
                                </td>
                            </tr>
                            <tr>
                                <td>Alamat Cabang</td>
                                <td>
                                    <input type="text" name="alamat_cabang" data-field="alamatCabang"
                                        placeholder="Masukan alamat cabang baru" class="form-control" required>
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