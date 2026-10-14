<x-app title="Kelola Akun - Admin Apotek">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-primary">Daftar Akun Pengguna</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                        {{-- Alert Success --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                                <strong>Sukses!</strong> {{ session('success') }}
                            </div>
                        @endif

                        {{-- Alert Error --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                                <strong>Error!</strong> Silakan periksa kembali inputan Anda.
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <button data-bs-toggle="modal" data-bs-target="#tambah"
                            class="btn btn-primary btn-sm mb-3">
                            <i class="fas fa-plus me-2"></i> Tambah Akun Baru
                        </button>
                        <x-admin.modals.modal-kelola-akun :type="'tambah'" :judul="'Tambah Akun Baru'" />
                        <table id="example3" class="table table-borderd">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Opsi</th>
                                    </tr>
                                    </thead>
                                <tbody>
                                    @foreach ($data as $d)
                                        <tr>
                                            <td>{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                            <td class="font-weight-bold">{{ $d->username }}</td>
                                            <td>{{ $d->email }}</td>
                                            <td>
                                                @if($d->role == 'admin')
                                                    <span class="badge badge-primary">Admin</span>
                                                @elseif($d->role == 'apoteker')
                                                    <span class="badge badge-info">Apoteker</span>
                                                @else
                                                    <span class="badge badge-warning">Manajer</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($d->status == 'Aktif')
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <button data-bs-toggle="modal" data-bs-target="#edit{{ $d->id_user }}"
                                                        class="btn btn-primary shadow btn-xs sharp me-1" title="Edit Akun">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    <button data-bs-toggle="modal" data-bs-target="#reset{{ $d->id_user }}"
                                                        class="btn btn-info shadow btn-xs sharp me-1" title="Reset Password">
                                                        <i class="fas fa-key"></i>
                                                    </button>

                                                    <form action="{{ route('admin.kelola-akun.destroy', $d->id_user) }}" method="POST"
                                                        onsubmit="return confirm('Anda yakin hapus user {{ $d->username }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger shadow btn-xs sharp">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
{{-- ! Modal Edit --}}
@foreach ($data as $d)
    <x-admin.modals.modal-kelola-akun :type="'edit'" :judul="'Edit Akun ' . $d->username" :id="'edit' . $d->id_user"
        :d="$d" />
    <x-admin.modals.modal-kelola-akun :type="'reset'" :judul="'Reset Password ' . $d->username" :id="'reset' . $d->id_user"
        :d="$d" />
@endforeach
<div class="mt-3">
    {{ $data->links() }}
            </div>
            </div>
            </div>
            </div>
        </div>
    </div>
</x-app>
