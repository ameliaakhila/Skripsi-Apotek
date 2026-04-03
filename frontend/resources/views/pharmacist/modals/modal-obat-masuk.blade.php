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
                    <form action="{{ url('/obat-masuk-tambah') }}" method="POST">
                        @csrf
                        <table class="table table-sm table-borderless">
                            {{-- Kode Batch --}}
                            <tr>
                                <td><strong>Kode Batchu</strong></td>
                                <td><input type="text" name="kode_batch" value="{{ $kodeBatch }}"
                                        class="form-control form-control-sm" readonly>
                                </td>
                            </tr>
                            {{-- Supplier --}}
                            <tr>
                                <td><strong>Nama Supplier</strong></td>
                                <td>
                                    <select name="id_supplier" id="single-select-supplier"
                                        class="form-control form-control-sm" required>
                                        <option selected disabled>Pilih Supplier</selected>
                                            @foreach($supplier as $s)
                                                <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                                            @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Obat --}}
                            <tr>
                                <td><strong>Nama Obat</strong></td>
                                <td>
                                    <select name="id_obat" id="single-select-obat" class="form-control form-control-sm"
                                        required>
                                        <option selected disabled>Pilih Obat</option>
                                        @foreach($obat as $o)
                                            <option value="{{ $o->id_obat }}">{{ $o->nama_obat }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Satuan --}}
                            <tr>
                                <td><strong>Satuan</strong></td>
                                <td>
                                    <select name="id_satuan" id="single-select-satuan" class="form-control form-control-sm"
                                        required>
                                        <option selected disabled>Pilih Satuan</option>
                                        @foreach($satuan as $s)
                                            <option value="{{ $s->id_satuan }}">{{ $s->nama_satuan }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Kategori --}}
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>
                                    <select name="id_kategori" id="single-select-kategori"
                                        class="form-control form-control-sm" required>
                                        <option selected disabled>Pilih Kategori</option>
                                        @foreach($kategori as $o)
                                            <option value="{{ $o->id_kategori }}">{{ $o->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Tanggal --}}
                            <tr>
                                <td><strong>Tanggal Masuk</strong></td>
                                <td><input type="date" name="tanggal_masuk" value="{{ now()->format('Y-m-d') }}"
                                        class="form-control form-control-sm" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">Tanggal Kedaluwarsa</strong></td>
                                <td><input type="date" name="tanggal_kedaluwarsa" value="2028-02-24"
                                        placeholder="Masukan tanggal kedaluwarsa obat" class="form-control form-control-sm"
                                        required></td>
                            </tr>
                            {{-- Stok dan Harga --}}
                            <tr>
                                <td><strong>Stok Masuk</strong></td>
                                <td><input type="number" name="stok_masuk" value="120" placeholder="Masukan stok masuk obat"
                                        class="form-control form-control-sm" required></td>
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

@elseif($type === 'edit' || $type === 'detail')
    {{-- ! Modal Edit / Detail --}}
    <div class="modal fade modal-dynamic" id="{{ $type === 'edit' ? 'modalEditObat' : 'modalDetailObat' }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">
                        <i class="fas {{ $type === 'edit' ? 'fa-edit' : 'fa-eye' }} me-2"></i>{{ $judul }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? Body --}}
                <div class="{{ $type === 'edit' ? 'modal-body modal-body-custom' : 'modal-body' }}">
                    @if($type === 'edit')
                        <form id="formEditObatMasuk" method="POST">
                            @csrf
                            @method('PUT')
                    @endif

                        <table
                            class="{{ $type === 'edit' ? 'table table-sm table-borderless' : 'table table-sm table-striped' }}">
                            {{-- Kode Batch --}}
                            <tr>
                                <td><strong>Kode Batch</strong></td>
                                <td>
                                    @if($type === 'edit')
                                        <input type="hidden" name="kode_batch" data-field="kodeBatch" value="">
                                        <input type="text" data-field="kodeBatch" class="form-control form-control-sm" readonly>
                                        <small class="form-text text-muted d-block mt-2">
                                            Kode batch otomatis terisi saat penambahan data dan tidak dapat diubah
                                        </small>
                                    @else
                                        <span data-field="kode_batch"></span>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td><strong class="required-field">Nama Obat</strong></td>
                                <td>
                                    @if($type === 'edit')
                                        <select name="id_obat" class="form-control form-control-sm" data-field="idObat"
                                            required>
                                            @foreach($obat as $o)
                                                <option value="{{ $o->id_obat }}">{{ $o->nama_obat }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span data-field="nama_obat"></span> | <span data-field="nama_satuan"></span> |
                                        <span data-field="nama_kategori"></span>
                                    @endif
                                </td>
                            </tr>

                            @if($type === 'edit')
                                <tr>
                                    <td><strong class="required-field">Supplier</strong></td>
                                    <td>
                                        <select name="id_supplier" class="form-control form-control-sm" data-field="idSupplier"
                                            required>
                                            <option selected disabled>Pilih Supplier</option>
                                            @foreach($supplier as $s)
                                                <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong class="required-field">Satuan Obat</strong></td>
                                    <td>
                                        <select name="id_satuan" class="form-control form-control-sm" data-field="idSatuan"
                                            required>
                                            <option selected disabled>Pilih Satuan</option>
                                            @foreach($satuan as $s)
                                                <option value="{{ $s->id_satuan }}">{{ $s->nama_satuan }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong class="required-field">Kategori Obat</strong></td>
                                    <td>
                                        <select name="id_kategori" class="form-control form-control-sm" data-field="idKategori"
                                            required>
                                            <option selected disabled>Pilih Kategori</option>
                                            @foreach($kategori as $k)
                                                <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td><strong>Supplier</strong></td>
                                    <td><span data-field="nama_supplier"></span></td>
                                </tr>
                            @endif

                            <tr>
                                <td><strong>Tanggal Masuk</strong></td>
                                <td>
                                    @if($type === 'edit')
                                        <input type="date" name="tanggal_masuk" data-field="tanggalMasuk"
                                            class="form-control form-control-sm" readonly>
                                        <small class="form-text text-muted d-block mt-2">
                                            Tanggal masuk otomatis terisi dan tidak dapat diubah
                                        </small>
                                    @else
                                        <span data-field="tanggal_masuk"></span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">Tanggal Kedaluwarsa</strong></td>
                                <td>
                                    @if($type === 'edit')
                                        <input type="date" name="tanggal_kedaluwarsa" data-field="tanggalKedaluwarsa"
                                            class="form-control form-control-sm" required>
                                    @else
                                        <span data-field="tanggal_kedaluwarsa"></span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="required-field">Tanggal Masuk</strong></td>
                                <td>
                                    @if($type === 'edit')
                                        <input type="number" name="stok_masuk" data-field="stokMasuk"
                                            class="form-control form-control-sm stok-masuk" min="1" required>
                                    @else
                                        <span data-field="stok_masuk"></span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Stok Keluar</strong></td>
                                <td>
                                    @if($type === 'edit')
                                        <input type="number" name="stok_keluar" data-field="stokKeluar"
                                            class="form-control form-control-sm" min="0" readonly
                                            title="Hanya bisa diubah pada menu obat keluar">
                                        <small class="form-text text-muted d-block mt-2">
                                            Hanya bisa diubah pada menu obat keluar
                                        </small>
                                    @else
                                        <span data-field="stok_keluar"></span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Stok Sisa</strong></td>
                                <td>
                                    @if($type === 'edit')
                                        <input type="number" name="stok_sisa" data-field="stokSisa"
                                            class="form-control form-control-sm" readonly>
                                        <small class="form-text text-muted d-block mt-2">
                                            Stok sisa dihitung otomatis dari stok masuk - stok keluar
                                        </small>
                                    @else
                                        <span data-field="stok_sisa"></span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        @if($type === 'edit')
                                <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><i
                                            class="fas fa-times me-2"></i>Batal</button>
                                    <button type="submit" class="btn btn-sm btn-primary"><i
                                            class="fas fa-save me-2"></i>Simpan</button>
                                </div>
                            </form>
                        @else
                        <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal"><i
                                    class="fas fa-check me-2"></i>Tutup</button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endif