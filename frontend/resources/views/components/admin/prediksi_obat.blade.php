<x-layout title="Prediksi Obat">
    <div class="content-body">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Prediksi Obat</h4>
                        </div>

                        <div class="mx-4 d-flex justify-content-between">
                            <form action="{{-- route('prediksi.stok') --}}" method="GET">
                                <div class="basic-dropdown">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            <i class="bi bi-gear-fill fs-5"></i>
                                            Prediksi Berdasarkan
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item active" type="submit" name="periode"
                                                value="bulan">
                                                Januari 2026
                                            </button>
                                            <button class="dropdown-item" type="submit" name="periode" value="tahun">
                                                Desember 2025
                                            </button>
                                            <button class="dropdown-item" type="submit" name="periode" value="tahun">
                                                November 2025
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <button class="btn btn-success"><i class="fas fa-file-excel fs-6"></i> Cetak
                                Laporan</button>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example3" class="display">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Obat</th>
                                            <th style="text-align: center;">Stok Awal</th>
                                            <th style="text-align: center;">Stok Terjual</th>
                                            <th style="text-align: center;">Stok Sisa</th>
                                            <th>Status</th>
                                            <th style="text-align: center;">Stok Prediksi</th>
                                            {{-- <th style="text-align: center;">Rekomendasi Sistem</th> --}}
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
                                                <td style="text-align: center;">{{ $d['stok_sisa'] }}</td>
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

                                                <td style="text-align: center;">{{ $d['stok_prediksi'] }}</td>

                                                <td class="d-flex justify-content-center align-items-center gap-0">

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
