@extends('admin.layouts.master')
@section('title', 'Detail Pesanan')

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
                    <h3>Detail Pesanan</h3>
                    <p class="text-subtitle text-muted">Informasi detail pesanan</p>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4>Kode Pesanan: {{ $order->order_code }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>Dibuat Pada: {{ $order->created_at->format('d M Y H:i') }}</p>
                            <p>Nama Pelanggan: {{ $order->user->fullname }}</p>
                            <p>Status: <span
                                    class="badge {{ $order->status == 'settlement' ? 'bg-success' : ($order->status == 'pending' ? 'bg-warning' : ($order->status == 'cooked' ? 'bg-primary' : 'bg-danger')) }}">{{ $order->status }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>No. Meja: {{ $order->table_number }}</p>
                            <p>Metode Pembayaran: {{ $order->payment_method }}</p>
                            <p>Catatan: {{ $order->note ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4>Daftar Menu yang Dipesan</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Menu</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderItems as $menu)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($menu->item)
                                            <img src="{{ asset('img_item_upload/' . $menu->item->img) }}" width="60"
                                                class="img-fluid rounded" alt="{{ $menu->item->name }}"
                                                onerror="this.onerror=null; this.src='{{ $menu->item->img }}';">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $menu->item->name ?? 'Item tidak ditemukan' }}</td>
                                    <td>{{ $menu->quantity }}</td>
                                    <td>{{ 'Rp' . number_format($menu->price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="4" class="text-end">Subtotal:</th>
                                <th>{{ 'Rp' . number_format($order->subtotal, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Tax:</th>
                                <th>{{ 'Rp' . number_format($order->tax, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th>{{ 'Rp' . number_format($order->grand_total, 0, ',', '.') }}</th>
                            </tr>
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
