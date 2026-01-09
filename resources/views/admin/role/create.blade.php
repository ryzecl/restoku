@extends('admin.layouts.master')
@section('title', 'Tambah Role')

@section('content')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Role</h3>
                <p class="text-subtitle text-muted">Silahkan isi data role yang ingin ditambahkan</p>
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
            <form class="form" action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role_name">Nama Role</label>
                                <input type="text" class="form-control" id="role_name" name="role_name"
                                    placeholder="Masukkan Nama Role" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" placeholder="Masukkan Deskripsi" required></textarea>
                            </div>

                            <div class="form-group d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-warning">Kembali</a>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
