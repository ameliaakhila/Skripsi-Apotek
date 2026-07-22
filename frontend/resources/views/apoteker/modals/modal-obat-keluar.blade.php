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
                    <form action="{{ url('/obat-keluar-tambah') }}" method="POST">
                        @csrf
                            <table class="table table-sm table-borderless">
                                {{-- Pilih Cabang --}}
                                <tr>
                                    <td><strong class="required-field">Pilih Cabang</strong></td>
                                    <td>
                                        <select name="id_cabang" id="single-select-cabang-tambah" class="form-control form-control-sm" required>
                                            <option value="" disabled selected>Pilih Cabang Asal Stok</option>
                                            @foreach($cabang as $c)
                                                <option value="{{ $c->id_cabang }}">{{ $c->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                        </td>
                                        </tr>
                                {{-- Pilih Stok Obat --}}
                                <tr>
                                    <td><strong class="required-field">Pilih Stok Obat</strong></td>
                                    <td>
                                        <select name="id_stok_batch" id="single-select-obat-tambah" class="form-control form-control-sm" required disabled>
                                            <option value="" disabled selected>Pilih Cabang Terlebih Dahulu</option>
                                            @foreach($obatList as $item)
                                                <option value="{{ $item->id_stok_batch }}" data-id-cabang="{{ $item->id_cabang }}" data-kode="{{ $item->kode_batch }}"
                                                    data-obat="{{ $item->Obat->nama_obat ?? '-' }}" data-exp="{{ $item->tanggal_kedaluwarsa }}"
                                                    data-stok="{{ $item->stok_sisa }}" class="d-none batch-option">
                                                    {{ $item->Obat->nama_obat ?? '-' }} ({{ $item->kode_batch }}) | Stok:
                                                    {{ $item->stok_sisa }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    </tr>
                                    {{-- Stok Tersedia --}}
                                    <tr>
                                        <td><strong>Stok Tersedia</strong></td>
                                        <td>
                                            <input type="text" data-field="stok_sisa" id="stok-sisa-tambah" class="form-control form-control-sm" readonly
                                                placeholder="0">
                                        </td>
                                    </tr>
                                    {{-- Tanggal Keluar --}}
                                    <tr>
                                        <td><strong>Tanggal Keluar</strong></td>
                                        <td>
                                            <input type="date" name="tanggal_keluar" id="tanggal_keluar" value="{{ now()->format('Y-m-d') }}"
                                                class="form-control form-control-sm" readonly>
                                        </td>
                                    </tr>
                                    {{-- Stok Keluar --}}
                                    <tr>
                                    <td><strong class="required-field">Stok Keluar</strong></td>
                                    <td>
                                        <input type="number" name="stok_keluar" data-field="stok_keluar" class="form-control form-control-sm" min="1" required>
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

@elseif($type === 'detail')
    {{-- ! Modal Detail --}}
    <div class="modal fade modal-dynamic" id="modalDetailObatKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Obat Keluar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? Body --}}
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
                    <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal"><i
                                class="fas fa-check me-2"></i>Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'edit')
    {{-- ! Modal Edit --}}
    <div class="modal fade modal-dynamic" id="modalEditObatKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Obat Keluar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? Body --}}
                <div class="modal-body modal-body-custom">
                    <form id="formEditObatKeluar" method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table table-sm table-borderless">
                            {{-- Pilih Cabang --}}
                            <table class="table table-sm table-borderless">
                                {{-- Pilih Cabang --}}
                                <tr>
                                    <td><strong>Pilih Cabang</strong></td>
                                    <td>
                                        <select name="id_cabang" id="single-select-cabang-edit" data-field="id_cabang" class="form-control form-control-sm"
                                            required>
                                            @foreach($cabang as $c)
                                                <option value="{{ $c->id_cabang }}">{{ $c->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                        </td>
                                        </tr>
                                {{-- Pilih Stok Obat --}}
                                <tr>
                                    <td><strong>Pilih Stok Obat</strong></td>
                                    <td>
                                        <select name="id_stok_batch" id="single-select-obat-edit" data-field="id_stok_batch"
                                            class="form-control form-control-sm" required>
                                        @foreach($obatList as $item)
                                            <option value="{{ $item->id_stok_batch }}" data-id-cabang="{{ $item->id_cabang }}" data-kode="{{ $item->kode_batch }}"
                                                data-obat="{{ $item->Obat->nama_obat ?? '-' }}" data-exp="{{ $item->tanggal_kedaluwarsa }}"
                                                data-stok="{{ $item->stok_sisa }}">
                                                {{ $item->Obat->nama_obat ?? '-' }} ({{ $item->kode_batch }}) | Stok:
                                                {{ $item->stok_sisa ?? '-' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                </tr>
                                {{-- Stok Tersedia --}}
                                <tr>
                                    <td><strong>Stok Tersedia</strong></td>
                                    <td>
                                        <input type="text" data-field="stok_sisa" class="form-control form-control-sm" readonly placeholder="0">
                                </td>
                            </tr>
                            {{-- Tanggal Keluar --}}
                            <tr>
                                <td><strong>Tanggal Keluar</strong></td>
                                <td>
                                    <input type="date" data-field="tanggal_keluar" class="form-control form-control-sm"
                                        required>
                                </td>
                            </tr>
                            {{-- Stok Keluar --}}
                            <tr>
                                <td><strong class="required-field">Stok Keluar</strong></td>
                                <td>
                                    <input type="number" name="stok_keluar" data-field="stok_keluar"
                                        class="form-control form-control-sm" min="1" required>
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
