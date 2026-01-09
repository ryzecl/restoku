@extends('admin.layouts.master')
@section('title', 'Tambah Menu')

@section('content')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Ubah Menu</h3>
                <p class="text-subtitle text-muted">Silahkan isi data menu yang ingin diubah</p>
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
            <form class="form" action="{{ route('items.update', $item->id) }}" enctype="multipart/form-data"
                method="POST">
                @csrf
                @method('PUT')
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Nama Menu</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Masukkan Nama Menu" value="{{ $item->name }}" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" placeholder="Masukkan Deskripsi" required>{{ $item->description }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="price">Harga</label>
                                <input type="number" class="form-control" id="price" name="price"
                                    placeholder="Masukkan Harga" value="{{ $item->price }}" required>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Kategori</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="" disabled>Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @if ($category->id == $item->category_id) selected @endif>{{ $category->cat_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="img">Gambar</label>
                                @if ($item->img)
                                    <img src="{{ asset('img_item_upload/' . $item->img) }}" width="60"
                                        class="img-thumbnail mb-2" style="width: 100px; height: 100px;"
                                        alt="{{ $item->name }}"
                                        onerror="this.onerror=null; this.src='{{ $item->img }}';">
                                @endif
                                <input type="file" class="form-control" id="img" name="img">
                            </div>

                            <div class="form-group">
                                <label for="basicInput">Status</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" class="form-check-input" name="is_active"
                                        id="flexSwitchCheckChecked" value="1"
                                        @if ($item->is_active == 1) checked @endif>
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
