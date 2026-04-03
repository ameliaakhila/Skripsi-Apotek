{{-- ! Modal Tambah --}}
<div class="modal fade modal-dynamic" id="tambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>{{ $judul }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-custom">
                <form id="formTambahKategori" action="{{ url('/data-kategori-tambah') }}" method="POST"
                    class="modal-form-container">
                    @csrf

                    <div class="modal-form-group">
                        <label class="modal-form-label required">Kategori</label>
                        <div class="modal-form-control-wrapper">
                            <input type="text" name="nama_kategori" value="" placeholder="Masukan kategori baru"
                                class="form-control form-control-sm modal-form-control" required>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer modal-footer-custom">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="submit" form="formTambahKategori" class="btn btn-sm btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>