<x-app>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Laporan Manajemen Stok</h4>
                </div>
                <div class="card-body">
                    <style>
                        .custom-tab-pane { display: none; }
                        .custom-tab-pane.active { display: block; }
                    </style>

                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('laporan.export-full') }}" class="btn btn-primary">
                            <i class="fas fa-file-excel"></i> Export Laporan Full
                        </a>
                    </div>

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#obat" role="tab"><span>Data Obat</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#obat-masuk" role="tab"><span>Obat Masuk</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#obat-keluar" role="tab"><span>Obat Keluar</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#mutasi-stok" role="tab"><span>Mutasi Stok</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#stok-opname" role="tab"><span>Stok Opname</span></a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">
                        <!-- TAB 0: DATA OBAT -->
                        <div class="custom-tab-pane active" id="obat">
                            <div class="d-flex justify-content-end gap-2 py-3">
                                <a href="{{ route('laporan.export-obat') }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                            </div>
                            <table id="example0" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Obat</th>
                                        <th>Kategori</th>
                                        <th>Supplier</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($obat as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama_obat }}</td>
                                            <td>{{ $item->DataKategori->nama_kategori ?? '-' }}</td>
                                            <td>{{ $item->DataSupplier->nama_supplier ?? '-' }}</td>
                                            <td>{{ $item->DataSatuan->nama_satuan ?? '-' }}</td>
                                        </tr>
                                    @empty <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr> @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- TAB 1: OBAT MASUK -->
                        <div class="custom-tab-pane" id="obat-masuk">
                            <div class="d-flex justify-content-end gap-2 py-3">
                                <a href="{{ route('laporan.export-obat-masuk') }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                            </div>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Batch</th>
                                        <th>Nama Obat</th>
                                        <th>Stok Masuk</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Tanggal Kedaluwarsa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($obatMasuk as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->kode_batch ?? '-' }}</td>
                                            <td>{{ $item->Obat->nama_obat ?? '-' }}</td>
                                            <td align="center">{{ $item->stok_masuk ?? 0 }}</td>
                                            <td>{{ $item->tanggal_masuk_formatted ?? '-' }}</td>
                                            <td align="center">{{ $item->tanggal_kedaluwarsa_formatted ?? '-' }}</td>
                                        </tr>
                                    @empty <tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr> @endforelse
                                </tbody>
                            </table>
                            {{ $obatMasuk->links() }}
                        </div>

                        <!-- TAB 2: OBAT KELUAR -->
                        <div class="custom-tab-pane" id="obat-keluar">
                            <div class="d-flex justify-content-end gap-2 py-3">
                                <a href="{{ route('laporan.export-obat-keluar') }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                            </div>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Batch</th>
                                        <th>Nama Obat</th>
                                        <th>Tanggal Keluar</th>
                                        <th>Stok Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($obatKeluar as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @php
                                                    $batch = $item->StokBatch;
                                                    $kodeBatch = is_array($batch) ? ($batch['kode_batch'] ?? '-') : ($batch->kode_batch ?? '-');
                                                @endphp
                                                {{ $kodeBatch }}
                                            </td>
                                            <td>{{ $item->ObatMasuk->Obat->nama_obat ?? '-' }}</td>
                                            <td>{{ $item->tanggal_keluar_formatted }}</td>
                                            <td>{{ $item->stok_keluar }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $obatKeluar->links() }}
                        </div>

                        <!-- TAB 3: MUTASI STOK -->
                        <div class="custom-tab-pane" id="mutasi-stok">
                            <div class="d-flex justify-content-end gap-2 py-3">
                                <a href="{{ route('laporan.export-mutasi-stok') }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                            </div>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Mutasi</th>
                                        <th>Nama Obat</th>
                                        <th>Cabang Asal</th>
                                        <th>Cabang Tujuan</th>
                                        <th>Jumlah Mutasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mutasiStok as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ date('d M Y', strtotime($item->tanggal_mutasi)) }}</td>
                                            <td>{{ $item->StokBatch->Obat->nama_obat }}</td>
                                            <td>{{ $item->CabangAsal->nama_cabang }}</td>
                                            <td>{{ $item->CabangTujuan->nama_cabang }}</td>
                                            <td align="center">{{ $item->jumlah_mutasi }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $mutasiStok->links() }}
                        </div>

                        <!-- TAB 4: STOK OPNAME -->
                        <div class="custom-tab-pane" id="stok-opname">
                            <div class="d-flex justify-content-end gap-2 py-3">
                                <a href="{{ route('laporan.export-stok-opname') }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                            </div>
                            <table id="example4" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Opname</th>
                                        <th>Tanggal Opname</th>
                                        <th>Nama Obat</th>
                                        <th>Stok Fisik</th>
                                        <th>Stok Sistem</th>
                                        <th>Selisih</th>
                                        <th>Cabang</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stokOpname as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->kode_opname }}</td>
                                            <td>{{ date('d M Y', strtotime($item->tanggal_opname)) }}</td>
                                            <td>{{ $item->Obat->nama_obat }}</td>
                                            <td align="center">{{ $item->stok_fisik }}</td>
                                            <td align="center">{{ $item->stok_sistem }}</td>
                                            <td align="center">
                                                <span class="badge badge-{{ $item->selisih >= 0 ? 'success' : 'danger' }}">
                                                    {{ $item->selisih >= 0 ? '+' : '' }}{{ $item->selisih }}
                                                </span>
                                            </td>
                                            <td>{{ $item->DataCabang->nama_cabang }}</td>
                                            <td>
                                                <span class="badge badge-{{ $item->keterangan == 'Sesuai' ? 'success' : ($item->keterangan == 'Lebih' ? 'warning' : 'danger') }}">
                                                    {{ $item->keterangan }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted">Tidak ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $stokOpname->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <script>
        $(document).ready(function () {
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                $('.custom-tab-pane').removeClass('active');

                let target = $(this).attr('href');
                $(target).addClass('active');

                let table = $(target).find('table');
                if (table.length > 0) {
                    initStandardDataTable('#' + table.attr('id'));
                }
            });

            initStandardDataTable('#example0');
        });
    </script>
@endpush
</x-app>
