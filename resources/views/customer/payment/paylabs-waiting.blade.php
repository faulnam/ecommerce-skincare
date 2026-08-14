@extends('layouts.app')

@section('title', 'Waiting for Payment')

@push('styles')
<style>
    body { padding-top: 0 !important; padding-bottom: 0 !important; }
    #mainNavbar, .mobile-bottom-nav, .footer { display: none !important; }
</style>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/paymentwaiting.json');
    $payTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp
<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerBtn = document.getElementById('hamburgerMenuBtn');
        const hamburgerDropdown = document.getElementById('hamburgerMenuDropdown');
        if (hamburgerBtn && hamburgerDropdown) {
            hamburgerBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                hamburgerDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!hamburgerDropdown.contains(e.target) && e.target !== hamburgerBtn && !hamburgerBtn.contains(e.target)) {
                    hamburgerDropdown.classList.add('hidden');
                }
            });
        }
    });
</script>

<div class="min-h-screen bg-zinc-50 py-12 pt-24 md:pt-20">
    <div class="mx-auto max-w-2xl px-6">
        <div class="rounded-2xl bg-white p-8 shadow-sm text-center">
            <div class="mb-6 flex h-20 w-20 mx-auto items-center justify-center rounded-full bg-amber-100">
                <i class="fas fa-clock text-3xl text-amber-600"></i>
            </div>
            <h1 class="text-2xl font-semibold text-black mb-2">{{ $payTrans['page_title'][$lang] ?? 'Waiting for Payment' }}</h1>
            <p class="text-zinc-600 mb-4">{{ $payTrans['page_subtitle'][$lang] ?? 'Please complete your payment' }}</p>

            <!-- Order Number Card -->
            <div class="mb-6 rounded-xl bg-zinc-50 border border-zinc-200 p-4 text-left">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-zinc-500 mb-1">{{ $payTrans['label_order_no'][$lang] ?? 'Order Number' }}</p>
                        <p class="text-lg font-mono font-bold text-black tracking-wide">{{ $order->order_number }}</p>
                    </div>
                    <button onclick="copyOrderNumber()" class="rounded-lg bg-black px-4 py-2 text-sm text-white hover:bg-black/90 transition">
                        <i class="fas fa-copy me-1"></i> {{ $payTrans['btn_copy'][$lang] ?? 'Copy' }}
                    </button>
                </div>
                <p class="mt-2 text-xs text-zinc-500">
                    <i class="fas fa-info-circle me-1"></i>
                    {{ $payTrans['hint_save_order'][$lang] ?? 'Save this order number to track your order status' }}
                </p>
            </div>

            <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 p-4">
                <p class="text-sm text-amber-800">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $payTrans['alert_expiry'][$lang] ?? 'Complete payment before' }} <strong id="expiry-time">{{ $expiryTime }}</strong>
                </p>
            </div>

            <div class="mb-6 rounded-xl bg-blue-50 border border-blue-200 p-3">
                <p class="text-xs text-blue-800 flex items-center justify-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ $payTrans['alert_auto_check'][$lang] ?? 'Payment status will be automatically checked every 10 seconds' }}</span>
                    <span id="auto-check-indicator" class="inline-block w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                </p>
            </div>


            <!-- Payment Instructions -->
            <div class="text-left mb-6">
                @if(str_starts_with($paymentChannel, 'VA_'))
                    <h3 class="text-base font-semibold text-black mb-4"><i class="fas fa-university me-2"></i>{{ $payTrans['title_va'][$lang] ?? 'Virtual Account' }}</h3>
                    <div class="rounded-xl bg-zinc-50 p-4 mb-4">
                        <label class="text-xs text-zinc-500 mb-2 block">@lang($payTrans['label_va_number'][$lang] ?? 'Virtual Account Number')</label>
                        <div class="flex items-center gap-2">
                            <span id="va-number" class="flex-1 text-lg font-mono font-semibold text-black">{{ $paymentData['va_number_display'] ?? ($paymentData['va_number'] ?? '-') }}</span>
                            <button onclick="copyText('va-number')" class="rounded-lg bg-black px-4 py-2 text-sm text-white hover:bg-black/90">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-sm text-zinc-600 space-y-2">
                        <p class="font-medium text-black">{{ $payTrans['title_instruction'][$lang] ?? 'Payment Instructions:' }}</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>{{ $payTrans['va_step_1'][$lang] ?? 'Open mobile banking app or ATM' }}</li>
                            <li>{{ $payTrans['va_step_2'][$lang] ?? 'Select Transfer / Pay menu' }}</li>
                            <li>{{ $payTrans['va_step_3'][$lang] ?? 'Enter the Virtual Account number above' }}</li>
                            <li>{{ $payTrans['va_step_4'][$lang] ?? 'Enter amount:' }} <strong>{{ $order->formatted_total }}</strong></li>
                           <li>{{ $payTrans['va_step_5'][$lang] ?? 'Confirm payment' }}</li>
                        </ol>
                    </div>
                @elseif($paymentChannel === 'QRIS')
                    <h3 class="text-base font-semibold text-black mb-4"><i class="fas fa-qrcode me-2"></i>{{ $payTrans['title_qris'][$lang] ?? 'QRIS' }}</h3>
                    <div class="flex justify-center mb-4">
                        <div class="rounded-xl border-2 border-zinc-200 p-4 bg-white">
                            @php
                                $qrImage = $paymentData['qr_url_display'] ?? ($paymentData['qr_url'] ?? '');
                            @endphp

                            @if(!empty($qrImage))
                                <img src="{{ $qrImage }}" alt="QR Code" class="w-64 h-64">
                            @else
                                <div class="w-64 h-64 flex items-center justify-center text-sm text-zinc-500 text-center px-4">
                                    {!! $payTrans['qris_unavailable'][$lang] ?? 'QRIS not yet available from provider.<br>Please click the check status button or recreate payment.' !!}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-sm text-zinc-600 space-y-2">
                        <p class="font-medium text-black">{{ $payTrans['title_instruction'][$lang] ?? 'Payment Instructions:' }}</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>{{ $payTrans['qris_step_1'][$lang] ?? 'Open e-wallet app or mobile banking' }}</li>
                            <li>{{ $payTrans['qris_step_2'][$lang] ?? 'Select Scan QR menu' }}</li>
                            <li>{{ $payTrans['qris_step_3'][$lang] ?? 'Scan the QR Code above' }}</li>
                           <li>{{ $payTrans['qris_step_4'][$lang] ?? 'Confirm payment' }}</li>
                        </ol>
                    </div>
                @elseif(str_starts_with($paymentChannel, 'EWALLET_'))
                    <h3 class="text-base font-semibold text-black mb-4"><i class="fas fa-wallet me-2"></i>{{ $payTrans['title_ewallet'][$lang] ?? 'E-Wallet' }}</h3>
                    <a href="{{ $paymentData['deeplink_url_display'] ?? ($paymentData['deeplink_url'] ?? '#') }}" target="_blank"
                       class="block w-full rounded-xl bg-black py-4 text-center text-white font-medium hover:bg-black/90 mb-4">
                        <i class="fas fa-external-link-alt me-2"></i>{{ $payTrans['btn_open_app'][$lang] ?? 'Open App' }}
                    </a>
                    <div class="text-sm text-zinc-600 space-y-2">
                        <p class="font-medium text-black">{{ $payTrans['title_instruction'][$lang] ?? 'Payment Instructions:' }}</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>{{ $payTrans['ewallet_step_1'][$lang] ?? 'Click the "Open App" button above' }}</li>
                            <li>{{ $payTrans['ewallet_step_2'][$lang] ?? 'E-wallet app will open automatically' }}</li>
                            <li>{{ $payTrans['ewallet_step_3'][$lang] ?? 'Confirm payment in the app' }}</li>
                        </ol>
                    </div>
                @elseif(str_starts_with($paymentChannel, 'RETAIL_'))
                    <h3 class="text-base font-semibold text-black mb-4"><i class="fas fa-store me-2"></i>{{ $payTrans['title_retail'][$lang] ?? 'Retail' }}</h3>
                    <div class="rounded-xl bg-zinc-50 p-4 mb-4">
                        <label class="text-xs text-zinc-500 mb-2 block">{{ $payTrans['label_payment_code'][$lang] ?? 'Payment Code' }}</label>
                        <div class="flex items-center gap-2">
                            <span id="payment-code" class="flex-1 text-lg font-mono font-semibold text-black">{{ $paymentData['payment_code_display'] ?? ($paymentData['payment_code'] ?? '-') }}</span>
                            <button onclick="copyText('payment-code')" class="rounded-lg bg-black px-4 py-2 text-sm text-white hover:bg-black/90">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-sm text-zinc-600 space-y-2">
                        <p class="font-medium text-black">{{ $payTrans['title_instruction'][$lang] ?? 'Payment Instructions:' }}</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>{!! str_replace(':channel', str_replace('RETAIL_', '', $paymentChannel), $payTrans['retail_step_1'][$lang] ?? 'Visit the nearest ' . str_replace('RETAIL_', '', $paymentChannel)) !!}</li>
                            <li>{{ $payTrans['retail_step_2'][$lang] ?? 'Provide the payment code above to the cashier' }}</li>
                            <li>{{ $payTrans['retail_step_3'][$lang] ?? 'Pay amount:' }} <strong>{{ $order->formatted_total }}</strong></li>
                            <li>{{ $payTrans['retail_step_4'][$lang] ?? 'Save the payment receipt' }}</li>
                        </ol>
                    </div>
                @endif
            </div>

            <div class="flex gap-3">
                <a href="{{ route('customer.orders.guest-show', $order) }}" 
                   class="flex-1 rounded-xl border border-zinc-300 bg-white py-3 text-center text-sm font-medium text-black hover:bg-zinc-50">
                    {{ $payTrans['btn_view_order'][$lang] ?? 'View Order' }}
                </a>
                <button onclick="checkPaymentStatus()" 
                        class="flex-1 rounded-xl bg-black py-3 text-center text-sm font-medium text-white hover:bg-black/90">
                    <i class="fas fa-sync me-2"></i>{{ $payTrans['btn_check_status'][$lang] ?? 'Check Status' }}
                </button>
            </div>

            @if($canSimulate ?? false)
                <form action="{{ route('customer.payment.paylabs.simulate', $order) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-xl bg-emerald-600 py-3 text-center text-sm font-medium text-white hover:bg-emerald-700">
                        <i class="fas fa-flask me-2"></i>{{ $payTrans['btn_simulate'][$lang] ?? 'Simulate Successful Payment' }}
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>



<script>
function copyText(elementId) {
    const text = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert("{{ $payTrans['toast_copied'][$lang] ?? 'Successfully copied!' }}");
    });
}

function copyOrderNumber() {
    const text = '{{ $order->order_number }}';
    navigator.clipboard.writeText(text).then(() => {
        alert("{{ $payTrans['toast_copied_order'][$lang] ?? $payTrans['toast_copied'][$lang] ?? 'Order number copied!' }}");
    });
}

let isChecking = false;
let checkInterval = null;

function checkPaymentStatus(isAutoCheck = false) {
    if (isChecking) return;
    
    isChecking = true;
    const button = document.querySelector('button[onclick*="checkPaymentStatus"]');
    const originalText = button ? button.innerHTML : '';
    
    if (button && !isAutoCheck) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>' + "{{ $payTrans['js_checking'][$lang] ?? 'Checking...' }}";
    }
    
    fetch('{{ route('customer.payment.paylabs.check-status', $order) }}')
        .then(response => response.json())
        .then(data => {
            console.log('Payment status check:', data);
            
            if (data.paid || data.status === 'paid' || data.status === 'success' || data.status === '02') {
                // Stop auto-check
                if (checkInterval) {
                    clearInterval(checkInterval);
                }
                
                // Show success message
                document.querySelector('.rounded-2xl.bg-white').innerHTML = `
                    <div class="text-center py-12">
                        <div class="mb-6 flex h-20 w-20 mx-auto items-center justify-center rounded-full bg-emerald-100">
                            <i class="fas fa-check text-3xl text-emerald-600"></i>
                        </div>
                        <h1 class="text-2xl font-semibold text-black mb-2">${ "{{ $payTrans['success_title'][$lang] ?? 'Payment Successful!' }}" }</h1>
                        <p class="text-zinc-600 mb-6">${ "{{ $payTrans['success_desc'][$lang] ?? 'Thank you, your payment has been received' }}" }</p>
                        <div class="animate-spin h-8 w-8 border-4 border-zinc-200 border-t-emerald-600 rounded-full mx-auto mb-3"></div>
                        <p class="text-sm text-zinc-600">${ "{{ $payTrans['success_redirect'][$lang] ?? 'Redirecting to order page...' }}" }</p>
                    </div>
                `;
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '{{ route('customer.orders.guest-show', $order) }}';
                }, 2000);
            } else {
                if (!isAutoCheck) {
                    alert("{{ $payTrans['js_not_received'][$lang] ?? 'Payment not yet received. Please try again or wait a moment.' }}");
                }
                
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
                isChecking = false;
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
            
            if (!isAutoCheck) {
                alert("{{ $payTrans['js_failed_check'][$lang] ?? 'Failed to check status. Please try again.' }}");
            }
            
            if (button) {
                button.disabled = false;
                button.innerHTML = originalText;
            }
            isChecking = false;
        });
}

// Auto-check payment status every 10 seconds
checkInterval = setInterval(() => {
    checkPaymentStatus(true);
}, 10000);

// Check immediately on page load
setTimeout(() => {
    checkPaymentStatus(true);
}, 2000);

// Countdown timer
const expiryTime = new Date('{{ $expiryTime }}').getTime();
const countdownInterval = setInterval(() => {
    const now = new Date().getTime();
    const distance = expiryTime - now;

    if (distance < 0) {
        clearInterval(countdownInterval);
        if (checkInterval) {
            clearInterval(checkInterval);
        }
        document.querySelector('.bg-amber-50').innerHTML = '<p class="text-sm text-red-600"><i class="fas fa-times-circle me-2"></i><strong>' + "{{ $payTrans['js_expired'][$lang] ?? 'Payment expired' }}" + '</strong></p>';
    }
}, 1000);

// Check when page becomes visible again (user switches back to tab)
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        checkPaymentStatus(true);
    }
});
</script>
@endsection