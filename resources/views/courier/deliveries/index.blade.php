@extends('layouts.courier')

@section('title', 'Tugas Pengiriman')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('courier.dashboard') }}" class="text-decoration-none" style="color: var(--primary);">Dashboard</a></li>
        <li class="breadcrumb-item active">Tugas Pengiriman</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="font-weight: 700; color: var(--dark);">Tugas Pengiriman</h4>
        <p class="text-muted mb-0">Daftar pengiriman yang ditugaskan kepada Anda</p>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('courier.deliveries.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status Aktif</option>
                    <option value="{{ \App\Models\Order::STATUS_ASSIGNED }}" {{ request('status') == \App\Models\Order::STATUS_ASSIGNED ? 'selected' : '' }}>Menunggu Diambil</option>
                    <option value="{{ \App\Models\Order::STATUS_PICKED_UP }}" {{ request('status') == \App\Models\Order::STATUS_PICKED_UP ? 'selected' : '' }}>Sudah Diambil</option>
                    <option value="{{ \App\Models\Order::STATUS_ON_DELIVERY }}" {{ request('status') == \App\Models\Order::STATUS_ON_DELIVERY ? 'selected' : '' }}>Sedang Diantar</option>
                    <option value="{{ \App\Models\Order::STATUS_DELIVERED }}" {{ request('status') == \App\Models\Order::STATUS_DELIVERED ? 'selected' : '' }}>Sudah Sampai</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Tanggal Pengiriman</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('courier.deliveries.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Delivery List -->
<div class="card">
    <div class="card-body p-0">
        @forelse($deliveries as $delivery)
            <div class="delivery-card p-4 border-bottom">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                @switch($delivery->status)
                                    @case(\App\Models\Order::STATUS_ASSIGNED)
                                        <div class="icon-box" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">
                                            <i class="fas fa-clock fa-lg"></i>
                                        </div>
                                        @break
                                    @case(\App\Models\Order::STATUS_PICKED_UP)
                                        <div class="icon-box" style="background: rgba(107, 114, 128, 0.1); color: var(--gray);">
                                            <i class="fas fa-box fa-lg"></i>
                                        </div>
                                        @break
                                    @case(\App\Models\Order::STATUS_ON_DELIVERY)
                                        <div class="icon-box" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                            <i class="fas fa-truck fa-lg"></i>
                                        </div>
                                        @break
                                    @case(\App\Models\Order::STATUS_DELIVERED)
                                        <div class="icon-box" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                            <i class="fas fa-check fa-lg"></i>
                                        </div>
                                        @break
                                @endswitch
                            </div>
                            <div>
                                <h5 class="mb-1" style="font-weight: 600;">
                                    <a href="{{ route('courier.deliveries.show', $delivery) }}" class="text-decoration-none" style="color: var(--dark);">
                                        #{{ $delivery->order_number }}
                                    </a>
                                    @switch($delivery->status)
                                        @case(\App\Models\Order::STATUS_ASSIGNED)
                                            <span class="badge ms-2" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">Menunggu Diambil</span>
                                            @break
                                        @case(\App\Models\Order::STATUS_PICKED_UP)
                                            <span class="badge bg-secondary ms-2">Sudah Diambil</span>
                                            @break
                                        @case(\App\Models\Order::STATUS_ON_DELIVERY)
                                            <span class="badge ms-2" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Sedang Diantar</span>
                                            @break
                                        @case(\App\Models\Order::STATUS_DELIVERED)
                                            <span class="badge ms-2" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Sudah Sampai</span>
                                            @break
                                    @endswitch
                                </h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-user me-1"></i>{{ $delivery->user->name }}
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-phone me-1"></i>{{ $delivery->user->phone ?? '-' }}
                                </p>
                                <p class="mb-0 small">
                                    <i class="fas fa-map-marker-alt me-1" style="color: #ef4444;"></i>
                                    {{ $delivery->delivery_address }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 mt-3 mt-lg-0">
                        <div class="text-muted small mb-1">Jadwal Pengiriman</div>
                        <div style="font-weight: 600; color: var(--dark);">
                            <i class="fas fa-calendar me-1" style="color: var(--primary);"></i>
                            {{ $delivery->delivery_date->format('d M Y') }}
                        </div>
                        <div>
                            <i class="fas fa-clock me-1" style="color: var(--primary);"></i>
                            {{ $delivery->delivery_time }}
                        </div>
                        <div class="mt-2" style="font-weight: 700; color: var(--primary);">
                            Rp {{ number_format($delivery->total_amount, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-lg-3 mt-3 mt-lg-0 text-lg-end">
                        <a href="{{ route('courier.deliveries.show', $delivery) }}" class="btn btn-outline-primary mb-2 w-100">
                            <i class="fas fa-eye me-1"></i>Detail
                        </a>
                        @if($delivery->status === \App\Models\Order::STATUS_ASSIGNED)
                            <form action="{{ route('courier.deliveries.pickup', $delivery) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-hand-holding me-1"></i>Ambil Barang
                                </button>
                            </form>
                        @elseif($delivery->status === \App\Models\Order::STATUS_PICKED_UP)
                            <form action="{{ route('courier.deliveries.start', $delivery) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-play me-1"></i>Mulai Antar
                                </button>
                            </form>
                        @elseif($delivery->status === \App\Models\Order::STATUS_ON_DELIVERY)
                            <a href="{{ route('courier.deliveries.show', $delivery) }}" class="btn btn-primary w-100">
                                <i class="fas fa-check me-1"></i>Selesaikan
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5>Tidak ada pengiriman</h5>
                <p>Belum ada pengiriman yang ditugaskan kepada Anda.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
@if($deliveries->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $deliveries->withQueryString()->links() }}
    </div>
@endif

<style>
.icon-box {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray);
}
.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}
.empty-state h5 {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.5rem;
}
.empty-state p {
    margin-bottom: 0;
}
.delivery-card:hover {
    background: #f8fafc;
}
</style>
@endsection
