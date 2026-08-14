@extends('layouts.admin')

@section('title', 'Diskon Ongkir')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="font-weight: 700;">Manajemen Diskon Ongkir</h4>
        <p class="text-muted mb-0">Kelola diskon ongkos kirim untuk pelanggan</p>
    </div>
    <a href="{{ route('admin.shipping-discounts.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Diskon
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($discounts->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nama Diskon</th>
                        <th>Diskon</th>
                        <th>Maks Diskon</th>
                        <th>Min. Subtotal</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discounts as $discount)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $discount->name }}</div>
                            @if($discount->description)
                                <small class="text-muted">{{ Str::limit($discount->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-success fs-6">{{ $discount->formatted_discount }}</span>
                        </td>
                        <td>{{ $discount->formatted_max_discount ?? '-' }}</td>
                        <td>{{ $discount->formatted_min_subtotal ?? '-' }}</td>
                        <td>
                            @if($discount->start_date || $discount->end_date)
                                <small>
                                    {{ $discount->start_date?->format('d/m/Y') ?? '-' }} s/d {{ $discount->end_date?->format('d/m/Y') ?? 'Selamanya' }}
                                </small>
                            @else
                                <small class="text-muted">Selamanya</small>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.shipping-discounts.toggle', $discount) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $discount->is_active ? 'btn-success' : 'btn-secondary' }}">
                                    @if($discount->is_active)
                                        <i class="fas fa-check me-1"></i>Aktif
                                    @else
                                        <i class="fas fa-times me-1"></i>Nonaktif
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.shipping-discounts.edit', $discount) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.shipping-discounts.destroy', $discount) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus diskon ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{ $discounts->links('pagination.admin') }}
        @else
        <div class="text-center py-5">
            <i class="fas fa-percent fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Diskon Ongkir</h5>
            <p class="text-muted">Tambahkan diskon ongkir untuk menarik lebih banyak pembeli.</p>
            <a href="{{ route('admin.shipping-discounts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Tambah Diskon Pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
