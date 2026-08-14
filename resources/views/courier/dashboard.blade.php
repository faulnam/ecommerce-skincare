@extends('layouts.courier')

@section('title', 'Dashboard')

@section('breadcrumb')
@php
    $jsonPath = public_path('translation/courierdashboard.json');
    $cdTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp
    <a href="{{ route('courier.dashboard') }}" style="color: var(--primary); font-weight: 500;" class="text-decoration-none">
        {{ $cdTrans['bc_dashboard'][$lang] ?? 'Dashboard' }}
    </a> / {{ $cdTrans['bc_overview'][$lang] ?? 'Overview' }}
@endsection

@section('content')
@php
    // Re-check inside block configuration to make sure variables are universally bound
    $jsonPath = public_path('translation/courierdashboard.json');
    $cdTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h4 class="mb-1" style="font-weight: 700; color: var(--dark);">{{ $cdTrans['welcome_prefix'][$lang] ?? 'Selamat datang,' }} {{ auth()->user()->name }}</h4>
        <p class="text-muted mb-0" style="font-size: 13px;">{{ $cdTrans['welcome_subtitle'][$lang] ?? 'Berikut ringkasan pengiriman Anda hari ini' }}</p>
    </div>
    <div class="text-muted small bg-white px-3 py-2 border rounded-pill shadow-sm align-self-start align-self-sm-center">
        <i class="fas fa-calendar me-1.5 text-secondary"></i> {{ now()->isoFormat('dddd, D MMMM Y') }}
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-3">
            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-3" style="width: 48px; height: 48px;">
                <i class="fas fa-box text-info fs-5"></i>
            </div>
            <div class="stat-info">
                <h3 class="mb-0 fw-bold text-dark" style="font-size: 22px; line-height: 1;">{{ $stats['pending'] }}</h3>
                <p class="text-muted small mb-0 mt-1 fw-medium">{{ $cdTrans['stat_pending'][$lang] ?? 'Menunggu Diambil' }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-3">
            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-3" style="width: 48px; height: 48px;">
                <i class="fas fa-truck text-warning fs-5"></i>
            </div>
            <div class="stat-info">
                <h3 class="mb-0 fw-bold text-dark" style="font-size: 22px; line-height: 1;">{{ $stats['on_progress'] }}</h3>
                <p class="text-muted small mb-0 mt-1 fw-medium">{{ $cdTrans['stat_on_progress'][$lang] ?? 'Sedang Diantar' }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-3">
            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-3" style="width: 48px; height: 48px;">
                <i class="fas fa-calendar-check text-success fs-5"></i>
            </div>
            <div class="stat-info">
                <h3 class="mb-0 fw-bold text-dark" style="font-size: 22px; line-height: 1;">{{ $stats['delivered_today'] }}</h3>
                <p class="text-muted small mb-0 mt-1 fw-medium">{{ $cdTrans['stat_delivered_today'][$lang] ?? 'Selesai Hari Ini' }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-3">
            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-3" style="width: 48px; height: 48px; bg-color: rgba(0,60,82,0.1);">
                <i class="fas fa-star fs-5" style="color: #003C52;"></i>
            </div>
            <div class="stat-info">
                <h3 class="mb-0 fw-bold text-dark" style="font-size: 22px; line-height: 1;">{{ $stats['total_completed'] }}</h3>
                <p class="text-muted small mb-0 mt-1 fw-medium">{{ $cdTrans['stat_total_completed'][$lang] ?? 'Total Selesai' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark" style="font-size: 14px;"><i class="fas fa-route me-2 text-muted"></i>{{ $cdTrans['card_active_title'][$lang] ?? 'Pengiriman Aktif' }}</span>
                <a href="{{ route('courier.deliveries.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 999px; font-size: 11px; font-weight: 600;">
                    {{ $cdTrans['btn_view_all'][$lang] ?? 'Lihat Semua' }}
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($activeDeliveries as $delivery)
                    <div class="delivery-card p-3 border-bottom border-light transition hover-bg-light" style="last-child { border-bottom: none !important; }">
                        <div class="d-flex justify-content-between align-items-start mb-2.5">
                            <div>
                                <h6 class="mb-1" style="font-weight: 700; font-size: 14px;">
                                    <a href="{{ route('courier.deliveries.show', $delivery) }}" class="text-decoration-none" style="color: #003C52;">
                                        #{{ $delivery->order_number }}
                                    </a>
                                </h6>
                                <div class="text-muted small d-flex align-items-center gap-1">
                                    <i class="fas fa-user text-[11px]"></i><span>{{ $delivery->user->name }}</span>
                                </div>
                            </div>
                            <div>
                                @switch($delivery->status)
                                    @case(\App\Models\Order::STATUS_ASSIGNED)
                                        <span class="badge py-1.5 px-2.5 bg-info bg-opacity-10 text-info font-semibold border-0 rounded-pill" style="font-size: 11px;">
                                            {{ $cdTrans['status_assigned'][$lang] ?? 'Menunggu Diambil' }}
                                        </span>
                                        @break
                                    @case(\App\Models\Order::STATUS_PICKED_UP)
                                        <span class="badge py-1.5 px-2.5 bg-secondary bg-opacity-10 text-secondary font-semibold border-0 rounded-pill" style="font-size: 11px;">
                                            {{ $cdTrans['status_picked_up'][$lang] ?? 'Sudah Diambil' }}
                                        </span>
                                        @break
                                    @case(\App\Models\Order::STATUS_ON_DELIVERY)
                                        <span class="badge py-1.5 px-2.5 bg-warning bg-opacity-10 text-warning font-semibold border-0 rounded-pill" style="font-size: 11px;">
                                            {{ $cdTrans['status_on_delivery'][$lang] ?? 'Sedang Diantar' }}
                                        </span>
                                        @break
                                    @case(\App\Models\Order::STATUS_DELIVERED)
                                        <span class="badge py-1.5 px-2.5 bg-success bg-opacity-10 text-success font-semibold border-0 rounded-pill" style="font-size: 11px;">
                                            {{ $cdTrans['status_delivered'][$lang] ?? 'Sudah Sampai' }}
                                        </span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        
                        <div class="row g-2 text-muted small mb-3 border-top border-light pt-2" style="font-size: 12px;">
                            <div class="col-md-6 text-truncate">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                {{ Str::limit($delivery->delivery_address, 45) }}
                            </div>
                            <div class="col-6 col-md-3">
                                <i class="fas fa-calendar-alt me-1 text-zinc-400"></i>
                                {{ $delivery->delivery_date->format('d M Y') }}
                            </div>
                            <div class="col-6 col-md-3">
                                <i class="fas fa-clock me-1 text-zinc-400"></i>
                                {{ $delivery->delivery_time }}
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center bg-light p-2.5 rounded-3">
                            <span class="fw-bold text-dark" style="font-size: 13.5px;">
                                Rp {{ number_format($delivery->total_amount, 0, ',', '.') }}
                            </span>
                            <div>
                                @if($delivery->status === \App\Models\Order::STATUS_ASSIGNED)
                                    <form action="{{ route('courier.deliveries.pickup', $delivery) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #003C52; border-radius: 999px; font-size: 11px; font-weight: 600;">
                                            <i class="fas fa-hand-holding me-1.5"></i>{{ $cdTrans['action_pickup'][$lang] ?? 'Ambil Barang' }}
                                        </button>
                                    </form>
                                @elseif($delivery->status === \App\Models\Order::STATUS_PICKED_UP)
                                    <form action="{{ route('courier.deliveries.start', $delivery) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #003C52; border-radius: 999px; font-size: 11px; font-weight: 600;">
                                            <i class="fas fa-play me-1.5"></i>{{ $cdTrans['action_start'][$lang] ?? 'Mulai Antar' }}
                                        </button>
                                    </form>
                                @elseif($delivery->status === \App\Models\Order::STATUS_ON_DELIVERY)
                                    <a href="{{ route('courier.deliveries.show', $delivery) }}" class="btn btn-sm btn-success text-white px-3" style="border-radius: 999px; font-size: 11px; font-weight: 600;">
                                        <i class="fas fa-check me-1.5"></i>{{ $cdTrans['action_complete'][$lang] ?? 'Selesaikan' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 border-0 bg-white">
                        <div class="d-flex h-12 w-12 mx-auto items-center justify-center bg-light rounded-circle text-muted shadow-inner mb-2.5" style="width: 48px; height: 48px;">
                            <i class="fas fa-inbox fs-5"></i>
                        </div>
                        <h6 class="text-dark fw-bold mb-1">{{ $cdTrans['card_active_title'][$lang] ?? 'Tidak ada pengiriman' }}</h6>
                        <p class="text-muted small mb-0">{{ $cdTrans['empty_active'][$lang] ?? 'Tidak ada pengiriman aktif saat ini' }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom border-light fw-bold text-dark" style="font-size: 14px;">
                <i class="fas fa-check-double me-2 text-muted"></i>{{ $cdTrans['card_recent_title'][$lang] ?? 'Baru Selesai' }}
            </div>
            <div class="card-body p-0">
                @forelse($recentCompleted as $completed)
                    <div class="p-3 border-bottom border-light transition hover-bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="min-w-0">
                                <h6 class="mb-1 text-dark text-truncate" style="font-weight: 700; font-size: 13.5px;">#{{ $completed->order_number }}</h6>
                                <small class="text-muted d-block text-truncate" style="font-size: 12px;">{{ $completed->user->name }}</small>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge py-1 px-2 bg-success bg-opacity-10 text-success rounded" style="font-size: 10px; font-weight: 600;">{{ $cdTrans['status_delivered'][$lang] ?? 'Selesai' }}</span>
                                <div class="text-muted mt-1 font-medium" style="font-size: 10.5px;">
                                    {{ $completed->delivered_at ? $completed->delivered_at->diffForHumans() : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="d-flex h-12 w-12 mx-auto items-center justify-center bg-light rounded-circle text-muted shadow-inner mb-2.5" style="width: 48px; height: 48px;">
                            <i class="fas fa-box-open fs-5"></i>
                        </div>
                        <p class="mb-0 small text-muted font-medium">{{ $cdTrans['empty_recent'][$lang] ?? 'Belum ada pengiriman selesai' }}</p>
                    </div>
                @endforelse
            </div>
            
            @if($recentCompleted->count() > 0)
                <div class="card-footer bg-white border-top border-light text-center py-2.5">
                    <a href="{{ route('courier.deliveries.history') }}" class="text-decoration-none small font-semibold" style="color: #003C52; font-size: 12.5px;">
                        {{ $cdTrans['btn_view_history'][$lang] ?? 'Lihat Semua Riwayat' }} <i class="fas fa-arrow-right ms-1 text-[11px]"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection