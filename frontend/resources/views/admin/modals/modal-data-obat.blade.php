@if($type === 'tambah')
    {{-- ! Modal Tambah --}}
    <div class="modal fade modal-dynamic" id="tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>{{ $judul }} aw</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form action="{{ url('/data-obat-tambah') }}" method="POST">
                        @csrf
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong class="required-field">Nama Obat</strong></td>
                                <td>
                                    <input type="text" name="nama_obat" value="" placeholder="Masukan nama obat"
                                        class="form-control form-control-sm" required>
                                </td>
                            </tr>
                            {{-- Safety Stock --}}
                            <tr>
                                <td><strong class="required-field">Safety Stock</strong></td>
                                <td>
                                    <input type="number" name="safety_stock" value=""
                                        placeholder="Masukan safety stock minimal" class="form-control form-control-sm"
                                        min="1" required>
                                    <small class="text-muted">Stok minimal untuk reorder</small>
                                </td>
                            </tr>
                            {{-- Supplier Obat --}}
                            <tr>
                                <td><strong class="required-field">Supplier Obat</strong></td>
                                <td>
                                    <select id="single-select-supplier-tambah" name="id_supplier" class="form-control form-control-sm" required>
                                        <option selected disabled>Pilih Supplier</option>
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
                                <td><strong class="required-field">Satuan Obat</strong></td>
                                <td>
                                    <select id="single-select-satuan" name="id_satuan" class="form-control form-control-sm" required>
                                        <option selected disabled>Pilih Satuan</option>
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
                                <td><strong class="required-field">Kategori</strong></td>
                                <td>
                                    <select id="single-select-kategori" name="id_kategori" class="form-control form-control-sm" required>
                                        <option selected disabled>Pilih Kategori</option>
                                        @foreach($kategori as $k)
                                            <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                        @endforeach
                                    </select>
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

@elseif(($type === 'edit') && isset($d))
    {{-- ! Modal Edit / Detail --}}
    <div class="modal fade modal-dynamic" id="modalEditObat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>{{ $judul }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? Body --}}
                <div class="modal-body modal-body-custom">
                    <form id="formEditObat" method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong class="required-field">Nama Obat</strong></td>
                                <td>
                                    <input type="text" data-field="namaObat" name="nama_obat"
                                        class="form-control form-control-sm" required>
                                </td>
                            </tr>
                            {{-- Safety Stock --}}
                            <tr>
                                <td><strong class="required-field">Safety Stock</strong></td>
                                <td>
                                    <input type="number" data-field="safetyStock" name="safety_stock"
                                        class="form-control form-control-sm" min="1" required>
                                    <small class="text-muted">Stok minimal untuk reorder</small>
                                </td>
                            </tr>
                            {{-- Supplier Obat --}}
                            <tr>
                                <td><strong class="required-field">Supplier Obat</strong></td>
                                <td>
                                    <select id="single-select-supplier" data-field="supplier" name="id_supplier" class="form-control form-control-sm"
                                        required>
                                        <option selected disabled>Pilih Supplier</option>
                                        @foreach($supplier as $s)
                                            <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Satuan Obat --}}
                            <tr>
                                <td><strong class="required-field">Satuan Obat</strong></td>
                                <td>
                                    <select id="single-select-satuan" data-field="satuan" name="id_satuan" class="form-control form-control-sm"
                                        required>
                                        <option selected disabled>Pilih Satuan</option>
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
                                <td><strong class="required-field">Kategori</strong></td>
                                <td>
                                    <select data-field="kategori" name="id_kategori" class="form-control form-control-sm"
                                        required>
                                        <option selected disabled>Pilih Kategori</option>
                                        @foreach($kategori as $k)
                                            <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                        @endforeach
                                    </select>
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
