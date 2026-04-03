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
                    <form id="formEditStokBatch" method="POST">
                        @csrf
                        @method('PUT')

                        <table class="table table-sm table-borderless">
                            {{-- Kode Batch --}}
                            <tr>
                                <td><strong>Kode Batch</strong></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" data-field="kodeBatch" readonly>
                                    <small class="form-text text-muted d-block mt-1">Kode batch tidak dapat diubah</small>
                                </td>
                            </tr>

                            {{-- Nama Obat --}}
                            <tr>
                                <td><strong>Nama Obat</strong></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" data-field="namaObat" readonly>
                                </td>
                            </tr>

                            {{-- Cabang --}}
                            <tr>
                                <td><strong>Cabang</strong></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" data-field="namaCabang"
                                        readonly>
                                </td>
                            </tr>

                            {{-- Stok Masuk --}}
                            <tr>
                                <td><strong>Stok Masuk</strong></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" data-field="stokMasuk" readonly>
                                </td>
                            </tr>
                            {{-- Stok Keluar --}}
                            <tr>
                                <td><strong>Stok Keluar</strong></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" data-field="stokKeluar"
                                        required>
                                </td>
                            </tr>

                            {{-- Stok Sisa --}}
                            <tr>
                                <td><strong class="required-field">Stok Sisa</strong></td>
                                <td>
                                    <input type="number" name="stok_sisa" class="form-control form-control-sm"
                                        data-field="stokSisa" min="0" required>
                                    <small class="form-text text-muted d-block mt-1">
                                        Maksimal: <span data-field="stokMasuk"></span> unit
                                    </small>
                                </td>
                            </tr>

                            {{-- Tanggal Kedaluwarsa --}}
                            <tr>
                                <td><strong class="required-field">Tanggal Kedaluwarsa</strong></td>
                                <td>
                                    <input type="date" name="tanggal_kedaluwarsa" class="form-control form-control-sm"
                                        data-field="tanggalKedaluwarsa" required>
                                </td>
                            </tr>

                            {{-- Tujuan Penyesuaian --}}
                            <tr>
                                <td><strong class="required-field">Tujuan Penyesuaian</strong></td>
                                <td>
                                    <select name="tujuan_penyesuaian" class="form-control form-control-sm" required>
                                        <option value="">-- Pilih Tujuan --</option>
                                        <option value="Opname">Stok Opname</option>
                                        <option value="Loss">Rusak</option>
                                        <option value="Transfer">Transfer Cabang</option>
                                        <option value="Return">Retur ke Supplier</option>
                                    </select>
                                </td>
                            </tr>

                            {{-- Catatan --}}
                            <tr>
                                <td><strong>Catatan</strong></td>
                                <td>
                                    <textarea name="catatan" class="form-control form-control-sm" rows="3"
                                        placeholder="Masukan catatan penyesuaian stok (opsional)"></textarea>
                                </td>
                            </tr>

                            {{-- Status Stok --}}
                            <tr>
                                <td><strong>Estimasi Status</strong></td>
                                <td>
                                    <span class="badge badge-secondary" id="estimasiStatus">Belum dihitung</span>
                                    <small class="form-text text-muted d-block mt-1">
                                        Status akan otomatis dihitung berdasarkan stok sisa dan safety stock
                                    </small>
                                </td>
                            </tr>
                        </table>

                        <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Penyesuaian
                            </button>
                        </div>
                    </form>
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
                <div class="modal-body">
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
                            <td>
                                <strong>Status Stok</strong>
                            </td>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
