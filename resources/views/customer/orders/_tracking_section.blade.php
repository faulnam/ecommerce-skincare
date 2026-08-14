@if($order->courier_code && $order->waybill_id)
@php
    $jsonPath = public_path('translation/tracking.json');
    $trackTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

<div class="order-card">
    <div class="order-card-header">
        <h6><i class="fas fa-shipping-fast"></i> {{ $trackTrans['card_title'][$lang] ?? 'Tracking Pengiriman' }}</h6>
    </div>
    <div class="order-card-body">
        <div class="shipping-info-box">
            <div class="row">
                <div class="col-6">
                    <div class="info-label">{{ $trackTrans['label_courier'][$lang] ?? 'Ekspedisi' }}</div>
                    <div class="info-value">{{ $order->courier_name }}</div>
                </div>
                <div class="col-6">
                    <div class="info-label">{{ $trackTrans['label_service'][$lang] ?? 'Layanan' }}</div>
                    <div class="info-value">{{ $order->courier_service_name }}</div>
                </div>
            </div>
            <div class="resi-box">
                <div class="info-label">{{ $trackTrans['label_receipt'][$lang] ?? 'Nomor Resi' }}</div>
                <div class="resi-number">{{ $order->waybill_id }}</div>
                <button type="button" class="btn-copy-resi" onclick="copyResi('{{ $order->waybill_id }}')">
                    <i class="fas fa-copy"></i> {{ $trackTrans['btn_copy'][$lang] ?? 'Salin' }}
                </button>
            </div>
        </div>

        <div class="tracking-section">
            <button type="button" class="btn-load-tracking" onclick="loadCustomerTracking()">
                <i class="fas fa-map-marker-alt"></i> {{ $trackTrans['btn_load'][$lang] ?? 'Lihat Status Pengiriman' }}
            </button>
            
            <div id="customerTrackingResult" style="display: none; margin-top: 20px;">
                <div class="tracking-title">{{ $trackTrans['timeline_title'][$lang] ?? 'Status Pengiriman' }}</div>
                <div id="customerTrackingContent"></div>
            </div>
        </div>
    </div>
</div>

<style>
.shipping-info-box {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
}
.info-label {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 4px;
}
.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}
.resi-box {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}
.resi-number {
    font-size: 18px;
    font-weight: 700;
    color: #003C52; /* Menyerasikan dengan palet warna utama corporate branding kamu */
    letter-spacing: 1px;
    margin: 8px 0;
}
.btn-copy-resi {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 12px;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-copy-resi:hover {
    background: #f9fafb;
    border-color: #003C52;
    color: #003C52;
}
.btn-load-tracking {
    width: 100%;
    background: #003C52;
    color: white;
    border: none;
    border-radius: 12px;
    padding: 14px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-load-tracking:hover {
    background: #004b66;
    transform: scale(1.01);
}
.tracking-title {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e5e7eb;
}
.tracking-timeline {
    position: relative;
}
.tracking-item {
    display: flex;
    gap: 16px;
    padding: 16px 0;
    position: relative;
}
.tracking-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 15px;
    top: 48px;
    bottom: -16px;
    width: 2px;
    background: #e5e7eb;
}
.tracking-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f3f4f6;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
.tracking-item.active .tracking-icon {
    background: #003C52;
    color: white;
}
.tracking-content {
    flex: 1;
}
.tracking-status {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}
.tracking-note {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 4px;
}
.tracking-time {
    font-size: 12px;
    color: #9ca3af;
}
</style>

<script>
function copyResi(resi) {
    navigator.clipboard.writeText(resi).then(() => {
        alert("{{ $trackTrans['js_copied'][$lang] ?? 'Nomor resi berhasil disalin!' }}");
    });
}

function loadCustomerTracking() {
    const resultDiv = document.getElementById('customerTrackingResult');
    const contentDiv = document.getElementById('customerTrackingContent');
    
    resultDiv.style.display = 'block';
    contentDiv.innerHTML = `<div style="text-align: center; padding: 30px; color: #6b7280;"><i class="fas fa-spinner fa-spin me-2"></i>{{ $trackTrans['js_loading'][$lang] ?? 'Memuat data tracking...' }}</div>`;
    
    fetch('{{ route("customer.orders.tracking", $order) }}')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                contentDiv.innerHTML = '<div style="background: #fef3c7; border: 1px solid #fbbf24; color: #92400e; padding: 12px; border-radius: 8px; font-size: 13px;">Gagal memuat tracking: ' + data.message + '</div>';
                return;
            }
            
            const tracking = data.data;
            let html = '<div class="tracking-timeline">';
            
            if (tracking.history && tracking.history.length > 0) {
                tracking.history.forEach((item, index) => {
                    const isActive = index === 0;
                    html += `
                    <div class="tracking-item ${isActive ? 'active' : ''}">
                        <div class="tracking-icon">
                            <i class="fas fa-${isActive ? 'check' : 'circle'}"></i>
                        </div>
                        <div class="tracking-content">
                            <div class="tracking-status">${item.note || item.status}</div>
                            \${item.service_type ? '<div class="tracking-note">' + item.service_type + '</div>' : ''}
                            \${item.receiver_name ? '<div class="tracking-note">{{ $trackTrans['js_received'][$lang] ?? 'Diterima oleh:' }} ' + item.receiver_name + '</div>' : ''}
                            <div class="tracking-time">\${item.updated_at || item.created_at}</div>
                        </div>
                    </div>`;
                });
            } else {
                html = `<div style="text-align: center; padding: 30px; color: #6b7280;">{{ $trackTrans['js_empty'][$lang] ?? 'Belum ada data tracking' }}</div>`;
            }
            
            html += '</div>';
            contentDiv.innerHTML = html;
        })
        .catch(error => {
            contentDiv.innerHTML = '<div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 13px;">Error: ' + error.message + '</div>';
        });
}
</script>
@endif