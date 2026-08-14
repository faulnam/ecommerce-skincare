@extends('layouts.app')

@section('title', 'Pesanan Saya - Hijab')

@section('content')
<div class="container py-4 py-lg-5">
    <h3 class="mb-4 order-title">
        <i class="fas fa-shopping-bag me-2 text-success"></i>Pesanan Saya
    </h3>
    
    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body py-2 py-lg-3">
            <form action="{{ route('customer.orders.index') }}" method="GET" class="row g-2 g-lg-3">
                <div class="col-8 col-md-10">
                    <select class="form-select form-select-sm form-select-lg-md" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-4 col-md-2">
                    <button type="submit" class="btn btn-success btn-sm btn-lg-md w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Orders List -->
    @forelse($orders as $order)
        <div class="card mb-3 order-card">
            <div class="card-header bg-white">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-1">
                    <div class="order-header-info">
                        <strong class="order-number">{{ $order->order_number }}</strong>
                        <span class="text-muted order-date d-block d-sm-inline ms-sm-2">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <span class="badge bg-{{ $order->status_color }} order-status-badge">{{ $order->status_label }}</span>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="row">
                    <div class="col-12 col-md-8 mb-3 mb-md-0">
                        @foreach($order->items->take(2) as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="order-item-name">
                                    <strong>{{ $item->product_name }}</strong> <span class="text-muted">x{{ $item->quantity }}</span>
                                </div>
                                <span class="text-muted order-item-price">{{ $item->formatted_subtotal }}</span>
                            </div>
                        @endforeach
                        @if($order->items->count() > 2)
                            <small class="text-muted">+{{ $order->items->count() - 2 }} item lainnya</small>
                        @endif
                    </div>
                    <div class="col-12 col-md-4 text-start text-md-end">
                        <div class="d-flex flex-row flex-md-column justify-content-between align-items-center align-items-md-end">
                            <div>
                                <p class="mb-0 mb-md-1 text-muted small">Total Pembayaran</p>
                                <h5 class="text-success mb-0 order-total">{{ $order->formatted_total }}</h5>
                            </div>
                            <span class="badge bg-{{ $order->payment_status_color }} mt-md-2">{{ $order->payment_status_label }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <div class="order-info-mobile">
                        @if($order->courier)
                            <small class="text-success d-block d-sm-inline me-sm-3">
                                <i class="fas fa-motorcycle me-1"></i>Kurir: {{ $order->courier->name }}
                            </small>
                        @endif
                        @if($order->delivery_date)
                            <small class="text-warning d-block d-sm-inline">
                                <i class="fas fa-calendar-alt me-1"></i><span class="d-none d-sm-inline">Pengiriman:</span> {{ $order->delivery_date->format('d M Y') }}
                            </small>
                        @elseif($order->tracking_number)
                            <small class="text-muted d-block d-sm-inline">
                                <i class="fas fa-truck me-1"></i>Resi: {{ $order->tracking_number }}
                            </small>
                        @endif
                        @if($order->delivery_photo && in_array($order->status, [\App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_COMPLETED]))
                            <small class="text-info d-block d-sm-inline ms-sm-2">
                                <i class="fas fa-camera me-1"></i>Foto tersedia
                            </small>
                        @endif
                    </div>
                    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-success btn-sm w-100 w-sm-auto">
                        <i class="fas fa-eye me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-3x fa-lg-4x text-muted mb-3 mb-lg-4"></i>
            <h4 class="text-muted empty-title">Belum Ada Pesanan</h4>
            <p class="text-muted mb-4 small">Ayo mulai berbelanja perlengkapan Hijab!</p>
            <a href="{{ route('customer.products.index') }}" class="btn btn-success">
                <i class="fas fa-shopping-cart me-2"></i>Mulai Belanja
            </a>
        </div>
    @endforelse
    
    {{ $orders->links() }}
</div>
@endsection

@push('styles')
<style>
    /* Mobile Responsive */
    @media (max-width: 767.98px) {
        .order-title {
            font-size: 1.3rem;
        }
        .order-number {
            font-size: 0.85rem;
        }
        .order-date {
            font-size: 0.75rem;
        }
        .order-status-badge {
            font-size: 0.65rem;
        }
        .order-item-name {
            font-size: 0.85rem;
        }
        .order-item-price {
            font-size: 0.8rem;
        }
        .order-total {
            font-size: 1rem;
        }
        .order-info-mobile small {
            font-size: 0.7rem;
        }
        .order-card .card-header,
        .order-card .card-footer {
            padding: 0.5rem 0.75rem;
        }
        .empty-title {
            font-size: 1.1rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .order-number {
            font-size: 0.8rem;
        }
        .order-total {
            font-size: 0.95rem;
        }
    }
    
    @media (min-width: 576px) {
        .w-sm-auto {
            width: auto !important;
        }
    }
    
    @media (min-width: 992px) {
        .btn-lg-md {
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
        }
        .form-select-lg-md {
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            font-size: 1rem;
        }
    }
</style>
@endpush
