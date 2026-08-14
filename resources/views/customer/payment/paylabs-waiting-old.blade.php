@extends('layouts.app')

@section('title', 'Menunggu Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="payment-icon mb-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="mb-2">Menunggu Pembayaran</h4>
                    <p class="text-muted mb-4">Silakan selesaikan pembayaran Anda</p>

                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Selesaikan pembayaran sebelum <strong id="expiry-time">{{ $expiryTime }}</strong>
                    </div>

                    <!-- Payment Instructions -->
                    <div class="payment-instructions text-start mb-4">
                        @if(str_starts_with($paymentChannel, 'VA_'))
                            <h6 class="mb-3"><i class="fas fa-university me-2"></i>Virtual Account</h6>
                            <div class="info-box mb-3">
                                <label>Nomor Virtual Account</label>
                                <div class="copy-box">
                                    <span id="va-number">{{ $paymentData['va_number'] ?? '-' }}</span>
                                    <button class="btn-copy" onclick="copyText('va-number')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="steps">
                                <p class="mb-2"><strong>Cara Pembayaran:</strong></p>
                                <ol class="ps-3">
                                    <li>Buka aplikasi mobile banking atau ATM</li>
                                    <li>Pilih menu Transfer / Bayar</li>
                                    <li>Masukkan nomor Virtual Account di atas</li>
                                    <li>Masukkan nominal: <strong>{{ $order->formatted_total }}</strong></li>
                                    <li>Konfirmasi pembayaran</li>
                                </ol>
                            </div>
                        @elseif($paymentChannel === 'QRIS')
                            <h6 class="mb-3"><i class="fas fa-qrcode me-2"></i>QRIS</h6>
                            <div class="qr-code-box mb-3">
                                <img src="{{ $paymentData['qr_url'] ?? '' }}" alt="QR Code" class="img-fluid">
                            </div>
                            <div class="steps">
                                <p class="mb-2"><strong>Cara Pembayaran:</strong></p>
                                <ol class="ps-3">
                                    <li>Buka aplikasi e-wallet atau mobile banking</li>
                                    <li>Pilih menu Scan QR</li>
                                    <li>Scan QR Code di atas</li>
                                    <li>Konfirmasi pembayaran</li>
                                </ol>
                            </div>
                        @elseif(str_starts_with($paymentChannel, 'EWALLET_'))
                            <h6 class="mb-3"><i class="fas fa-wallet me-2"></i>E-Wallet</h6>
                            <div class="info-box mb-3">
                                <a href="{{ $paymentData['deeplink_url'] ?? '#' }}" class="btn btn-primary btn-lg w-100" target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>Buka Aplikasi
                                </a>
                            </div>
                            <div class="steps">
                                <p class="mb-2"><strong>Cara Pembayaran:</strong></p>
                                <ol class="ps-3">
                                    <li>Klik tombol "Buka Aplikasi" di atas</li>
                                    <li>Aplikasi e-wallet akan terbuka otomatis</li>
                                    <li>Konfirmasi pembayaran di aplikasi</li>
                                </ol>
                            </div>
                        @elseif(str_starts_with($paymentChannel, 'RETAIL_'))
                            <h6 class="mb-3"><i class="fas fa-store me-2"></i>Retail</h6>
                            <div class="info-box mb-3">
                                <label>Kode Pembayaran</label>
                                <div class="copy-box">
                                    <span id="payment-code">{{ $paymentData['payment_code'] ?? '-' }}</span>
                                    <button class="btn-copy" onclick="copyText('payment-code')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="steps">
                                <p class="mb-2"><strong>Cara Pembayaran:</strong></p>
                                <ol class="ps-3">
                                    <li>Kunjungi {{ str_replace('RETAIL_', '', $paymentChannel) }} terdekat</li>
                                    <li>Berikan kode pembayaran di atas ke kasir</li>
                                    <li>Bayar sejumlah: <strong>{{ $order->formatted_total }}</strong></li>
                                    <li>Simpan struk pembayaran</li>
                                </ol>
                            </div>
                        @endif
                    </div>

                    

                    <div class="d-flex gap-2">
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-secondary flex-grow-1">
                            <i class="fas fa-arrow-left me-2"></i>Lihat Pesanan
                        </a>
                        <button type="button" class="btn btn-primary flex-grow-1" onclick="checkPaymentStatus()">
                            <i class="fas fa-sync me-2"></i>Cek Status
                        </button>
                    </div>

                    @if(config('paylabs.sandbox'))
                    <div class="alert alert-info mt-4">
                        <strong>Mode Sandbox:</strong> Untuk testing, klik tombol di bawah untuk simulasi pembayaran berhasil
                        <form action="{{ route('customer.payment.paylabs.simulate', $order) }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-check me-2"></i>Simulasi Pembayaran Berhasil
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #fff3cd;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 36px;
    color: #ffc107;
}

.info-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
}

.info-box label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
    display: block;
}

.copy-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 12px;
}

.copy-box span {
    flex: 1;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.btn-copy {
    background: #0071e3;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 12px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-copy:hover {
    background: #0051a8;
}

.qr-code-box {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    display: inline-block;
}

.qr-code-box img {
    max-width: 250px;
}

.steps ol {
    margin: 0;
}

.steps li {
    margin-bottom: 8px;
    color: #4b5563;
}

.status-indicator {
    padding: 24px;
}
</style>

<script>
function copyText(elementId) {
    const text = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('Berhasil disalin!');
    });
}

// Auto check payment status every 5 seconds
let checkInterval = setInterval(checkPaymentStatus, 5000);

function checkPaymentStatus() {
    fetch('{{ route('customer.payment.paylabs.check-status', $order) }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'paid') {
                clearInterval(checkInterval);
                window.location.href = '{{ route('customer.orders.show', $order) }}';
            }
        })
        .catch(error => console.error('Error:', error));
}

// Countdown timer
const expiryTime = new Date('{{ $expiryTime }}').getTime();
const countdownInterval = setInterval(() => {
    const now = new Date().getTime();
    const distance = expiryTime - now;

    if (distance < 0) {
        clearInterval(countdownInterval);
        clearInterval(checkInterval);
        document.querySelector('.alert-warning').innerHTML = '<i class="fas fa-times-circle me-2"></i><strong>Pembayaran expired</strong>';
    }
}, 1000);
</script>
@endsection
