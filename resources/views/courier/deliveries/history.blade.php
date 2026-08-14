@extends('layouts.courier')

@section('title', 'Riwayat Pengiriman')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('courier.dashboard') }}" class="text-decoration-none" style="color: var(--primary);">Dashboard</a></li>
        <li class="breadcrumb-item active">Riwayat Pengiriman</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="font-weight: 700; color: var(--dark);">Riwayat Pengiriman</h4>
        <p class="text-muted mb-0">Daftar pengiriman yang sudah Anda selesaikan</p>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-check-double fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0" style="font-weight: 700; color: var(--dark);">{{ $totalDelivered }}</h3>
                    <small class="text-muted">Total Pengiriman Selesai</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">
                    <i class="fas fa-wallet fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0" style="font-weight: 700; color: var(--dark);">Rp {{ number_format($totalEarnings ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-muted">Total Nilai Ongkir</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('courier.deliveries.history') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted">Dari Tanggal</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Sampai Tanggal</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Cari
                </button>
                <a href="{{ route('courier.deliveries.history') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- History List -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="font-weight: 600; color: var(--gray);">No. Pesanan</th>
                        <th style="font-weight: 600; color: var(--gray);">Customer</th>
                        <th style="font-weight: 600; color: var(--gray);">Alamat</th>
                        <th style="font-weight: 600; color: var(--gray);">Tanggal Antar</th>
                        <th style="font-weight: 600; color: var(--gray);">Foto</th>
                        <th style="font-weight: 600; color: var(--gray);">Total</th>
                        <th style="font-weight: 600; color: var(--gray);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                        <tr>
                            <td>
                                <span style="font-weight: 600; color: var(--dark);">#{{ $delivery->order_number }}</span>
                                @if($delivery->status === \App\Models\Order::STATUS_COMPLETED)
                                    <br><span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Selesai</span>
                                @else
                                    <br><span class="badge" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">Sudah Diantar</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($delivery->user->name) }}&background=2563eb&color=fff&size=32" 
                                         class="rounded-circle me-2" alt="">
                                    {{ $delivery->user->name }}
                                </div>
                            </td>
                            <td>
                                <span title="{{ $delivery->delivery_address }}">
                                    {{ Str::limit($delivery->delivery_address, 30) }}
                                </span>
                            </td>
                            <td>
                                @if($delivery->delivered_at)
                                    {{ $delivery->delivered_at->format('d M Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($delivery->pickup_photo)
                                        <a href="{{ config('filesystems.disks.r2.url').'/' . $delivery->pickup_photo }}" target="_blank" title="Foto Pengambilan">
                                            <img src="{{ config('filesystems.disks.r2.url').'/' . $delivery->pickup_photo }}" class="rounded" style="width: 35px; height: 35px; object-fit: cover;">
                                        </a>
                                    @endif
                                    @if($delivery->delivery_photo)
                                        <a href="{{ config('filesystems.disks.r2.url').'/' . $delivery->delivery_photo }}" target="_blank" title="Foto Pengiriman">
                                            <img src="{{ config('filesystems.disks.r2.url').'/' . $delivery->delivery_photo }}" class="rounded" style="width: 35px; height: 35px; object-fit: cover;">
                                        </a>
                                    @endif
                                    @if(!$delivery->pickup_photo && !$delivery->delivery_photo)
                                        <span class="text-muted small">-</span>
                                    @endif
                                </div>
                            </td>
                            <td style="font-weight: 600; color: var(--primary);">Rp {{ number_format($delivery->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('courier.deliveries.show', $delivery) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-history fa-3x mb-3" style="color: var(--gray); opacity: 0.5;"></i>
                                <p class="text-muted mb-0">Belum ada riwayat pengiriman</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<!-- Pagination -->
@if($deliveries->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $deliveries->withQueryString()->links() }}
    </div>
@endif
@endsection
