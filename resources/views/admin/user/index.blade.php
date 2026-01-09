@extends('admin.layouts.master')
@section('title', 'Karyawan')

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
                    <h3>Manajemen Karyawan</h3>
                    <p class="text-subtitle text-muted">Informasi Karyawan yang Terdaftar</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <a href="{{ route('users.create') }}" class="btn btn-primary float-start float-lg-end"><i
                            class="bi bi-plus"></i>Tambah Karyawan</a>
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
                                <th>Username</th>
                                <th>Nama lengkap</th>
                                <th>Email</th>
                                <th>No. Telp</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ Str::limit($user->fullname, 20) }}</td>
                                    <td>{{ Str::limit($user->email, 20) }}</td>
                                    <td>{{ Str::limit($user->phone, 20) }}</td>
                                    <td>{{ Str::limit($user->role->role_name, 20) }}</td>
                                    <td>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm"><i
                                                class="bi bi-pencil me-2"></i>Ubah</a>
                                        <form action="{{ route('users.destroy', $user->id) }}"
                                            onclick="return confirm('Yakin ingin menghapus user ini?')" method="POST"
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
