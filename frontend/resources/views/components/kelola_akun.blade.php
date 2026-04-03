<x-app>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">

                                {{-- Alert --}}
                                {{-- @include('alert') --}}

                                <button data-bs-toggle="modal" data-bs-target="#tambah"
                                    class="btn btn-primary btn-xs mb-2">
                                    Tambah Akun
                                </button>

                                {{-- Modal Tambah --}}
                                {{-- @include('kelola_akun.modal_tambah') --}}

                                <table id="example3" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($data as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d['username'] }}</td>
                                                <td>
                                                    @if ($d['status'] == 'Aktif')
                                                        <span class="badge badge-success">Aktif</span>
                                                    @else
                                                        <span class="badge badge-danger">Nonaktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ url('kelola-akun/hapus/' . $d['id_user']) }}"
                                                        onclick="return confirm('Anda yakin hapus user {{ $d['username'] }}?')"
                                                        class="btn btn-danger shadow btn-xs sharp me-1">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>

                                                    <button data-bs-toggle="modal"
                                                        data-bs-target="#reset{{ $d['id_user'] }}"
                                                        class="btn btn-info shadow btn-xs sharp me-1">
                                                        <i class="fas fa-key"></i>
                                                    </button>

                                                    <button data-bs-toggle="modal" data-bs-target="#edit{{ $d['id_user'] }}"
                                                        class="btn btn-primary shadow btn-xs sharp me-1">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    {{-- Modal Edit & Reset --}}
                                                    {{-- @include('kelola_akun.modal', ['d' => $d]) --}}
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
</x-app>
