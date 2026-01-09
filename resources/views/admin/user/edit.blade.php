@extends('admin.layouts.master')
@section('title', 'Ubah Karyawan')

@section('content')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Ubah Karyawan</h3>
                <p class="text-subtitle text-muted">Silahkan isi data karyawan yang ingin diubah</p>
            </div>
        </div>
    </div>
    <div class="card">

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5>Update Gagal!</h5>
                    @foreach ($errors->all() as $error)
                        <li></i> {{ $error }}</li>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form class="form" action="{{ route('users.update', $user->id) }}" method="POST">
                @method('PUT')
                @csrf
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="fullname">Nama Karyawan</label>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    placeholder="Masukkan Nama Karyawan" value="{{ $user->fullname }}" required>
                            </div>

                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Masukkan Username" value="{{ $user->username }}" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">No. Telp</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="Masukkan No. Telp" value="{{ $user->phone }}" required>
                            </div>

                            <div class="form-group">
                                <label for="role_id">Role</label>
                                <select class="form-control" id="role_id" name="role_id" required>
                                    <option value="">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ $role->id == $user->role_id ? 'selected' : '' }}>{{ $role->role_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Masukkan Email" value="{{ $user->email }}" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Masukkan Password">
                                <small><a href="" class="toggle-password" data-target="password">Lihat
                                        Password</a></small>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Masukkan Konfirmasi Password">
                                <small><a href="" class="toggle-password" data-target="password_confirmation">Lihat
                                        Password</a></small>
                            </div>

                            <div class="form-group d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <a href="{{ route('users.index') }}" class="btn btn-warning">Kembali</a>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.querySelectorAll('.toggle-password').forEach((toggle) => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                let input = document.getElementById(this.dataset.target);
                let isHidden = input.getAttribute('type') === 'password';
                input.type = isHidden ? 'text' : 'password';
                document.querySelector(`a[data-target="${this.dataset.target}"]`).textContent = isHidden ?
                    'Sembunyikan Password' : 'Lihat Password';
            });
        });
    </script>
@endsection
