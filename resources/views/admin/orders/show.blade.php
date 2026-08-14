@extends('layouts.admin')

@section('page-title', 'Detail Pesanan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    :root {
        --admin-bg: #fafafa;
        --card-bg: #ffffff;
        --border-color: #e5e7eb;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --accent: #374151;
    }
    
    #courierMap { height: 400px; border-radius: 8px; margin-top: 16px; }
    
    .order-detail-page {
        background: var(--admin-bg);
        min-height: 100vh;
        padding: 24px 0;
    }
    
    .detail-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .detail-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .detail-card-header h6 {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .detail-card-header i {
        color: var(--text-secondary);
        font-size: 14px;
    }
    
    .detail-card-body {
        padding: 20px;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        background: #f3f4f6;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }
    
    .status-badge.pending { background: #fef3c7; color: #92400e; border-color: #fde68a; }
    .status-badge.processing { background: #e0e7ff; color: #3730a3; border-color: #c7d2fe; }
    .status-badge.delivery { background: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
    .status-badge.success { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
    .status-badge.danger { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
    
    .info-row {
        display: flex;
        margin-bottom: 12px;
    }
    
    .info-row:last-child {
        margin-bottom: 0;
    }
    
    .info-label {
        font-size: 13px;
        color: var(--text-muted);
        width: 140px;
        flex-shrink: 0;
    }
    
    .info-value {
        font-size: 13px;
        color: var(--text-primary);
        font-weight: 500;
    }
    
    .section-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .items-table th {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        background: #f9fafb;
        text-align: left;
    }
    
    .items-table th:last-child,
    .items-table td:last-child {
        text-align: right;
    }
    
    .items-table th:nth-child(2),
    .items-table td:nth-child(2) {
        text-align: center;
    }
    
    .items-table td {
        font-size: 13px;
        color: var(--text-primary);
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .items-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 16px;
        font-size: 13px;
    }
    
    .summary-row.total {
        background: #f9fafb;
        font-weight: 600;
        border-top: 1px solid var(--border-color);
    }
    
    .courier-card {
        background: #f9fafb;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 16px;
    }
    
    .courier-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--text-secondary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
    }
    
    .timeline-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .timeline-item:last-child {
        border-bottom: none;
    }
    
    .timeline-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: var(--text-secondary);
        margin-right: 12px;
        font-size: 12px;
    }
    
    .timeline-label {
        font-size: 13px;
        color: var(--text-primary);
    }
    
    .timeline-time {
        font-size: 12px;
        color: var(--text-muted);
    }
    
    .note-box {
        background: #f9fafb;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 12px 16px;
        margin-top: 16px;
    }
    
    .note-box-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    
    .note-box-content {
        font-size: 13px;
        color: var(--text-primary);
    }
    
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .photo-item {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        overflow: hidden;
    }
    
    .photo-item img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    
    .photo-item-label {
        padding: 10px;
        text-align: center;
        font-size: 12px;
        color: var(--text-secondary);
        background: #f9fafb;
    }
    
    .photo-item-label strong {
        display: block;
        font-size: 13px;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    
    .photo-datetime {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    
    .photo-datetime i {
        width: 14px;
        margin-right: 4px;
        color: #9ca3af;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }
    
    .action-btn-primary {
        background: var(--text-primary);
        color: white;
    }
    
    .action-btn-primary:hover {
        background: #374151;
        color: white;
    }
    
    .action-btn-outline {
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-primary);
    }
    
    .action-btn-outline:hover {
        background: #f9fafb;
        color: var(--text-primary);
    }
    
    .action-btn-success {
        background: #065f46;
        color: white;
    }
    
    .action-btn-success:hover {
        background: #047857;
    }
    
    .action-btn-danger {
        background: transparent;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    
    .action-btn-danger:hover {
        background: #fee2e2;
    }
    
    .form-label-minimal {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    
    .form-select-minimal,
    .form-control-minimal {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 13px;
        color: var(--text-primary);
        background: white;
        transition: border-color 0.2s ease;
    }
    
    .form-select-minimal:focus,
    .form-control-minimal:focus {
        outline: none;
        border-color: var(--text-secondary);
    }
    
    .breadcrumb-minimal {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        margin-bottom: 20px;
    }
    
    .breadcrumb-minimal a {
        color: var(--text-muted);
        text-decoration: none;
    }
    
    .breadcrumb-minimal a:hover {
        color: var(--text-primary);
    }
    
    .breadcrumb-minimal span {
        color: var(--text-muted);
    }
    
    .breadcrumb-minimal .current {
        color: var(--text-primary);
        font-weight: 500;
    }
    
    .payment-proof-img {
        width: 100%;
        max-height: 180px;
        object-fit: contain;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        margin-top: 12px;
    }
    
    .verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        color: #065f46;
        margin-top: 8px;
    }
</style>
@endpush

@section('content')
<div class="order-detail-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb-minimal">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.orders.index') }}">Pesanan</a>
        <span>/</span>
        <span class="current">{{ $order->order_number }}</span>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Order Info -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h6><i class="fas fa-file-alt"></i> Informasi Pesanan</h6>
                    @php
                        $statusClass = match($order->status) {
                            'pending_payment' => 'pending',
                            'processing' => 'processing',
                            'ready_to_ship' => 'processing',
                            'shipped' => 'delivery',
                            'delivered', 'completed' => 'success',
                            'cancelled' => 'danger',
                            default => ''
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $order->status_label }}</span>
                </div>
                <div class="detail-card-body">
                    @if($order->status === 'processing' && $order->payment_status === 'paid' && $order->courier_code)
                    <div class="alert alert-warning" style="font-size: 13px; padding: 12px; border-radius: 6px; margin-bottom: 16px; border-left: 4px solid #f59e0b;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Action Required:</strong> Pesanan sedang diproses. Silakan pack barang, lalu ubah status ke "Siap Pickup" atau langsung request pickup.
                    </div>
                    @endif
                    
                    @if($order->status === 'ready_to_ship' && $order->payment_status === 'paid' && $order->courier_code && !$order->biteship_order_id)
                    <div class="alert alert-info" style="font-size: 13px; padding: 12px; border-radius: 6px; margin-bottom: 16px; border-left: 4px solid #0ea5e9;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Siap Pickup:</strong> Barang sudah siap. Silakan request pickup ke ekspedisi di bawah.
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">No. Pesanan</span>
                                <span class="info-value">{{ $order->order_number }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Tanggal</span>
                                <span class="info-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($order->delivery_date)
                            <div class="info-row">
                                <span class="info-label">Jadwal Kirim</span>
                                <span class="info-value">{{ $order->formatted_delivery_date }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Waktu</span>
                                <span class="info-value">{{ $order->delivery_time_slot ?? '10:00 - 16:00' }} WIB</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Customer Info -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h6><i class="fas fa-user"></i> Data Customer</h6>
                </div>
                <div class="detail-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Nama</span>
                                <span class="info-value">{{ $order->user->name }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $order->user->email }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Telepon</span>
                                <span class="info-value">{{ $order->user->phone }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="section-title" style="margin-top: 0;">Alamat Pengiriman</div>
                            <div class="info-row">
                                <span class="info-label">Penerima</span>
                                <span class="info-value">{{ $order->shipping_name }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Telepon</span>
                                <span class="info-value">{{ $order->shipping_phone }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Alamat</span>
                                <span class="info-value">{{ $order->shipping_address }}</span>
                            </div>
                            @if($order->delivery_distance_minutes)
                            <div class="info-row">
                                <span class="info-label">Estimasi</span>
                                <span class="info-value">{{ $order->delivery_distance_minutes }} menit</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($order->notes)
                    <div class="note-box">
                        <div class="note-box-title">Catatan Pesanan</div>
                        <div class="note-box-content">{{ $order->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h6><i class="fas fa-box"></i> Item Pesanan</h6>
                </div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->formatted_price }}</td>
                            <td>{{ $item->formatted_subtotal }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>{{ $order->formatted_subtotal }}</span>
                </div>
                @if($order->product_discount > 0)
                    <div class="summary-row text-danger">
                        <span>Diskon Produk</span>
                        <span>-{{ $order->formatted_product_discount }}</span>
                    </div>
                @endif
                <div class="summary-row">
                    <span>Ongkir @if($order->delivery_distance_km)<small style="color: var(--text-muted);">({{ number_format($order->delivery_distance_km, 1) }} km)</small>@elseif($order->delivery_distance_minutes)<small style="color: var(--text-muted);">({{ $order->delivery_distance_minutes }} menit)</small>@endif</span>
                    <span>{{ $order->formatted_shipping_cost }}</span>
                </div>
                @if($order->shipping_discount > 0)
                    <div class="summary-row text-danger">
                        <span>Diskon Ongkir</span>
                        <span>-{{ $order->formatted_shipping_discount }}</span>
                    </div>
                @endif
                <div class="summary-row total">
                    <span>Total</span>
                    <span>{{ $order->formatted_total }}</span>
                </div>
            </div>

            <!-- Ekspedisi & Pickup -->
            @if($order->courier_code)
                @include('admin.orders._pickup_section')
            @else
                <div class="detail-card">
                    <div class="detail-card-header">
                        <h6><i class="fas fa-shipping-fast"></i> Ekspedisi & Pickup</h6>
                        <span class="status-badge pending">Belum Ada Ekspedisi</span>
                    </div>
                    <div class="detail-card-body">
                        <div class="alert alert-warning" style="font-size: 13px; padding: 12px; border-radius: 6px;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Customer belum memilih ekspedisi saat checkout.</strong>
                            <br><small style="color: var(--text-muted); margin-top: 4px; display: block;">Order ini dibuat sebelum sistem ekspedisi diimplementasikan atau customer skip pilih ekspedisi.</small>
                        </div>
                        
                        <div style="margin-top: 16px; padding: 12px; background: #f9fafb; border-radius: 6px; border: 1px solid var(--border-color);">
                            <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px;">Solusi:</div>
                            <ol style="font-size: 13px; color: var(--text-primary); margin: 0; padding-left: 20px;">
                                <li>Hubungi customer untuk konfirmasi alamat</li>
                                <li>Kirim paket manual ke ekspedisi</li>
                                <li>Input nomor resi manual di bawah setelah dapat resi</li>
                            </ol>
                        </div>
                        
                        <!-- Manual Input Resi -->
                        @if($order->payment_status === 'paid')
                        <div style="margin-top: 16px;">
                            <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px;">Input Nomor Resi Manual:</div>
                            <form action="{{ route('admin.orders.update-waybill', $order) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" class="form-control-minimal" name="waybill_id" placeholder="Masukkan nomor resi" required style="border-radius: 6px 0 0 6px;">
                                    <button type="submit" class="action-btn action-btn-outline" style="border-radius: 0 6px 6px 0; width: auto; padding: 0 16px;">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Courier Info (Legacy) -->
            @if($order->courier)
            <div class="detail-card">
                <div class="detail-card-header">
                    <h6><i class="fas fa-motorcycle"></i> Kurir</h6>
                    <span class="status-badge success">Ditugaskan</span>
                </div>
                <div class="detail-card-body">
                    <div class="courier-card mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $order->courier->avatar_url }}" alt="{{ $order->courier->name }}" 
                                 class="courier-avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $order->courier->name }}</div>
                                <div style="font-size: 13px; color: var(--text-muted);">{{ $order->courier->phone ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="section-title">Timeline Pengiriman</div>
                    
                    @if($order->assigned_at)
                    <div class="timeline-item">
                        <div class="d-flex align-items-center">
                            <div class="timeline-icon"><i class="fas fa-clipboard-list"></i></div>
                            <span class="timeline-label">Ditugaskan</span>
                        </div>
                        <span class="timeline-time">{{ $order->assigned_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($order->picked_up_at)
                    <div class="timeline-item">
                        <div class="d-flex align-items-center">
                            <div class="timeline-icon"><i class="fas fa-box"></i></div>
                            <span class="timeline-label">Barang Diambil</span>
                        </div>
                        <span class="timeline-time">{{ $order->picked_up_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($order->on_delivery_at)
                    <div class="timeline-item">
                        <div class="d-flex align-items-center">
                            <div class="timeline-icon"><i class="fas fa-truck"></i></div>
                            <span class="timeline-label">Dalam Perjalanan</span>
                        </div>
                        <span class="timeline-time">{{ $order->on_delivery_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($order->delivered_at)
                    <div class="timeline-item">
                        <div class="d-flex align-items-center">
                            <div class="timeline-icon" style="background: #d1fae5; color: #065f46;"><i class="fas fa-check"></i></div>
                            <span class="timeline-label">Terkirim</span>
                        </div>
                        <span class="timeline-time">{{ $order->delivered_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    
                    @if($order->delivery_notes)
                    <div class="note-box">
                        <div class="note-box-title">Catatan Kurir</div>
                        <div class="note-box-content">{{ $order->delivery_notes }}</div>
                    </div>
                    @endif
                    
                    @if($order->pickup_photo || $order->delivery_photo)
                    <div class="section-title" style="margin-top: 20px;">Foto Dokumentasi</div>
                    <div class="photo-grid">
                        @if($order->pickup_photo)
                        <div class="photo-item">
                            <a href="{{ config('filesystems.disks.r2.url').'/' . $order->pickup_photo }}" target="_blank">
                                <img src="{{ config('filesystems.disks.r2.url').'/' . $order->pickup_photo }}" alt="Foto Pengambilan">
                            </a>
                            <div class="photo-item-label">
                                <strong>Barang Diambil</strong>
                                @if($order->picked_up_at)
                                <div class="photo-datetime">
                                    <i class="fas fa-calendar"></i> {{ $order->picked_up_at->format('d M Y') }}
                                </div>
                                <div class="photo-datetime">
                                    <i class="fas fa-clock"></i> {{ $order->picked_up_at->format('H:i') }} WIB
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if($order->delivery_photo)
                        <div class="photo-item">
                            <a href="{{ config('filesystems.disks.r2.url').'/' . $order->delivery_photo }}" target="_blank">
                                <img src="{{ config('filesystems.disks.r2.url').'/' . $order->delivery_photo }}" alt="Foto Pengiriman">
                            </a>
                            <div class="photo-item-label">
                                <strong>Pesanan Diterima</strong>
                                @if($order->delivered_at)
                                <div class="photo-datetime">
                                    <i class="fas fa-calendar"></i> {{ $order->delivered_at->format('d M Y') }}
                                </div>
                                <div class="photo-datetime">
                                    <i class="fas fa-clock"></i> {{ $order->delivered_at->format('H:i') }} WIB
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Payment Status -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h6><i class="fas fa-credit-card"></i> Pembayaran</h6>
                    @php
                        $paymentClass = match($order->payment_status) {
                            'pending' => 'pending',
                            'pending_verification' => 'processing',
                            'paid' => 'success',
                            'failed' => 'danger',
                            default => ''
                        };
                    @endphp
                    <span class="status-badge {{ $paymentClass }}">{{ $order->payment_status_label }}</span>
                </div>
                <div class="detail-card-body">
                    <!-- Payment Method Info -->
                    @if($order->payment_method)
                        @if(strtolower($order->payment_method) === 'cod' || strtolower($order->payment_gateway) === 'cod')
                            <!-- COD Payment Info -->
                            <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                                <div style="display: flex; align-items: start; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: #fbbf24; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-hand-holding-usd" style="color: white; font-size: 18px;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 14px; font-weight: 600; color: #92400e; margin-bottom: 4px;">Cash on Delivery (COD)</div>
                                        <div style="font-size: 12px; color: #92400e; margin-bottom: 8px;">Pembayaran saat barang diterima</div>
                                        <div style="background: white; border-radius: 6px; padding: 10px; margin-bottom: 8px;">
                                            <div style="font-size: 11px; color: #92400e; margin-bottom: 4px;">Total yang harus dibayar:</div>
                                            <div style="font-size: 16px; font-weight: 700; color: #92400e;">{{ $order->formatted_total }}</div>
                                        </div>
                                        <div style="font-size: 11px; color: #92400e;">
                                            <div style="margin-bottom: 4px;"><i class="fas fa-info-circle" style="margin-right: 6px;"></i>Customer akan bayar ke kurir saat pengiriman</div>
                                            <div style="margin-bottom: 4px;"><i class="fas fa-check-circle" style="margin-right: 6px;"></i>Pastikan kurir collect payment</div>
                                            @if($order->status === 'processing' || $order->status === 'ready_to_ship')
                                            <div style="margin-bottom: 4px;"><i class="fas fa-clock" style="margin-right: 6px;"></i>Siapkan barang untuk dikirim</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Non-COD Payment Info -->
                            <div style="background: #dbeafe; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                                <div style="display: flex; align-items: start; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-credit-card" style="color: white; font-size: 18px;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 14px; font-weight: 600; color: #1e40af; margin-bottom: 4px;">
                                            {{ ucfirst($order->payment_gateway ?? 'Online Payment') }}
                                        </div>
                                        <div style="font-size: 12px; color: #1e40af; margin-bottom: 8px;">
                                            @if($order->payment_channel)
                                                {{ str_replace('_', ' ', ucwords($order->payment_channel, '_')) }}
                                            @else
                                                Pembayaran Online
                                            @endif
                                        </div>
                                        @if($order->payment_status === 'paid')
                                            <div style="background: #d1fae5; border: 1px solid #10b981; border-radius: 6px; padding: 10px; margin-bottom: 8px;">
                                                <div style="font-size: 11px; color: #065f46; margin-bottom: 4px;"><i class="fas fa-check-circle" style="margin-right: 6px;"></i>Pembayaran Berhasil</div>
                                                @if($order->paid_at)
                                                <div style="font-size: 11px; color: #065f46;"><i class="fas fa-calendar-check" style="margin-right: 6px;"></i>{{ $order->paid_at->format('d M Y, H:i') }} WIB</div>
                                                @endif
                                            </div>
                                        @elseif($order->payment_status === 'pending')
                                            <div style="background: white; border: 1px solid #fbbf24; border-radius: 6px; padding: 10px; margin-bottom: 8px;">
                                                <div style="font-size: 11px; color: #92400e;"><i class="fas fa-clock" style="margin-right: 6px;"></i>Menunggu pembayaran dari customer</div>
                                            </div>
                                        @elseif($order->payment_status === 'pending_verification')
                                            <div style="background: white; border: 1px solid #3b82f6; border-radius: 6px; padding: 10px; margin-bottom: 8px;">
                                                <div style="font-size: 11px; color: #1e40af;"><i class="fas fa-hourglass-half" style="margin-right: 6px;"></i>Menunggu verifikasi admin</div>
                                            </div>
                                        @endif
                                        <div style="background: white; border-radius: 6px; padding: 10px;">
                                            <div style="font-size: 11px; color: #1e40af; margin-bottom: 4px;">Total Pembayaran:</div>
                                            <div style="font-size: 16px; font-weight: 700; color: #1e40af;">{{ $order->formatted_total }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    @if($order->payment_proof)
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">Bukti Pembayaran:</div>
                        <a href="{{ config('filesystems.disks.r2.url').'/' . $order->payment_proof }}" target="_blank">
                            <img src="{{ config('filesystems.disks.r2.url').'/' . $order->payment_proof }}" class="payment-proof-img" alt="Bukti Pembayaran">
                        </a>
                        
                        @if($order->payment_status === 'pending_verification')
                        <div class="d-grid gap-2 mt-3">
                            <form action="{{ route('admin.orders.verify-payment', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="action-btn action-btn-success">
                                    <i class="fas fa-check"></i> Verifikasi Pembayaran
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.reject-payment', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="action-btn action-btn-danger" 
                                        onclick="return confirm('Tolak bukti pembayaran ini?')">
                                    <i class="fas fa-times"></i> Tolak Pembayaran
                                </button>
                            </form>
                        </div>
                        @endif
                    @elseif(!$order->payment_method || (strtolower($order->payment_method) !== 'cod' && strtolower($order->payment_gateway) !== 'cod'))
                        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Belum ada bukti pembayaran</p>
                    @endif

                    @if($order->payment_verified_at)
                    <div class="verified-badge">
                        <i class="fas fa-check-circle"></i>
                        Diverifikasi {{ $order->payment_verified_at->format('d/m/Y H:i') }}
                    </div>
                    @endif

                    @if($order->payment_gateway === 'paylabs' && $order->paylabs_transaction_id && $order->payment_status !== 'paid')
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                        <form action="{{ route('admin.orders.check-paylabs-status', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="action-btn action-btn-outline" style="width: 100%;">
                                <i class="fas fa-sync-alt"></i> Cek Status Pembayaran Paylabs
                            </button>
                        </form>
                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 8px; text-align: center;">
                            Gunakan ini jika customer sudah bayar tapi status belum update
                        </p>
                    </div>
                    @endif

                    @if($order->refund_status)
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                        <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px;">Refund Request</div>
                        <div style="font-size: 13px; margin-bottom: 8px;">
                            <span style="color: var(--text-muted);">Status:</span>
                            <span class="status-badge {{ in_array($order->refund_status, ['completed']) ? 'success' : (in_array($order->refund_status, ['failed','rejected']) ? 'danger' : 'pending') }}">
                                {{ ucfirst($order->refund_status) }}
                            </span>
                        </div>
                        @if($order->refund_amount)
                        <div style="font-size: 13px; margin-bottom: 8px;">
                            <span style="color: var(--text-muted);">Jumlah:</span>
                            <strong>Rp {{ number_format($order->refund_amount, 0, ',', '.') }}</strong>
                        </div>
                        @endif
                        @if($order->refund_note)
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px; background: #f9fafb; padding: 8px; border-radius: 6px;">
                            {{ $order->refund_note }}
                        </div>
                        @endif

                        @if($order->refund_status === 'pending')
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.orders.process-refund', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="action-btn action-btn-success" onclick="return confirm('Proses refund Rp {{ number_format($order->refund_amount, 0, \',\', \'.\') }} ke customer?')">
                                    <i class="fas fa-check"></i> Approve & Proses Refund
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.reject-refund', $order) }}" method="POST">
                                @csrf
                                <input type="text" name="reject_reason" class="form-control-minimal mb-2" placeholder="Alasan penolakan..." required>
                                <button type="submit" class="action-btn action-btn-danger" onclick="return confirm('Tolak refund request ini?')">
                                    <i class="fas fa-times"></i> Tolak Refund
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Update Status -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h6><i class="fas fa-edit"></i> Update Status</h6>
                </div>
                <div class="detail-card-body">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" id="statusForm">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label class="form-label-minimal">Status Pesanan</label>
                            <select class="form-select-minimal" name="status" id="statusSelect" onchange="toggleCancelReason()">
                                <option value="pending_payment" {{ $order->status == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Pesanan Diproses</option>
                                <option value="ready_to_ship" {{ $order->status == 'ready_to_ship' ? 'selected' : '' }}>Siap Pickup</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Dikirim Ekspedisi</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Sudah Sampai</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="cancelReasonDiv" style="display: none;">
                            <label class="form-label-minimal">Alasan Pembatalan</label>
                            <textarea class="form-control-minimal" name="cancel_reason" rows="3">{{ $order->cancel_reason }}</textarea>
                        </div>
                        
                        <button type="submit" class="action-btn action-btn-primary">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Print Receipt -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h6><i class="fas fa-print"></i> Dokumen</h6>
                </div>
                <div class="detail-card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.orders.receipt', $order) }}" class="action-btn action-btn-outline" target="_blank">
                            <i class="fas fa-eye"></i> Lihat Resi
                        </a>
                        <a href="{{ route('admin.orders.print-receipt', $order) }}" class="action-btn action-btn-outline">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Back Button -->
            <a href="{{ route('admin.orders.index') }}" class="action-btn action-btn-outline">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCancelReason() {
    const status = document.getElementById('statusSelect').value;
    const cancelDiv = document.getElementById('cancelReasonDiv');
    cancelDiv.style.display = status === 'cancelled' ? 'block' : 'none';
}

toggleCancelReason();
</script>
@endpush
