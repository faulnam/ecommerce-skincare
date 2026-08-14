@extends('layouts.app')

@php
    $jsonPath = public_path('translation/register.json');
    $regTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@section('title', $regTrans['meta_title'][$lang] ?? 'Register - Hijab')

@section('content')
<style>
    #mainNavbar { display: none !important; }
    .mobile-bottom-nav { display: none !important; }
</style>
@include('components.luxury-navbar')
<div class="min-h-[100dvh] w-full flex flex-col md:flex-row bg-white text-zinc-900 antialiased pt-16 md:pt-20">
    <!-- Left: Form -->
    <section class="flex-1 flex items-start md:items-center justify-center p-5 md:p-8 overflow-y-auto">
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-2">
                <h1 class="animate-element animate-delay-100 text-3xl md:text-4xl font-light leading-tight tracking-tighter">
                    {{ $regTrans['title_1'][$lang] ?? 'Create' }} <span class="font-semibold">{{ $regTrans['title_2'][$lang] ?? 'account' }}</span>
                </h1>
                <p class="animate-element animate-delay-200 text-sm text-zinc-500">{{ $regTrans['desc'][$lang] ?? 'Join Hijab and enjoy a premium hijab shopping experience.' }}</p>

                @if($errors->any())
                    <div class="animate-element animate-delay-250 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="registerAlert" class="hidden rounded-2xl border px-4 py-3 text-sm"></div>

                <form id="registerForm" action="{{ route('register.request-otp') }}" method="POST" class="space-y-2.5" data-request-otp-url="{{ route('register.request-otp') }}" data-verify-otp-url="{{ route('register.verify-otp') }}">
                    @csrf

                    <div class="animate-element animate-delay-300">
                        <label for="name" class="text-xs font-medium text-zinc-500">{{ $regTrans['label_name'][$lang] ?? 'Full Name' }}</label>
                        <div class="mt-0.5 rounded-xl border border-zinc-200 bg-zinc-50 transition-colors focus-within:border-violet-400 focus-within:bg-violet-50">
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="{{ $regTrans['placeholder_name'][$lang] ?? 'Your full name' }}" class="w-full bg-transparent text-sm p-2 rounded-xl focus:outline-none">
                        </div>
                    </div>

                    <div class="animate-element animate-delay-350">
                        <label for="email" class="text-xs font-medium text-zinc-500">{{ $regTrans['label_email'][$lang] ?? 'Email Address' }}</label>
                        <div class="mt-0.5 rounded-xl border border-zinc-200 bg-zinc-50 transition-colors focus-within:border-violet-400 focus-within:bg-violet-50">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="{{ $regTrans['placeholder_email'][$lang] ?? 'email@example.com' }}" class="w-full bg-transparent text-sm p-2 rounded-xl focus:outline-none">
                        </div>
                    </div>

                    <div class="animate-element animate-delay-400">
                        <label for="phone" class="text-xs font-medium text-zinc-500">{{ $regTrans['label_phone'][$lang] ?? 'Phone Number' }}</label>
                        <div class="mt-0.5 rounded-xl border border-zinc-200 bg-zinc-50 transition-colors focus-within:border-violet-400 focus-within:bg-violet-50">
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="{{ $regTrans['placeholder_phone'][$lang] ?? '08xxxxxxxxxx' }}" class="w-full bg-transparent text-sm p-2 rounded-xl focus:outline-none">
                        </div>
                    </div>

                    <div class="animate-element animate-delay-450">
                        <label for="address" class="text-xs font-medium text-zinc-500">{{ $regTrans['label_address'][$lang] ?? 'Address' }}</label>
                        <div class="mt-0.5 rounded-xl border border-zinc-200 bg-zinc-50 transition-colors focus-within:border-violet-400 focus-within:bg-violet-50">
                            <input type="text" id="address" name="address" value="{{ old('address') }}" required placeholder="{{ $regTrans['placeholder_address'][$lang] ?? 'Your full address' }}" class="w-full bg-transparent text-sm p-2 rounded-xl focus:outline-none">
                        </div>
                    </div>

                    <div class="animate-element animate-delay-500">
                        <label for="password" class="text-xs font-medium text-zinc-500">{{ $regTrans['label_password'][$lang] ?? 'Password' }}</label>
                        <div class="mt-0.5 rounded-xl border border-zinc-200 bg-zinc-50 transition-colors focus-within:border-violet-400 focus-within:bg-violet-50">
                            <input type="password" id="password" name="password" required placeholder="{{ $regTrans['placeholder_password'][$lang] ?? 'Minimum 8 characters' }}" class="w-full bg-transparent text-sm p-2 rounded-xl focus:outline-none">
                        </div>
                    </div>

                    <div class="animate-element animate-delay-550">
                        <label for="password_confirmation" class="text-xs font-medium text-zinc-500">{{ $regTrans['label_confirm'][$lang] ?? 'Confirm Password' }}</label>
                        <div class="mt-0.5 rounded-xl border border-zinc-200 bg-zinc-50 transition-colors focus-within:border-violet-400 focus-within:bg-violet-50">
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="{{ $regTrans['placeholder_confirm'][$lang] ?? 'Repeat your password' }}" class="w-full bg-transparent text-sm p-2 rounded-xl focus:outline-none">
                        </div>
                    </div>

                    <div class="animate-element animate-delay-600">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-refresh-expired="auto"></div>
                        @error('cf-turnstile-response')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" id="registerSubmitBtn" class="animate-element animate-delay-600 w-full rounded-xl bg-zinc-900 py-2 font-medium text-white hover:bg-zinc-800 transition-colors">
                        {{ $regTrans['btn_submit'][$lang] ?? 'Create Account' }}
                    </button>
                </form>

                <div class="animate-element animate-delay-700 relative flex items-center justify-center py-3">
                    <span class="w-full border-t border-zinc-200"></span>
                    <span class="px-4 text-sm text-zinc-500 bg-white absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 whitespace-nowrap">{{ $regTrans['divider_text'][$lang] ?? 'Or continue with' }}</span>
                </div>

                <button type="button" id="googleSignInBtn" class="animate-element animate-delay-750 flex w-full items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    {{ $regTrans['btn_google'][$lang] ?? 'Sign up with Google' }}
                </button>

                <p class="animate-element animate-delay-800 text-center text-sm text-zinc-500">
                    {{ $regTrans['footer_text'][$lang] ?? 'Already have an account?' }} <a href="{{ route('login') }}" class="text-violet-500 hover:underline transition-colors">{{ $regTrans['btn_login'][$lang] ?? 'Sign In' }}</a>
                </p>
            </div>
        </div>
    </section>

    <!-- Right: Hero Image + Testimonials -->
    <section class="hidden md:block flex-1 relative p-4 min-h-[100dvh] md:min-h-0 md:sticky md:top-0 md:h-[100dvh]">
        <div class="animate-slide-right animate-delay-300 absolute inset-4 rounded-3xl bg-cover bg-center" style="background-image: url('{{ asset('storage/model-1.jpg') }}');"></div>
    </section>
</div>

<!-- OTP Verification Modal -->
<div id="otpModal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/45" data-close-otp-modal></div>
    <div class="relative flex min-h-full items-center justify-center px-4 py-8">
        <div class="w-full max-w-md rounded-3xl border border-black/10 bg-white p-6 shadow-[0_18px_50px_rgba(0,0,0,0.18)] sm:p-7">
            <div class="mb-5 text-center">
                <h3 class="text-xl font-semibold tracking-tight text-black">{{ $regTrans['modal_title'][$lang] ?? 'Verifikasi OTP' }}</h3>
                <p class="mt-2 text-sm text-zinc-600">{{ $regTrans['modal_desc'][$lang] ?? 'Enter the 6-digit OTP code sent to' }} <span id="otpTargetEmail" class="font-medium text-black"></span>.</p>
            </div>

            <div id="otpAlert" class="mb-4 hidden rounded-2xl border px-4 py-3 text-sm"></div>

            <form id="otpVerifyForm" class="space-y-4">
                <div>
                    <label for="otpCode" class="mb-2 block text-sm font-medium text-zinc-700">{{ $regTrans['label_otp'][$lang] ?? 'OTP Code' }}</label>
                    <input
                        id="otpCode"
                        name="otp"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        required
                        class="w-full rounded-xl border border-black/15 px-4 py-3 text-center text-lg tracking-[0.35em] text-black outline-none transition focus:border-black/30"
                        placeholder="000000"
                    >
                </div>

                <button type="submit" id="verifyOtpBtn" class="inline-flex w-full items-center justify-center rounded-full bg-black px-5 py-3 text-sm font-medium text-white transition duration-300 hover:bg-zinc-800">
                    {{ $regTrans['btn_verify'][$lang] ?? 'Verify & Activate Account' }}
                </button>
            </form>

            <div class="mt-4 flex items-center justify-between gap-3">
                <button type="button" id="resendOtpBtn" class="text-sm font-medium text-black underline decoration-black/30 underline-offset-4 transition hover:decoration-black">{{ $regTrans['btn_resend'][$lang] ?? 'Resend OTP' }}</button>
                <button type="button" class="text-sm text-zinc-500 transition hover:text-black" data-close-otp-modal>{{ $regTrans['btn_close'][$lang] ?? 'Close' }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
        <style>
        .marquee-container { display: flex; overflow: hidden; }
        .marquee-content { display: flex; animation: marquee 30s linear infinite; white-space: nowrap; }
        .marquee-item { font-size: 11px; letter-spacing: 0.15em; font-weight: 600; padding-right: 1rem; }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        @keyframes fadeSlideIn { from { opacity: 0; filter: blur(8px); transform: translateY(20px); } to { opacity: 1; filter: blur(0); transform: translateY(0); } }
        @keyframes slideRightIn { from { opacity: 0; filter: blur(8px); transform: translateX(40px); } to { opacity: 1; filter: blur(0); transform: translateX(0); } }
        @keyframes testimonialIn { from { opacity: 0; filter: blur(8px); transform: translateY(20px) scale(0.95); } to { opacity: 1; filter: blur(0); transform: translateY(0) scale(1); } }
        .animate-element { opacity: 0; animation: fadeSlideIn 0.8s ease forwards; }
        .animate-slide-right { opacity: 0; animation: slideRightIn 0.9s ease forwards; }
        .animate-testimonial { opacity: 0; animation: testimonialIn 0.8s ease forwards; }
        .animate-delay-50 { animation-delay: 50ms; }
        .animate-delay-100 { animation-delay: 100ms; }
        .animate-delay-200 { animation-delay: 200ms; }
        .animate-delay-250 { animation-delay: 250ms; }
        .animate-delay-300 { animation-delay: 300ms; }
        .animate-delay-350 { animation-delay: 350ms; }
        .animate-delay-400 { animation-delay: 400ms; }
        .animate-delay-450 { animation-delay: 450ms; }
        .animate-delay-500 { animation-delay: 500ms; }
        .animate-delay-550 { animation-delay: 550ms; }
        .animate-delay-600 { animation-delay: 600ms; }
        .animate-delay-650 { animation-delay: 650ms; }
        .animate-delay-700 { animation-delay: 700ms; }
        .animate-delay-750 { animation-delay: 750ms; }
        .animate-delay-800 { animation-delay: 800ms; }
        .animate-delay-1000 { animation-delay: 1000ms; }
        .animate-delay-1200 { animation-delay: 1200ms; }
    </style>
@endpush

@push('scripts')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
    <script>
        // Firebase Configuration
        const firebaseConfig = {
            apiKey: "AIzaSyAKtin9WevURPmDUdoBwanJNS9kc0prh_A",
            authDomain: "hijab-fba4f.firebaseapp.com",
            projectId: "hijab-fba4f",
            storageBucket: "hijab-fba4f.firebasestorage.app",
            messagingSenderId: "660330989978",
            appId: "1:660330989978:web:70e570eb75b668fdef9223",
            measurementId: "G-6KZ86FEM4K"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();

        // Google Sign-In
        const googleSignInBtn = document.getElementById('googleSignInBtn');
        if (googleSignInBtn) {
            googleSignInBtn.addEventListener('click', async function() {
                const provider = new firebase.auth.GoogleAuthProvider();
                
                try {
                    const result = await auth.signInWithPopup(provider);
                    const idToken = await result.user.getIdToken();
                    
                    // Send token to backend
                    const response = await fetch('{{ route('auth.firebase') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            idToken: idToken,
                            displayName: result.user.displayName,
                            email: result.user.email,
                            photoURL: result.user.photoURL
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        window.location.href = data.redirect || '{{ route('home') }}';
                    } else {
                        alert(data.message || 'Registration failed');
                        await auth.signOut();
                    }
                } catch (error) {
                    console.error('Google sign-in error:', error);
                    alert('Gagal daftar dengan Google: ' + error.message);
                }
            });
        }
    </script>
    <script>
        (function () {
            const toggle = document.querySelector('[data-mobile-menu-toggle]');
            const menu = document.querySelector('[data-mobile-menu]');
            if (toggle && menu) {
                toggle.addEventListener('click', function () {
                    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    menu.classList.toggle('hidden', isOpen);
                });
            }
        })();
        (function () {
            const form = document.getElementById('registerForm');
            if (!form) return;

            const submitBtn = document.getElementById('registerSubmitBtn');
            const alertBox = document.getElementById('registerAlert');
            const modal = document.getElementById('otpModal');
            const otpForm = document.getElementById('otpVerifyForm');
            const verifyBtn = document.getElementById('verifyOtpBtn');
            const otpInput = document.getElementById('otpCode');
            const otpAlert = document.getElementById('otpAlert');
            const targetEmail = document.getElementById('otpTargetEmail');
            const resendBtn = document.getElementById('resendOtpBtn');
            const closeButtons = document.querySelectorAll('[data-close-otp-modal]');

            const requestOtpUrl = form.dataset.requestOtpUrl;
            const verifyOtpUrl = form.dataset.verifyOtpUrl;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            let pendingEmail = '';
            let latestPayload = {};

            const showAlert = (element, message, type = 'error') => {
                if (!element) return;

                const classes = type === 'success'
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                    : 'border-red-200 bg-red-50 text-red-700';

                element.className = `mb-4 rounded-2xl border px-4 py-3 text-sm ${classes}`;
                element.textContent = message;
                element.classList.remove('hidden');
            };

            const hideAlert = (element) => {
                if (!element) return;
                element.classList.add('hidden');
                element.textContent = '';
            };

            const openModal = () => {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                otpInput.focus();
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            const parseErrorMessage = (payload, fallback = 'Terjadi kesalahan. Silakan coba lagi.') => {
                if (!payload) return fallback;

                if (payload.errors && typeof payload.errors === 'object') {
                    const firstKey = Object.keys(payload.errors)[0];
                    if (firstKey && Array.isArray(payload.errors[firstKey]) && payload.errors[firstKey][0]) {
                        return payload.errors[firstKey][0];
                    }
                }

                return payload.message || fallback;
            };

            const sendOtpRequest = async (payload) => {
                const response = await fetch(requestOtpUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(parseErrorMessage(data, 'Gagal mengirim OTP.'));
                }

                return data;
            };

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                hideAlert(alertBox);

                const formData = new FormData(form);
                const payload = Object.fromEntries(formData.entries());
                latestPayload = payload;

                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');

                try {
                    const data = await sendOtpRequest(payload);

                    pendingEmail = data.email || payload.email || '';
                    targetEmail.textContent = pendingEmail;
                    otpInput.value = '';
                    hideAlert(otpAlert);
                    openModal();
                    showAlert(alertBox, data.message || 'OTP has been sent to your email.', 'success');
                } catch (error) {
                    showAlert(alertBox, error.message || 'Failed to send OTP.', 'error');
                    if (window.turnstile) { turnstile.reset(); }
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                }
            });

            otpForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                hideAlert(otpAlert);

                const otp = (otpInput.value || '').replace(/\D/g, '').slice(0, 6);
                otpInput.value = otp;

                if (!pendingEmail) {
                    showAlert(otpAlert, 'Verification email not found. Please register again.', 'error');
                    return;
                }

                verifyBtn.disabled = true;
                verifyBtn.classList.add('opacity-70', 'cursor-not-allowed');

                try {
                    const response = await fetch(verifyOtpUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            email: pendingEmail,
                            otp,
                        }),
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(parseErrorMessage(data, 'Verifikasi OTP gagal.'));
                    }

                    window.location.href = data.redirect || '{{ route('customer.products.index') }}';
                } catch (error) {
                    showAlert(otpAlert, error.message || 'Verifikasi OTP gagal.', 'error');
                } finally {
                    verifyBtn.disabled = false;
                    verifyBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                }
            });

            resendBtn.addEventListener('click', async function () {
                hideAlert(otpAlert);

                if (!latestPayload.email) {
                    showAlert(otpAlert, 'Registration data not available. Please fill the form again.', 'error');
                    return;
                }

                resendBtn.disabled = true;
                resendBtn.classList.add('opacity-70', 'cursor-not-allowed');

                try {
                    const data = await sendOtpRequest(latestPayload);
                    pendingEmail = data.email || latestPayload.email;
                    targetEmail.textContent = pendingEmail;
                    showAlert(otpAlert, data.message || 'New OTP has been sent.', 'success');
                } catch (error) {
                    showAlert(otpAlert, error.message || 'Failed to resend OTP.', 'error');
                } finally {
                    resendBtn.disabled = false;
                    resendBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                }
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });
        })();
    </script>
@endpush