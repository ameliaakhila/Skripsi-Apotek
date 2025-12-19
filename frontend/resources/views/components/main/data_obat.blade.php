<x-layout title="Data Obat | Apotek Sena Farma">
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Obat</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <button data-bs-toggle="modal" data-bs-target="#tambahObat"
                                    class="btn btn-primary btn-xs mb-2">
                                    <i class="fas fa-plus me-2"></i>
                                    Tambah</button>
                                <table id="example3" class="table table-borderd">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama obat</th>
                                            <th>Pabrik Obat</th>
                                            <th>Kategori obat</th>
                                            <th>Satuan Obat</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}
                                                </td>
                                                <td> {{ $d->nama_obat }} </td>
                                                <td> {{ $d->pabrik_obat }} </td>
                                                <td> {{ $d->KategoriObat->nama_kategori_obat ?? '-' }} </td>
                                                <td> {{ $d->satuan_obat }} </td>
                                                <td class="d-flex justify-content-center align-items-center gap-0">
                                                    <a href="data_obat_hapus.php?id_obat= {{-- <?= $d['id_obat']; ?> --}}"
                                                        onclick="return confirm('Anda yakin Hapus data jenis obat {{-- <?php    echo $d['nama_obat']; ?> --}} ?')"
                                                        class="btn btn-danger shadow btn-xs sharp me-1">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                    <button data-bs-toggle="modal"
                                                        data-bs-target="#edit {{-- <?= $d['id_obat']; ?> --}}"
                                                        id=".$d['id_obat']."
                                                        class="btn btn-primary shadow btn-xs sharp me-1">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                    {{--
                                                    <?php    include('data_obat_modal.php') ?> --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout>
