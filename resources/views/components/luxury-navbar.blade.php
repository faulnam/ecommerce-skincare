@php
    $lang = 'id';
@endphp

<!-- Top Marquee -->
<div id="top-marquee" class="fixed top-0 left-0 w-full z-[100] border-b border-gray-200 overflow-hidden py-2 flex items-center bg-white" style="background-color: #ffffff !important; transition: transform 0.3s ease;">
    <style>
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            animation: marquee 20s linear infinite;
            min-width: 200%;
        }
    </style>
    <div class="whitespace-nowrap animate-marquee flex gap-12 items-center font-bold uppercase text-gray-600" style="font-size: 10px; letter-spacing: 0.2em;">
        <span>Koleksi Skincare Terbaru Sudah Tersedia</span>
        <span>Gratis Ongkir Ke Seluruh Indonesia</span>
        <span>100% Produk Original & Teruji BPOM</span>
        <span>Konsultasi Kulit Gratis Bersama Beauty Advisor</span>
        <span>Koleksi Skincare Terbaru Sudah Tersedia</span>
        <span>Gratis Ongkir Ke Seluruh Indonesia</span>
        <span>100% Produk Original & Teruji BPOM</span>
        <span>Konsultasi Kulit Gratis Bersama Beauty Advisor</span>
    </div>
</div>

<!-- Custom Navbar -->
<div id="custom-navbar" class="fixed left-0 w-full px-6 md:px-12 flex items-center justify-between bg-transparent border-b border-transparent" style="top: 31px; padding-top: 15px; padding-bottom: 15px; z-index: 90; transition: all 0.3s ease;">
    <!-- Custom Fonts for Luxury Feel -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;0,900;1,700&display=swap');
        
        .nav-link-custom {
            position: relative;
            font-size: 15px;
            font-weight: 400;
            color: #111;
            padding-bottom: 6px; /* space between text and underline */
            transition: color 0.3s ease;
        }
        
        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1.5px;
            background-color: #111;
            transition: width 0.3s ease;
        }
        
        .nav-link-custom:hover::after,
        .nav-link-custom.active::after {
            width: 100%;
        }
    </style>

    <!-- Left: Mobile (Hamburger + Search) / Desktop (Logo) -->
    <div class="flex-1 flex justify-start items-center text-black">
        <!-- Mobile Only -->
        <div class="flex lg:hidden gap-3 items-center">
            <button type="button" id="openMobileMenuBtn" onclick="window.openMobileMenu()" class="hover:text-gray-500 transition-colors">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <a href="javascript:void(0)" onclick="openSearchModal()" class="hover:text-gray-500 transition-colors">
                <i class="fas fa-search text-lg"></i>
            </a>
        </div>
        <!-- Desktop Only -->
        <div class="hidden lg:flex">
            <a href="{{ route('home') }}" class="flex items-center w-fit group" style="gap: 2px;">
                <span class="text-3xl text-black leading-none tracking-widest group-hover:text-gray-600 transition-colors" style="font-family: 'Playfair Display', serif; font-weight: 800;">LUMINA</span>
            </a>
        </div>
    </div>
    
    <!-- Center: Mobile (Logo) / Desktop (Links) -->
    <div class="flex-[2] flex justify-center items-center">
        <!-- Mobile Only -->
        <div class="flex lg:hidden">
            <a href="{{ route('home') }}" class="flex items-center w-fit group" style="gap: 2px;">
                <span class="text-3xl text-black leading-none tracking-widest group-hover:text-gray-600 transition-colors" style="font-family: 'Playfair Display', serif; font-weight: 800;">LUMINA</span>
            </a>
        </div>
        <!-- Desktop Only -->
        <div class="hidden lg:flex justify-center" style="gap: 40px;">
            <a href="{{ route('home') }}" class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('new-arrivals') }}" class="nav-link-custom {{ request()->routeIs('new-arrivals') ? 'active' : '' }}">Produk Terbaru</a>
            <a href="{{ route('about') }}" class="nav-link-custom {{ request()->routeIs('about') ? 'active' : '' }}">Tentang Kami</a>
            <a href="{{ route('insight') }}" class="nav-link-custom {{ request()->routeIs('insight') ? 'active' : '' }}">Insight</a>
            <a href="{{ route('contact') }}" class="nav-link-custom {{ request()->routeIs('contact') ? 'active' : '' }}">Kontak</a>
        </div>
    </div>

    <!-- Right: Desktop & Mobile (Icons) -->
    <div class="flex-1 flex justify-end items-center text-black gap-3 md:gap-4 lg:gap-5">
        <!-- Profile (Desktop Only) -->
        <div class="hidden lg:block">
            @auth
                @if(auth()->user()->isCustomer())
                    <a href="{{ route('customer.profile.index') }}" class="hover:text-gray-500 transition-colors" title="My Profile"><i class="far fa-user" style="font-size: 18px;"></i></a>
                @elseif(auth()->user()->isAdmin() || auth()->user()->isDeveloper() || auth()->user()->isBlogger())
                    <a href="{{ route('admin.profile.index') }}" class="hover:text-gray-500 transition-colors" title="Admin Profile"><i class="far fa-user" style="font-size: 18px;"></i></a>
                @elseif(auth()->user()->isCourier())
                    <a href="{{ route('courier.profile') }}" class="hover:text-gray-500 transition-colors" title="Courier Profile"><i class="far fa-user" style="font-size: 18px;"></i></a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-gray-500 transition-colors" title="Login"><i class="far fa-user" style="font-size: 18px;"></i></a>
                @endif
            @else
                <a href="{{ route('login') }}" class="hover:text-gray-500 transition-colors" title="Login"><i class="far fa-user" style="font-size: 18px;"></i></a>
            @endauth
        </div>
        <!-- Search (Desktop Only) -->
        <a href="javascript:void(0)" onclick="openSearchModal()" class="hidden lg:block hover:text-gray-500 transition-colors"><i class="fas fa-search" style="font-size: 18px;"></i></a>
        <a href="{{ route('customer.wishlist.index') }}" class="hover:text-gray-500 transition-colors relative">
            <i class="far fa-heart" style="font-size: 18px;"></i>
            @php
                $wishlistCount = 0;
                if(auth()->check() && auth()->user()->role === 'customer') {
                    $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
                } else {
                    $wishlistCount = count(session()->get('guest_wishlist', []));
                }
            @endphp
            @if($wishlistCount > 0)
            <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-black text-[9px] font-bold text-white">{{ $wishlistCount > 9 ? '9+' : $wishlistCount }}</span>
            @endif
        </a>
        <a href="{{ route('customer.cart.index') }}" class="hover:text-gray-500 transition-colors relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            @php
                $cartCount = 0;
                if(auth()->check() && auth()->user()->role === 'customer') {
                    $cartCount = auth()->user()->cartItems()->sum('quantity');
                } else {
                    $cartCount = array_sum(array_column(session()->get('guest_cart', []), 'quantity'));
                }
            @endphp
            @if($cartCount > 0)
            <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-black text-[9px] font-bold text-white">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
            @endif
        </a>
    </div>
</div>

@include('components.search-modal')

<!-- Mobile Slide-Out Menu -->
<div id="mobileMenu" class="fixed inset-0 bg-white transition-transform duration-300 flex flex-col lg:hidden" style="z-index: 110; transform: translateX(-100%);">
    <!-- Header of Slide-Out Menu -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100" style="padding-top: 25px; padding-bottom: 25px;">
        <!-- Left: Close & Search -->
        <div class="flex flex-1 items-center gap-3 text-black">
            <button type="button" id="closeMobileMenuBtn" onclick="window.closeMobileMenu()" class="hover:text-gray-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            <a href="javascript:void(0)" id="mobileMenuSearchBtn" onclick="window.closeMobileMenu(); setTimeout(openSearchModal, 300);" class="hover:text-gray-500 transition-colors">
                <i class="fas fa-search text-lg"></i>
            </a>
        </div>
        <!-- Center: Logo -->
        <div class="flex-[2] flex justify-center">
            <span class="text-3xl text-black leading-none tracking-widest" style="font-family: 'Playfair Display', serif; font-weight: 800;">LUMINA</span>
        </div>
        <!-- Right: Wishlist & Cart -->
        <div class="flex flex-1 justify-end items-center gap-3 text-black">
            <a href="{{ route('customer.wishlist.index') }}" class="hover:text-gray-500 transition-colors relative">
                <i class="far fa-heart text-xl"></i>
            </a>
            <a href="{{ route('customer.cart.index') }}" class="hover:text-gray-500 transition-colors relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </a>
        </div>
    </div>
    
    <!-- Menu Links -->
    <div class="flex flex-col px-6 py-4 overflow-y-auto bg-white flex-1" style="font-family: 'Inter', sans-serif;">
        <a href="{{ route('home') }}" class="py-4 border-b border-gray-100 text-[13px] font-semibold tracking-wider text-black flex justify-between items-center hover:bg-gray-50">
            BERANDA
        </a>
        <a href="{{ route('new-arrivals') }}" class="py-4 border-b border-gray-100 text-[13px] font-semibold tracking-wider text-black flex justify-between items-center hover:bg-gray-50">
            PRODUK TERBARU
        </a>
        <a href="{{ route('about') }}" class="py-4 border-b border-gray-100 text-[13px] font-semibold tracking-wider text-black flex justify-between items-center hover:bg-gray-50">
            TENTANG KAMI
        </a>
        <a href="{{ route('insight') }}" class="py-4 border-b border-gray-100 text-[13px] font-semibold tracking-wider text-black flex justify-between items-center hover:bg-gray-50">
            INSIGHT
        </a>
        <a href="{{ route('contact') }}" class="py-4 border-b border-gray-100 text-[13px] font-semibold tracking-wider text-black flex justify-between items-center hover:bg-gray-50">
            KONTAK
        </a>
        
        <!-- Account link -->
        <div class="mt-10 flex flex-col gap-6">
            <a href="{{ route('login') }}" class="flex items-center gap-3 text-sm font-medium text-black hover:text-gray-600">
                <i class="far fa-user text-lg"></i>
                AKUN SAYA
            </a>
        </div>
    </div>
</div>

<script>
    // Global functions to guarantee execution
    window.openMobileMenu = function() {
        const menu = document.getElementById('mobileMenu');
        if (menu) {
            menu.style.transform = 'translateX(0)';
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeMobileMenu = function() {
        const menu = document.getElementById('mobileMenu');
        if (menu) {
            menu.style.transform = 'translateX(-100%)';
            document.body.style.overflow = '';
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Navbar Scroll Effect
        const navbar = document.getElementById('custom-navbar');
        
        function handleScroll() {
            if (!navbar) return;
            if (window.scrollY > 10) {
                navbar.classList.add('bg-white', 'border-b', 'border-gray-100', 'shadow-sm');
                navbar.classList.remove('bg-transparent', 'border-transparent');
            } else {
                navbar.classList.remove('bg-white', 'border-b', 'border-gray-100', 'shadow-sm');
                navbar.classList.add('bg-transparent', 'border-transparent');
            }
        }

        window.addEventListener('scroll', handleScroll);
        
        // Trigger once on load in case the page is already scrolled
        handleScroll();
    });
</script>
