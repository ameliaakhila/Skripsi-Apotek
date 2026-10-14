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
                    <form id="formTambahObatMasuk" action="{{ url('/obat-masuk-tambah') }}" method="POST">
                        @csrf

                        {{-- Kode Batch --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label">Kode Batch</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="kode_batch" value="{{ $kodeBatch }}"
                                    class="form-control form-control-sm modal-form-control" readonly>
                            </div>
                        </div>

                        {{-- Nama Obat --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Nama Obat</label>
                            <select name="id_obat" id="single-select-obat" class="form-control form-control-sm modal-form-control" required>
                            <option value="" disabled selected>Pilih Obat</option>
                            @foreach($obat as $o)
                                <option value="{{ $o->id_obat }}">{{ $o->nama_obat }} |
                                    {{ $o->DataKategori->nama_kategori }} | {{ $o->DataSatuan->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                        </div>

                        {{-- Tanggal Masuk --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label">Tanggal Masuk</label>
                            <div class="modal-form-control-wrapper">
                                <input type="date" name="tanggal_masuk" value="{{ now()->format('Y-m-d') }}"
                                    class="form-control form-control-sm modal-form-control" readonly>
                                <small class="modal-form-hint">Tanggal otomatis terisi saat penambahan</small>
                            </div>
                        </div>

                        {{-- Tanggal Kedaluwarsa --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Tanggal Kedaluwarsa</label>
                            <div class="modal-form-control-wrapper">
                                <input type="date" name="tanggal_kedaluwarsa" value=""
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        {{-- Stok Masuk --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Stok Masuk</label>
                            <div class="modal-form-control-wrapper">
                                <input type="number" name="stok_masuk" placeholder="Masukkan stok masuk" value=""
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formTambahObatMasuk" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'edit')
    {{-- ! Modal Edit --}}
    <div class="modal fade modal-dynamic" id="modalEditObat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>{{ $judul }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="formEditObatMasuk" method="POST" class="modal-form-container">
                        @csrf
                        @method('PUT')

                        {{-- Kode Batch --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label">Kode Batch</label>
                            <div class="modal-form-control-wrapper">
                                <input type="hidden" name="kode_batch" data-field="kodeBatch" value="">
                                <input type="text" data-field="kodeBatch"
                                    class="form-control form-control-sm modal-form-control" readonly>
                                <small class="modal-form-hint">Kode batch otomatis terisi saat penambahan</small>
                            </div>
                        </div>

                        {{-- Nama Obat --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Nama Obat</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_obat" id="single-select-obat"
                                    class="form-control form-control-sm modal-form-control modal-form-select"
                                    data-field="idObat" required>
                                    @foreach($obat as $o)
                                        <option value="{{ $o->id_obat }}">{{ $o->nama_obat }} |
                                            {{ $o->DataSatuan->nama_satuan }} | {{ $o->DataKategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Tanggal Masuk --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label">Tanggal Masuk</label>
                            <div class="modal-form-control-wrapper">
                                <input type="date" name="tanggal_masuk" data-field="tanggalMasuk"
                                    class="form-control form-control-sm modal-form-control" readonly>
                                <small class="modal-form-hint">Tanggal masuk otomatis terisi saat penambahan</small>
                            </div>
                        </div>

                        {{-- Tanggal Kedaluwarsa --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Tanggal Kedaluwarsa</label>
                            <div class="modal-form-control-wrapper">
                                <input type="date" name="tanggal_kedaluwarsa" data-field="tanggalKedaluwarsa"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        {{-- Stok Masuk --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Stok Masuk</label>
                            <div class="modal-form-control-wrapper">
                                <input type="number" name="stok_masuk" data-field="stokMasuk"
                                    class="form-control form-control-sm modal-form-control stok-masuk" min="1" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formEditObatMasuk" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'detail')
    {{-- ! Modal Detail --}}
    <div class="modal fade modal-dynamic" id="modalDetailObat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-eye me-2"></i>{{ $judul }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <table class="table table-sm table-striped">
                        <tr>
                            <td><strong>Kode Batch</strong></td>
                            <td><span data-field="kode_batch"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Obat</strong></td>
                            <td><span data-field="nama_obat"></span> | <span data-field="nama_satuan"></span> | <span
                                    data-field="nama_kategori"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Supplier</strong></td>
                            <td><span data-field="nama_supplier"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Masuk</strong></td>
                            <td><span data-field="tanggal_masuk"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Kedaluwarsa</strong></td>
                            <td><span data-field="tanggal_kedaluwarsa"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Stok Masuk</strong></td>
                            <td><span data-field="stok_masuk"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">
                        <i class="fas fa-check me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
