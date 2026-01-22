@props([
    'type' => 'default',
    'd' => null,
    'kategori'
])

@if($type === 'tambah')
    {{-- ! Modal Tambah --}}
    <div class="modal fade" id="tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">Tambah Data Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form action="{{ url('/data-obat-tambah') }}" method="POST">
                        @csrf
                        <table class="table">
                            <tr>
                                <td>Nama Obat</td>
                                <td>
                                    <input type="text" name="nama_obat" value="Paramex" placeholder="Masukan nama obat baru" class="form-control" required>
                                </td>
                            </tr>
                            {{-- Pabrik Obat --}}
                            <tr>
                                <td>Pabrik Obat</td>
                                <td>
                                    <input type="text" name="pabrik_obat" value="PT Kalbe" placeholder="Masukan pabrik obat baru" class="form-control" required>
                                </td>
                            </tr>
                            {{-- Satuan Obat --}}
                            <tr>
                                <td>Satuan Obat</td>
                                <td>
                                    <input type="text" name="satuan_obat" value="Strip" placeholder="Masukan satuan obat baru" class="form-control" required>
                                </td>
                            </tr>
                            {{-- Kategori --}}
                            <tr>
                                <td>Kategori</td>
                                <td>
                                    <select name="id_kategori_obat" class="default-select form-control wide" required>
                                        <option value="1">Pilih Kategori</option>
                                        @foreach($kategori as $k)
                                            <option value="{{ $k->id_kategori_obat }}">{{ $k->nama_kategori_obat }}</option>
                                        @endforeach
                                    </select>
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

@elseif(($type === 'edit') && isset($d))
    {{-- ! Modal Edit / Detail --}}
    <div class="modal fade" id="edit{{ $d->id_obat }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">
                        Edit Obat Masuk
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? Body --}}
                <div class="modal-body modal-body-custom">
                        <form action="{{ url('/data-obat-edit/' . $d->id_obat) }}" method="POST">
                            @csrf
                            @method('PUT')
                    <table class="table">
                            <tr>
                                <td>Nama Obat</td>
                                <td>
                                    <input type="text" name="nama_obat" value="{{ $d->nama_obat }}" placeholder="Masukan nama obat baru" class="form-control" required>
                                </td>
                            </tr>
                            {{-- Pabrik Obat --}}
                            <tr>
                                <td>Pabrik Obat</td>
                                <td>
                                    <input type="text" name="pabrik_obat" value="{{ $d->pabrik_obat }}" placeholder="Masukan pabrik obat baru" class="form-control" required>
                                </td>
                            </tr>
                            {{-- Satuan Obat --}}
                            <tr>
                                <td>Satuan Obat</td>
                                <td>
                                    <input type="text" name="satuan_obat" value="{{ $d->satuan_obat }}" placeholder="Masukan satuan obat baru" class="form-control" required>
                                </td>
                            </tr>
                            {{-- Kategori --}}
                            <tr>
                                <td>Kategori</td>
                                <td>
                                    <select name="id_kategori_obat" class="form-control" required>
                                        <option value="{{ $d->id_kategori_obat }}">{{ $d->KategoriObat->nama_kategori_obat }}</option>
                                        @foreach($kategori as $k)
                                            @if($k->id_kategori_obat != $d->id_kategori_obat)
                                                <option value="{{ $k->id_kategori_obat }}">{{ $k->nama_kategori_obat }}</option>
                                            @endif
                                        @endforeach
                                    </select>
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
