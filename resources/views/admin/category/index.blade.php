@extends('admin.layouts.master')
@section('title', 'Kategori')

@section('css')
    {{-- Datatable --}}
    <link rel="stylesheet" href="{{ asset('assets/admin/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/compiled/css/table-datatable.css') }}">
@endsection

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Manajemen Kategori</h3>
                    <p class="text-subtitle text-muted">Informasi Kategori yang Terdaftar</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <a href="{{ route('categories.create') }}" class="btn btn-primary float-start float-lg-end"><i
                            class="bi bi-plus"></i>Tambah Kategori</a>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <p><i class="bi bi-check-circle"></i> {{ session('success') }}</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->cat_name }}</td>
                                    <td>{{ Str::limit($category->description, 30) }}</td>
                                    <td>
                                        <a href="{{ route('categories.edit', $category->id) }}"
                                            class="btn btn-warning btn-sm"><i class="bi bi-pencil me-2"></i>Ubah</a>
                                        <form action="{{ route('categories.destroy', $category->id) }}"
                                            onclick="return confirm('Yakin ingin menghapus kategori ini?')" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                    class="bi bi-trash me-2"></i>Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </div>
@endsection

@section('script')
    {{-- Datatable --}}
    <script src="{{ asset('assets/admin/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/admin/static/js/pages/simple-datatables.js') }}"></script>
@endsection
