@extends('layouts.courier')

@section('title', 'Detail Pengiriman')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('courier.dashboard') }}" class="text-decoration-none" style="color: var(--primary);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('courier.deliveries.index') }}" class="text-decoration-none" style="color: var(--primary);">Pengiriman</a></li>
        <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0" style="font-weight: 700; color: var(--dark);">Detail Pengiriman #{{ $order->order_number }}</h4>
    </div>
    <a href="{{ route('courier.deliveries.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row">
    <!-- Order Details -->
    <div class="col-lg-8">
        <!-- Status Progress -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-truck me-2" style="color: var(--primary);"></i>Status Pengiriman
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between position-relative mb-4">
                    <!-- Progress Line -->
                    <div class="position-absolute" style="top: 15px; left: 30px; right: 30px; height: 3px; background: #e9ecef; z-index: 0;">
                        @php
                            $progress = match($order->status) {
                                \App\Models\Order::STATUS_ASSIGNED => '0%',
                                \App\Models\Order::STATUS_PICKED_UP => '33%',
                                \App\Models\Order::STATUS_ON_DELIVERY => '66%',
                                \App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_COMPLETED => '100%',
                                default => '0%'
                            };
                        @endphp
                        <div style="width: {{ $progress }}; transition: width 0.5s; background: var(--primary); height: 100%;"></div>
                    </div>
                    
                    <!-- Step 1: Assigned -->
                    <div class="text-center position-relative" style="z-index: 1;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 35px; height: 35px; {{ in_array($order->status, [\App\Models\Order::STATUS_ASSIGNED, \App\Models\Order::STATUS_PICKED_UP, \App\Models\Order::STATUS_ON_DELIVERY, \App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_COMPLETED]) ? 'background: var(--primary); color: white;' : 'background: #e9ecef; color: var(--gray);' }}">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="small mt-2 fw-bold">Ditugaskan</div>
                        @if($order->assigned_at)
                            <small class="text-muted">{{ $order->assigned_at->format('d/m H:i') }}</small>
                        @endif
                    </div>
                    
                    <!-- Step 2: Picked Up -->
                    <div class="text-center position-relative" style="z-index: 1;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 35px; height: 35px; {{ in_array($order->status, [\App\Models\Order::STATUS_PICKED_UP, \App\Models\Order::STATUS_ON_DELIVERY, \App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_COMPLETED]) ? 'background: var(--primary); color: white;' : 'background: #e9ecef; color: var(--gray);' }}">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="small mt-2 fw-bold">Diambil</div>
                        @if($order->picked_up_at)
                            <small class="text-muted">{{ $order->picked_up_at->format('d/m H:i') }}</small>
                        @endif
                    </div>
                    
                    <!-- Step 3: On Delivery -->
                    <div class="text-center position-relative" style="z-index: 1;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 35px; height: 35px; {{ in_array($order->status, [\App\Models\Order::STATUS_ON_DELIVERY, \App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_COMPLETED]) ? 'background: var(--primary); color: white;' : 'background: #e9ecef; color: var(--gray);' }}">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="small mt-2 fw-bold">Diantar</div>
                        @if($order->on_delivery_at)
                            <small class="text-muted">{{ $order->on_delivery_at->format('d/m H:i') }}</small>
                        @endif
                    </div>
                    
                    <!-- Step 4: Delivered -->
                    <div class="text-center position-relative" style="z-index: 1;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 35px; height: 35px; {{ in_array($order->status, [\App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_COMPLETED]) ? 'background: var(--primary); color: white;' : 'background: #e9ecef; color: var(--gray);' }}">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="small mt-2 fw-bold">Sampai</div>
                        @if($order->delivered_at)
                            <small class="text-muted">{{ $order->delivered_at->format('d/m H:i') }}</small>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4 pt-3 border-top">
                    @if($order->status === \App\Models\Order::STATUS_ASSIGNED)
                        <button type="button" class="btn btn-primary btn-lg px-5" data-bs-toggle="modal" data-bs-target="#pickupModal">
                            <i class="fas fa-hand-holding me-2"></i>Ambil Barang
                        </button>
                        <p class="text-muted small mt-2 mb-0">Klik tombol di atas dan foto barang setelah mengambil dari toko</p>
                    @elseif($order->status === \App\Models\Order::STATUS_PICKED_UP)
                        <form action="{{ route('courier.deliveries.start', $order) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-play me-2"></i>Mulai Pengiriman
                            </button>
                        </form>
                        <p class="text-muted small mt-2 mb-0">Klik tombol di atas untuk memulai pengiriman</p>
                        
                        @if($order->pickup_photo)
                        <div class="mt-3 text-start">
                            <strong class="d-block mb-2"><i class="fas fa-camera me-1" style="color: var(--primary);"></i>Foto Pengambilan:</strong>
                            <img src="{{ config('filesystems.disks.r2.url').'/' . $order->pickup_photo }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        @endif
                    @elseif($order->status === \App\Models\Order::STATUS_ON_DELIVERY)
                        <button type="button" class="btn btn-primary btn-lg px-5" data-bs-toggle="modal" data-bs-target="#deliveredModal">
                            <i class="fas fa-check me-2"></i>Pesanan Sudah Sampai
                        </button>
                        <p class="text-muted small mt-2 mb-0">Klik tombol di atas dan foto bukti pengiriman</p>
                        
                        @if($order->pickup_photo)
                        <div class="mt-3 text-start">
                            <strong class="d-block mb-2"><i class="fas fa-camera me-1" style="color: var(--primary);"></i>Foto Pengambilan:</strong>
                            <img src="{{ config('filesystems.disks.r2.url').'/' . $order->pickup_photo }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        @endif
                    @elseif($order->status === \App\Models\Order::STATUS_DELIVERED || $order->status === \App\Models\Order::STATUS_COMPLETED)
                        @if($order->canVerifyCod())
                            <!-- COD Verification Needed -->
                            <div class="alert mb-3" style="background: rgba(245, 158, 11, 0.1); border: 2px solid #f59e0b; color: #92400e;">
                                <strong><i class="fas fa-money-bill-wave me-2"></i>Konfirmasi Pembayaran COD</strong>
                                <p class="mb-2 mt-2">Barang sudah diterima customer. Pastikan pembayaran <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong> sudah diterima.</p>
                                <form action="{{ route('courier.deliveries.verify-cod', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin pembayaran COD sudah diterima?');">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-lg w-100">
                                        <i class="fas fa-check-circle me-2"></i>Konfirmasi COD Diterima
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="alert mb-0" style="background: rgba(37, 99, 235, 0.1); border: none; color: var(--primary);">
                                <i class="fas fa-trophy me-2"></i>
                                Pengiriman selesai! Terima kasih.
                                @if($order->isCod() && $order->cod_verified)
                                    <br><small class="text-muted"><i class="fas fa-check-circle text-success me-1"></i>Pembayaran COD sudah dikonfirmasi pada {{ $order->cod_verified_at->format('d/m/Y H:i') }}</small>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Show Photos -->
                        <div class="row mt-3 text-start">
                            @if($order->pickup_photo)
                            <div class="col-6">
                                <strong class="d-block mb-2"><i class="fas fa-box me-1" style="color: var(--primary);"></i>Foto Pengambilan:</strong>
                                <a href="{{ config('filesystems.disks.r2.url').'/' . $order->pickup_photo }}" target="_blank">
                                    <img src="{{ config('filesystems.disks.r2.url').'/' . $order->pickup_photo }}" class="img-thumbnail" style="max-height: 120px;">
                                </a>
                                @if($order->picked_up_at)
                                <small class="d-block text-muted mt-1">{{ $order->picked_up_at->format('d/m/Y H:i') }}</small>
                                @endif
                            </div>
                            @endif
                            @if($order->delivery_photo)
                            <div class="col-6">
                                <strong class="d-block mb-2"><i class="fas fa-check-circle me-1" style="color: #10b981;"></i>Foto Pengiriman:</strong>
                                <a href="{{ config('filesystems.disks.r2.url').'/' . $order->delivery_photo }}" target="_blank">
                                    <img src="{{ config('filesystems.disks.r2.url').'/' . $order->delivery_photo }}" class="img-thumbnail" style="max-height: 120px;">
                                </a>
                                @if($order->delivered_at)
                                <small class="d-block text-muted mt-1">{{ $order->delivered_at->format('d/m/Y H:i') }}</small>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-shopping-bag me-2" style="color: var(--primary);"></i>Item Pesanan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product)
                                                <img src="{{ $item->product->image_url }}" 
                                                     class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <span>{{ $item->product->name ?? 'Produk tidak tersedia' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end">Subtotal</td>
                                <td class="text-end">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Ongkir</td>
                                <td class="text-end">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end" style="color: var(--primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($order->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-sticky-note me-2" style="color: #f59e0b;"></i>Catatan Pesanan
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Customer Info -->
    <div class="col-lg-4">
        <!-- Customer Details -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user me-2" style="color: var(--primary);"></i>Info Customer
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->user->name) }}&background=2563eb&color=fff" 
                         class="rounded-circle me-3" style="width: 50px; height: 50px;">
                    <div>
                        <h6 class="mb-0" style="font-weight: 600;">{{ $order->user->name }}</h6>
                        <small class="text-muted">{{ $order->user->email }}</small>
                    </div>
                </div>
                @if($order->user->phone)
                    <div class="mb-2">
                        <i class="fas fa-phone me-2" style="color: var(--primary);"></i>
                        <a href="tel:{{ $order->user->phone }}" class="text-decoration-none" style="color: var(--dark);">{{ $order->user->phone }}</a>
                    </div>
                @endif
                @if($order->user->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->user->phone) }}" 
                       class="btn btn-sm w-100 mt-2" target="_blank" style="background: #25d366; color: white;">
                        <i class="fab fa-whatsapp me-1"></i>Hubungi via WhatsApp
                    </a>
                @endif
            </div>
        </div>

        <!-- Delivery Info -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-map-marker-alt me-2" style="color: #ef4444;"></i>Info Pengiriman
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small mb-1">Alamat Tujuan</div>
                    <div style="font-weight: 500;">{{ $order->delivery_address }}</div>
                </div>
                @if($order->delivery_latitude && $order->delivery_longitude)
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Koordinat</div>
                        <div class="small">{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}</div>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->delivery_latitude }},{{ $order->delivery_longitude }}" 
                           class="btn btn-outline-primary btn-sm w-100 mt-2" target="_blank">
                            <i class="fas fa-directions me-1"></i>Buka di Google Maps
                        </a>
                    </div>
                @endif
                <hr>
                <div class="mb-3">
                    <div class="text-muted small mb-1">Tanggal Pengiriman</div>
                    <div style="font-weight: 600; color: var(--dark);">
                        <i class="fas fa-calendar me-1" style="color: var(--primary);"></i>
                        {{ $order->delivery_date->isoFormat('dddd, D MMMM Y') }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small mb-1">Waktu Pengiriman</div>
                    <div style="font-weight: 600; color: var(--dark);">
                        <i class="fas fa-clock me-1" style="color: var(--primary);"></i>
                        {{ $order->delivery_time }}
                    </div>
                </div>
                <div>
                    <div class="text-muted small mb-1">Metode Pembayaran</div>
                    <div>
                        @if($order->payment_method === 'cod')
                            <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <i class="fas fa-money-bill-wave me-1"></i>Bayar di Tempat (COD)
                            </span>
                        @else
                            <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="fas fa-credit-card me-1"></i>Transfer Bank
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($order->delivery_notes)
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clipboard me-2" style="color: var(--primary);"></i>Catatan Pengiriman
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->delivery_notes }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Pickup dengan Foto -->
@if($order->status === \App\Models\Order::STATUS_ASSIGNED)
<div class="modal fade" id="pickupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('courier.deliveries.pickup', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="background: var(--primary); color: white;">
                    <h5 class="modal-title"><i class="fas fa-camera me-2"></i>Foto Pengambilan Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert" style="background: rgba(37, 99, 235, 0.1); border: none; color: var(--primary);">
                        <i class="fas fa-info-circle me-1"></i>
                        Ambil foto barang yang sudah siap dikirim sebagai bukti pengambilan.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Pengambilan Barang <span class="text-danger">*</span></label>
                        
                        <!-- Camera Capture Only -->
                        <div class="camera-capture-wrapper">
                            <video id="pickupCameraStream" class="camera-preview d-none" autoplay playsinline></video>
                            <canvas id="pickupCanvas" class="d-none"></canvas>
                            <input type="hidden" name="pickup_photo_base64" id="pickupPhotoBase64">
                            
                            <div id="pickupCapturedPreview" class="captured-preview d-none">
                                <img id="pickupCapturedImg" src="" class="img-fluid rounded">
                            </div>
                            
                            <div class="camera-buttons">
                                <button type="button" class="btn btn-primary w-100" id="startPickupCamera">
                                    <i class="fas fa-camera me-2"></i>Buka Kamera
                                </button>
                                <button type="button" class="btn btn-success w-100 d-none" id="capturePickupPhoto">
                                    <i class="fas fa-circle me-2"></i>Ambil Foto
                                </button>
                                <button type="button" class="btn btn-warning w-100 d-none" id="retakePickupPhoto">
                                    <i class="fas fa-redo me-2"></i>Ulangi Foto
                                </button>
                            </div>
                        </div>
                        <div class="form-text text-danger"><i class="fas fa-exclamation-circle me-1"></i>Foto harus diambil langsung dari kamera</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i>Konfirmasi Pengambilan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Delivered dengan Foto -->
@if($order->status === \App\Models\Order::STATUS_ON_DELIVERY)
<div class="modal fade" id="deliveredModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('courier.deliveries.delivered', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="background: #10b981; color: white;">
                    <h5 class="modal-title"><i class="fas fa-camera me-2"></i>Foto Bukti Pengiriman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert" style="background: rgba(16, 185, 129, 0.1); border: none; color: #10b981;">
                        <i class="fas fa-info-circle me-1"></i>
                        Ambil foto barang yang sudah diterima customer sebagai bukti pengiriman.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Bukti Pengiriman <span class="text-danger">*</span></label>
                        
                        <!-- Camera Capture Only -->
                        <div class="camera-capture-wrapper">
                            <video id="deliveryCameraStream" class="camera-preview d-none" autoplay playsinline></video>
                            <canvas id="deliveryCanvas" class="d-none"></canvas>
                            <input type="hidden" name="delivery_photo_base64" id="deliveryPhotoBase64">
                            
                            <div id="deliveryCapturedPreview" class="captured-preview d-none">
                                <img id="deliveryCapturedImg" src="" class="img-fluid rounded">
                            </div>
                            
                            <div class="camera-buttons">
                                <button type="button" class="btn btn-success w-100" id="startDeliveryCamera">
                                    <i class="fas fa-camera me-2"></i>Buka Kamera
                                </button>
                                <button type="button" class="btn btn-primary w-100 d-none" id="captureDeliveryPhoto">
                                    <i class="fas fa-circle me-2"></i>Ambil Foto
                                </button>
                                <button type="button" class="btn btn-warning w-100 d-none" id="retakeDeliveryPhoto">
                                    <i class="fas fa-redo me-2"></i>Ulangi Foto
                                </button>
                            </div>
                        </div>
                        <div class="form-text text-danger"><i class="fas fa-exclamation-circle me-1"></i>Foto harus diambil langsung dari kamera</div>
                    </div>
                    
                    <!-- Preview dihapus karena sudah ada di camera capture -->
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Pengiriman (Opsional)</label>
                        <textarea name="delivery_notes" class="form-control" rows="2" 
                                  placeholder="Contoh: Diterima oleh ibu di depan rumah"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" style="background: #10b981; color: white;">
                        <i class="fas fa-check me-1"></i>Pesanan Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .camera-capture-wrapper {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        background: #f8f9fa;
    }
    
    .camera-preview {
        width: 100%;
        max-height: 300px;
        border-radius: 8px;
        background: #000;
        margin-bottom: 1rem;
    }
    
    .captured-preview {
        margin-bottom: 1rem;
    }
    
    .captured-preview img {
        max-height: 250px;
        border: 3px solid #10b981;
        border-radius: 8px;
    }
    
    .camera-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
// Camera Capture untuk Pickup Photo
let pickupStream = null;

document.getElementById('startPickupCamera')?.addEventListener('click', async function() {
    try {
        pickupStream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        });
        
        const video = document.getElementById('pickupCameraStream');
        video.srcObject = pickupStream;
        video.classList.remove('d-none');
        
        document.getElementById('startPickupCamera').classList.add('d-none');
        document.getElementById('capturePickupPhoto').classList.remove('d-none');
        document.getElementById('pickupCapturedPreview').classList.add('d-none');
        document.getElementById('retakePickupPhoto').classList.add('d-none');
    } catch (err) {
        alert('Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.');
        console.error('Error accessing camera:', err);
    }
});

document.getElementById('capturePickupPhoto')?.addEventListener('click', function() {
    const video = document.getElementById('pickupCameraStream');
    const canvas = document.getElementById('pickupCanvas');
    const context = canvas.getContext('2d');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);
    
    const imageData = canvas.toDataURL('image/jpeg', 0.8);
    document.getElementById('pickupPhotoBase64').value = imageData;
    document.getElementById('pickupCapturedImg').src = imageData;
    
    // Stop camera stream
    if (pickupStream) {
        pickupStream.getTracks().forEach(track => track.stop());
    }
    
    video.classList.add('d-none');
    document.getElementById('capturePickupPhoto').classList.add('d-none');
    document.getElementById('pickupCapturedPreview').classList.remove('d-none');
    document.getElementById('retakePickupPhoto').classList.remove('d-none');
});

document.getElementById('retakePickupPhoto')?.addEventListener('click', function() {
    document.getElementById('pickupPhotoBase64').value = '';
    document.getElementById('pickupCapturedPreview').classList.add('d-none');
    document.getElementById('retakePickupPhoto').classList.add('d-none');
    document.getElementById('startPickupCamera').classList.remove('d-none');
});

// Camera Capture untuk Delivery Photo
let deliveryStream = null;

document.getElementById('startDeliveryCamera')?.addEventListener('click', async function() {
    try {
        deliveryStream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        });
        
        const video = document.getElementById('deliveryCameraStream');
        video.srcObject = deliveryStream;
        video.classList.remove('d-none');
        
        document.getElementById('startDeliveryCamera').classList.add('d-none');
        document.getElementById('captureDeliveryPhoto').classList.remove('d-none');
        document.getElementById('deliveryCapturedPreview').classList.add('d-none');
        document.getElementById('retakeDeliveryPhoto').classList.add('d-none');
    } catch (err) {
        alert('Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.');
        console.error('Error accessing camera:', err);
    }
});

document.getElementById('captureDeliveryPhoto')?.addEventListener('click', function() {
    const video = document.getElementById('deliveryCameraStream');
    const canvas = document.getElementById('deliveryCanvas');
    const context = canvas.getContext('2d');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);
    
    const imageData = canvas.toDataURL('image/jpeg', 0.8);
    document.getElementById('deliveryPhotoBase64').value = imageData;
    document.getElementById('deliveryCapturedImg').src = imageData;
    
    // Stop camera stream
    if (deliveryStream) {
        deliveryStream.getTracks().forEach(track => track.stop());
    }
    
    video.classList.add('d-none');
    document.getElementById('captureDeliveryPhoto').classList.add('d-none');
    document.getElementById('deliveryCapturedPreview').classList.remove('d-none');
    document.getElementById('retakeDeliveryPhoto').classList.remove('d-none');
});

document.getElementById('retakeDeliveryPhoto')?.addEventListener('click', function() {
    document.getElementById('deliveryPhotoBase64').value = '';
    document.getElementById('deliveryCapturedPreview').classList.add('d-none');
    document.getElementById('retakeDeliveryPhoto').classList.add('d-none');
    document.getElementById('startDeliveryCamera').classList.remove('d-none');
});

// Validasi form sebelum submit
document.querySelector('#pickupModal form')?.addEventListener('submit', function(e) {
    const photoBase64 = document.getElementById('pickupPhotoBase64').value;
    if (!photoBase64) {
        e.preventDefault();
        alert('Silakan ambil foto pengambilan barang terlebih dahulu!');
        return false;
    }
});

document.querySelector('#deliveredModal form')?.addEventListener('submit', function(e) {
    const photoBase64 = document.getElementById('deliveryPhotoBase64').value;
    if (!photoBase64) {
        e.preventDefault();
        alert('Silakan ambil foto bukti pengiriman terlebih dahulu!');
        return false;
    }
});

// Stop camera when modal is closed
document.getElementById('pickupModal')?.addEventListener('hidden.bs.modal', function() {
    if (pickupStream) {
        pickupStream.getTracks().forEach(track => track.stop());
        pickupStream = null;
    }
    document.getElementById('pickupCameraStream').classList.add('d-none');
    document.getElementById('startPickupCamera').classList.remove('d-none');
    document.getElementById('capturePickupPhoto').classList.add('d-none');
});

document.getElementById('deliveredModal')?.addEventListener('hidden.bs.modal', function() {
    if (deliveryStream) {
        deliveryStream.getTracks().forEach(track => track.stop());
        deliveryStream = null;
    }
    document.getElementById('deliveryCameraStream').classList.add('d-none');
    document.getElementById('startDeliveryCamera').classList.remove('d-none');
    document.getElementById('captureDeliveryPhoto').classList.add('d-none');
});

// ==========================================
// REAL-TIME LOCATION TRACKING FOR COURIER
// ==========================================
@if($order->status === \App\Models\Order::STATUS_ON_DELIVERY)
let locationWatchId = null;
let lastLocationUpdate = 0;
const UPDATE_INTERVAL = 5000; // Update every 5 seconds

function startLocationTracking() {
    if (!navigator.geolocation) {
        console.log('Geolocation is not supported by this browser.');
        return;
    }
    
    // Request permission and start watching position
    locationWatchId = navigator.geolocation.watchPosition(
        function(position) {
            const now = Date.now();
            // Only send update every 5 seconds to reduce server load
            if (now - lastLocationUpdate >= UPDATE_INTERVAL) {
                sendLocationUpdate(position);
                lastLocationUpdate = now;
            }
        },
        function(error) {
            console.log('Geolocation error:', error.message);
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
    
    console.log('Location tracking started for order #{{ $order->order_number }}');
}

function sendLocationUpdate(position) {
    const data = {
        order_id: {{ $order->id }},
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        heading: position.coords.heading || 0,
        speed: position.coords.speed || 0
    };
    
    fetch('{{ route("courier.location.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            console.log('Location updated:', data.latitude, data.longitude);
        }
    })
    .catch(error => {
        console.log('Failed to send location:', error);
    });
}

function stopLocationTracking() {
    if (locationWatchId !== null) {
        navigator.geolocation.clearWatch(locationWatchId);
        locationWatchId = null;
        console.log('Location tracking stopped');
    }
}

// Start tracking when page loads
document.addEventListener('DOMContentLoaded', function() {
    startLocationTracking();
});

// Stop tracking when page is closed
window.addEventListener('beforeunload', function() {
    stopLocationTracking();
});
@endif
</script>
@endpush
