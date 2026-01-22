<x-layout :title="'Obat Kadaluarsa'">
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Obat Kadaluarsa</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" class="display">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Obat</th>
                                            <th style="text-align:center;">Jumlah</th>
                                            <th style="text-align:center;">Kadaluarsa</th>
                                            <th style="text-align:center;">Status</th>
                                            <th style="text-align:ce.nter;">Opsi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($data as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d['nama_obat'] }}</td>
                                                <td style="text-align:center;">{{ $d['jumlah_obat'] }}</td>
                                                <td style="text-align:center;">{{ $d['tanggal_kadaluarsa'] }}</td>
                                                <td style="text-align:center;">
                                                    @if(isset($d['status_terjual']))
                                                        @if($d['status_terjual'] == "Belum Terjual")
                                                            <div class="bootstrap-badge">
                                                                <span class="badge badge-sm badge-primary">Belum Terjual</span>
                                                            </div>
                                                        @elseif($d['status_terjual'] == "Terjual")
                                                            <div class="bootstrap-badge">
                                                                <span class="badge badge-sm badge-success">Terjual</span>
                                                            </div>
                                                        @else
                                                            <div class="bootstrap-badge">
                                                                <span class="badge badge-sm badge-primary">Belum Terjual</span>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="bootstrap-badge">
                                                            <span class="badge badge-sm badge-primary">Belum Terjual</span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($d['status_terjual']))
                                                        @if ($d['status_terjual'] == "Belum Terjual")
                                                            <button class="bootstrap-badge">
                                                                <span class="badge badge-sm badge-danger">Musnahkan!</span>
                                                            </button>
                                                        @elseif($d['status_terjual'] == "Terjual")
                                                            <div class="bootstrap-badge">
                                                                <span class="badge badge-sm badge-success">Aman</span>
                                                            </div>
                                                        @else
                                                        @endif
                                                    @endif
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