{{-- ! Modal Tambah --}}
<div class="modal fade modal-dynamic" id="tambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>{{ $judul }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-custom">
                <form action="{{ url('/data-kategori-tambah') }}" method="POST">
                    @csrf
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong class="required-field">Kategori Obat</strong></td>
                            <td>
                                <input type="text" name="nama_kategori" value=""
                                    placeholder="Masukan Kategori obat baru" class="form-control form-control-sm"
                                    required>
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
