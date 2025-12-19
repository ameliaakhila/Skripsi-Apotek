<x-layout>
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Laporan Obat</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                    <a href="cetak-obat-masuk/laporan_exel_full.php" class="btn btn-success btn-sm"><i data-feather="plus"></i> Laporan Exel</a>
                                    <a href="cetak-obat-masuk/laporan_exel_full.php" class="btn btn-danger btn-sm"><i data-feather="plus"></i> Laporan PDF</a>
                                {{-- <tr>
                                    <form action="cetak-obat-masuk/laporan_exel_bulanan.php" method="post">
                                    <td>
                                        Bulan
                                        <select name="bulan" id="">
                                            <option value="01">Januari</option>
                                            <option value="02">Februari</option>
                                            <option value="03">Maret</option>
                                            <option value="04">April</option>
                                            <option value="05">Mei</option>
                                            <option value="06">Juni</option>
                                            <option value="07">Juli</option>
                                            <option value="08">Agustus</option>
                                            <option value="09">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                        <input type="submit" class="btn btn-primary btn-sm" value="Download" >
                                        </form><br>
                                    </td>
                                </tr>
                                <tr>
                                    <form action="cetak-obat-masuk/laporan_exel_tahunan.php" method="post">
                                    <td>
                                        Tahun
                                        <select name="tahun" id="">
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                            <option value="2027">2027</option>
                                            <option value="2028">2028</option>
                                            <option value="2029">2029</option>
                                            <option value="2030">2030</option>
                                        </select>
                                        <input type="submit" class="btn btn-primary btn-sm" value="Download" >
                                        </form><br>
                                    </td>
                                </tr> --}}
                                <table id="example" class="display" >
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Transaksi</th>
                                            <th>Tanggal</th>
                                            <th>Nama Obat</th>
                                            <th>Jumlah Obat</th>
                                            <th>Tanggal Kadaluarsa</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($data as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d['kode_transaksi'] }}</td>
                                            <td>{{ date('d M Y', strtotime($d['tanggal'])); }}</td>
                                            <td>{{ $d['nama_obat'] }}</td>
                                            <td align="center">{{ $d['jumlah_obat'] }}</td>
                                            <td align="center">{{ date('d M Y', strtotime($d['tanggal_kadaluarsa'])) }}</td>
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
