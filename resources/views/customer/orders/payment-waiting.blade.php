@extends('layouts.app')

@php
    $jsonPath = public_path('translation/payment.json');
    $payTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@section('title', ($payTrans['wait_title'][$lang] ?? 'Waiting for Payment') . ' - Hijab')

@push('styles')
<style>
    .payment-waiting-page {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }
    .waiting-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        text-align: center;
        padding: 2.5rem 2rem;
    }
    .payment-status-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 1.25rem;
    }
    .status-pending {
        background: #fef3c7;
        color: #d97706;
    }
    .status-success {
        background: #dcfce7;
        color: #166534;
    }
    .payment-amount {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    .payment-method-badge {
        display: inline-block;
        background: #f3f4f6;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 1.5rem;
    }
    .qr-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.5rem;
        display: inline-block;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .qr-container canvas {
        display: block;
    }
    .va-number-box {
        background: #f9fafb;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
    }
    .va-number {
        font-size: 24px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        color: #1f2937;
        letter-spacing: 2px;
    }
    .btn-copy {
        background: #003C52;
        color: white;
        border: none;
        border-radius: 999px;
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-copy:hover {
        background: #002533;
        color: white;
    }
    .timer-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 1.5rem;
    }
    .timer {
        font-size: 22px;
        font-weight: 700;
        color: #92400e;
    }
    .instruction-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        margin-top: 1.5rem;
        text-align: left;
        overflow: hidden;
    }
    .instruction-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #1f2937;
    }
    .instruction-body {
        padding: 1.5rem;
    }
    .instruction-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .instruction-list li {
        display: flex;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .instruction-list li:last-child {
        border-bottom: none;
    }
    .instruction-number {
        width: 24px;
        height: 24px;
        background: #003C52;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
    }
    .checking-status {
        font-size: 13px;
        color: #6b7280;
        margin-top: 1.25rem;
    }
    .checking-status i {
        animation: spin 1.2s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    @media (max-width: 767.98px) {
        .payment-waiting-page {
            padding: 1rem 0;
        }
        .waiting-card {
            padding: 1.75rem 1.25rem;
        }
        .payment-status-icon {
            width: 64px;
            height: 64px;
            font-size: 26px;
        }
        .payment-amount {
            font-size: 22px;
        }
        .va-number {
            font-size: 18px;
            letter-spacing: 1px;
        }
        .instruction-header {
            padding: 1rem;
            font-size: 14px;
        }
        .instruction-body {
            padding: 1rem;
        }
        .instruction-list li {
            font-size: 13px;
            padding: 10px 0;
        }
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endpush

@section('content')
<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')
    <div class="payment-waiting-page pt-24 md:pt-20">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="waiting-card">
                        <div class="payment-status-icon status-pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        
                        <h4 class="mb-2 font-semibold tracking-tight text-dark">{{ $payTrans['wait_title'][$lang] ?? 'Waiting for Payment' }}</h4>
                        <p class="text-muted small mb-4">{{ $payTrans['wait_subtitle'][$lang] ?? 'Please complete your payment' }}</p>
                        
                        <div class="payment-amount">
                            Rp {{ number_format($paymentTransaction['total_payment'] ?? $order->total_amount, 0, ',', '.') }}
                        </div>
                        
                        @php
                            $methodName = $paymentMethods[$paymentTransaction['method']]['name'] ?? strtoupper($paymentTransaction['method']);
                        @endphp
                        <div class="payment-method-badge shadow-sm">
                            <i class="{{ $paymentMethods[$paymentTransaction['method']]['icon'] ?? 'fas fa-credit-card' }} me-1"></i>
                            {{ $methodName }}
                        </div>

                        @if($paymentTransaction['fee'] > 0)
                        <p class="text-muted small mb-3">
                            {{ $payTrans['wait_fee_hint'][$lang] ?? 'Includes service fee:' }} Rp {{ number_format($paymentTransaction['fee'], 0, ',', '.') }}
                        </p>
                        @endif

                        @if($paymentTransaction['expired_at'])
                        <div class="timer-box">
                            <div class="small text-muted mb-1">{{ $payTrans['wait_timer_label'][$lang] ?? 'Complete payment within:' }}</div>
                            <div class="timer" id="countdown">{{ $payTrans['wait_timer_loading'][$lang] ?? 'Memuat...' }}</div>
                            <div class="small text-muted mt-1" id="expiredTime"></div>
                        </div>
                        @endif

                        @if($paymentTransaction['method'] === 'qris')
                            <!-- QR Code Display -->
                            <div class="qr-container">
                                <div id="qrcode"></div>
                            </div>
                            <p class="text-muted small mt-2">{{ $payTrans['wait_qris_hint'][$lang] ?? 'Scan QR code with your e-wallet app' }}</p>
                        @else
                            <!-- Virtual Account Display -->
                            <div class="va-number-box">
                                <div class="small text-muted mb-2">{{ $payTrans['wait_va_label'][$lang] ?? 'Virtual Account Number' }}</div>
                                <div class="va-number" id="vaNumber">{{ $paymentTransaction['payment_number'] }}</div>
                            </div>
                            <button type="button" class="btn-copy shadow-sm" onclick="copyVA(event)">
                                <i class="fas fa-copy me-1"></i>{{ $payTrans['wait_btn_copy'][$lang] ?? 'Copy Number' }}
                            </button>
                        @endif

                        <div class="checking-status">
                            <i class="fas fa-sync-alt me-1"></i>
                            {{ $payTrans['wait_check_status'][$lang] ?? 'Checking payment status...' }}
                        </div>
                    </div>

                    <!-- Payment Instructions -->
                    <div class="instruction-card">
                        <div class="instruction-header d-flex align-items-center">
                            <i class="fas fa-info-circle me-2 text-muted"></i>{{ $payTrans['inst_title'][$lang] ?? 'Payment Instructions' }}
                        </div>
                        <div class="instruction-body">
                            @if($paymentTransaction['method'] === 'qris')
                            <ol class="instruction-list">
                                <li>
                                    <span class="instruction-number">1</span>
                                    <span class="text-zinc-700">{{ $payTrans['inst_qris_1'][$lang] ?? 'Open e-wallet app' }}</span>
                                </li>
                                <li>
                                    <span class="instruction-number">2</span>
                                    <span class="text-zinc-700">{!! $payTrans['inst_qris_2'][$lang] ?? 'Select Scan or Pay menu' !!}</span>
                                </li>
                                <li>
                                    <span class="instruction-number">3</span>
                                    <span class="text-zinc-700">{{ $payTrans['inst_qris_3'][$lang] ?? 'Point camera to QR code above' }}</span>
                                </li>
                                <li>
                                    <span class="instruction-number">4</span>
                                    <span class="text-zinc-700">{{ $payTrans['inst_qris_4'][$lang] ?? 'Check the amount and confirm payment' }}</span>
                                </li>
                                <li>
                                    <span class="instruction-number">5</span>
                                    <span class="text-zinc-700">{{ $payTrans['inst_qris_5'][$lang] ?? 'Payment completed!' }}</span>
                                </li>
                            </ol>
                            @else
                            <ol class="instruction-list">
                                <li>
                                    <span class="instruction-number">1</span>
                                    <span class="text-zinc-700">{{ $payTrans['inst_va_1'][$lang] ?? 'Copy Virtual Account number above' }}</span>
                                </li>
                                <li>
                                    <span class="instruction-number">2</span>
                                    <span class="text-zinc-700">{{ $payTrans['inst_va_2'][$lang] ?? 'Open your m-banking or internet banking app' }}</span>
                                </li>
                                <li>
                                    <span class="instruction-number">3</span>
                                    <span class="text-zinc-700">{!! $payTrans['inst_va_3'][$lang] ?? 'Select Transfer menu to Virtual Account' !!}</span>
                                </li>
                                <li>
                                    <span class="instruction-number">4</span>
                                    <span class="text-zinc-700">{{ $payTrans['inst_va_4'][$lang] ?? 'Enter Virtual Account number and transfer amount' }}</span>
                                </li>
                                <li>
                                    <span class="instruction-number">5</span>
                                    <span class="text-zinc-700">{{ $payTrans['inst_va_5'][$lang] ?? 'Confirm and complete payment' }}</span>
                                </li>
                            </ol>
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-link text-muted text-decoration-none small transition hover:text-dark">
                            <i class="fas fa-arrow-left me-1 text-[11px]"></i>{{ $payTrans['btn_back'][$lang] ?? 'Back to Order Details' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate QR Code untuk QRIS
    @if($paymentTransaction['method'] === 'qris')
    const qrString = @json($paymentTransaction['payment_number']);
    const qrcodeEl = document.getElementById('qrcode');
    
    if (typeof QRCode !== 'undefined' && qrcodeEl) {
        new QRCode(qrcodeEl, {
            text: qrString,
            width: 240,
            height: 240,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    }
    @endif

    // Logika Hitung Mundur Batas Waktu
    @if($paymentTransaction['expired_at'])
    const expiredAtStr = @json($paymentTransaction['expired_at']);
    const expiredAt = new Date(expiredAtStr);
    const expiredTimeEl = document.getElementById('expiredTime');
    
    if (expiredTimeEl) {
        const options = { 
            day: 'numeric', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', hour12: false
        };
        expiredTimeEl.textContent = "{{ $payTrans['js_limit_prefix'][$lang] ?? 'Batas waktu:' }} " + expiredAt.toLocaleDateString('id-ID', options);
    }
    
    function updateCountdown() {
        const now = new Date();
        const diff = expiredAt - now;
        
        if (diff <= 0) {
            document.getElementById('countdown').textContent = "{{ $payTrans['js_expired'][$lang] ?? 'Kadaluarsa' }}";
            document.getElementById('countdown').style.color = '#dc2626';
            return;
        }
        
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        document.getElementById('countdown').textContent = 
            String(hours).padStart(2, '0') + ':' + 
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0');
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
    @else
    const timerBox = document.querySelector('.timer-box');
    if (timerBox) timerBox.style.display = 'none';
    @endif

    // Realtime polling status transaksi ke API Laravel setiap 5 detik
    function checkPaymentStatus() {
        fetch('{{ route("customer.payment.check-status", $order) }}')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'paid') {
                    document.querySelector('.payment-status-icon').classList.remove('status-pending');
                    document.querySelector('.payment-status-icon').classList.add('status-success');
                    document.querySelector('.payment-status-icon').innerHTML = '<i class="fas fa-check"></i>';
                    document.querySelector('.waiting-card h4').textContent = "{{ $payTrans['js_success_title'][$lang] ?? 'Pembayaran Berhasil!' }}";
                    document.querySelector('.checking-status').innerHTML = '<i class="fas fa-check-circle text-success"></i> ' + data.message;
                    
                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 2000);
                }
            })
            .catch(error => console.error('Error checking status:', error));
    }
    
    setInterval(checkPaymentStatus, 5000);
    setTimeout(checkPaymentStatus, 3000);
});

function copyVA(event) {
    const vaNumber = document.getElementById('vaNumber').textContent;
    navigator.clipboard.writeText(vaNumber).then(function() {
        const btn = event.target.closest('.btn-copy');
        const originalText = btn.innerHTML;
        btn.innerHTML = `<i class="fas fa-check me-1"></i>{{ $payTrans['js_copied'][$lang] ?? 'Tersalin!' }}`;
        btn.style.background = '#16a34a';
        
        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.style.background = '';
        }, 2000);
    });
}
</script>
@endpush