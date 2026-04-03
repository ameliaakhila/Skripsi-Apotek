{{-- ! Modal Mutasi Stok - Conditional Type (tambah/edit) - PATTERN: modal-obat-keluar.blade.php --}}

@if($type === 'tambah')
    {{-- ! Modal Tambah Mutasi Stok --}}
    <div class="modal fade modal-dynamic" id="tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>{{ $judul }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form action="{{ url('/mutasi-stok-tambah') }}" method="POST">
                        @csrf
                        {{-- Cabang Asal --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label">Cabang Asal</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_cabang_asal" id="single-select-mutasi-asal"
                                    class="form-control form-control-sm modal-form-control modal-form-select">
                                    @foreach($dataCabangAsal as $cabang)
                                        <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Kode Batch --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Kode Batch</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_stok_batch" id="single-select-mutasi-batch"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    @foreach($dataStokBatch as $batch)
                                        <option value="{{ $batch->id_stok_batch }}" data-batch="{{ $batch->kode_batch }}"
                                            data-obat="{{ $batch->Obat->nama_obat ?? '-' }}"
                                            data-stok="{{ $batch->stok_sisa }}">
                                            {{ $batch->kode_batch }} | {{ $batch->Obat->nama_obat ?? '-' }} | Stok:
                                            {{ $batch->stok_sisa }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Tanggal Mutasi --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Tanggal Mutasi</label>
                            <div class="modal-form-control-wrapper">
                                <input type="date" name="tanggal_mutasi" value="{{ now()->format('Y-m-d') }}"
                                    class="form-control form-control-sm modal-form-control" readonly>
                                <small class="modal-form-hint">Akan secara otomatis terisi sesuai dengan tanggal hari
                                    ini</small>
                            </div>
                        </div>

                        {{-- Cabang Tujuan --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Cabang Tujuan</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_cabang_tujuan" id="single-select-mutasi-tujuan"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    @foreach($dataCabangTujuan as $cabang)
                                        <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                                <small class="modal-form-hint">Harus berbeda dengan cabang asal</small>
                            </div>
                        </div>

                        {{-- Jumlah Mutasi --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Jumlah Mutasi</label>
                            <div class="modal-form-control-wrapper">
                                <input type="number" name="jumlah_mutasi" placeholder="Jumlah unit yang dimutasi"
                                    class="form-control form-control-sm modal-form-control" min="1" required>
                                <small class="modal-form-hint">Maksimal: <span data-field="stokMasuk">-</span> unit</small>
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label">Keterangan</label>
                            <div class="modal-form-control-wrapper">
                                <textarea name="keterangan" placeholder="Alasan atau catatan mutasi (opsional)"
                                    class="form-control form-control-sm modal-form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="alert alert-info small mt-3">
                            <strong><i class="fas fa-lightbulb me-2"></i>Catatan FEFO:</strong> Jika batch tidak dipilih,
                            sistem akan otomatis memilih batch dengan tanggal kedaluwarsa paling dulu (First Expired, First
                            Out).
                        </div>

                        <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@elseif($type === 'detail')
    {{-- ! Modal Detail Mutasi Stok (Read-Only) --}}
    <div class="modal fade modal-dynamic" id="modalDetailMutasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detail Mutasi Stok</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-striped">
                        <tr>
                            <td><strong>Kode Batch</strong></td>
                            <td><span data-field="batch">-</span></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Obat</strong></td>
                            <td><span data-field="namaObat">-</span>| <span data-field="namaSatuan">-</span> | <span
                                    data-field="namaKategori">-</span></td>
                        </tr>
                        <tr>
                            <td><strong>Cabang Asal</strong></td>
                            <td><span data-field="cabangAsal">-</span></td>
                        </tr>
                        <tr>
                            <td><strong>Cabang Tujuan</strong></td>
                            <td><span data-field="cabangTujuan">-</span></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Mutasi</strong></td>
                            <td><span data-field="tanggal">-</span></td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah Mutasi</strong></td>
                            <td><span data-field="jumlah">-</span> box</td>
                        </tr>
                        <tr>
                            <td><strong>Keterangan</strong></td>
                            <td><span data-field="keterangan">-</span></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'edit')
    {{-- ! Modal Edit Mutasi Stok --}}
    <div class="modal fade modal-dynamic" id="modalEditMutasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>{{ $judul }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="formMutasiStokEdit" method="POST">
                        @csrf
                        @method('PUT')
                        {{-- Cabang Asal --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label">Cabang Asal</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_cabang_asal" id="single-select-mutasi-asal-edit"
                                    class="form-control form-control-sm modal-form-control modal-form-select"
                                    data-field="idCabangAsal" required>
                                    @foreach($dataCabangEdit as $cabang)
                                        <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Kode Batch --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Kode Batch</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_stok_batch" id="single-select-kode-batch-edit"
                                    class="form-control form-control-sm modal-form-control" data-field="idStokBatch"
                                    required>
                                    @foreach($dataStokBatch as $batch)
                                        <option value="{{ $batch->id_stok_batch }}" data-batch="{{ $batch->kode_batch }}"
                                            data-obat="{{ $batch->Obat->nama_obat ?? '-' }}"
                                            data-stok="{{ $batch->stok_sisa }}">
                                            {{ $batch->kode_batch }} | {{ $batch->Obat->nama_obat ?? '-' }} | Stok:
                                            {{ $batch->stok_sisa }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Tanggal Mutasi --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Tanggal Mutasi</label>
                            <div class="modal-form-control-wrapper">
                                <input type="date" name="tanggal_mutasi" data-field="tanggal"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        {{-- Cabang Tujuan --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Cabang Tujuan</label>
                            <div class="modal-form-control-wrapper">
                                <select name="id_cabang_tujuan" id="single-select-mutasi"
                                    class="form-control form-control-sm modal-form-control" data-field="idCabangTujuan"
                                    required>
                                    @foreach($dataCabangEdit as $cabang)
                                        <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Jumlah Mutasi --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label required">Jumlah Mutasi</label>
                            <div class="modal-form-control-wrapper">
                                <input type="number" name="jumlah_mutasi" data-field="jumlah"
                                    class="form-control form-control-sm modal-form-control" min="1" required>
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="modal-form-group">
                            <label class="modal-form-label">Keterangan</label>
                            <div class="modal-form-control-wrapper">
                                <textarea name="keterangan" data-field="keterangan"
                                    class="form-control form-control-sm modal-form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
