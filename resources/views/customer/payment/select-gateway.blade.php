@extends('layouts.app')

@section('title', 'Select Payment Method')

@push('styles')
<style>
    body { padding-top: 0 !important; padding-bottom: 0 !important; }
    #mainNavbar, .mobile-bottom-nav, .footer { display: none !important; }
</style>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/paylabs.json');
    $plTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp
<!-- Navbar -->
<header class="fixed left-0 top-0 z-50 w-full border-b border-black/6 bg-white/80 backdrop-blur-xl md:sticky">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-6 md:px-10 lg:px-12">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ config('filesystems.disks.r2.url').'/logo.png' }}" alt="Hijab" class="h-7 w-7 object-contain" loading="lazy">
            <span class="text-xl font-semibold tracking-tight text-black">Hijab</span>
        </a>
         <nav class="hidden items-center gap-8 md:flex" id="navLinks">
                    <a href="{{ route('home') }}"
                        class="border-b border-transparent text-sm text-white/90 transition duration-300 hover:border-white/30 hover:text-white">{{ $plTrans['nav_home'][$lang] ?? 'Home' }}</a>
                    <a href="{{ route('new-arrivals') }}"
                        class="border-b border-transparent text-sm text-white/90 transition duration-300 hover:border-white/30 hover:text-white">{{ $plTrans['nav_new_arrivals'][$lang] ?? 'New Arrivals' }}</a>
                    <a href="{{ route('brand-catalog') }}"
                        class="border-b border-transparent text-sm text-white/90 transition duration-300 hover:border-white/30 hover:text-white">{{ $plTrans['nav_brand_catalog'][$lang] ?? 'Brand Catalog' }}</a>
                    <a href="{{ route('about') }}"
                        class="border-b border-transparent text-sm text-white/90 transition duration-300 hover:border-white/30 hover:text-white">{{ $plTrans['nav_about'][$lang] ?? 'About' }}</a>
                    <a href="{{ route('contact') }}"
                        class="border-b border-transparent text-sm text-white/90 transition duration-300 hover:border-white/30 hover:text-white">{{ $plTrans['nav_contact'][$lang] ?? 'Contact' }}</a>
                </nav>
        <div class="flex items-center gap-3 text-black/80">
            @guest
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-1 rounded-full border border-black/15 bg-black/5 px-3 py-1.5 text-xs font-medium text-black transition duration-300 hover:bg-black/10"
                    aria-label="Masuk">
                    <i class="fas fa-sign-in-alt text-[11px]"></i>
                    <span>{{ $plTrans['btn_login'][$lang] ?? 'Masuk' }}</span>
                </a>
            @endguest
            @auth
                <a href="{{ route('customer.orders.index') }}" class="transition duration-300 hover:text-black" title="Riwayat Pesanan">
                    <i class="fas fa-history text-sm"></i>
                </a>
                <a href="{{ route('customer.profile.index') }}" class="transition duration-300 hover:text-black" title="Profile">
                    <i class="fas fa-user text-sm"></i>
                </a>
            @endauth
            <a href="{{ route('customer.cart.index') }}" class="relative transition duration-300 hover:text-black" title="Keranjang">
                <i class="fas fa-shopping-bag text-sm"></i>
                @auth
                    @php $cartCount = auth()->user()->cartItems()->sum('quantity'); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                    @endif
                @endauth
                @guest
                    @php
                        $guestCart = session()->get('guest_cart', []);
                        $guestCartCount = array_sum(array_column($guestCart, 'quantity'));
                    @endphp
                    @if($guestCartCount > 0)
                        <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">{{ $guestCartCount > 9 ? '9+' : $guestCartCount }}</span>
                    @endif
                @endguest
            </a>
        </div>
            </div>
</header>

<div class="min-h-screen bg-zinc-50 py-12 pt-16 md:pt-0">
    <div class="mx-auto max-w-2xl px-6">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-black">{{ $plTrans['page_title'][$lang] ?? 'Pilih Metode Pembayaran' }}</h1>
            <p class="mt-2 text-sm text-zinc-500">{{ $plTrans['label_order'][$lang] ?? 'Order:' }} {{ $order->order_number }} • Total: {{ $order->formatted_total }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-1">
            <!-- Paylabs -->
            <a href="{{ route('customer.payment.paylabs.show', $order) }}" 
               class="group relative overflow-hidden rounded-2xl border-2 border-zinc-200 bg-white p-6 transition hover:border-black hover:shadow-lg">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-black/5">
                    <i class="fas fa-credit-card text-2xl text-black"></i>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-black">Paylabs</h3>
                <p class="text-sm text-zinc-600">{{ $plTrans['gateway_desc'][$lang] ?? 'Virtual Account, QRIS, E-Wallet, Retail' }}</p>
                <span class="mt-3 inline-block rounded-full bg-black px-3 py-1 text-xs font-medium text-white">{{ $plTrans['badge_recommended'][$lang] ?? 'Recommended' }}</span>
            </a>
        </div>
    </div>
</div>

@endsection