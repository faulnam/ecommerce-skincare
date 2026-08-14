@php
    $jsonPath = public_path('translation/paymentwaiting.json');
    $payTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
    
    // Fallback language if not defined
    $lang = $lang ?? 'id';
@endphp

<div class="rounded-2xl bg-white p-6 shadow-sm border border-black/5 text-center" id="waitingPaymentContainer">
    <div class="mb-4 flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-amber-100">
        <i class="fas fa-clock text-2xl text-amber-600"></i>
    </div>
    <h2 class="text-xl font-semibold text-black mb-1">{{ $payTrans['page_title'][$lang] ?? 'Waiting for Payment' }}</h2>
    <p class="text-sm text-zinc-600 mb-6">{{ $payTrans['page_subtitle'][$lang] ?? 'Please complete your payment' }}</p>

    <!-- Order Number Card -->
    <div class="mb-6 rounded-xl bg-zinc-50 border border-zinc-200 p-4 text-left">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-zinc-500 mb-1">{{ $payTrans['label_order_no'][$lang] ?? 'Order Number' }}</p>
                <p class="text-lg font-mono font-bold text-black tracking-wide">{{ $order->order_number }}</p>
            </div>
            <button type="button" onclick="copyOrderNumber()" class="rounded-lg bg-black px-4 py-2 text-sm text-white hover:bg-black/90 transition">
                <i class="fas fa-copy me-1"></i> {{ $payTrans['btn_copy'][$lang] ?? 'Copy' }}
            </button>
        </div>
    </div>

    <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 p-3 text-left">
        <p class="text-xs text-amber-800">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ $payTrans['alert_expiry'][$lang] ?? 'Complete payment before' }} <strong id="expiry-time">{{ $expiryTime ?? '' }}</strong>
        </p>
    </div>

    <div class="mb-6 rounded-xl bg-blue-50 border border-blue-200 p-3 text-left">
        <p class="text-xs text-blue-800 flex items-center justify-between gap-2">
            <span><i class="fas fa-info-circle me-1"></i> {{ $payTrans['alert_auto_check'][$lang] ?? 'Payment status will be automatically checked every 10 seconds' }}</span>
            <span id="auto-check-indicator" class="inline-block w-2 h-2 bg-blue-500 rounded-full animate-pulse flex-shrink-0"></span>
        </p>
    </div>

    <!-- Payment Instructions -->
    <div class="text-left mb-6">
        @if(str_starts_with($paymentChannel, 'VA_'))
            <h3 class="text-sm font-semibold text-black mb-3"><i class="fas fa-university me-2"></i>{{ str_replace('VA_', '', $paymentChannel) }} {{ $payTrans['title_va'][$lang] ?? 'Virtual Account' }}</h3>
            <div class="rounded-xl bg-zinc-50 p-4 mb-4 border border-zinc-200">
                <label class="text-xs text-zinc-500 mb-2 block">@lang($payTrans['label_va_number'][$lang] ?? 'Virtual Account Number')</label>
                <div class="flex items-center gap-2">
                    <span id="va-number" class="flex-1 text-lg font-mono font-semibold text-black">{{ $paymentData['va_number_display'] ?? ($paymentData['va_number'] ?? '-') }}</span>
                    <button type="button" onclick="copyText('va-number')" class="rounded-lg bg-black px-3 py-1.5 text-xs text-white hover:bg-black/90">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <div class="text-xs text-zinc-600 space-y-2">
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
            <h3 class="text-sm font-semibold text-black mb-3"><i class="fas fa-qrcode me-2"></i>{{ $payTrans['title_qris'][$lang] ?? 'QRIS' }}</h3>
            <div class="flex justify-center mb-4">
                <div class="rounded-xl border-2 border-zinc-200 p-4 bg-white">
                    @php
                        $qrImage = $paymentData['qr_url_display'] ?? ($paymentData['qr_url'] ?? '');
                    @endphp

                    @if(!empty($qrImage))
                        <img src="{{ $qrImage }}" alt="QR Code" class="w-48 h-48 mx-auto">
                    @else
                        <div class="w-48 h-48 flex items-center justify-center text-xs text-zinc-500 text-center px-4">
                            {!! $payTrans['qris_unavailable'][$lang] ?? 'QRIS not yet available from provider.<br>Please click the check status button or recreate payment.' !!}
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-xs text-zinc-600 space-y-2">
                <p class="font-medium text-black">{{ $payTrans['title_instruction'][$lang] ?? 'Payment Instructions:' }}</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>{{ $payTrans['qris_step_1'][$lang] ?? 'Open e-wallet app or mobile banking' }}</li>
                    <li>{{ $payTrans['qris_step_2'][$lang] ?? 'Select Scan QR menu' }}</li>
                    <li>{{ $payTrans['qris_step_3'][$lang] ?? 'Scan the QR Code above' }}</li>
                    <li>{{ $payTrans['qris_step_4'][$lang] ?? 'Confirm payment' }}</li>
                </ol>
            </div>
        @elseif(str_starts_with($paymentChannel, 'EWALLET_'))
            <h3 class="text-sm font-semibold text-black mb-3"><i class="fas fa-wallet me-2"></i>{{ str_replace('EWALLET_', '', $paymentChannel) }}</h3>
            <a href="{{ $paymentData['deeplink_url_display'] ?? ($paymentData['deeplink_url'] ?? '#') }}" target="_blank"
               class="block w-full rounded-xl bg-black py-3 text-center text-sm text-white font-medium hover:bg-black/90 mb-4">
                <i class="fas fa-external-link-alt me-2"></i>{{ $payTrans['btn_open_app'][$lang] ?? 'Open App' }}
            </a>
            <div class="text-xs text-zinc-600 space-y-2">
                <p class="font-medium text-black">{{ $payTrans['title_instruction'][$lang] ?? 'Payment Instructions:' }}</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>{{ $payTrans['ewallet_step_1'][$lang] ?? 'Click the "Open App" button above' }}</li>
                    <li>{{ $payTrans['ewallet_step_2'][$lang] ?? 'E-wallet app will open automatically' }}</li>
                    <li>{{ $payTrans['ewallet_step_3'][$lang] ?? 'Confirm payment in the app' }}</li>
                </ol>
            </div>
        @elseif(str_starts_with($paymentChannel, 'RETAIL_'))
            <h3 class="text-sm font-semibold text-black mb-3"><i class="fas fa-store me-2"></i>{{ str_replace('RETAIL_', '', $paymentChannel) }}</h3>
            <div class="rounded-xl bg-zinc-50 p-4 mb-4 border border-zinc-200">
                <label class="text-xs text-zinc-500 mb-2 block">{{ $payTrans['label_payment_code'][$lang] ?? 'Payment Code' }}</label>
                <div class="flex items-center gap-2">
                    <span id="payment-code" class="flex-1 text-lg font-mono font-semibold text-black">{{ $paymentData['payment_code_display'] ?? ($paymentData['payment_code'] ?? '-') }}</span>
                    <button type="button" onclick="copyText('payment-code')" class="rounded-lg bg-black px-3 py-1.5 text-xs text-white hover:bg-black/90">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <div class="text-xs text-zinc-600 space-y-2">
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

    <div class="flex gap-2">
        <a href="{{ route('customer.orders.guest-show', $order) }}" 
           class="flex-1 rounded-xl border border-zinc-300 bg-white py-2.5 text-center text-xs font-medium text-black hover:bg-zinc-50">
            {{ $payTrans['btn_view_order'][$lang] ?? 'View Order' }}
        </a>
        <button type="button" onclick="checkPaymentStatus()" 
                class="flex-1 rounded-xl bg-black py-2.5 text-center text-xs font-medium text-white hover:bg-black/90" id="btnCheckStatus">
            <i class="fas fa-sync me-1"></i>{{ $payTrans['btn_check_status'][$lang] ?? 'Check Status' }}
        </button>
    </div>

    @if($canSimulate ?? false)
        <form action="{{ route('customer.payment.paylabs.simulate', $order) }}" method="POST" class="mt-3">
            @csrf
            <button type="submit"
                    class="w-full rounded-xl bg-emerald-600 py-2 text-center text-xs font-medium text-white hover:bg-emerald-700">
                <i class="fas fa-flask me-1"></i>{{ $payTrans['btn_simulate'][$lang] ?? 'Simulate Successful Payment' }}
            </button>
        </form>
    @endif
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

// Ensure old intervals are cleared when partial is re-rendered
if (window.checkInterval) clearInterval(window.checkInterval);
if (window.countdownInterval) clearInterval(window.countdownInterval);

window.isChecking = false;

function checkPaymentStatus(isAutoCheck = false) {
    if (window.isChecking) return;
    
    window.isChecking = true;
    const button = document.getElementById('btnCheckStatus');
    const originalText = button ? button.innerHTML : '';
    
    if (button && !isAutoCheck) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>' + "{{ $payTrans['js_checking'][$lang] ?? 'Checking...' }}";
    }
    
    fetch('{{ route('customer.payment.paylabs.check-status', $order) }}')
        .then(response => response.json())
        .then(data => {
            if (data.paid || data.status === 'paid' || data.status === 'success' || data.status === '02') {
                if (window.checkInterval) clearInterval(window.checkInterval);
                
                document.getElementById('waitingPaymentContainer').innerHTML = `
                    <div class="text-center py-10">
                        <div class="mb-4 flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-emerald-100">
                            <i class="fas fa-check text-2xl text-emerald-600"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-black mb-1">${ "{{ $payTrans['success_title'][$lang] ?? 'Payment Successful!' }}" }</h2>
                        <p class="text-xs text-zinc-600 mb-6">${ "{{ $payTrans['success_desc'][$lang] ?? 'Thank you, your payment has been received' }}" }</p>
                        <div class="animate-spin h-6 w-6 border-2 border-zinc-200 border-t-emerald-600 rounded-full mx-auto mb-3"></div>
                        <p class="text-[10px] text-zinc-500">${ "{{ $payTrans['success_redirect'][$lang] ?? 'Redirecting to order page...' }}" }</p>
                    </div>
                `;
                
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
                window.isChecking = false;
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
            if (!isAutoCheck) alert("{{ $payTrans['js_failed_check'][$lang] ?? 'Failed to check status. Please try again.' }}");
            if (button) {
                button.disabled = false;
                button.innerHTML = originalText;
            }
            window.isChecking = false;
        });
}

// Start auto-check
window.checkInterval = setInterval(() => {
    checkPaymentStatus(true);
}, 10000);

// Timer
const expiryTimeRaw = '{{ $expiryTime ?? "" }}';
if (expiryTimeRaw) {
    const expiryTimeDate = new Date(expiryTimeRaw).getTime();
    window.countdownInterval = setInterval(() => {
        const now = new Date().getTime();
        const distance = expiryTimeDate - now;

        if (distance < 0) {
            clearInterval(window.countdownInterval);
            if (window.checkInterval) clearInterval(window.checkInterval);
            const alertBox = document.querySelector('.bg-amber-50');
            if(alertBox) {
                alertBox.innerHTML = '<p class="text-xs text-red-600"><i class="fas fa-times-circle me-2"></i><strong>' + "{{ $payTrans['js_expired'][$lang] ?? 'Payment expired' }}" + '</strong></p>';
            }
        }
    }, 1000);
}
</script>
