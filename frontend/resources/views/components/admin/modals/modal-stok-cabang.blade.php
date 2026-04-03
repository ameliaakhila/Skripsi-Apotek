@if($type === 'edit')
    {{-- ! Modal Edit Stok Batch (Inventory Adjustment) --}}
    <div class="modal fade modal-dynamic" id="modalEditStokBatch" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Penyesuaian Stok Batch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="formEditStokBatch" method="POST" class="modal-form-container">
                        @csrf
                        @method('PUT')

                        <div class="modal-form-group">
                            <label class="modal-form-label">Kode Batch</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" class="form-control form-control-sm modal-form-control"
                                    data-field="kodeBatch" readonly>
                                <small class="modal-form-hint">Kode batch tidak dapat diubah</small>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Nama Obat</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" class="form-control form-control-sm modal-form-control"
                                    data-field="namaObat" readonly>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Cabang</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" class="form-control form-control-sm modal-form-control"
                                    data-field="namaCabang" readonly>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Stok Masuk</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" class="form-control form-control-sm modal-form-control"
                                    data-field="stokMasuk" readonly>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Stok Keluar</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" class="form-control form-control-sm modal-form-control"
                                    data-field="stokKeluar" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Stok Sisa</label>
                            <div class="modal-form-control-wrapper">
                                <input type="number" name="stok_sisa"
                                    class="form-control form-control-sm modal-form-control" data-field="stokSisa" min="0"
                                    required>
                                <small class="modal-form-hint">
                                    Maksimal: <span data-field="stokMasuk"></span> unit
                                </small>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Tanggal Kedaluwarsa</label>
                            <div class="modal-form-control-wrapper">
                                <input type="date" name="tanggal_kedaluwarsa"
                                    class="form-control form-control-sm modal-form-control" data-field="tanggalKedaluwarsa"
                                    required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Tujuan Penyesuaian</label>
                            <div class="modal-form-control-wrapper">
                                <select name="tujuan_penyesuaian"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option value="">-- Pilih Tujuan --</option>
                                    <option value="Opname">Stok Opname</option>
                                    <option value="Loss">Rusak</option>
                                    <option value="Transfer">Transfer Cabang</option>
                                    <option value="Return">Retur ke Supplier</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Catatan</label>
                            <div class="modal-form-control-wrapper">
                                <textarea name="catatan" class="form-control form-control-sm modal-form-control" rows="3"
                                    placeholder="Masukan catatan penyesuaian stok (opsional)"></textarea>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label">Estimasi Status</label>
                            <div class="modal-form-control-wrapper">
                                <span class="badge badge-secondary" id="estimasiStatus">Belum dihitung</span>
                                <small class="modal-form-hint">
                                    Status akan otomatis dihitung berdasarkan stok sisa dan safety stock
                                </small>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formEditStokBatch" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Penyesuaian
                    </button>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'detail')
    {{-- ! Modal Detail Batch (Readonly) --}}
    <div class="modal fade modal-dynamic" id="modalDetailStokBatch" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Stok Batch</h5>
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
                            <td>
                                <span data-field="nama_obat"></span><br>
                                <small class="text-muted">
                                    Satuan: <span data-field="nama_satuan"></span> |
                                    Kategori: <span data-field="nama_kategori"></span>
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Cabang</strong></td>
                            <td><span data-field="nama_cabang"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Stok Masuk</strong></td>
                            <td><span data-field="stok_masuk"></span> unit</td>
                        </tr>
                        <tr>
                            <td><strong>Stok Keluar</strong></td>
                            <td><span data-field="stok_keluar"></span> unit</td>
                        </tr>
                        <tr>
                            <td><strong>Stok Sisa</strong></td>
                            <td><span data-field="stok_sisa"></span> unit</td>
                        </tr>
                        <tr>
                            <td><strong>Safety Stock</strong></td>
                            <td><span data-field="safety_stock"></span> unit</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Kedaluwarsa</strong></td>
                            <td><span data-field="tanggal_kedaluwarsa"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Status Stok</strong></td>
                            <td id="detail-status">
                                <span data-field="status_stok"></span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Catatan</strong></td>
                            <td><span data-field="catatan">-</span></td>
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
