@extends('layouts.app')

@section('title', 'Detail Pesanan - Hijab')

@push('styles')
<style>
    .order-detail-page {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }
    .breadcrumb-minimal {
        font-size: 13px;
        margin-bottom: 1.5rem;
    }
    .breadcrumb-minimal a {
        color: #6b7280;
        text-decoration: none;
    }
    .breadcrumb-minimal a:hover {
        color: #16a34a;
    }
    .detail-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    .detail-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        font-size: 14px;
        color: #1f2937;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .detail-card-header i {
        color: #6b7280;
        margin-right: 8px;
    }
    .detail-card-body {
        padding: 1.5rem;
    }
    .order-number {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-paid { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
    .status-processing { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .status-shipping { background: rgba(249, 115, 22, 0.1); color: #f97316; }
    .status-completed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-cancelled { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    
    .progress-tracker {
        display: flex;
        justify-content: space-between;
        margin: 1.5rem 0;
        position: relative;
    }
    .progress-tracker::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 10%;
        right: 10%;
        height: 2px;
        background: #e5e7eb;
    }
    .progress-step {
        text-align: center;
        position: relative;
        z-index: 1;
        flex: 1;
    }
    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e5e7eb;
        color: #9ca3af;
        font-size: 14px;
        margin-bottom: 8px;
    }
    .step-icon.active {
        background: #16a34a;
        color: white;
    }
    .step-label {
        font-size: 12px;
        color: #6b7280;
    }
    
    .courier-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 1rem 1.25rem;
    }
    .courier-name {
        font-weight: 600;
        color: #166534;
    }
    .btn-wa {
        background: #25d366;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .btn-wa:hover {
        background: #128c7e;
        color: white;
    }
    
    .item-row {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .item-row:last-child { border-bottom: none; }
    .item-img {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 1rem;
    }
    .item-name {
        font-weight: 500;
        color: #1f2937;
        font-size: 14px;
    }
    .item-qty {
        color: #6b7280;
        font-size: 13px;
    }
    .item-price {
        font-weight: 600;
        color: #1f2937;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
        color: #4b5563;
    }
    .summary-total {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        font-weight: 700;
        font-size: 18px;
        color: #1f2937;
        border-top: 1px solid #e5e7eb;
        margin-top: 8px;
    }
    
    .address-label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }
    .address-value {
        font-weight: 500;
        color: #1f2937;
    }
    
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    .photo-item { text-align: center; }
    .photo-item img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    .photo-label {
        font-size: 12px;
        color: #374151;
        margin-top: 8px;
        background: #f9fafb;
        padding: 8px;
        border-radius: 6px;
    }
    .photo-label strong {
        display: block;
        font-size: 13px;
        color: #1f2937;
        margin-bottom: 4px;
    }
    .photo-datetime {
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
    }
    .photo-datetime i {
        width: 14px;
        color: #9ca3af;
    }
    
    .upload-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: 1.25rem;
    }
    .bank-info {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1rem;
        font-size: 13px;
        margin-bottom: 1rem;
    }
    .bank-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
    }
    
    .btn-action {
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 500;
    }
    .btn-primary-custom {
        background: #16a34a;
        color: white;
        border: none;
    }
    .btn-primary-custom:hover {
        background: #15803d;
        color: white;
    }
    .btn-outline-custom {
        background: white;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    .btn-outline-custom:hover {
        background: #f3f4f6;
    }
    
    /* Cancel Countdown Card */
    .cancel-countdown-card {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 12px;
        border: 1px solid #f59e0b;
    }
    .cancel-countdown-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 600;
        padding: 10px;
        background: #f59e0b;
        color: white;
    }
    .cancel-countdown-header.bg-warning {
        background: #f59e0b !important;
    }
    .cancel-countdown-header.bg-secondary {
        background: #6b7280 !important;
    }
    .cancel-countdown-header i {
        font-size: 16px;
    }
    .cancel-countdown-body {
        text-align: center;
        padding: 16px;
    }
    .cancel-countdown-body p {
        font-size: 13px;
        color: #78350f;
    }
    .countdown-timer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 12px 0;
    }
    .countdown-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .countdown-number {
        background: #1f2937;
        color: white;
        font-size: 32px;
        font-weight: 700;
        padding: 10px 16px;
        border-radius: 8px;
        min-width: 70px;
        text-align: center;
    }
    .countdown-label {
        font-size: 11px;
        color: #78350f;
        margin-top: 4px;
        text-transform: uppercase;
        font-weight: 500;
    }
    .countdown-separator {
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
    }
    .countdown-urgent .countdown-number {
        background: #dc2626;
        animation: pulse-urgent 1s infinite;
    }
    @keyframes pulse-urgent {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .btn-danger {
        background: #dc2626;
        color: white;
        border: none;
    }
    .btn-danger:hover {
        background: #b91c1c;
        color: white;
    }
    .btn-danger:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }
    
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    .rating input { display: none; }
    .rating label {
        cursor: pointer;
        font-size: 1.25rem;
        color: #e5e7eb;
        padding: 0 3px;
    }
    .rating label:hover,
    .rating label:hover ~ label,
    .rating input:checked ~ label {
        color: #f59e0b;
    }
    
    .schedule-box {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fcd34d;
        border-radius: 10px;
        padding: 1rem 1.25rem;
    }
    
    /* Mobile Responsive */
    @media (max-width: 991.98px) {
        .order-detail-page {
            padding: 1rem 0;
        }
    }
    
    @media (max-width: 767.98px) {
        .order-detail-page {
            padding: 0.75rem 0;
        }
        .detail-card {
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .detail-card-header {
            padding: 0.75rem 1rem;
            font-size: 13px;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .detail-card-body {
            padding: 1rem;
        }
        .order-number {
            font-size: 15px;
        }
        .breadcrumb-minimal {
            font-size: 12px;
            margin-bottom: 1rem;
        }
        
        /* Progress Tracker Mobile */
        .progress-tracker {
            margin: 1rem 0;
        }
        .progress-tracker::before {
            top: 15px;
            left: 5%;
            right: 5%;
        }
        .step-icon {
            width: 30px;
            height: 30px;
            font-size: 11px;
        }
        .step-label {
            font-size: 10px;
        }
        
        /* Items Mobile */
        .item-row {
            padding: 0.75rem 0;
        }
        .item-img {
            width: 40px;
            height: 40px;
            margin-right: 0.75rem;
        }
        .item-name {
            font-size: 13px;
        }
        .item-qty {
            font-size: 11px;
        }
        .item-price {
            font-size: 13px;
        }
        
        /* Summary Mobile */
        .summary-row {
            font-size: 13px;
        }
        .summary-total {
            font-size: 15px;
        }
        
        /* Photo Grid Mobile */
        .photo-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        .photo-item img {
            height: 100px;
        }
        .photo-label {
            font-size: 11px;
            padding: 6px;
        }
        .photo-label strong {
            font-size: 12px;
        }
        .photo-datetime {
            font-size: 10px;
        }
        
        /* Courier Box Mobile */
        .courier-box {
            padding: 0.75rem 1rem;
        }
        .courier-name {
            font-size: 14px;
        }
        
        /* Address Mobile */
        .address-label {
            font-size: 11px;
        }
        .address-value {
            font-size: 13px;
        }
        
        /* Bank Info Mobile */
        .bank-info {
            font-size: 12px;
            padding: 0.75rem;
        }
        
        /* Upload Box Mobile */
        .upload-box {
            padding: 1rem;
        }
        
        /* Buttons Mobile */
        .btn-action {
            padding: 8px 12px;
            font-size: 13px;
        }
    }
    
    @media (max-width: 575.98px) {
        .progress-tracker::before {
            left: 3%;
            right: 3%;
        }
        .step-icon {
            width: 26px;
            height: 26px;
            font-size: 10px;
        }
        .step-label {
            font-size: 9px;
        }
        .item-img {
            width: 35px;
            height: 35px;
        }
        .photo-grid {
            gap: 0.5rem;
        }
        .photo-item img {
            height: 80px;
        }
    }
</style>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/payment.json');
    $payTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

<div class="order-detail-page">
    <div class="container">
        <div class="breadcrumb-minimal">
            <a href="{{ route('customer.orders.index') }}" class="text-decoration-none text-muted transition hover:text-dark">{{ $payTrans['bc_orders'][$lang] ?? 'Pesanan Saya' }}</a>
            <span class="mx-2 text-muted">/</span>
            <span class="text-dark fw-medium">{{ $order->order_number }}</span>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Order Status -->
                <div class="detail-card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: white;">
                    <div class="detail-card-header d-flex justify-content-between align-items-center bg-white border-bottom" style="padding: 1.25rem 1.5rem;">
                        <span class="fw-semibold text-dark"><i class="fas fa-receipt me-2 text-muted"></i>{{ $payTrans['detail_title'][$lang] ?? 'Detail Pesanan' }}</span>
                        @php
                            $statusClass = match($order->status) {
                                'pending_payment' => 'status-pending',
                                'paid', 'assigned', 'picked_up' => 'status-paid',
                                'processing' => 'status-processing',
                                'on_delivery', 'shipped' => 'status-shipping',
                                'delivered', 'completed' => 'status-completed',
                                'cancelled' => 'status-cancelled',
                                default => 'status-pending'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $order->status_label }}</span>
                    </div>
                    <div class="detail-card-body" style="padding: 1.5rem;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <div class="order-number fw-bold" style="font-size: 18px; color: #003C52;">{{ $order->order_number }}</div>
                                <div class="text-muted mt-0.5" style="font-size: 13px;">{{ $order->created_at->format('d F Y, H:i') }}</div>
                            </div>
                            @if(in_array($order->status, ['paid', 'assigned', 'picked_up', 'on_delivery', 'delivered', 'completed']))
                                <a href="{{ route('customer.orders.receipt', $order) }}" class="btn btn-sm text-white px-3 shadow-sm transition" style="background: #003C52; border-radius: 999px; font-size: 12px; font-weight: 500;" target="_blank">
                                    <i class="fas fa-file-invoice me-1.5"></i>{{ $payTrans['btn_see_receipt'][$lang] ?? 'Lihat Resi' }}
                                </a>
                            @endif
                        </div>

                        <!-- Progress Tracker -->
                        <div class="progress-tracker d-flex justify-content-between align-items-center position-relative mb-4">
                            <div class="progress-step text-center z-index-1">
                                <div class="step-icon mx-auto {{ !in_array($order->status, ['cancelled']) ? 'active' : '' }}" style="transition: all 0.3s;">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="step-label mt-2 small fw-medium">{{ $payTrans['step_created'][$lang] ?? 'Dibuat' }}</div>
                            </div>
                            <div class="progress-step text-center z-index-1">
                                <div class="step-icon mx-auto {{ in_array($order->status, ['paid', 'assigned', 'picked_up', 'on_delivery', 'delivered', 'completed', 'processing', 'shipped']) ? 'active' : '' }}" style="transition: all 0.3s;">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="step-label mt-2 small fw-medium">{{ $payTrans['step_paid'][$lang] ?? 'Dibayar' }}</div>
                            </div>
                            <div class="progress-step text-center z-index-1">
                                <div class="step-icon mx-auto {{ in_array($order->status, ['on_delivery', 'delivered', 'completed', 'shipped']) ? 'active' : '' }}" style="transition: all 0.3s;">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="step-label mt-2 small fw-medium">{{ $payTrans['step_shipping'][$lang] ?? 'Diantar' }}</div>
                            </div>
                            <div class="progress-step text-center z-index-1">
                                <div class="step-icon mx-auto {{ in_array($order->status, ['delivered', 'completed']) ? 'active' : '' }}" style="transition: all 0.3s;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="step-label mt-2 small fw-medium">{{ $payTrans['step_completed'][$lang] ?? 'Selesai' }}</div>
                            </div>
                        </div>
                        
                        @if($order->status == 'cancelled')
                            <div class="alert alert-danger py-2.5 px-3 border-0 mb-0 shadow-sm" style="font-size: 13px; border-radius: 12px; background: #fef2f2; color: #991b1b;">
                                <strong><i class="fas fa-times-circle me-1.5"></i>{{ $payTrans['alert_cancelled_title'][$lang] ?? 'Pesanan Dibatalkan' }}</strong>
                                @if($order->cancel_reason)
                                    <p class="mb-0 mt-1" style="opacity: 0.9;">{{ $order->cancel_reason }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Courier Info -->
                        @if($order->courier)
                            <div class="courier-box mt-4 p-3 border" style="border-radius: 12px; background: rgba(0, 60, 82, 0.01); border-color: #e5e7eb !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $order->courier->avatar_url }}" alt="{{ $order->courier->name }}" 
                                             class="rounded-circle shadow-sm" style="width: 48px; height: 48px; object-fit: cover; border: 2px solid white;">
                                        <div>
                                            <div style="font-size: 11px; font-weight: 600; color: #003C52; text-transform: uppercase; letter-spacing: 0.5px;">{{ $payTrans['label_courier'][$lang] ?? 'Kurir' }}</div>
                                            <div class="courier-name fw-bold text-dark" style="font-size: 14px;">{{ $order->courier->name }}</div>
                                            @if($order->courier->phone)
                                                <div style="font-size: 13px; color: #4b5563;" class="mt-0.5"><i class="fas fa-phone-alt me-1 text-[11px] text-muted"></i>{{ $order->courier->phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($order->courier->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->courier->phone) }}" class="btn text-white px-3 btn-sm transition" style="background: #16a34a; border-radius: 999px; font-weight: 500;" target="_blank">
                                            <i class="fab fa-whatsapp me-1.5"></i>Chat
                                        </a>
                                    @endif
                                </div>
                                @if($order->status === \App\Models\Order::STATUS_ON_DELIVERY)
                                    <div class="mt-3 pt-2.5 border-top border-light">
                                        <button type="button" class="btn text-white btn-sm w-100 transition shadow-sm" id="btnOpenTracking" style="background: #003C52; border-radius: 999px; font-weight: 500;">
                                            <i class="fas fa-map-marker-alt me-1.5"></i>{{ $payTrans['btn_track_courier'][$lang] ?? 'Lacak Posisi Kurir' }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Delivery Photos -->
                        @if($order->pickup_photo || $order->delivery_photo)
                            <div class="mt-4 pt-3 border-top border-light">
                                <div class="fw-semibold text-dark mb-3" style="font-size: 13px;">
                                    <i class="fas fa-camera me-1.5 text-muted"></i>{{ $payTrans['label_doc_photo'][$lang] ?? 'Foto Dokumentasi' }}
                                </div>
                                <div class="photo-grid d-flex gap-3">
                                    @if($order->pickup_photo)
                                    <div class="photo-item border position-relative" style="border-radius: 12px; overflow: hidden; width: 50%;">
                                        <a href="{{ config('filesystems.disks.r2.url').'/' . $order->pickup_photo }}" target="_blank">
                                            <img src="{{ config('filesystems.disks.r2.url').'/' . $order->pickup_photo }}" alt="Foto Pengambilan" class="w-100" style="height: 120px; object-fit: cover;">
                                        </a>
                                        <div class="photo-label p-2 bg-light" style="font-size: 12px;">
                                            <strong class="text-dark d-block">{{ $payTrans['photo_pickup'][$lang] ?? 'Barang Diambil' }}</strong>
                                            @if($order->picked_up_at)
                                            <div class="photo-datetime text-muted extra-small mt-0.5">
                                                <i class="fas fa-calendar-alt me-1"></i>{{ $order->picked_up_at->format('d M Y') }} | <i class="fas fa-clock me-1"></i>{{ $order->picked_up_at->format('H:i') }} WIB
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    @if($order->delivery_photo)
                                    <div class="photo-item border position-relative" style="border-radius: 12px; overflow: hidden; width: 50%;">
                                        <a href="{{ config('filesystems.disks.r2.url').'/' . $order->delivery_photo }}" target="_blank">
                                            <img src="{{ config('filesystems.disks.r2.url').'/' . $order->delivery_photo }}" alt="Foto Selesai" class="w-100" style="height: 120px; object-fit: cover;">
                                        </a>
                                        <div class="photo-label p-2 bg-light" style="font-size: 12px;">
                                            <strong class="text-dark d-block">{{ $payTrans['photo_delivered'][$lang] ?? 'Pesanan Diterima' }}</strong>
                                            @if($order->delivered_at)
                                            <div class="photo-datetime text-muted extra-small mt-0.5">
                                                <i class="fas fa-calendar-alt me-1"></i>{{ $order->delivered_at->format('d M Y') }} | <i class="fas fa-clock me-1"></i>{{ $order->delivered_at->format('H:i') }} WIB
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Info Pengiriman -->
                <div class="detail-card border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
                    <div class="detail-card-body p-0">
                        <div class="schedule-box border-0 p-3" style="border-radius: 16px; background: rgba(0, 60, 82, 0.04); border-left: 4px solid #003C52 !important;">
                            <div class="fw-bold mb-1" style="font-size: 13px; color: #003C52;">
                                <i class="fas fa-truck-loading me-1.5"></i>{{ $payTrans['shipping_info_title'][$lang] ?? 'Informasi Pengiriman' }}
                            </div>
                            <div style="font-size: 13px; color: #1e293b; opacity: 0.85;">
                                <i class="fas fa-info-circle me-1"></i>{{ $payTrans['shipping_info_desc'][$lang] ?? 'Pantau status pesanan di halaman ini. Notifikasi akan dikirim saat ada update.' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="detail-card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: white;">
                    <div class="detail-card-header d-flex justify-content-between align-items-center bg-white border-bottom" style="padding: 1.25rem 1.5rem;">
                        <span class="fw-semibold text-dark"><i class="fas fa-box me-2 text-muted"></i>{{ $payTrans['card_items'][$lang] ?? 'Item Pesanan' }}</span>
                        <span class="text-muted fw-medium" style="font-size: 13px;">{{ $order->items->count() }} item</span>
                    </div>
                    <div class="detail-card-body" style="padding: 1.5rem;">
                        @foreach($order->items as $item)
                            <div class="item-row d-flex align-items-center gap-3 py-3 border-bottom" style="last-child { border-bottom: none !important; }">
                                <img src="{{ $item->product ? $item->product->image_url : 'https://via.placeholder.com/56' }}" alt="{{ $item->product_name }}" class="item-img shadow-sm border" style="width: 56px; height: 56px; border-radius: 10px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="item-name fw-bold text-dark" style="font-size: 14px;">{{ $item->product_name }}</div>
                                    <div class="item-qty text-muted small mt-0.5">{{ $item->formatted_price }} × {{ $item->quantity }}</div>
                                </div>
                                <div class="item-price fw-bold text-dark" style="font-size: 14px;">{{ $item->formatted_subtotal }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Shipping Address -->
                <div class="detail-card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: white;">
                    <div class="detail-card-header bg-white border-bottom" style="padding: 1.25rem 1.5rem;">
                        <span class="fw-semibold text-dark"><i class="fas fa-map-marker-alt me-2 text-muted"></i>{{ $payTrans['label_address'][$lang] ?? 'Alamat Pengiriman' }}</span>
                    </div>
                    <div class="detail-card-body" style="padding: 1.5rem;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="address-label text-muted small fw-medium" style="text-transform: uppercase; letter-spacing: 0.5px;">{{ $payTrans['label_recipient'][$lang] ?? 'Penerima' }}</div>
                                <div class="address-value fw-bold text-dark mt-0.5" style="font-size: 14px;">{{ $order->shipping_name }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="address-label text-muted small fw-medium" style="text-transform: uppercase; letter-spacing: 0.5px;">{{ $payTrans['label_phone'][$lang] ?? 'Telepon' }}</div>
                                <div class="address-value fw-bold text-dark mt-0.5" style="font-size: 14px;">{{ $order->shipping_phone }}</div>
                            </div>
                        </div>
                        <div class="address-label text-muted small fw-medium mt-1" style="text-transform: uppercase; letter-spacing: 0.5px;">{{ $payTrans['label_address'][$lang] ?? 'Alamat' }}</div>
                        <div class="address-value text-zinc-700 mt-0.5" style="font-size: 13px; line-height: 1.5;">{{ $order->shipping_address }}</div>
                        @if($order->delivery_distance_minutes)
                            <div class="mt-3 text-muted small d-flex align-items-center gap-1.5">
                                <i class="fas fa-route text-zinc-400"></i>
                                <span>{{ $payTrans['label_est_distance'][$lang] ?? 'Estimasi jarak:' }} <strong>{{ $order->delivery_distance_minutes }} {{ $payTrans['label_minutes'][$lang] ?? 'menit' }}</strong></span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Testimonial Form / Display -->
                @if($order->canGiveTestimonial())
                    <div class="detail-card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: white;">
                        <div class="detail-card-header bg-white border-bottom" style="padding: 1.25rem 1.5rem;">
                            <span class="fw-semibold text-dark"><i class="fas fa-star me-2 text-muted"></i>{{ $payTrans['testi_give_title'][$lang] ?? 'Berikan Testimoni' }}</span>
                        </div>
                        <div class="detail-card-body" style="padding: 1.5rem;">
                            <form action="{{ route('customer.testimonials.store', $order) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-medium" style="font-size: 13px;">{{ $payTrans['testi_rating'][$lang] ?? 'Rating' }}</label>
                                    <div class="rating d-flex gap-1">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" {{ $i == 5 ? 'checked' : '' }}>
                                            <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-medium" style="font-size: 13px;">{{ $payTrans['testi_give_title'][$lang] ?? 'Testimoni' }}</label>
                                    <textarea class="form-control border-light shadow-sm" name="content" rows="3" placeholder="{{ $payTrans['testi_placeholder'][$lang] ?? 'Bagikan pengalaman belanja Anda...' }}" style="font-size: 13px; border-radius: 10px; padding: 10px;" required>{{ old('content') }}</textarea>
                                </div>
                                <button type="submit" class="btn text-white px-4 py-2 transition" style="background: #003C52; border-radius: 999px; font-weight: 600; font-size: 13px;">
                                    <i class="fas fa-paper-plane me-1.5"></i>{{ $payTrans['testi_btn_send'][$lang] ?? 'Kirim Testimoni' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($order->testimonial)
                    <div class="detail-card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: white;">
                        <div class="detail-card-header d-flex justify-content-between align-items-center bg-white border-bottom" style="padding: 1.25rem 1.5rem;">
                            <span class="fw-semibold text-dark"><i class="fas fa-star me-2 text-muted"></i>{{ $payTrans['testi_user_title'][$lang] ?? 'Testimoni Anda' }}</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 999px; font-size: 12px; font-weight: 500;" data-bs-toggle="collapse" data-bs-target="#editTestimonial">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                        </div>
                        <div class="detail-card-body" style="padding: 1.5rem;">
                            <div id="showTestimonial">
                                <div class="mb-2 text-warning">{!! $order->testimonial->stars !!}</div>
                                <p class="mb-2 text-dark" style="font-size: 13px; line-height: 1.5;">{{ $order->testimonial->content }}</p>
                                @if($order->testimonial->is_approved)
                                    <small style="color: #16a34a;" class="fw-medium"><i class="fas fa-check-circle me-1"></i>{{ $payTrans['testi_status_approved'][$lang] ?? 'Ditampilkan di website' }}</small>
                                @else
                                    <small class="text-muted fw-medium"><i class="fas fa-clock me-1"></i>{{ $payTrans['testi_status_pending'][$lang] ?? 'Menunggu persetujuan' }}</small>
                                @endif
                            </div>
                            
                            <!-- Edit Form (Collapsed) -->
                            <div class="collapse mt-3" id="editTestimonial">
                                <hr class="my-3 opacity-10">
                                <form action="{{ route('customer.testimonials.update', $order->testimonial) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-medium" style="font-size: 13px;">{{ $payTrans['testi_rating'][$lang] ?? 'Rating' }}</label>
                                        <div class="rating d-flex gap-1">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="rating" value="{{ $i }}" id="editStar{{ $i }}" {{ $order->testimonial->rating == $i ? 'checked' : '' }}>
                                                <label for="editStar{{ $i }}"><i class="fas fa-star"></i></label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-medium" style="font-size: 13px;">{{ $payTrans['testi_give_title'][$lang] ?? 'Testimoni' }}</label>
                                        <textarea class="form-control border-light shadow-sm" name="content" rows="3" style="font-size: 13px; border-radius: 10px; padding: 10px;" required>{{ $order->testimonial->content }}</textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn text-white px-4" style="background: #003C52; border-radius: 999px; font-weight: 600; font-size: 13px;">
                                            <i class="fas fa-save me-1.5"></i>{{ $payTrans['testi_btn_save'][$lang] ?? 'Simpan Perubahan' }}
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 999px; font-weight: 500; font-size: 13px;" data-bs-toggle="collapse" data-bs-target="#editTestimonial">
                                            {{ $payTrans['testi_btn_cancel'][$lang] ?? 'Batal' }}
                                        </button>
                                    </div>
                                    <div class="alert alert-warning border-0 mt-3 py-2 px-3 shadow-sm" style="font-size: 12px; border-radius: 10px; background: #fffbeb; color: #b45309;">
                                        <i class="fas fa-info-circle me-1.5"></i>{{ $payTrans['testi_edit_hint'][$lang] ?? 'Testimoni yang diedit akan membutuhkan persetujuan admin ulang.' }}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="col-lg-4">
                <!-- Payment Summary -->
                <div class="detail-card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: white;">
                    <div class="detail-card-header d-flex justify-content-between align-items-center bg-white border-bottom" style="padding: 1.25rem 1.5rem;">
                        <span class="fw-semibold text-dark"><i class="fas fa-credit-card me-2 text-muted"></i>{{ $payTrans['summary_payment_title'][$lang] ?? 'Pembayaran' }}</span>
                        @php
                            $paymentClass = match($order->payment_status) {
                                'paid', 'verified' => 'status-completed',
                                'pending_verification' => 'status-processing',
                                'unpaid' => 'status-pending',
                                default => 'status-pending'
                            };
                        @endphp
                        <span class="status-badge {{ $paymentClass }}">{{ $order->payment_status_label }}</span>
                    </div>
                    <div class="detail-card-body" style="padding: 1.5rem;">
                        <div class="summary-row d-flex justify-content-between align-items-center py-2 text-zinc-600" style="font-size: 13px;">
                            <span>{{ $payTrans['label_subtotal'][$lang] ?? 'Subtotal' }}</span>
                            <span class="fw-medium text-dark">{{ $order->formatted_subtotal }}</span>
                        </div>
                        @if($order->product_discount > 0)
                            <div class="summary-row d-flex justify-content-between align-items-center py-2 text-danger" style="font-size: 13px;">
                                <span>{{ $payTrans['summary_product_disc'][$lang] ?? 'Diskon Produk' }}</span>
                                <span class="fw-medium">-{{ $order->formatted_product_discount }}</span>
                            </div>
                        @endif
                        <div class="summary-row d-flex justify-content-between align-items-center py-2 text-zinc-600" style="font-size: 13px;">
                            <span>{{ $payTrans['label_shipping'][$lang] ?? 'Ongkos Kirim' }}</span>
                            <span class="fw-medium text-dark">{{ $order->formatted_shipping_cost }}</span>
                        </div>
                        @if($order->shipping_discount > 0)
                            <div class="summary-row d-flex justify-content-between align-items-center py-2 text-danger" style="font-size: 13px;">
                                <span>{{ $payTrans['summary_shipping_disc'][$lang] ?? 'Diskon Ongkir' }}</span>
                                <span class="fw-medium">-{{ $order->formatted_shipping_discount }}</span>
                            </div>
                        @endif
                        <hr class="my-2.5 opacity-10">
                        <div class="summary-total d-flex justify-content-between align-items-center pt-1" style="font-size: 16px;">
                            <span class="fw-bold text-dark">{{ $payTrans['label_total'][$lang] ?? 'Total' }}</span>
                            <span class="fw-bold" style="color: #003C52; font-size: 18px;">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Online Payment Option -->
                @if($order->canUploadPaymentProof())
                    <div class="detail-card border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: white;">
                        <div class="detail-card-body p-0">
                            <div class="shadow-sm p-4 border" style="background: linear-gradient(135deg, rgba(0, 60, 82, 0.02) 0%, rgba(0, 60, 82, 0.05) 100%); border-radius: 16px; border-color: rgba(0, 60, 82, 0.1) !important;">
                                <div class="fw-bold mb-1" style="font-size: 14px; color: #003C52;">
                                    <i class="fas fa-bolt me-1.5"></i>{{ $payTrans['online_pay_title'][$lang] ?? 'Bayar Online' }}
                                </div>
                                <p class="text-zinc-600 small mb-3" style="line-height: 1.4;">
                                    {{ $payTrans['online_pay_desc'][$lang] ?? 'Bayar langsung via QRIS atau Virtual Account' }}
                                </p>
                                <a href="{{ route('customer.payment.show', $order) }}" class="btn text-white w-100 py-2.5 shadow-sm transition" style="background: #003C52; border-radius: 999px; font-weight: 600; font-size: 14px;">
                                    <i class="fas fa-credit-card me-2 text-[12px]"></i>{{ $payTrans['online_pay_btn'][$lang] ?? 'Bayar Sekarang' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Local Upload Payment Proof View -->
                @if($order->payment_proof)
                    <div class="detail-card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: white;">
                        <div class="detail-card-header bg-white border-bottom" style="padding: 1.25rem 1.5rem;">
                            <span class="fw-semibold text-dark"><i class="fas fa-image me-2 text-muted"></i>{{ $payTrans['proof_title'][$lang] ?? 'Bukti Pembayaran' }}</span>
                        </div>
                        <div class="detail-card-body text-center" style="padding: 1.25rem;">
                            <a href="{{ config('filesystems.disks.r2.url').'/' . $order->payment_proof }}" target="_blank" class="d-inline-block border p-1" style="border-radius: 12px; overflow: hidden;">
                                <img src="{{ config('filesystems.disks.r2.url').'/' . $order->payment_proof }}" class="img-fluid rounded" style="max-height: 180px; object-fit: contain; border-radius: 8px !important;">
                            </a>
                        </div>
                    </div>
                @endif
                
                <!-- Actions Buttons / Forms -->
                <div class="d-grid gap-2">
                    @if(in_array($order->status, ['shipped', 'delivered', \App\Models\Order::STATUS_DELIVERED]))
                        <form action="{{ route('customer.orders.confirm', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn text-white w-100 py-2.5 transition shadow-sm" style="background: #16a34a; border-radius: 999px; font-weight: 600;" onclick="return confirm('{{ $payTrans['confirm_prompt'][$lang] ?? 'Konfirmasi pesanan sudah diterima?' }}')">
                                <i class="fas fa-check me-1.5"></i>{{ $payTrans['btn_confirm_received'][$lang] ?? 'Konfirmasi Diterima' }}
                            </button>
                        </form>
                    @endif
                    
                    @if($order->canBeCancelled())
                        <!-- Cancel Countdown Card -->
                        <div class="cancel-countdown-card border shadow-sm mb-2" id="cancelCountdownCard" style="border-radius: 16px; overflow: hidden; background: white; border-color: #fcd34d !important;">
                            <div class="cancel-countdown-header bg-warning text-dark px-3 py-2 fw-semibold d-flex align-items-center gap-1.5" style="font-size: 13px;">
                                <i class="fas fa-clock"></i>
                                <span>{{ $payTrans['cancel_limit_title'][$lang] ?? 'Batas Waktu Pembatalan' }}</span>
                            </div>
                            <div class="cancel-countdown-body p-3 text-center">
                                <p class="mb-2 text-zinc-600 small">{{ $payTrans['cancel_limit_desc'][$lang] ?? 'Anda dapat membatalkan pesanan dalam:' }}</p>
                                <div class="countdown-timer d-flex justify-content-center align-items-center gap-1.5" id="cancelCountdown">
                                    <div class="countdown-item bg-light border px-2.5 py-1" style="border-radius: 8px;">
                                        <span class="countdown-number fw-bold text-dark" id="countdownMinutes" style="font-size: 16px;">05</span>
                                        <span class="countdown-label d-block text-muted" style="font-size: 9px; text-transform: uppercase;">{{ $payTrans['cancel_unit_minute'][$lang] ?? 'Menit' }}</span>
                                    </div>
                                    <span class="countdown-separator fw-bold text-muted">:</span>
                                    <div class="countdown-item bg-light border px-2.5 py-1" style="border-radius: 8px;">
                                        <span class="countdown-number fw-bold text-dark" id="countdownSeconds" style="font-size: 16px;">00</span>
                                        <span class="countdown-label d-block text-muted" style="font-size: 9px; text-transform: uppercase;">{{ $payTrans['cancel_unit_second'][$lang] ?? 'Detik' }}</span>
                                    </div>
                                </div>
                                @if($order->isPaidViaGateway())
                                <p class="text-muted small mt-2.5 mb-0" style="line-height: 1.4;">
                                    <i class="fas fa-info-circle me-1 text-amber-600"></i>{{ $payTrans['cancel_refund_hint_prefix'][$lang] ?? 'Dana sebesar' }} <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong> {{ $payTrans['cancel_refund_hint_suffix'][$lang] ?? 'akan dikembalikan' }}
                                </p>
                                @endif
                            </div>
                            <div class="p-3 pt-0">
                                <form action="{{ route('customer.orders.cancel', $order) }}" method="POST" id="cancelForm">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="cancel_reason" value="Dibatalkan oleh customer">
                                    <button type="submit" class="btn btn-danger w-100 py-2 transition" id="btnCancelOrder" style="border-radius: 999px; font-weight: 600; font-size: 13px;" onclick="return confirm('{{ $payTrans['cancel_prompt_prefix'][$lang] ?? 'Yakin ingin membatalkan pesanan?' }}{{ $order->isPaidViaGateway() ? ($payTrans['cancel_prompt_suffix'][$lang] ?? ' Dana akan dikembalikan ke saldo Pakasir Anda.') : '' }}')">
                                        <i class="fas fa-times me-1.5"></i>{{ $payTrans['cancel_btn'][$lang] ?? 'Batalkan Pesanan' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif(!in_array($order->status, [\App\Models\Order::STATUS_CANCELLED, \App\Models\Order::STATUS_COMPLETED, \App\Models\Order::STATUS_DELIVERED]))
                        @if(in_array($order->status, [\App\Models\Order::STATUS_PICKED_UP, \App\Models\Order::STATUS_ON_DELIVERY]))
                            <div class="alert alert-secondary py-2.5 px-3 border-0 mb-2 text-zinc-700 shadow-sm" style="font-size: 12.5px; border-radius: 12px; background: #f1f5f9;">
                                <i class="fas fa-shipping-fast me-1.5 text-zinc-500"></i>{{ $payTrans['cancel_err_courier'][$lang] ?? 'Pesanan tidak dapat dibatalkan karena kurir sudah mengambil barang' }}
                            </div>
                        @elseif($order->getCancelCountdownSeconds() <= 0)
                            <div class="alert alert-secondary py-2.5 px-3 border-0 mb-2 text-zinc-700 shadow-sm" style="font-size: 12.5px; border-radius: 12px; background: #f1f5f9;">
                                <i class="fas fa-clock me-1.5 text-zinc-500"></i>{{ $payTrans['cancel_err_timeout'][$lang] ?? 'Batas waktu pembatalan sudah habis (lebih dari 5 menit)' }}
                            </div>
                        @endif
                    @endif
                    
                    {{-- Show refund status if applicable --}}
                    @if($order->refund_status)
                        <div class="refund-status-card mb-2">
                            @if($order->refund_status === 'completed')
                                <div class="alert alert-success border-0 py-2.5 px-3 shadow-sm text-emerald-800" style="font-size: 12.5px; border-radius: 12px; background: #dcfce7;">
                                    <i class="fas fa-check-circle me-1.5 text-emerald-600"></i>
                                    <strong>{{ $payTrans['refund_success_title'][$lang] ?? 'Refund Berhasil!' }}</strong><br class="mb-1">
                                    Rp {{ number_format($order->refund_amount, 0, ',', '.') }} {{ $payTrans['refund_success_desc'][$lang] ?? 'telah dikembalikan ke saldo Pakasir Anda.' }}
                                </div>
                            @elseif($order->refund_status === 'processing')
                                <div class="alert alert-info border-0 py-2.5 px-3 shadow-sm text-sky-800" style="font-size: 12.5px; border-radius: 12px; background: #e0f2fe;">
                                    <i class="fas fa-spinner fa-spin me-1.5 text-sky-600"></i>
                                    <strong>{{ $payTrans['refund_proc_title'][$lang] ?? 'Refund Diproses' }}</strong><br class="mb-1">
                                    {{ $payTrans['refund_proc_desc'][$lang] ?? 'Dana sedang dalam proses pengembalian.' }}
                                </div>
                            @elseif($order->refund_status === 'failed')
                                <div class="alert alert-danger border-0 py-2.5 px-3 shadow-sm text-rose-800" style="font-size: 12.5px; border-radius: 12px; background: #ffe4e6;">
                                    <i class="fas fa-exclamation-circle me-1.5 text-rose-600"></i>
                                    <strong>{{ $payTrans['refund_failed_title'][$lang] ?? 'Refund Gagal' }}</strong><br class="mb-1">
                                    {{ $payTrans['refund_failed_desc'][$lang] ?? 'Silakan hubungi admin untuk bantuan.' }}
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary py-2.5 transition" style="border-radius: 999px; font-weight: 500; font-size: 14px;">
                        <i class="fas fa-arrow-left me-1.5"></i>{{ $payTrans['btn_general_back'][$lang] ?? 'Kembali' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tracking Popup Modals -->
@if($order->status === \App\Models\Order::STATUS_ON_DELIVERY && $order->courier)
<div id="trackingPopup" class="tracking-popup" style="display: none;">
    <div class="tracking-popup-content shadow-lg border border-light" style="border-radius: 16px; overflow: hidden;">
        <!-- Header -->
        <div class="tracking-popup-header d-flex justify-content-between align-items-center px-3 py-2.5 text-white" style="background: #003C52;">
            <div class="d-flex align-items-center gap-2" style="font-size: 14px;">
                <i class="fas fa-motorcycle"></i>
                <span class="fw-bold">{{ $payTrans['track_popup_title'][$lang] ?? 'Lacak Kurir' }}</span>
            </div>
            <button type="button" class="tracking-popup-close bg-transparent border-0 text-white opacity-75 hover:opacity-100" id="btnCloseTracking">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Map Canvas container -->
        <div id="trackingMap" style="height: 320px; background: #f8f9fa;"></div>
        
        <!-- Stats Tracker Metadata Info -->
        <div class="tracking-popup-info p-3 bg-white">
            <div class="tracking-stats d-flex gap-4 mb-3 border-bottom pb-2">
                <div class="tracking-stat d-flex align-items-center gap-1.5 text-dark fw-semibold" style="font-size: 13px;">
                    <i class="fas fa-route text-muted"></i>
                    <span id="distanceText">-- km</span>
                </div>
                <div class="tracking-stat d-flex align-items-center gap-1.5 text-dark fw-semibold" style="font-size: 13px;">
                    <i class="fas fa-clock text-muted"></i>
                    <span id="etaText">-- mnt</span>
                </div>
            </div>
            <div class="tracking-courier d-flex align-items-center gap-3">
                <img src="{{ $order->courier->avatar_url }}" alt="{{ $order->courier->name }}" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                <div class="tracking-courier-info flex-grow-1">
                    <div class="fw-bold text-dark" style="font-size: 13.5px;">{{ $order->courier->name }}</div>
                    <small id="lastUpdateText" class="text-muted" style="font-size: 11px;">{{ $payTrans['track_loading'][$lang] ?? 'Memuat...' }}</small>
                </div>
                @if($order->courier->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->courier->phone) }}" class="tracking-wa-btn btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center p-0" style="background: #16a34a; width: 32px; height: 32px;" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@include('customer.orders._tracking_section')`r`n`r`n@endsection

@if($order->status === \App\Models\Order::STATUS_ON_DELIVERY && $order->courier)
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Tracking Popup Styles */
    .tracking-popup {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1050;
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tracking-popup-content {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        overflow: hidden;
        width: 340px;
    }
    .tracking-popup-header {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        color: white;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .tracking-popup-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .tracking-popup-close:hover {
        background: rgba(255,255,255,0.3);
    }
    #trackingMap {
        height: 200px;
        width: 100%;
    }
    .tracking-popup-info {
        padding: 12px;
        background: #f9fafb;
    }
    .tracking-stats {
        display: flex;
        justify-content: center;
        gap: 24px;
        margin-bottom: 12px;
    }
    .tracking-stat {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #16a34a;
    }
    .tracking-stat i {
        color: #9ca3af;
        font-size: 12px;
    }
    .tracking-courier {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 10px 12px;
        border-radius: 10px;
    }
    .tracking-courier img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #16a34a;
    }
    .tracking-courier-info {
        flex: 1;
        font-size: 13px;
    }
    .tracking-courier-info small {
        color: #6b7280;
        font-size: 11px;
    }
    .tracking-wa-btn {
        width: 36px;
        height: 36px;
        background: #25d366;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
    }
    .tracking-wa-btn:hover {
        background: #128c7e;
        color: white;
        transform: scale(1.1);
    }
    
    /* Map Markers */
    .leaflet-control-attribution {
        font-size: 8px !important;
    }
    .courier-marker, .destination-marker {
        background: none;
        border: none;
    }
    .courier-marker-inner {
        background: #16a34a;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 3px 12px rgba(22, 163, 74, 0.4);
        border: 2px solid white;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.4); }
        70% { box-shadow: 0 0 0 12px rgba(22, 163, 74, 0); }
        100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
    }
    .destination-marker-inner {
        background: #dc2626;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        box-shadow: 0 3px 10px rgba(220, 38, 38, 0.3);
    }
    .destination-marker-inner i {
        transform: rotate(45deg);
    }
    
    /* Mobile */
    @media (max-width: 575.98px) {
        .tracking-popup {
            bottom: 70px;
            right: 10px;
            left: 10px;
        }
        .tracking-popup-content {
            width: 100%;
        }
        #trackingMap {
            height: 180px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let trackingMap = null;
let courierMarker = null;
let destinationMarker = null;
let routeLine = null;
let trackingInterval = null;
let isPopupOpen = false;

const ORDER_ID = {{ $order->id }};
const DESTINATION = {
    lat: {{ $order->shipping_latitude ?? -7.278417 }},
    lng: {{ $order->shipping_longitude ?? 112.632583 }}
};

// Open tracking popup
document.getElementById('btnOpenTracking').addEventListener('click', function() {
    document.getElementById('trackingPopup').style.display = 'block';
    isPopupOpen = true;
    
    setTimeout(() => {
        if (!trackingMap) {
            initTrackingMap();
        } else {
            trackingMap.invalidateSize();
        }
        startTracking();
    }, 100);
});

// Close tracking popup
document.getElementById('btnCloseTracking').addEventListener('click', function() {
    document.getElementById('trackingPopup').style.display = 'none';
    isPopupOpen = false;
    stopTracking();
});

function initTrackingMap() {
    trackingMap = L.map('trackingMap', {
        zoomControl: false
    }).setView([DESTINATION.lat, DESTINATION.lng], 15);
    
    // Add zoom control to bottom right
    L.control.zoom({ position: 'bottomright' }).addTo(trackingMap);
    
    // Use cleaner map tiles
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OSM',
        maxZoom: 19
    }).addTo(trackingMap);
    
    // Destination marker (customer location)
    const destIcon = L.divIcon({
        html: '<div class="destination-marker-inner"><i class="fas fa-home"></i></div>',
        className: 'destination-marker',
        iconSize: [28, 28],
        iconAnchor: [14, 28]
    });
    destinationMarker = L.marker([DESTINATION.lat, DESTINATION.lng], { icon: destIcon }).addTo(trackingMap);
}

function startTracking() {
    fetchLocation();
    trackingInterval = setInterval(fetchLocation, 5000);
}

function stopTracking() {
    if (trackingInterval) {
        clearInterval(trackingInterval);
        trackingInterval = null;
    }
}

function fetchLocation() {
    if (!isPopupOpen) return;
    
    fetch('{{ route("customer.orders.tracking", $order) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.location) {
                updateCourierPosition(data.location);
                document.getElementById('lastUpdateText').textContent = 'Aktif • ' + data.location.updated_ago;
            } else {
                document.getElementById('lastUpdateText').textContent = data.message || 'Menunggu lokasi...';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('lastUpdateText').textContent = 'Gagal memuat';
        });
}

function updateCourierPosition(location) {
    const lat = parseFloat(location.latitude);
    const lng = parseFloat(location.longitude);
    
    // Create courier marker icon
    const courierIcon = L.divIcon({
        html: '<div class="courier-marker-inner"><i class="fas fa-motorcycle"></i></div>',
        className: 'courier-marker',
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });
    
    if (courierMarker) {
        courierMarker.setLatLng([lat, lng]);
    } else {
        courierMarker = L.marker([lat, lng], { icon: courierIcon }).addTo(trackingMap);
    }
    
    // Get route from OSRM
    getRoute(lat, lng);
}

function getRoute(courierLat, courierLng) {
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${courierLng},${courierLat};${DESTINATION.lng},${DESTINATION.lat}?overview=full&geometries=geojson`;
    
    fetch(osrmUrl)
        .then(response => response.json())
        .then(data => {
            if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                const route = data.routes[0];
                const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);
                
                if (routeLine) {
                    trackingMap.removeLayer(routeLine);
                }
                
                routeLine = L.polyline(coords, {
                    color: '#16a34a',
                    weight: 4,
                    opacity: 0.8,
                    lineCap: 'round',
                    lineJoin: 'round'
                }).addTo(trackingMap);
                
                const distanceKm = (route.distance / 1000).toFixed(1);
                const durationMin = Math.round(route.duration / 60);
                
                document.getElementById('distanceText').textContent = distanceKm + ' km';
                document.getElementById('etaText').textContent = durationMin + ' mnt';
                
                const bounds = L.latLngBounds(coords);
                trackingMap.fitBounds(bounds, { padding: [30, 30] });
            } else {
                drawStraightLine(courierLat, courierLng);
            }
        })
        .catch(error => {
            drawStraightLine(courierLat, courierLng);
        });
}

function drawStraightLine(courierLat, courierLng) {
    if (routeLine) {
        trackingMap.removeLayer(routeLine);
    }
    
    routeLine = L.polyline([
        [courierLat, courierLng],
        [DESTINATION.lat, DESTINATION.lng]
    ], {
        color: '#16a34a',
        weight: 3,
        opacity: 0.6,
        dashArray: '6, 6'
    }).addTo(trackingMap);
    
    const distance = calculateDistance(courierLat, courierLng, DESTINATION.lat, DESTINATION.lng);
    document.getElementById('distanceText').textContent = distance.toFixed(1) + ' km';
    document.getElementById('etaText').textContent = '~' + Math.round(distance * 3) + ' mnt';
    
    const bounds = L.latLngBounds([
        [courierLat, courierLng],
        [DESTINATION.lat, DESTINATION.lng]
    ]);
    trackingMap.fitBounds(bounds, { padding: [30, 30] });
}

function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}
</script>
@endpush
@endif

{{-- Countdown Timer Script for Cancel Window --}}
@if($order->canBeCancelled())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countdownContainer = document.getElementById('cancelCountdown');
    const minutesEl = document.getElementById('countdownMinutes');
    const secondsEl = document.getElementById('countdownSeconds');
    const countdownCard = document.getElementById('cancelCountdownCard');
    const btnCancelOrder = document.getElementById('btnCancelOrder');
    
    if (!countdownContainer || !minutesEl || !secondsEl) return;
    
    // Get countdown from server (in seconds) - time remaining to cancel
    let countdown = {{ $order->cancel_countdown }};
    
    function updateDisplay() {
        const minutes = Math.floor(countdown / 60);
        const seconds = countdown % 60;
        
        minutesEl.textContent = minutes.toString().padStart(2, '0');
        secondsEl.textContent = seconds.toString().padStart(2, '0');
        
        // Update color based on remaining time
        if (countdown <= 60) {
            countdownContainer.classList.add('countdown-urgent');
        }
    }
    
    function disableCancelButton() {
        // Cancel window expired - cannot cancel anymore
        if (countdownCard) {
            countdownCard.innerHTML = `
                <div class="cancel-countdown-header bg-secondary text-white">
                    <i class="fas fa-times-circle"></i>
                    <span>Waktu Pembatalan Habis</span>
                </div>
                <div class="cancel-countdown-body text-center py-3">
                    <i class="fas fa-clock text-muted" style="font-size: 32px;"></i>
                    <p class="mt-2 mb-0 text-muted">Batas waktu pembatalan sudah habis.<br>Pesanan tidak dapat dibatalkan lagi.</p>
                </div>
            `;
        }
    }
    
    function tick() {
        if (countdown <= 0) {
            disableCancelButton();
            return;
        }
        
        countdown--;
        updateDisplay();
        
        setTimeout(tick, 1000);
    }
    
    // Initial display
    updateDisplay();
    
    // Start countdown
    if (countdown > 0) {
        setTimeout(tick, 1000);
    } else {
        disableCancelButton();
    }
    
    // Also poll server to check if courier picked up
    setInterval(function() {
        fetch('{{ route("customer.orders.cancel-status", $order) }}')
            .then(response => response.json())
            .then(data => {
                if (!data.can_cancel) {
                    // Cannot cancel anymore (maybe courier picked up)
                    if (data.reason) {
                        if (countdownCard) {
                            countdownCard.innerHTML = `
                                <div class="cancel-countdown-header bg-secondary text-white">
                                    <i class="fas fa-times-circle"></i>
                                    <span>Tidak Dapat Dibatalkan</span>
                                </div>
                                <div class="cancel-countdown-body text-center py-3">
                                    <i class="fas fa-info-circle text-muted" style="font-size: 32px;"></i>
                                    <p class="mt-2 mb-0 text-muted">${data.reason}</p>
                                </div>
                            `;
                        }
                    }
                } else {
                    // Sync countdown with server
                    countdown = data.countdown_seconds;
                }
            })
            .catch(error => console.log('Status check failed'));
    }, 10000); // Check every 10 seconds
});
</script>
@endpush
@endif
