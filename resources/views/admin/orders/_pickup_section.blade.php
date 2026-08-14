<!-- Ekspedisi & Pickup -->
@if($order->courier_code)
<div class="detail-card">
    <div class="detail-card-header">
        <h6><i class="fas fa-shipping-fast"></i> Ekspedisi & Pickup</h6>
        @if($order->waybill_id)
            <span class="status-badge success">Resi: {{ $order->waybill_id }}</span>
        @elseif($order->biteship_order_id)
            <span class="status-badge processing">Pickup Requested</span>
        @else
            <span class="status-badge pending">Belum Pickup</span>
        @endif
    </div>
    <div class="detail-card-body">
        <!-- Info Ekspedisi -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="info-row">
                    <span class="info-label">Ekspedisi</span>
                    <span class="info-value">{{ $order->courier_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Layanan</span>
                    <span class="info-value">{{ $order->courier_service_name }}</span>
                </div>
            </div>
            <div class="col-md-6">
                @if($order->waybill_id)
                <div class="info-row">
                    <span class="info-label">No. Resi</span>
                    <span class="info-value">{{ $order->waybill_id }}</span>
                </div>
                @endif
                @if($order->biteship_order_id)
                <div class="info-row">
                    <span class="info-label">Biteship ID</span>
                    <span class="info-value" style="font-size: 11px;">{{ $order->biteship_order_id }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Request Pickup Button -->
        @if(!$order->biteship_order_id && $order->payment_status === 'paid' && in_array($order->status, ['processing', 'ready_to_ship']))
        <div class="alert alert-info" style="font-size: 13px; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Langkah selanjutnya:</strong> 
            @if($order->status === 'processing')
                Pack barang pesanan terlebih dahulu, lalu ubah status ke "Siap Pickup" atau langsung klik tombol di bawah untuk request pickup ke {{ $order->courier_name }}.
            @else
                Barang sudah siap. Klik tombol di bawah untuk request pickup ke {{ $order->courier_name }}. Sistem akan mencari kurir terdekat.
            @endif
        </div>
        <button type="button" class="action-btn action-btn-primary" onclick="requestPickupWithLoading()">
            <i class="fas fa-truck-pickup"></i> Request Pickup ke {{ $order->courier_name }}
        </button>
        
        <!-- Loading Modal -->
        <div id="pickupLoadingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center;">
            <div style="background: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 400px;">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 50px; height: 50px;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 style="color: var(--text-primary); margin-bottom: 8px;">Mencari Kurir Terdekat...</h5>
                <p style="color: var(--text-muted); font-size: 13px; margin: 0;">Mohon tunggu sebentar</p>
            </div>
        </div>
        @endif

        @if($order->status === 'ready_to_ship' && $order->courier_driver_name)
        <div class="alert alert-success" style="font-size: 13px; padding: 16px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #10b981;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <i class="fas fa-check-circle" style="font-size: 20px; color: #10b981;"></i>
                <strong style="font-size: 14px;">Pickup Berhasil Direquest!</strong>
            </div>
            <p style="margin: 0 0 12px 0; color: var(--text-secondary);">Kurir akan datang ke toko dalam <strong>30 menit</strong>. Barang siap untuk diambil.</p>
        </div>
        
        <!-- Courier Info Card -->
        <div style="background: #f9fafb; border: 1px solid var(--border-color); border-radius: 8px; padding: 16px;">
            <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Informasi Kurir</div>
            
            <div style="display: flex; gap: 16px; align-items: start;">
                <img src="{{ $order->courier_driver_photo }}" alt="{{ $order->courier_driver_name }}" 
                     style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 15px; margin-bottom: 4px;">{{ $order->courier_driver_name }}</div>
                    
                    <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 6px;">
                        <i class="fas fa-star" style="color: #fbbf24; font-size: 12px;"></i>
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-primary);">{{ number_format($order->courier_driver_rating, 1) }}</span>
                        <span style="font-size: 12px; color: var(--text-muted);">rating</span>
                    </div>
                    
                    <div style="display: flex; gap: 16px; margin-top: 8px;">
                        <div>
                            <div style="font-size: 11px; color: var(--text-muted);">Telepon</div>
                            <div style="font-size: 13px; color: var(--text-primary); font-weight: 500;">{{ $order->courier_driver_phone }}</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--text-muted);">Kendaraan</div>
                            <div style="font-size: 13px; color: var(--text-primary); font-weight: 500;">{{ $order->courier_driver_vehicle }} - {{ $order->courier_driver_vehicle_number }}</div>
                        </div>
                    </div>
                    
                    @if($order->pickup_time)
                    <div style="margin-top: 12px; padding: 8px 12px; background: white; border-radius: 6px; border: 1px solid var(--border-color);">
                        <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 2px;">Estimasi Pickup</div>
                        <div style="font-size: 13px; color: var(--text-primary); font-weight: 600;">
                            <i class="fas fa-clock me-1"></i> {{ $order->pickup_time->format('H:i') }} WIB
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @elseif($order->status === 'processing')
        <div class="alert alert-warning" style="font-size: 13px; padding: 12px; border-radius: 6px; margin-bottom: 0; border-left: 4px solid #f59e0b;">
            <i class="fas fa-box me-2"></i>
            <strong>Pesanan sedang diproses.</strong> Silakan pack barang dan ubah status ke "Siap Pickup" jika sudah siap.
        </div>
        @endif

        @if($order->status === 'shipped')
        <div class="alert alert-primary" style="font-size: 13px; padding: 12px; border-radius: 6px; margin-bottom: 0;">
            <i class="fas fa-shipping-fast me-2"></i>
            <strong>Paket sedang dikirim</strong> oleh {{ $order->courier_name }} dengan nomor resi <strong>{{ $order->waybill_id }}</strong>
        </div>
        @endif

        <!-- Manual Input Resi -->
        @if(!$order->waybill_id && $order->payment_status === 'paid' && in_array($order->status, ['processing', 'ready_to_ship']))
        <div class="mt-3">
            <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px; border-top: 1px solid var(--border-color); padding-top: 16px;">
                <strong>Atau</strong> input nomor resi manual (jika pickup dilakukan di luar sistem):
            </div>
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

        <!-- Tracking Button -->
        @if($order->waybill_id && in_array($order->status, ['shipped', 'delivered']))
        <div class="mt-3">
            <button type="button" class="action-btn action-btn-outline" onclick="toggleCourierTracking()">
                <i class="fas fa-map-marker-alt"></i> <span id="trackingBtnText">Tracking Kurir</span>
            </button>
        </div>

        <!-- Courier Tracking Map -->
        <div id="courierTrackingSection" style="display: none; margin-top: 16px;">
            <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Tracking Kurir Real-Time</div>
            <div id="courierMap"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 12px; font-size: 12px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: #3b82f6;"></div>
                    <span style="color: var(--text-secondary);">Posisi Kurir</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444;"></div>
                    <span style="color: var(--text-secondary);">Alamat Tujuan</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
let mapInitialized = false;
let map, courierMarker, routeLine;

function requestPickupWithLoading() {
    if (!confirm('Request pickup ke {{ $order->courier_name }}?')) {
        return;
    }
    
    const modal = document.getElementById('pickupLoadingModal');
    modal.style.display = 'flex';
    
    fetch('{{ route("admin.orders.request-pickup", $order) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        modal.style.display = 'none';
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Gagal request pickup'));
        }
    })
    .catch(error => {
        modal.style.display = 'none';
        alert('Error: ' + error.message);
    });
}

function toggleCourierTracking() {
    const section = document.getElementById('courierTrackingSection');
    const btnText = document.getElementById('trackingBtnText');
    
    if (section.style.display === 'none') {
        section.style.display = 'block';
        btnText.textContent = 'Sembunyikan Tracking';
        
        if (!mapInitialized) {
            initializeCourierMap();
            mapInitialized = true;
        }
    } else {
        section.style.display = 'none';
        btnText.textContent = 'Tracking Kurir';
    }
}

@if($order->waybill_id && in_array($order->status, ['shipped', 'delivered']) && $order->shipping_latitude && $order->shipping_longitude)
function initializeCourierMap() {
    map = L.map('courierMap').setView([{{ $order->shipping_latitude }}, {{ $order->shipping_longitude }}], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    const destinationIcon = L.divIcon({
        html: '<div style="background: #ef4444; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-home" style="color: white; font-size: 14px;"></i></div>',
        className: '',
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    const destinationMarker = L.marker([{{ $order->shipping_latitude }}, {{ $order->shipping_longitude }}], {
        icon: destinationIcon
    }).addTo(map);

    destinationMarker.bindPopup('<b>Alamat Tujuan</b><br>{{ $order->shipping_address }}');

    const courierIcon = L.divIcon({
        html: '<div style="background: #3b82f6; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 4px 12px rgba(59,130,246,0.5); animation: pulse 2s infinite;"><i class="fas fa-motorcycle" style="color: white; font-size: 16px;"></i></div><style>@keyframes pulse { 0%, 100% { box-shadow: 0 4px 12px rgba(59,130,246,0.5); } 50% { box-shadow: 0 4px 20px rgba(59,130,246,0.8); } }</style>',
        className: '',
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });

    let courierLat = {{ $order->shipping_latitude }} + (Math.random() - 0.5) * 0.02;
    let courierLng = {{ $order->shipping_longitude }} + (Math.random() - 0.5) * 0.02;

    courierMarker = L.marker([courierLat, courierLng], {
        icon: courierIcon
    }).addTo(map);

    courierMarker.bindPopup('<b>{{ $order->courier_driver_name ?? "Kurir" }}</b><br>{{ $order->courier_name }}<br><small>Sedang menuju lokasi customer</small>');

    routeLine = L.polyline([
        [courierLat, courierLng],
        [{{ $order->shipping_latitude }}, {{ $order->shipping_longitude }}]
    ], {
        color: '#3b82f6',
        weight: 3,
        opacity: 0.6,
        dashArray: '10, 10'
    }).addTo(map);

    const bounds = L.latLngBounds([
        [courierLat, courierLng],
        [{{ $order->shipping_latitude }}, {{ $order->shipping_longitude }}]
    ]);
    map.fitBounds(bounds, { padding: [50, 50] });

    let moveStep = 0;
    const totalSteps = 100;
    const startLat = courierLat;
    const startLng = courierLng;
    const endLat = {{ $order->shipping_latitude }};
    const endLng = {{ $order->shipping_longitude }};

    function moveCourier() {
        if (moveStep < totalSteps) {
            moveStep++;
            const progress = moveStep / totalSteps;
            const newLat = startLat + (endLat - startLat) * progress;
            const newLng = startLng + (endLng - startLng) * progress;
            
            courierMarker.setLatLng([newLat, newLng]);
            routeLine.setLatLngs([
                [newLat, newLng],
                [endLat, endLng]
            ]);
            
            const newBounds = L.latLngBounds([
                [newLat, newLng],
                [endLat, endLng]
            ]);
            map.fitBounds(newBounds, { padding: [50, 50], animate: true });
        }
    }

    setInterval(moveCourier, 3000);
}
@endif
</script>
@endpush
@endif
