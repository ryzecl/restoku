@extends('admin.layouts.master')
@section('title', 'Tambah Menu')

@section('content')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Menu</h3>
                <p class="text-subtitle text-muted">Silahkan isi data menu yang ingin ditambahkan</p>
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
            <form class="form" action="{{ route('items.store') }}" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Nama Menu</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Masukkan Nama Menu" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" placeholder="Masukkan Deskripsi" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="price">Harga</label>
                                <input type="number" class="form-control" id="price" name="price"
                                    placeholder="Masukkan Harga" required>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Kategori</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="" disabled>Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->cat_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="img">Gambar</label>
                                <input type="file" class="form-control" id="img" name="img" required>
                            </div>

                            <div class="form-group">
                                <label for="basicInput">Status</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" class="form-check-input" name="is_active"
                                        id="flexSwitchCheckChecked" value="1" checked>
                                    <label class="form-check-label" for="flexSwitchCheckChecked">Aktif/Tidak Aktif</label>
                                </div>
                            </div>

                            <div class="form-group d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <a href="{{ route('items.index') }}" class="btn btn-warning">Kembali</a>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
