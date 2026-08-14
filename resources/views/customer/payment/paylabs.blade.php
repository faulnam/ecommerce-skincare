@extends('layouts.app')

@section('title', 'Paylabs Payment')

@push('styles')
<style>
    body { padding-top: 0 !important; padding-bottom: 0 !important; }
    #mainNavbar, .mobile-bottom-nav, .footer { display: none !important; }
    .payment-option input[type="radio"] { display: none; }
    .payment-option .option-content {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }
    .payment-option input[type="radio"]:checked + .option-content {
        border-color: #000;
        background: #fafafa;
    }
    .payment-option:hover .option-content { border-color: #000; }
    .payment-option .option-content i {
        font-size: 24px;
        color: #000;
        display: block;
        margin-bottom: 8px;
    }
    .payment-option .option-content span {
        font-weight: 500;
        color: #000;
    }
</style>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/paylabs.json');
    $plTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@include('components.luxury-navbar')

<script>
    function generateUUID() {
        if (typeof crypto !== 'undefined' && crypto.randomUUID) {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

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

        // Generate idempotency key once on page load
        document.getElementById('paylabsIdempotencyKey').value = generateUUID();
    });
</script>

<div class="min-h-screen bg-zinc-50 pb-12" style="padding-top: 140px;">
    <div class="mx-auto max-w-6xl px-6">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-black">{{ $plTrans['page_title'][$lang] ?? 'Select Payment Method' }}</h1>
            <p class="mt-2 text-sm text-zinc-500">{{ $plTrans['label_order'][$lang] ?? 'Order:' }} {{ $order->order_number }} • Total: {{ $order->formatted_total }}</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <!-- Kiri: Pilih Metode -->
            <div class="lg:col-span-2">
                <!-- Order Number Card (Moved from full width to here) -->
                <div class="mb-6 rounded-xl bg-white border border-zinc-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-zinc-500 mb-1">{{ $plTrans['label_order_no'][$lang] ?? 'Order Number' }}</p>
                            <p class="text-lg font-mono font-bold text-black tracking-wide">{{ $order->order_number }}</p>
                        </div>
                        <button onclick="copyOrderNumber()" class="rounded-lg bg-black px-4 py-2 text-sm text-white hover:bg-black/90 transition">
                            <i class="fas fa-copy me-1"></i> {{ $plTrans['btn_copy'][$lang] ?? 'Copy' }}
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-zinc-500">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $plTrans['hint_save_order'][$lang] ?? 'Save this order number to track your order status' }}
                    </p>
                </div>

                <script>
                    function copyOrderNumber() {
                        const text = '{{ $order->order_number }}';
                        navigator.clipboard.writeText(text).then(() => {
                            alert("{{ $plTrans['toast_copied'][$lang] ?? 'Order number copied!' }}");
                        });
                    }
                </script>

                <form action="{{ route('customer.payment.paylabs.process', $order) }}" method="POST" class="space-y-6" id="paylabsForm">
                    @csrf
                    <input type="hidden" name="idempotency_key" id="paylabsIdempotencyKey" value="">

                    @php
                        $paymentMethods = config('paylabs.payment_methods');
                        $vaList = $paymentMethods['va'] ?? [];
                        $qrisList = $paymentMethods['qris'] ?? [];
                        $ewalletList = $paymentMethods['ewallet'] ?? [];
                        $retailList = $paymentMethods['retail'] ?? [];
                    @endphp

                    @php
                        $logos = [
                            'VA_BCA'     => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg',
                            'VA_BNI'     => 'https://upload.wikimedia.org/wikipedia/id/5/55/BNI_logo.svg',
                            'VA_BRI'     => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/BRI_2020.svg',
                            'VA_MANDIRI' => 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg',
                            'VA_PERMATA' => 'https://upload.wikimedia.org/wikipedia/commons/a/a8/PermataBank.png',
                            'VA_CIMB'    => 'https://upload.wikimedia.org/wikipedia/commons/2/23/CIMB_Niaga_logo.svg',
                            'QRIS'       => 'https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg',
                            'EWALLET_OVO'       => 'https://upload.wikimedia.org/wikipedia/commons/e/e1/OVO_Logo.svg',
                            'EWALLET_DANA'      => 'https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg',
                            'EWALLET_GOPAY'     => 'https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg',
                            'EWALLET_SHOPEEPAY' => 'https://upload.wikimedia.org/wikipedia/commons/f/fe/ShopeePay_Logo.svg',
                            'EWALLET_LINKAJA'   => 'https://upload.wikimedia.org/wikipedia/commons/8/85/LinkAja.svg',
                            'RETAIL_ALFAMART'   => 'https://upload.wikimedia.org/wikipedia/commons/8/86/Alfamart_logo.svg',
                            'RETAIL_INDOMARET'  => 'https://upload.wikimedia.org/wikipedia/commons/9/9d/Logo_Indomaret.png',
                        ];
                    @endphp

                    @if(!empty($vaList))
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-black/5">
                        <h3 class="mb-4 text-sm font-semibold text-black"><i class="fas fa-university me-2"></i>{{ $plTrans['title_va'][$lang] ?? 'Virtual Account' }}</h3>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($vaList as $value => $label)
                            <label class="payment-option">
                                <input type="radio" name="payment_channel" value="{{ $value }}" required>
                                <div class="option-content">
                                    @if(isset($logos[$value]))
                                        <img src="{{ $logos[$value] }}" alt="{{ $label }}" class="h-6 mx-auto mb-2 object-contain">
                                    @else
                                        <i class="fas fa-university"></i>
                                    @endif
                                    <span class="text-xs">{{ $label }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($qrisList))
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-black/5">
                        <h3 class="mb-4 text-sm font-semibold text-black"><i class="fas fa-qrcode me-2"></i>{{ $plTrans['title_qris'][$lang] ?? 'QRIS' }}</h3>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($qrisList as $value => $label)
                            <label class="payment-option">
                                <input type="radio" name="payment_channel" value="{{ $value }}" required>
                                <div class="option-content">
                                    @if(isset($logos[$value]))
                                        <img src="{{ $logos[$value] }}" alt="{{ $label }}" class="h-6 mx-auto mb-2 object-contain">
                                    @else
                                        <i class="fas fa-qrcode"></i>
                                    @endif
                                   <span class="text-xs">{{ $label }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($ewalletList))
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-black/5">
                        <h3 class="mb-4 text-sm font-semibold text-black"><i class="fas fa-wallet me-2"></i>{{ $plTrans['title_ewallet'][$lang] ?? 'E-Wallet' }}</h3>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($ewalletList as $value => $label)
                            <label class="payment-option">
                                <input type="radio" name="payment_channel" value="{{ $value }}" required>
                                <div class="option-content">
                                    @if(isset($logos[$value]))
                                        <img src="{{ $logos[$value] }}" alt="{{ $label }}" class="h-6 mx-auto mb-2 object-contain">
                                    @else
                                        <i class="fas fa-wallet"></i>
                                    @endif
                                    <span class="text-xs">{{ $label }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($retailList))
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-black/5">
                        <h3 class="mb-4 text-sm font-semibold text-black"><i class="fas fa-store me-2"></i>{{ $plTrans['title_retail'][$lang] ?? 'Gerai Retail' }}</h3>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($retailList as $value => $label)
                            <label class="payment-option">
                                <input type="radio" name="payment_channel" value="{{ $value }}" required>
                                <div class="option-content">
                                    @if(isset($logos[$value]))
                                        <img src="{{ $logos[$value] }}" alt="{{ $label }}" class="h-6 mx-auto mb-2 object-contain">
                                    @else
                                        <i class="fas fa-store"></i>
                                    @endif
                                    <span class="text-xs">{{ $label }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="flex gap-3">
                        <button type="submit" 
                                class="w-full rounded-xl bg-black py-3 text-center text-sm font-medium text-white transition hover:bg-black/90"
                                id="btnLanjutkan"
                                {{ !empty($existingPaymentHtml) ? 'disabled' : '' }}>
                            {{ !empty($existingPaymentHtml) ? 'Menunggu Pembayaran' : ($plTrans['btn_continue'][$lang] ?? 'Lanjutkan') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Kanan: Waiting / Instructions -->
            <div class="lg:col-span-1" id="paymentInstructionArea">
                @if(!empty($existingPaymentHtml))
                    {!! $existingPaymentHtml !!}
                @else
                    <div class="sticky top-32 rounded-2xl bg-white p-6 shadow-sm border border-black/5 text-center flex flex-col items-center justify-center min-h-[300px]">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100">
                            <i class="fas fa-mouse-pointer text-2xl text-zinc-400"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-black mb-2">Pilih Pembayaran</h2>
                        <p class="text-xs text-zinc-500">Silakan pilih salah satu metode pembayaran di sebelah kiri untuk melanjutkan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paylabsForm');
    const instructionArea = document.getElementById('paymentInstructionArea');
    const btnLanjutkan = document.getElementById('btnLanjutkan');
    const radios = document.querySelectorAll('input[name="payment_channel"]');
    
    // Check if there's already a selected payment method (from existing transaction)
    const hasExistingPayment = {!! !empty($existingPaymentHtml) ? 'true' : 'false' !!};
    const existingChannel = "{{ $order->payment_channel ?? '' }}";
    
    if (hasExistingPayment) {
        // Disable all radios if payment exists
        radios.forEach(r => {
            r.disabled = true;
            if (r.value === existingChannel) {
                r.checked = true;
                // Add a visual indicator for locked state
                r.parentElement.querySelector('.option-content').classList.add('bg-zinc-100', 'border-black');
            }
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Check if any radio is selected
        const selected = document.querySelector('input[name="payment_channel"]:checked');
        if (!selected) {
            alert('Silakan pilih metode pembayaran terlebih dahulu.');
            return;
        }

        // Scroll to instruction area on mobile
        if (window.innerWidth < 1024) {
            setTimeout(() => {
                instructionArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }

        // Show loading spinner
        instructionArea.innerHTML = `
            <div class="sticky top-32 rounded-2xl bg-white p-6 shadow-sm border border-black/5 text-center flex flex-col items-center justify-center min-h-[300px]">
                <div class="animate-spin h-10 w-10 border-4 border-zinc-200 border-t-black rounded-full mb-4"></div>
                <p class="text-sm text-zinc-500">Memproses pembayaran...</p>
            </div>
        `;

        // Disable button to prevent double submit
        btnLanjutkan.disabled = true;
        btnLanjutkan.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
        radios.forEach(r => r.disabled = true);

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                instructionArea.innerHTML = data.html;
                btnLanjutkan.innerHTML = 'Menunggu Pembayaran';
                
                // Execute scripts in the injected HTML
                const scripts = instructionArea.querySelectorAll('script');
                scripts.forEach(script => {
                    const newScript = document.createElement('script');
                    newScript.text = script.innerHTML;
                    document.body.appendChild(newScript).parentNode.removeChild(newScript);
                });
            } else {
                // Re-enable form on error
                btnLanjutkan.disabled = false;
                btnLanjutkan.innerHTML = "{{ $plTrans['btn_continue'][$lang] ?? 'Lanjutkan' }}";
                radios.forEach(r => r.disabled = false);

                instructionArea.innerHTML = `
                    <div class="sticky top-32 rounded-2xl bg-red-50 p-6 text-center border border-red-200">
                        <i class="fas fa-exclamation-circle text-red-500 text-3xl mb-3"></i>
                        <p class="text-red-700 text-sm">${data.message || 'Gagal memproses pembayaran. Silakan coba lagi.'}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Re-enable form on error
            btnLanjutkan.disabled = false;
            btnLanjutkan.innerHTML = "{{ $plTrans['btn_continue'][$lang] ?? 'Lanjutkan' }}";
            radios.forEach(r => r.disabled = false);

            instructionArea.innerHTML = `
                <div class="sticky top-32 rounded-2xl bg-red-50 p-6 text-center border border-red-200">
                    <i class="fas fa-wifi text-red-500 text-3xl mb-3"></i>
                    <p class="text-red-700 text-sm">Terjadi kesalahan jaringan. Silakan coba lagi.</p>
                </div>
            `;
        });
    });
});
</script>

@endsection