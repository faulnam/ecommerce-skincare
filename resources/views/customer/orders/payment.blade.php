@extends('layouts.app')

@php
    $jsonPath = public_path('translation/payment.json');
    $payTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@section('title', $payTrans['meta_title'][$lang] ?? 'Payment - LUMINA')

@push('styles')
<style>
    .payment-page {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }
    .payment-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .payment-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #1f2937;
    }
    .payment-card-body {
        padding: 1.5rem;
    }
    .order-summary {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1.25rem;
    }
    .order-number {
        font-size: 14px;
        color: #6b7280;
    }
    .order-total {
        font-size: 24px;
        font-weight: 700;
        color: #003C52;
    }
    .payment-method-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-bottom: 0.75rem;
    }
    .payment-method-card:hover {
        border-color: #003C52;
        background: rgba(0, 60, 82, 0.02);
    }
    .payment-method-card.selected {
        border-color: #003C52;
        background: rgba(0, 60, 82, 0.04);
    }
    .payment-method-card input[type="radio"] {
        display: none;
    }
    .payment-method-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        border-radius: 10px;
        font-size: 18px;
        color: #6b7280;
        transition: all 0.2s ease;
    }
    .payment-method-card.selected .payment-method-icon {
        background: #003C52;
        color: white;
    }
    .payment-method-name {
        font-weight: 600;
        color: #1f2937;
    }
    .payment-method-desc {
        font-size: 13px;
        color: #6b7280;
    }
    .btn-pay {
        background: #003C52;
        color: white;
        border: none;
        border-radius: 999px;
        padding: 14px;
        font-weight: 600;
        font-size: 15px;
        width: 100%;
        transition: all 0.2s ease;
    }
    .btn-pay:hover:not(:disabled) {
        background: #002b3b;
        color: white;
    }
    .btn-pay:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }
    .redirect-option {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid #fcd34d;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    @media (max-width: 767.98px) {
        .payment-page {
            padding: 1rem 0;
        }
        .payment-card {
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .payment-card-header {
            padding: 1rem;
            font-size: 14px;
        }
        .payment-card-body {
            padding: 1rem;
        }
        .order-total {
            font-size: 20px;
        }
        .payment-method-card {
            padding: 0.75rem;
        }
        .payment-method-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
        .payment-method-name {
            font-size: 14px;
        }
        .payment-method-desc {
            font-size: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="payment-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb m-0" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="{{ route('customer.orders.index') }}" class="text-decoration-none text-muted">{{ $payTrans['bc_orders'][$lang] ?? 'Orders' }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.orders.show', $order) }}" class="text-decoration-none text-muted">{{ $order->order_number }}</a></li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">{{ $payTrans['bc_payment'][$lang] ?? 'Payment' }}</li>
                    </ol>
                </nav>

                <!-- Order Summary -->
                <div class="payment-card">
                    <div class="payment-card-header d-flex align-items-center">
                        <i class="fas fa-receipt me-2 text-muted"></i>{{ $payTrans['card_summary'][$lang] ?? 'Order Summary' }}
                    </div>
                    <div class="payment-card-body">
                        <div class="order-summary">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="order-number">{{ $payTrans['label_order_no'][$lang] ?? 'Order No.' }}</span>
                                <strong class="text-dark">{{ $order->order_number }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="order-number">{{ $payTrans['label_subtotal'][$lang] ?? 'Subtotal' }} ({{ $order->items->count() }} item)</span>
                                <span class="text-dark fw-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="order-number">{{ $payTrans['label_shipping'][$lang] ?? 'Shipping Cost' }}</span>
                                <span class="text-dark fw-medium">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            <hr class="my-3 opacity-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold text-dark">{{ $payTrans['label_total'][$lang] ?? 'Total Payment' }}</span>
                                <span class="order-total">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Form -->
                <form action="{{ route('customer.payment.process', $order) }}" method="POST" id="paymentForm">
                    @csrf
                    <input type="hidden" name="idempotency_key" id="paymentIdempotencyKey" value="">
                    
                    <div class="payment-card">
                        <div class="payment-card-header d-flex align-items-center">
                            <i class="fas fa-credit-card me-2 text-muted"></i>{{ $payTrans['card_methods'][$lang] ?? 'Select Payment Method' }}
                        </div>
                        <div class="payment-card-body">
                            <!-- COD Option -->
                            <label class="payment-method-card d-flex align-items-center gap-3" style="background: rgba(0, 60, 82, 0.03); border-color: #003C52;">
                                <input type="radio" name="payment_method" value="cod" required checked>
                                <div class="payment-method-icon" style="background: #003C52; color: white;">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="payment-method-name">
                                        <span class="badge bg-dark me-1" style="font-size: 10px; font-weight: 500; vertical-align: middle;">{{ $payTrans['badge_recom'][$lang] ?? 'Rekomendasi' }}</span>
                                        {{ $payTrans['cod_title'][$lang] ?? 'COD (Pay on Delivery)' }}
                                    </div>
                                    <div class="payment-method-desc">{{ $payTrans['cod_desc'][$lang] ?? 'Pay cash when receiving goods from courier' }}</div>
                                </div>
                                <i class="fas fa-check-circle text-dark check-icon"></i>
                            </label>

                            <div class="text-muted small my-3 d-flex align-items-center gap-1">
                                <i class="fas fa-shield-alt text-[11px]"></i>
                                <span>{{ $payTrans['label_online'][$lang] ?? 'Or pay online:' }}</span>
                            </div>

                            <!-- QRIS -->
                            <label class="payment-method-card d-flex align-items-center gap-3">
                                <input type="radio" name="payment_method" value="qris" required>
                                <div class="payment-method-icon">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="payment-method-name">QRIS</div>
                                    <div class="payment-method-desc">{{ $payTrans['qris_desc'][$lang] ?? 'Scan QR dengan aplikasi e-wallet (GoPay, OVO, Dana, dll)' }}</div>
                                </div>
                                <i class="fas fa-check-circle text-dark d-none check-icon"></i>
                            </label>

                            <!-- Virtual Account Options -->
                            @foreach(['bni_va' => 'BNI', 'bri_va' => 'BRI', 'cimb_niaga_va' => 'CIMB Niaga', 'permata_va' => 'Permata', 'maybank_va' => 'Maybank'] as $method => $name)
                            <label class="payment-method-card d-flex align-items-center gap-3">
                                <input type="radio" name="payment_method" value="{{ $method }}" required>
                                <div class="payment-method-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="payment-method-name">{{ $name }} Virtual Account</div>
                                    <div class="payment-method-desc">{{ $payTrans['va_desc'][$lang] ?? 'Transfer ke nomor Virtual Account' }} {{ $name }}</div>
                                </div>
                                <i class="fas fa-check-circle text-dark d-none check-icon"></i>
                            </label>
                            @endforeach

                            <!-- Redirect Option -->
                            <div class="redirect-option">
                                <label class="d-flex align-items-start gap-3 cursor-pointer m-0">
                                    <input type="radio" name="payment_method" value="redirect" class="mt-1">
                                    <div>
                                        <div class="fw-bold" style="color: #92400e;">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $payTrans['redirect_title'][$lang] ?? 'Select on Payasir page' }}
                                        </div>
                                        <div style="font-size: 13px; color: #78350f;" class="mt-0.5">
                                            {{ $payTrans['redirect_desc'][$lang] ?? 'You will be redirected to Payasir page to select payment method' }}
                                        </div>
                                    </div>
                                </label>
                            </div>

                            @error('payment_method')
                                <div class="text-danger small mt-2"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn-pay shadow-sm" id="payButton">
                        <i class="fas fa-lock me-2 text-[12px]"></i>{{ $payTrans['btn_pay'][$lang] ?? 'Pay Now' }}
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('customer.orders.show', $order) }}" class="text-muted text-decoration-none small transition hover:text-dark">
                        <i class="fas fa-arrow-left me-1 text-[11px]"></i>{{ $payTrans['btn_back'][$lang] ?? 'Back to Order Details' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.payment-method-card');
    const radios = document.querySelectorAll('input[name="payment_method"]');
    
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Bersihkan semua state aktif sebelumnya
            cards.forEach(card => {
                card.classList.remove('selected');
                card.style.background = '';
                card.style.borderColor = '';
                const checkIcon = card.querySelector('.check-icon');
                if (checkIcon) checkIcon.classList.add('d-none');
            });
            
            // Atur state aktif pada item yang terpilih
            const card = this.closest('.payment-method-card');
            if (card) {
                card.classList.add('selected');
                const checkIcon = card.querySelector('.check-icon');
                if (checkIcon) checkIcon.classList.remove('d-none');
            }
        });
    });

    // UUID helper
    function generateUUID() {
        if (typeof crypto !== 'undefined' && crypto.randomUUID) {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    // Generate idempotency key once on page load
    document.getElementById('paymentIdempotencyKey').value = generateUUID();

    // Indikator loading saat form disubmit
    document.getElementById('paymentForm').addEventListener('submit', function() {
        const btn = document.getElementById('payButton');
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>{{ $payTrans['js_processing'][$lang] ?? 'Memproses...' }}`;
    });
});
</script>
@endpush