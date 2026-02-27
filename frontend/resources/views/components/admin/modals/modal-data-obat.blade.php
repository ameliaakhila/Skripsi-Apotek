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
                    <form action="{{ url('/data-obat-tambah') }}" method="POST">
                        @csrf
                        <table class="table">
                            <tr>
                                <td>Nama Obat</td>
                                <td>
                                    <input type="text" name="nama_obat" value="" placeholder="Masukan nama obat baru"
                                        class="form-control" required>
                                </td>
                            </tr>
                            {{-- Supplier Obat --}}
                            <tr>
                                <td>Supplier Obat</td>
                                <td>
                                    <select name="id_supplier" class="form-control" required>
                                        <option selected disabled>Pilih</option>
                                        @foreach($supplier as $s)
                                            <option value="{{ $s->id_supplier }}">
                                                {{ $s->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Satuan Obat --}}
                            <tr>
                                <td>Satuan Obat</td>
                                <td>
                                    <select name="id_satuan" class="form-control" required>
                                        <option selected disabled>Pilih</option>
                                        @foreach($satuan as $s)
                                            <option value="{{ $s->id_satuan }}">
                                                {{ $s->nama_satuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Kategori --}}
                            <tr>
                                <td>Kategori</td>
                                <td>
                                    <select name="id_kategori" class="form-control" required>
                                        <option selected disabled>Pilih</option>
                                        @foreach($kategori as $k)
                                            <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
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
    <div class="modal fade modal-dynamic" id="modalEditObat" tabindex="-1" aria-hidden="true">
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
                                <td>Nama Obat</td>
                                <td>
                                    <input type="text" data-field="namaObat" name="nama_obat" class="form-control" required>
                                </td>
                            </tr>
                            {{-- Supplier Obat --}}
                            <tr>
                                <td>Supplier Obat</td>
                                <td>
                                    <select data-field="supplier" name="id_supplier" class="form-control" required>
                                        @foreach($supplier as $s)
                                            <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Satuan Obat --}}
                            <tr>
                                <td>Satuan Obat</td>
                                <td>
                                    <select data-field="satuan" name="id_satuan" class="form-control" required>
                                        @foreach($satuan as $s)
                                            <option value="{{ $s->id_satuan }}">
                                                {{ $s->nama_satuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Kategori --}}
                            <tr>
                                <td>Kategori</td>
                                <td>
                                    <select data-field="kategori" name="id_kategori" class="form-control" required>
                                        @foreach($kategori as $k)
                                            <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
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