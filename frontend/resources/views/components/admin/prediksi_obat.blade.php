<x-layout>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Prediksi Obat</h4>
                        </div>


                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="basic-form custom_file_input w-50">
                                    <form action="#">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-primary">Upload</span>
                                            <div class="form-file">
                                                <input type="file" class="form-file-input form-control">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <table id="example3" class="display">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Obat</th>
                                            <th style="text-align: center;">Stok Awal</th>
                                            <th style="text-align: center;">Stok Terjual</th>
                                            <th style="text-align: center;">Stok Prediksi</th>
                                            <th>Status</th>
                                            <th style="text-align: center;">Opsi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($data as $d)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $d['nama_obat'] }}</td>
                                                <td style="text-align: center;">{{ $d['stok_awal'] }}</td>
                                                <td style="text-align: center;">{{ $d['stok_terjual'] }}</td>
                                                <td style="text-align: center;">{{ $d['stok_prediksi'] }}</td>
                                                <td>
                                                    @if ($d['status'] == 'menipis')
                                                        <span class="badge badge-sm badge-warning">Menipis</span>

                                                    @elseif ($d['status'] == 'aman')
                                                        <span class="badge badge-sm badge-success">Aman</span>

                                                    @elseif ($d['status'] == 'habis')
                                                        <span class="badge badge-sm badge-danger">Habis</span>

                                                    @else
                                                        <span class="badge badge-sm badge-secondary">Error</span>
                                                    @endif
                                                </td>

                                                <td class="d-flex justify-content-center align-items-center gap-0">
                                                    <a href="{{-- url('permintaan_obat_hapus/' . $d->id_permintaan_obat) --}}"
                                                        onclick="return confirm('Anda yakin Hapus data permintaan obat {{-- $d->nama_obat --}} ?')"
                                                        class="btn btn-danger shadow btn-xs sharp me-1">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>

                                                    <button data-bs-toggle="modal"
                                                        data-bs-target="#detail{{-- $d->id_permintaan_obat --}}"
                                                        class="btn btn-primary shadow btn-xs sharp me-1" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    <button data-bs-toggle="modal"
                                                        data-bs-target="#konfirmasi{{-- $d->id_permintaan_obat --}}"
                                                        class="btn btn-info shadow btn-xs sharp me-1" title="Konfirmasi">
                                                        <i class="fas fa-location-arrow"></i>
                                                    </button>

                                                    {{---- @include('permintaan_obat_modal') ----}}

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
