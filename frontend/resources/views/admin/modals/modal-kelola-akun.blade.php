@if ($type === 'tambah')
    {{-- ! Modal Tambah --}}
    <div class="modal fade modal-dynamic" id="tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">Tambah Akun Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.kelola-akun.store') }}" method="POST">
                    @csrf
                    <div class="modal-body modal-body-custom">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="contoh@apotek.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Role</label>
                            <select name="role" id="single-select-{{ $id }}" class="form-control form-control-sm modal-form-control" required>
                                <option value="" disabled selected>Pilih Role</option>
                                <option value="apoteker">Apoteker</option>
                                <option value="admin">Admin</option>
                                <option value="manajer">Manajer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Status Akun</label>
                            <div class="btn-group w-100" role="group" aria-label="Status Akun">
                                <input type="radio" class="btn-check" name="status" id="statusAktif" value="Aktif" autocomplete="off" required>
                                <label class="btn btn-outline-primary btn-sm" for="statusAktif">Aktif</label>

                                <input type="radio" class="btn-check" name="status" id="statusNonaktif" value="Nonaktif" autocomplete="off" required>
                                <label class="btn btn-outline-danger btn-sm" for="statusNonaktif">Nonaktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@elseif ($type === 'edit')
    {{-- ! Modal Edit --}}
    <div class="modal fade modal-dynamic" id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">Edit Akun ({{ $d->username }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.kelola-akun.update', $d->id_user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body modal-body-custom">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ $d->username }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $d->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Role</label>
                            <select name="role" id="single-select-kelola-obat" class="form-control form-control-sm modal-form-control" required>
                                <option value="" disabled selected></option>
                                <option value="admin" {{ $d->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="apoteker" {{ $d->role == 'apoteker' ? 'selected' : '' }}>Apoteker</option>
                                <option value="manajer" {{ $d->role == 'manajer' ? 'selected' : '' }}>Manajer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Status Akun</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="status" id="statusAktif{{ $d->id_user }}" value="Aktif" {{ $d->status == 'Aktif' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-primary btn-sm" for="statusAktif{{ $d->id_user }}">Aktif</label>

                                <input type="radio" class="btn-check" name="status" id="statusNonaktif{{ $d->id_user }}" value="Nonaktif" {{ $d->status == 'Nonaktif' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-danger btn-sm" for="statusNonaktif{{ $d->id_user }}">Nonaktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@elseif ($type === 'reset')
    {{-- ! Modal Reset Password --}}
    <div class="modal fade modal-dynamic" id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title">Reset Password ({{ $d->username }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.kelola-akun.reset-password', $d->id_user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
