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
                    <form action="{{ url('/data-obat-tambah') }}" method="POST" id="formTambahObat"
                        class="modal-form-container">
                        @csrf

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Nama Obat</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" name="nama_obat" value="" placeholder="Masukan nama obat"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Safety Stock</label>
                            <div class="modal-form-control-wrapper">
                                <input type="number" name="safety_stock" value="" placeholder="Masukan safety stock minimal"
                                    class="form-control form-control-sm modal-form-control" min="1" required>
                                <small class="modal-form-hint">Stok minimal untuk reorder otomatis</small>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Supplier Obat</label>
                            <div class="modal-form-control-wrapper">
                                <select id="single-select-supplier-tambah" name="id_supplier"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option selected disabled>Pilih Supplier</option>
                                    @foreach($supplier as $s)
                                        <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Satuan Obat</label>
                            <div class="modal-form-control-wrapper">
                                <select id="single-select-satuan-tambah" name="id_satuan"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option selected disabled>Pilih Satuan</option>
                                    @foreach($satuan as $s)
                                        <option value="{{ $s->id_satuan }}">{{ $s->nama_satuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Kategori</label>
                            <div class="modal-form-control-wrapper">
                                <select id="single-select-kategori-tambah" name="id_kategori"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option selected disabled>Pilih Kategori</option>
                                    @foreach($kategori as $k)
                                        <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formTambahObat" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

@elseif(($type === 'edit') && isset($d))
    {{-- ! Modal Edit / Detail --}}
    <div class="modal fade modal-dynamic" id="modalEditObat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>{{ $judul }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="formEditObat" method="POST" class="modal-form-container">
                        @csrf
                        @method('PUT')

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Nama Obat</label>
                            <div class="modal-form-control-wrapper">
                                <input type="text" data-field="namaObat" name="nama_obat"
                                    class="form-control form-control-sm modal-form-control" required>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Safety Stock</label>
                            <div class="modal-form-control-wrapper">
                                <input type="number" data-field="safetyStock" name="safety_stock"
                                    class="form-control form-control-sm modal-form-control" min="1" required>
                                <small class="modal-form-hint">Stok minimal untuk reorder otomatis</small>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Supplier Obat</label>
                            <div class="modal-form-control-wrapper">
                                <select id="single-select-supplier-edit" data-field="supplier" name="id_supplier"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option selected disabled>Pilih Supplier</option>
                                    @foreach($supplier as $s)
                                        <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Satuan Obat</label>
                            <div class="modal-form-control-wrapper">
                                <select id="single-select-satuan-edit" data-field="satuan" name="id_satuan"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option selected disabled>Pilih Satuan</option>
                                    @foreach($satuan as $s)
                                        <option value="{{ $s->id_satuan }}">{{ $s->nama_satuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-form-group">
                            <label class="modal-form-label required">Kategori</label>
                            <div class="modal-form-control-wrapper">
                                <select id="single-select-kategori-edit" data-field="kategori" name="id_kategori"
                                    class="form-control form-control-sm modal-form-control modal-form-select" required>
                                    <option selected disabled>Pilih Kategori</option>
                                    @foreach($kategori as $k)
                                        <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="formEditObat" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
