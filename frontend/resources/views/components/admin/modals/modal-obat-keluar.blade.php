@if($type === 'tambah')
    {{-- ! Modal Tambah --}}
    <div class="modal fade modal-dynamic" id="tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>{{ $judul }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form action="{{ url('/obat-keluar-tambah') }}" method="POST" id="formTambahKeluarObat"
                        class="modal-form-container">
                        @csrf
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Nama Obat</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_obat_masuk" id="single-select-supplier"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option value="" disabled selected>Pilih Obat</option>
                                    @foreach($obatList as $item)
                                        <option value="{{ $item->id_obat_masuk }}" data-kode="{{ $item->kode_batch }}"
                                            data-obat="{{ $item->Obat->nama_obat ?? "-" }}"
                                            data-exp="{{ $item->tanggal_kedaluwarsa }}" data-stok="{{ $item->stok_sisa ?? 0 }}">
                                            {{ $item->Obat->nama_obat ?? "-" }} ({{ $item->kode_batch }}) | Exp:
                                            {{ \Carbon\Carbon::parse($item->tanggal_kedaluwarsa)->format('d M Y') }} | Stok:
                                            {{ $item->stok_sisa ?? 0 }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Tanggal Keluar</label>
                            <div class="modal-form-control-wrapper readonly-field">
                                <input type="date" name="tanggal_keluar" id="tanggal_keluar"
                                    value="{{ now()->format('Y-m-d') }}"
                                    class="form-control form-control-sm modal-form-control" readonly>
                                <small class="modal-form-hint">Tanggal otomatis terisi saat pengiriman</small>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Cabang Tujuan</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_cabang" id="single-select-cabang"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach($cabang as $c)
                                        <option value="{{ $c->id_cabang }}">{{ $c->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Stok Keluar</label>
                            <div class="modal-form-control-wrapper">
                                <input type="number" name="stok_keluar" id="stok_keluar_input"
                                    placeholder="Masukan jumlah stok keluar"
                                    class="form-control form-control-sm modal-form-control" min="1" required>
                                <small class="modal-form-hint">
                                    Stok Sisa Tersedia: <span id="stok_tersedia">-</span> unit
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formTambahKeluarObat" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'detail')
    {{-- ! Modal Detail --}}
    <div class="modal fade modal-dynamic" id="modalDetailObatKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Obat Keluar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <table class="table table-sm table-striped">
                        <tr>
                            <td><strong>Kode Batch</strong></td>
                            <td><span data-field="batch"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Obat</strong></td>
                            <td><span data-field="nama_obat"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Keluar</strong></td>
                            <td><span data-field="tanggal_keluar"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Kedaluwarsa</strong></td>
                            <td><span data-field="tanggal_kedaluwarsa"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Cabang</strong></td>
                            <td><span data-field="nama_cabang"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Stok Keluar</strong></td>
                            <td><span data-field="stok_keluar"></span></td>
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

@elseif($type === 'edit')
    {{-- ! Modal Edit --}}
    <div class="modal fade modal-dynamic" id="modalEditObatKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Obat Keluar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="formEditObatKeluar" method="POST" class="modal-form-container">
                        @csrf
                        @method('PUT')

                        <div class="modal-form-group">
                            <label class="modal-form-label">Kode Batch</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" data-field="batch"
                                    class="form-control form-control-sm modal-form-control" readonly>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Nama Obat</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_obat_masuk" data-field="idObatMasuk" id="single-select-obat-edit"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option value="" disabled selected>Pilih Obat</option>
                                    @foreach($obatList as $item)
                                        <option value="{{ $item->id_obat_masuk }}" data-kode="{{ $item->kode_batch }}"
                                            data-obat="{{ $item->Obat->nama_obat }}" data-exp="{{ $item->tanggal_kedaluwarsa }}"
                                            data-stok="{{ $item->stok_sisa ?? 0 }}">
                                            {{ $item->Obat->nama_obat }} ({{ $item->kode_batch }}) |
                                            Exp: {{ \Carbon\Carbon::parse($item->tanggal_kedaluwarsa)->format('d M Y') }} |
                                            Stok: {{ $item->stok_sisa ?? 0 }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Tanggal Keluar</label>
                            <div class="modal-form-control-wrapper">
                                <input type="date" data-field="tanggalKeluar"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Tanggal Kedaluwarsa</label>
                            <div class="modal-form-control-wrapper">
                                <input type="date" data-field="tanggalKedaluwarsa"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Cabang Tujuan</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_cabang" id="single-select-cabang-edit" data-field="idCabang"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option value="" disabled selected>Pilih Cabang</option>
                                    @foreach($cabang as $c)
                                        <option value="{{ $c->id_cabang }}">{{ $c->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Stok Keluar</label>
                            <div class="modal-form-control-wrapper">
                                <input type="number" name="stok_keluar" id="stok_keluar_edit_input" data-field="stokKeluar"
                                    class="form-control form-control-sm modal-form-control" min="1" required>
                                <small class="modal-form-hint">
                                    Stok Sisa Tersedia: <span data-field="stokSisa"></span> unit
                                </small>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formEditObatKeluar" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif