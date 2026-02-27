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
                    <form action="{{ url('/obat-masuk-tambah') }}" method="POST">
                        @csrf
                        <table class="table">
                            {{-- Kode Obat --}}
                            <tr>
                                <td>Kode Obat</td>
                                <td><input type="text" name="kode_obat" value="{{ $kodeObat }}"
                                        class="form-control text-opacity-75" readonly>
                                </td>
                            </tr>
                            {{-- Kode Batch --}}
                            <tr>
                                <td>Kode Batch</td>
                                <td><input type="text" name="kode_batch" value="{{ $kodeBatch }}"
                                        class="form-control text-opacity-75" readonly>
                                </td>
                            </tr>
                            {{-- Nama Obat --}}
                            <tr>
                                <td>Nama Obat</td>
                                <td>
                                    <select name="id_obat" class="default-select wide form-control" required>
                                        <option value="1">Pilih</option>
                                        @foreach($obat as $o)
                                            <option value="{{ $o->id_obat }}">{{ $o->nama_obat }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Satuan --}}
                            <tr>
                                <td>Satuan</td>
                                <td>
                                    <select name="id_satuan" class="default-select wide form-control" required>
                                        <option value="1">Pilih</option>
                                        @foreach($satuan as $s)
                                            <option value="{{ $s->id_satuan }}">{{ $s->nama_satuan }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Kategori --}}
                            <tr>
                                <td>Kategori</td>
                                <td>
                                    <select name="id_kategori" class="default-select wide form-control" required>
                                        <option value="1">Pilih</option>
                                        @foreach($kategori as $o)
                                            <option value="{{ $o->id_kategori }}">{{ $o->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Tanggal --}}
                            <tr>
                                <td>Tanggal Masuk</td>
                                <td><input type="text" name="tanggal_masuk" value="{{ now()->format('Y-m-d') }}"
                                        placeholder="Masukan tanggal masuk obat" class="form-control datepicker-default"
                                        required></td>
                            </tr>
                            <tr>
                                <td>Tanggal kedaluwarsa</td>
                                <td><input type="text" name="tanggal_kedaluwarsa" value="2028-02-24"
                                        placeholder="Masukan tanggal kedaluwarsa obat"
                                        class="form-control datepicker-default" required></td>
                            </tr>
                            {{-- Stok dan Harga --}}
                            <tr>
                                <td>Stok Masuk</td>
                                <td><input type="number" name="stok_masuk" value="120" placeholder="Masukan stok masuk obat"
                                        class="form-control" required></td>
                            </tr>
                            <tr>
                                <td>Harga Obat</td>
                                <td><input type="number" name="harga_obat" value="10000" placeholder="Masukan harga obat"
                                        class="form-control" required></td>
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

@elseif($type === 'edit' || $type === 'detail')
    {{-- ! Modal Edit / Detail --}}
    <div class="modal fade modal-dynamic" id="{{ $type === 'edit' ? 'modalEditObatMasuk' : 'modalDetailObatMasuk' }}"
        tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">
                        {{ $judul }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? Body --}}
                <div class="{{ $type === 'edit' ? 'modal-body modal-body-custom' : 'modal-body' }}">
                    @if($type === 'edit')
                        <form method="POST">
                            @csrf
                            @method('PUT')
                    @endif

                        <table class="{{ $type === 'edit' ? 'table' : 'table table-striped' }}">
                            <tr>
                                <td>Nama Obat</td>
                                <td>
                                    <input type="hidden" name="kode_obat" data-field="kodeObat" required>
                                    <input type="hidden" name="kode_batch" data-field="kodeBatch" required>
                                    @if($type === 'edit')
                                        <select name="id_obat" class="form-control" data-field="obat" required>
                                            @foreach($obat as $o)
                                                <option value="{{ $o->id_obat }}">
                                                    {{ $o->nama_obat }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="fw-light" data-field="nama_obat"></span>
                                        | <span class="fw-light" data-field="kode_obat"></span>
                                        | <span class="fw-light" data-field="satuan_obat"></span>
                                        | <span class="fw-light" data-field="kategori_obat"></span>
                                    @endif
                                </td>
                            </tr>

                            @if($type === 'edit')
                                <tr>
                                    <td>Satuan Obat</td>
                                    <td>
                                        <select name="id_satuan" class="form-control" data-field="satuan" required>
                                            @foreach($satuan as $o)
                                                <option value="{{ $o->id_satuan }}">{{ $o->nama_satuan }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Kategori Obat</td>
                                    <td>
                                        @if($type === 'edit')
                                            <select name="id_kategori" class="form-control" data-field="kategori" required>
                                                @foreach($kategori as $o)
                                                    <option value="{{ $o->id_kategori }}">{{ $o->nama_kategori }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            {{ $d->DataKategori->nama_kategori }}
                                        @endif
                                    </td>
                                </tr>
                            @else
                            @endif

                            <tr>
                                <td>Tanggal Masuk</td>
                                <td>
                                    @if($type === 'edit')
                                        <input type="text" name="tanggal_masuk" data-field="tanggalMasuk"
                                            placeholder="Masukan tanggal masuk obat" class="datepicker-default form-control"
                                            required>
                                    @else
                                        <span data-field="tanggal_masuk"></span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Tanggal kedaluwarsa</td>
                                @if ($type === 'edit')
                                    <td>
                                        <input type="text" name="tanggal_kedaluwarsa" data-field="tanggalKedaluwarsa"
                                            placeholder="Masukan tanggal kedaluwarsa obat"
                                            class="datepicker-default form-control" required>
                                    </td>
                                @else
                                    <td class="d-flex justify-content-between">
                                        <span data-field="tanggal_kedaluwarsa"></span>
                                        <span class="badge" :class="'bg-' + data.status_kedaluwarsa_badge"
                                            data-field="status_kedaluwarsa_data"></span>
                                    </td>
                                    </class=>
                                @endif
                            </tr>
                            <tr>
                                <td>Stok Masuk</td>
                                <td>
                                    @if ($type === 'edit')
                                        <input type="number" name="stok_masuk" data-field="stokMasuk"
                                            placeholder="Masukan stok masuk obat" class=" stok-masuk form-control" required>
                                    @else
                                        <span class="fw-light" data-field="stok_masuk"></span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Stok Keluar</td>
                                @if ($type === 'edit')
                                    <td>
                                        <input type="number" name="stok_keluar" data-field="stokKeluar"
                                            placeholder="Masukan jumlah stok keluar" class="stok-keluar form-control" required>
                                    </td>
                                @else
                                    <td class="d-flex justify-content-between">
                                        <span data-field="stok_keluar"></span>
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td>Stok Sisa</td>
                                @if ($type === 'edit')
                                    <td>
                                        <input type="number" data-field="stokSisa" placeholder="Masukan jumlah stok sisa"
                                            name="stok_sisa" class="stok-sisa form-control" readonly>
                                    </td>
                                @else
                                    <td class="d-flex justify-content-between">
                                        <span data-field="stok_sisa"></span>
                                        {{-- <span
                                            class="badge bg-{{ $d->status_stok['warna'] }}">{{$d->status_stok['status']}}</span>
                                        --}}
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td>Harga</td>
                                <td>
                                    @if ($type === 'edit')
                                        <input type="number" name="harga_obat" data-field="hargaObat" class="form-control"
                                            required>
                                    @else
                                        <span class="fw-light" data-field="harga_obat"></span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        @if($type === 'edit')
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        @else
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endif