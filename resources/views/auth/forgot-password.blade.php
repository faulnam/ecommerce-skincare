@extends('layouts.app')

@php
    $jsonPath = public_path('translation/forgotpassword.json');
    $forgotTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@section('title', $forgotTrans['meta_title'][$lang] ?? 'Forgot Password - LUMINA')

@section('content')
    <div class="min-h-screen bg-[#f5f5f7] text-black antialiased">
        <header class="fixed left-0 top-0 z-50 w-full border-b border-black/6 bg-white/80 backdrop-blur-xl md:sticky">
            <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-6 md:px-10 lg:px-12">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ config('filesystems.disks.r2.url').'/logo.png' }}" alt="LUMINA" class="h-7 w-7 object-contain" loading="lazy">
                    <span class="text-xl font-semibold tracking-tight text-black">LUMINA</span>
                </a>

                <nav class="hidden items-center gap-8 md:flex">
                    <a href="{{ route('home') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">{{ $forgotTrans['nav_home'][$lang] ?? 'Home' }}</a>
                    <a href="{{ route('new-arrivals') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">{{ $forgotTrans['nav_new_arrivals'][$lang] ?? 'New Arrivals' }}</a>
                    <a href="{{ route('brand-catalog') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">{{ $forgotTrans['nav_brand_catalog'][$lang] ?? 'Brand Catalog' }}</a>
                    <a href="{{ route('about') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">{{ $forgotTrans['nav_about'][$lang] ?? 'About' }}</a>
                    <a href="{{ route('contact') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">{{ $forgotTrans['nav_contact'][$lang] ?? 'Contact' }}</a>
                </nav>

                <div class="flex items-center gap-3 text-black/80">
                    @guest
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1 rounded-full border border-black/15 px-3 py-1.5 text-xs font-medium text-black/80 transition duration-300 hover:border-black/30 hover:text-black" aria-label="Login">
                            <i class="fas fa-sign-in-alt text-[11px]"></i>
                            <span>{{ $forgotTrans['nav_login'][$lang] ?? 'Login' }}</span>
                        </a>
                    @endguest
                    
                    <a href="{{ route('customer.wishlist.index') }}" class="relative transition duration-300 hover:text-black" aria-label="Wishlist" title="Wishlist">
                        <i class="fas fa-heart text-sm"></i>
                        @php
                            if (auth()->check() && auth()->user()->role === 'customer') {
                                $wishlistCount = auth()->user()->wishlistItems()->count();
                            } else {
                                $guestWishlist = session()->get('guest_wishlist', []);
                                $wishlistCount = count($guestWishlist);
                            }
                        @endphp
                        @if($wishlistCount > 0)
                            <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">{{ $wishlistCount > 9 ? '9+' : $wishlistCount }}</span>
                        @endif
                    </a>
                    
                    <a href="{{ route('customer.cart.index') }}" class="relative transition duration-300 hover:text-black" aria-label="Cart" title="Keranjang">
                        <i class="fas fa-shopping-bag text-sm"></i>
                        @php
                            if (auth()->check() && auth()->user()->role === 'customer') {
                                $cartCount = auth()->user()->cartItems()->sum('quantity');
                            } else {
                                $guestCart = session()->get('guest_cart', []);
                                $cartCount = array_sum(array_column($guestCart, 'quantity'));
                            }
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                        @endif
                    </a>
                    
                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-black/15 text-black transition duration-300 hover:border-black/35 md:hidden" data-mobile-menu-toggle aria-label="Toggle navigation" aria-expanded="false">
                        <i class="fas fa-bars text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="hidden border-t border-black/10 bg-white/95 px-6 py-4 md:hidden" data-mobile-menu>
                <nav class="flex flex-col gap-3 text-sm font-medium text-black/85">
                    <a href="{{ route('home') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">{{ $forgotTrans['nav_home'][$lang] ?? 'Home' }}</a>
                    <a href="{{ route('new-arrivals') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">{{ $forgotTrans['nav_new_arrivals'][$lang] ?? 'New Arrivals' }}</a>
                    <a href="{{ route('brand-catalog') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">{{ $forgotTrans['nav_brand_catalog'][$lang] ?? 'Brand Catalog' }}</a>
                    <a href="{{ route('about') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">{{ $forgotTrans['nav_about'][$lang] ?? 'About' }}</a>
                    <a href="{{ route('contact') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">{{ $forgotTrans['nav_contact'][$lang] ?? 'Contact' }}</a>
                </nav>
            </div>
        </header>

        <main class="pt-16 md:pt-0">
            <section class="px-6 py-10 md:px-10 lg:px-12 lg:py-14">
                <div class="mx-auto w-full max-w-md">
                    <div class="rounded-3xl border border-black/8 bg-white p-6 shadow-[0_16px_42px_rgba(0,0,0,0.08)] sm:p-8">
                        <div class="mb-6 text-center">
                            <img src="{{ asset(config('branding.logo', 'storage/logo.png')) }}" alt="{{ config('branding.name', 'LUMINA') }}" class="mx-auto h-12 w-auto">
                            <h2 class="mt-4 text-2xl font-semibold tracking-tight text-black">{{ $forgotTrans['card_title'][$lang] ?? 'Forgot Password' }}</h2>
                            <p class="mt-2 text-sm text-zinc-600">{{ $forgotTrans['card_desc'][$lang] ?? 'Enter your email to receive OTP code' }}</p>
                        </div>

                        <div id="alertContainer"></div>

                        <!-- Step 1: Request OTP -->
                        <form id="requestOtpForm" class="space-y-4">
                            @csrf
                            <div>
                                <label for="email" class="mb-2 block text-sm font-medium text-zinc-700">{{ $forgotTrans['label_email'][$lang] ?? 'Email' }}</label>
                                <div class="flex items-center gap-2 rounded-xl border border-black/10 bg-white px-3 focus-within:border-black/30">
                                    <i class="fas fa-envelope text-xs text-zinc-400"></i>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        required
                                        autofocus
                                        class="w-full border-0 bg-transparent py-3 text-sm text-black outline-none focus:ring-0"
                                        placeholder="{{ $forgotTrans['placeholder_email'][$lang] ?? 'email@example.com' }}"
                                    >
                                </div>
                            </div>

                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-black px-5 py-3 text-sm font-medium text-white transition duration-300 hover:bg-zinc-800">
                                <i class="fas fa-paper-plane mr-2 text-xs"></i>{{ $forgotTrans['btn_send_otp'][$lang] ?? 'Send OTP Code' }}
                            </button>
                        </form>

                        <!-- Step 2: Verify OTP -->
                        <form id="verifyOtpForm" class="hidden space-y-4">
                            @csrf
                            <input type="hidden" id="verify_email" name="email">
                            
                            <div>
                                <label for="otp" class="mb-2 block text-sm font-medium text-zinc-700">{{ $forgotTrans['label_otp'][$lang] ?? 'OTP Code' }}</label>
                                <div class="flex items-center gap-2 rounded-xl border border-black/10 bg-white px-3 focus-within:border-black/30">
                                    <i class="fas fa-key text-xs text-zinc-400"></i>
                                    <input
                                        type="text"
                                        id="otp"
                                        name="otp"
                                        maxlength="6"
                                        pattern="[0-9]{6}"
                                        required
                                        class="w-full border-0 bg-transparent py-3 text-sm text-black outline-none focus:ring-0"
                                        placeholder="{{ $forgotTrans['placeholder_otp'][$lang] ?? 'Enter 6-digit OTP code' }}"
                                    >
                                </div>
                                <p class="mt-1 text-xs text-zinc-500">{{ $forgotTrans['hint_otp'][$lang] ?? 'OTP code has been sent to your email' }}</p>
                            </div>

                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-black px-5 py-3 text-sm font-medium text-white transition duration-300 hover:bg-zinc-800">
                                <i class="fas fa-check mr-2 text-xs"></i>{{ $forgotTrans['btn_verify_otp'][$lang] ?? 'Verify OTP' }}
                            </button>

                            <button type="button" id="resendOtpBtn" class="w-full text-center text-sm text-zinc-600 hover:text-black">
                                {{ $forgotTrans['btn_resend_otp'][$lang] ?? 'Resend OTP code' }}
                            </button>
                        </form>

                        <!-- Step 3: Reset Password -->
                        <form id="resetPasswordForm" class="hidden space-y-4">
                            @csrf
                            <input type="hidden" id="reset_email" name="email">
                            
                            <div>
                                <label for="password" class="mb-2 block text-sm font-medium text-zinc-700">{{ $forgotTrans['label_password'][$lang] ?? 'New Password' }}</label>
                                <div class="flex items-center gap-2 rounded-xl border border-black/10 bg-white px-3 focus-within:border-black/30">
                                    <i class="fas fa-lock text-xs text-zinc-400"></i>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        required
                                        minlength="8"
                                        class="w-full border-0 bg-transparent py-3 text-sm text-black outline-none focus:ring-0"
                                        placeholder="{{ $forgotTrans['placeholder_password'][$lang] ?? 'Minimum 8 characters' }}"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-zinc-700">{{ $forgotTrans['label_confirm'][$lang] ?? 'Confirm Password' }}</label>
                                <div class="flex items-center gap-2 rounded-xl border border-black/10 bg-white px-3 focus-within:border-black/30">
                                    <i class="fas fa-lock text-xs text-zinc-400"></i>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        required
                                        minlength="8"
                                        class="w-full border-0 bg-transparent py-3 text-sm text-black outline-none focus:ring-0"
                                        placeholder="{{ $forgotTrans['placeholder_confirm'][$lang] ?? 'Repeat new password' }}"
                                    >
                                </div>
                            </div>

                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-black px-5 py-3 text-sm font-medium text-white transition duration-300 hover:bg-zinc-800">
                                <i class="fas fa-save mr-2 text-xs"></i>{{ $forgotTrans['btn_reset_submit'][$lang] ?? 'Reset Password' }}
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-zinc-600">
                            {{ $forgotTrans['footer_text'][$lang] ?? 'Already remember password?' }}
                            <a href="{{ route('login') }}" class="font-medium text-black underline decoration-black/30 underline-offset-4 transition hover:decoration-black">{{ $forgotTrans['footer_login'][$lang] ?? 'Login' }}</a>
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection

@push('styles')
        <style>
        #mainNavbar,
        .mobile-bottom-nav {
            display: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
            const mobileMenu = document.querySelector('[data-mobile-menu]');

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                    mobileMenuToggle.setAttribute('aria-expanded', String(!mobileMenu.classList.contains('hidden')));
                });
            }

            const requestOtpForm = document.getElementById('requestOtpForm');
            const verifyOtpForm = document.getElementById('verifyOtpForm');
            const resetPasswordForm = document.getElementById('resetPasswordForm');
            const alertContainer = document.getElementById('alertContainer');
            const resendOtpBtn = document.getElementById('resendOtpBtn');

            let currentEmail = '';

            function showAlert(message, type = 'error') {
                const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
                alertContainer.innerHTML = `
                    <div class="mb-5 rounded-2xl border ${bgColor} px-4 py-3 text-sm">
                        ${message}
                    </div>
                `;
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 5000);
            }

            function showForm(formToShow) {
                [requestOtpForm, verifyOtpForm, resetPasswordForm].forEach(form => {
                    form.classList.add('hidden');
                });
                formToShow.classList.remove('hidden');
            }

            // Step 1: Request OTP
            requestOtpForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = requestOtpForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>{{ $forgotTrans['js_sending'][$lang] ?? 'Mengirim...' }}`;

                const formData = new FormData(requestOtpForm);
                currentEmail = formData.get('email');

                try {
                    const response = await fetch('{{ route("password.request-otp") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert(data.message, 'success');
                        document.getElementById('verify_email').value = currentEmail;
                        showForm(verifyOtpForm);
                        document.getElementById('otp').focus();
                    } else {
                        showAlert(data.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    showAlert(`{{ $forgotTrans['js_error_conn'][$lang] ?? 'Terjadi kesalahan koneksi' }}`);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            // Step 2: Verify OTP
            verifyOtpForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = verifyOtpForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>{{ $forgotTrans['js_verifying'][$lang] ?? 'Memverifikasi...' }}`;

                const formData = new FormData(verifyOtpForm);

                try {
                    const response = await fetch('{{ route("password.verify-otp") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.verified) {
                        showAlert(data.message, 'success');
                        document.getElementById('reset_email').value = currentEmail;
                        showForm(resetPasswordForm);
                        document.getElementById('password').focus();
                    } else {
                        showAlert(data.message || 'Kode OTP tidak valid');
                    }
                } catch (error) {
                    showAlert(`{{ $forgotTrans['js_error_conn'][$lang] ?? 'Terjadi kesalahan koneksi' }}`);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            // Step 3: Reset Password
            resetPasswordForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = resetPasswordForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>{{ $forgotTrans['js_resetting'][$lang] ?? 'Mereset...' }}`;

                const formData = new FormData(resetPasswordForm);

                try {
                    const response = await fetch('{{ route("password.reset") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    } else {
                        showAlert(data.message || 'Terjadi kesalahan');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                } catch (error) {
                    showAlert(`{{ $forgotTrans['js_error_conn'][$lang] ?? 'Terjadi kesalahan koneksi' }}`);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            // Resend OTP
            resendOtpBtn.addEventListener('click', async () => {
                resendOtpBtn.disabled = true;
                resendOtpBtn.textContent = `{{ $forgotTrans['js_resending'][$lang] ?? 'Mengirim ulang...' }}`;

                const formData = new FormData();
                formData.append('email', currentEmail);

                try {
                    const response = await fetch('{{ route("password.request-otp") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert(data.message, 'success');
                    } else {
                        showAlert(data.message || 'Gagal mengirim ulang OTP');
                    }
                } catch (error) {
                    showAlert(`{{ $forgotTrans['js_error_conn'][$lang] ?? 'Terjadi kesalahan koneksi' }}`);
                } finally {
                    resendOtpBtn.disabled = false;
                    resendOtpBtn.textContent = `{{ $forgotTrans['btn_resend_otp'][$lang] ?? 'Kirim ulang kode OTP' }}`;
                }
            });
        })();
    </script>
@endpush