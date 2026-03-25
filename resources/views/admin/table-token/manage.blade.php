@extends('admin.layouts.master')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Kelola Akses Meja (QR Token)</h3>
                <p class="text-subtitle text-muted">Generate token akses menu untuk tiap meja agar terhindar dari spam order.</p>
            </div>
        </div>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <section class="section">
        <div class="row">
            <!-- Generate Token Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Buat Token Baru</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('table-tokens.generate') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="table_number">Nomor Meja</label>
                                <input type="number" name="table_number" id="table_number" class="form-control" required min="1" placeholder="Contoh: 1">
                                <small class="text-muted">Token lama untuk meja ini akan otomatis hangus.</small>
                            </div>
                            <div class="form-group mb-4">
                                <label for="duration">Masa Aktif (Jam)</label>
                                <select name="duration" id="duration" class="form-select" required>
                                    <option value="2">2 Jam (Makan normal)</option>
                                    <option value="4">4 Jam (Nongkrong)</option>
                                    <option value="8">8 Jam (1 Shift)</option>
                                    <option value="24">24 Jam (Seharian)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-qr-code me-1"></i> Generate QR Token</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Active Tokens Data -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Daftar Akses Meja</h4>
                        <form action="{{ route('table-tokens.cleanup') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua token yang sudah hangus?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Bersihkan Token Hangus</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Meja</th>
                                        <th>Status</th>
                                        <th>Link Menu (Token)</th>
                                        <th>Berakhir Pada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tokens as $t)
                                    <tr>
                                        <td class="text-center fw-bold">Meja {{ $t->table_number }}</td>
                                        <td>
                                            @if($t->isValid())
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($t->isValid())
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="text" class="form-control form-control-sm" readonly value="{{ url('/scan/'.$t->table_number) }}">
                                                {{-- Note: Idealnya ada tombol copy/print QR disini --}}
                                                <button class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ url('/scan/'.$t->table_number) }}'); alert('Link disalin!')" title="Salin Link"><i class="bi bi-clipboard"></i></button>
                                            </div>
                                            @else
                                                <span class="text-muted fst-italic">Hangus/Expired</span>
                                            @endif
                                        </td>
                                        <td>{{ $t->expires_at->format('d M Y, H:i') }}</td>
                                        <td>
                                            @if($t->isValid())
                                            <form action="{{ route('table-tokens.revoke', $t->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan akses meja ini? Customer tidak akan bisa order lagi dari link tersebut.')">
                                                @csrf
                                                <button class="btn btn-sm btn-warning" title="Nonaktifkan (Tutup Meja)"><i class="bi bi-x-circle"></i> Tutup Meja</button>
                                            </form>
                                            @else
                                            <form action="{{ route('table-tokens.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus token ini permanen dari database?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Hapus Permanen"><i class="bi bi-trash"></i> Hapus</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada token yang dibuat.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
