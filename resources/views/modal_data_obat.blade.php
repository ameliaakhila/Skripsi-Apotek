@props([
    'type' => 'default',
    'd' => null,
    'obat',
    'obatMasukList',
    'satuanList',
    'kategori'
])

@if($type === 'tambah')
    {{-- ! Modal Tambah --}}
    <div class="modal fade" id="tambahObat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">Tambah Obat Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form action="{{ url('/obat-masuk-tambah') }}" method="POST">
                        @csrf
                        <table class="table">
                            {{-- Kode Obat --}}
                            <tr>
                                <td>Kode Obat</td>
                                <td><input type="text" name="kode_obat" value="{{ $kodeObat }}" class="form-control" readonly></td>
                            </tr>
                            {{-- Nama Obat --}}
                            <tr>
                                <td>Nama Obat</td>
                                <td>
                                    <select name="id_obat" class="default-select wide form-control" required>
                                        <option value="">Pilih Obat</option>
                                        @foreach($obat as $o)
                                            <option value="{{ $o->id_obat }}">{{ $o->nama_obat }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Satuan --}}
                            <tr>
                                <td>Satuan</td>
                                <td>
                                    <select name="satuan_obat" class="default-select wide form-control" required>
                                        <option value="Strip">Pilih</option>
                                        @foreach($satuanList as $o)
                                            <option value="{{ $o->satuan_obat }}">{{ $o->satuan_obat }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Kategori --}}
                            <tr>
                                <td>Kategori</td>
                                <td>
                                    <select name="id_kategori_obat" class="default-select wide form-control" required>
                                        <option value="1">Pilih</option>
                                        @foreach($kategori as $o)
                                            <option value="{{ $o->id_kategori_obat }}">{{ $o->nama_kategori_obat }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            {{-- Tanggal --}}
                            <tr>
                                <td>Tanggal Masuk</td>
                                <td><input type="text" name="tanggal_masuk" value="2025-12-12" placeholder="Masukan tanggal masuk obat" class="form-control datepicker-default" required></td>
                            </tr>
                            <tr>
                                <td>Tanggal Kadaluarsa</td>
                                <td><input type="text" name="tanggal_kadaluarsa" value="2028-02-24" placeholder="Masukan tanggal kadaluarsa obat" class="form-control datepicker-default" required></td>
                            </tr>
                            <tr>
                                <td>Tanggal Keluar</td>
                                <td><input type="text" name="tanggal_keluar" value="2026-01-29" placeholder="Masukan tanggal keluar obat" class="form-control datepicker-default" required></td>
                            </tr>
                            {{-- Stok dan Harga --}}
                            <tr>
                                <td>Stok Masuk</td>
                                <td><input type="number" name="stok_masuk" value="120" placeholder="Masukan stok masuk obat" class="form-control" required></td>
                            </tr>
                            <tr>
                                <td>Harga Obat</td>
                                <td><input type="number" name="harga_obat" value="10000" placeholder="Masukan harga obat" class="form-control" required></td>
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

@elseif(($type === 'edit' || $type === 'detail') && isset($d))
    {{-- ! Modal Edit / Detail --}}
    <div class="modal fade" id="{{ $type }}{{ $d->id_obat_masuk }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                {{-- ? HEADER --}}
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">
                        @if($type === 'edit')
                            Edit Obat Masuk
                        @elseif($type === 'detail')
                            Detail Obat Masuk
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                {{-- ? Body --}}
                <div class="{{ $type === 'edit' ? 'modal-body modal-body-custom' : 'modal-body' }}">
                    @if($type === 'edit')
                        <form action="{{ url('/obat-masuk-edit/' . $d->id_obat_masuk) }}" method="POST">
                            @csrf
                            @method('PUT')
                    @endif

                    <table class="{{ $type === 'edit' ? 'table' : 'table table-striped' }}">
                        <tr>
                            <td>Nama Obat</td>
                            <td>
                                <input type="hidden" name="kode_obat" value="{{ $d->kode_obat}}">
                                @if($type === 'edit')
                                    <select name="id_obat" class="form-control" required>
                                        <option value="{{ $d->id_obat }}">{{ $d->Obat->nama_obat }} | {{ $d->kode_obat }}</option>
                                        @foreach($obatMasukList as $o)
                                        @if($o->id_obat != $d->id_obat)
                                                <option value="{{ $o->id_obat }}">{{ $o->Obat->nama_obat }} | {{ $d->kode_obat }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                @else
                                <span class="fw-light">{{  $d->Obat->nama_obat ?? '-'  }}</span>
                                | <span class="fw-light">{{ $d->kode_obat }}</span>
                                | <span class="fw-light">{{ $d->satuan_obat }}</span>
                                | <span class="fw-light">{{ $d->KategoriObat->nama_kategori_obat }}</span>
                                @endif
                            </td>
                        </tr>

                        @if($type === 'edit')
                            <tr>
                                <td>Satuan Obat</td>
                                <td>
                                    <select name="satuan_obat" class="form-control" required>
                                        <option value="{{ $d->satuan_obat }}">{{ $d->satuan_obat }}</option>
                                        @foreach($satuanList as $o)
                                            @if($o->satuan_obat != $d->satuan_obat)
                                                <option value="{{ $o->satuan_obat }}">{{ $o->satuan_obat }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Kategori Obat</td>
                                <td>
                                    @if($type === 'edit')
                                        <select name="id_kategori_obat" class="form-control" required>
                                            <option value="{{ $d->id_kategori_obat }}">{{ $d->KategoriObat->nama_kategori_obat }}</option>
                                            @foreach($kategori as $o)
                                                @if($o->id_kategori_obat != $d->id_kategori_obat)
                                                    <option value="{{ $o->id_kategori_obat }}">{{ $o->nama_kategori_obat }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @else
                                        {{ $d->KategoriObat->nama_kategori_obat }}
                                    @endif
                                </td>
                            </tr>
                        @else
                        @endif

                        <tr>
                            <td>Tanggal Masuk</td>
                            <td>
                                @if($type === 'edit')
                                <input type="text" name="tanggal_masuk" value="{{ $d->tanggal_masuk}}"
                                class="datepicker-default form-control" required>
                                @else
                                    {{ $d->tanggal_masuk_formatted }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Tanggal Kadaluarsa</td>
                            @if ($type === 'edit')
                            <td>
                                <input type="text" name="tanggal_kadaluarsa" value="{{ $d->tanggal_kadaluarsa}}"
                                class="datepicker-default form-control" required>
                            </td>
                                @else
                                    <td class="d-flex justify-content-between">
                                        {{ $d->tanggal_kadaluarsa_formatted }}
                                        <span class="badge bg-{{ $d->status_kadaluarsa_badge }}">{{ $d->status_kadaluarsa }}</span>
                                    </class=>
                                @endif
                        </tr>
                        <tr>
                            <td>Tanggal Keluar</td>
                            <td>
                                @if ($type === 'edit')
                                <input type="text" name="tanggal_keluar" value="{{ $d->tanggal_keluar}}"
                                class="datepicker-default form-control" required>
                                @else
                                {{ $d->tanggal_keluar_formatted }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Stok Masuk</td>
                            <td>
                                @if ($type === 'edit')
                                    <input type="number" id="stok_masuk_{{ $d->id_obat_masuk }}" name="stok_masuk" value="{{  $d->stok_masuk }}" class=" stok-masuk form-control" required>
                                @else
                                {{ $d->stok_masuk }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Stok Keluar</td>
                            @if ($type === 'edit')
                                <td>
                                    <input type="number" id="stok_keluar_{{ $d->id_obat_masuk }}" name="stok_keluar" value="{{  $d->stok_keluar }}" class="stok-keluar form-control" required>
                                </td>
                            @else
                                <td class="d-flex justify-content-between">
                                        {{ $d->stok_keluar }}
                                        <span class="badge bg-{{ $d->status_stok['warna'] }}">{{ $d->presentase_stok }}%</span>
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td>Stok Sisa</td>
                            @if ($type === 'edit')
                                <td>
                                    <input type="number" id="stok_sisa_{{ $d->id_obat_masuk }}" name="stok_sisa" class="stok-sisa form-control" readonly>
                                </td>
                            @else
                                <td class="d-flex justify-content-between">
                                    {{ $d->stok_sisa }}
                                    <span class="badge bg-{{ $d->status_stok['warna'] }}">{{ $d->status_stok['status']}}</span>
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td>Harga</td>
                            <td>
                                @if ($type === 'edit')
                                <input type="number" name="harga_obat"
                                placeholder="{{ $d->harga_obat_formatted }}"
                                value="{{ $d->harga_obat}}"
                                class="form-control" required>
                                @else
                                {{ $d->harga_obat_formatted }}
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if($type === 'edit')
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                        </form>
                    @else
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endif
