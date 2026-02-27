{{-- ! Modal Tambah --}}
<div class="modal fade" id="tambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h5 class="modal-title">Tambah Data Obat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-custom">
                <form action="{{ url('/data-kategori-tambah') }}" method="POST">
                    @csrf
                    <table class="table">
                        <tr>
                            <td>Kategori Obat</td>
                            <td>
                                <input type="text" name="nama_kategori" value="" placeholder="Masukan Kategori obat baru" class="form-control" required>
                            </td>
                        </tr>
                    </table>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
