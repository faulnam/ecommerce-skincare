<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q8K4Z88BFE"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-Q8K4Z88BFE');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hijab')</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-white text-black antialiased">
    <!-- Header -->
    <header class="fixed left-0 top-0 z-50 w-full border-b border-black/6 bg-white/80 backdrop-blur-xl md:sticky">
        <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-6 md:px-10 lg:px-12">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ config('filesystems.disks.r2.url').'/logo.png' }}" alt="Hijab" class="h-7 w-7 object-contain" loading="lazy">
                <span class="text-xl font-semibold tracking-tight text-black">Hijab</span>
            </a>

            <nav class="hidden items-center gap-8 md:flex">
                <a href="{{ route('home') }}"
                    class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Beranda</a>
                <a href="{{ route('new-arrivals') }}"
                    class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Produk Terbaru</a>
                <a href="{{ route('brand-catalog') }}"
                    class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Katalog Brand</a>
                <a href="{{ route('about') }}"
                    class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Tentang</a>
                <a href="{{ route('contact') }}"
                    class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Kontak</a>
            </nav>

            <div class="flex items-center gap-3 text-black/80">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                            class="inline-flex items-center gap-1.5 rounded-full border border-black/15 bg-black px-4 py-1.5 text-xs font-medium text-white transition duration-300 hover:bg-black/90"
                            aria-label="Back to Dashboard">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                            <span>Dasbor</span>
                        </a>
                    @elseif(auth()->user()->role === 'customer')
                        <a href="{{ route('customer.profile.index') }}" class="transition duration-300 hover:text-black" aria-label="Profile">
                            <i class="fas fa-user text-sm"></i>
                        </a>
                    @endif
                @endauth
                @guest
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-1 rounded-full border border-black/15 px-3 py-1.5 text-xs font-medium text-black/80 transition duration-300 hover:border-black/30 hover:text-black"
                        aria-label="Login">
                        <i class="fas fa-sign-in-alt text-[11px]"></i>
                        <span>Masuk</span>
                    </a>
                @endguest
                @auth
                    <a href="{{ route('customer.cart.index') }}" class="relative transition duration-300 hover:text-black"
                        aria-label="Cart">
                        <i class="fas fa-shopping-bag text-sm"></i>
                        @if(auth()->user()->role === 'customer')
                            @php $cartCount = auth()->user()->cartItems()->sum('quantity'); @endphp
                            @if($cartCount > 0)
                                <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                            @endif
                        @endif
                    </a>
                @else
                    <a href="{{ route('login') }}" class="transition duration-300 hover:text-black" aria-label="Cart">
                        <i class="fas fa-shopping-bag text-sm"></i>
                    </a>
                @endauth
                <button type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-black/15 text-black transition duration-300 hover:border-black/35 md:hidden"
                    data-mobile-menu-toggle aria-label="Toggle navigation" aria-expanded="false">
                    <i class="fas fa-bars text-sm"></i>
                </button>
            </div>
        </div>

        <div class="hidden border-t border-black/10 bg-white/95 px-6 py-4 md:hidden" data-mobile-menu>
            <nav class="flex flex-col gap-3 text-sm font-medium text-black/85">
                <a href="{{ route('home') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">Beranda</a>
                <a href="{{ route('new-arrivals') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">Produk Terbaru</a>
                <a href="{{ route('brand-catalog') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">Katalog Brand</a>
                <a href="{{ route('about') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">Tentang</a>
                <a href="{{ route('contact') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">Kontak</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16 md:pt-0">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-black/10 bg-white py-14 text-sm text-zinc-500">
        <div class="mx-auto w-full max-w-7xl px-6 md:px-10 lg:px-12">
            <div class="hidden grid-cols-2 gap-8 md:grid md:grid-cols-4">
                <div>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-black">Belanja</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('produk.index') }}" class="hover:underline">Hijab</a></li>
                        <li><a href="{{ route('produk.index') }}" class="hover:underline">Sepatu</a></li>
                        <li><a href="{{ route('produk.index') }}" class="hover:underline">Aksesoris</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-black">Bantuan</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('policy') }}" class="hover:underline">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('return-refund') }}" class="hover:underline">Pengembalian & Refund</a></li>
                        <li><a href="{{ route('guarantee') }}" class="hover:underline">Garansi</a></li>
                        <li><a href="{{ route('help-center') }}" class="hover:underline">Pusat Bantuan</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-black">Akun</h3>
                    <ul class="space-y-2">
                        @auth
                            <li><a href="{{ route('customer.profile.index') }}" class="hover:underline">Dasbor</a></li>
                            <li><a href="{{ route('customer.orders.index') }}" class="hover:underline">Pesanan</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:underline">Masuk</a></li>
                            <li><a href="{{ route('register') }}" class="hover:underline">Buat Akun</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-black">Tentang Kami</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('about') }}" class="text-sm text-black/70 hover:text-black">Tentang Kami</a></li>
                        <li><a href="{{ route('galeri') }}" class="hover:underline">Galeri</a></li>
                        <li><a href="{{ route('testimoni') }}" class="hover:underline">Testimoni</a></li>
                    </ul>
                </div>
            </div>

            <div class="space-y-2 md:hidden">
                <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold uppercase tracking-wide text-black">
                        Belanja
                        <i class="fas fa-chevron-down text-[10px] text-zinc-500 transition group-open:rotate-180"></i>
                    </summary>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a href="{{ route('produk.index') }}" class="hover:underline">Hijab</a></li>
                        <li><a href="{{ route('produk.index') }}" class="hover:underline">Sepatu</a></li>
                        <li><a href="{{ route('produk.index') }}" class="hover:underline">Aksesoris</a></li>
                    </ul>
                </details>

                <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold uppercase tracking-wide text-black">
                        Bantuan
                        <i class="fas fa-chevron-down text-[10px] text-zinc-500 transition group-open:rotate-180"></i>
                    </summary>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a href="{{ route('policy') }}" class="hover:underline">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('return-refund') }}" class="hover:underline">Pengembalian & Refund</a></li>
                        <li><a href="{{ route('guarantee') }}" class="hover:underline">Garansi</a></li>
                        <li><a href="{{ route('help-center') }}" class="hover:underline">Pusat Bantuan</a></li>
                    </ul>
                </details>

                <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold uppercase tracking-wide text-black">
                        Akun
                        <i class="fas fa-chevron-down text-[10px] text-zinc-500 transition group-open:rotate-180"></i>
                    </summary>
                    <ul class="mt-3 space-y-2 text-sm">
                        @auth
                            <li><a href="{{ route('customer.profile.index') }}" class="hover:underline">Dasbor</a></li>
                            <li><a href="{{ route('customer.orders.index') }}" class="hover:underline">Pesanan</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:underline">Masuk</a></li>
                            <li><a href="{{ route('register') }}" class="hover:underline">Buat Akun</a></li>
                        @endauth
                    </ul>
                </details>

                <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold uppercase tracking-wide text-black">
                        Tentang Kami
                        <i class="fas fa-chevron-down text-[10px] text-zinc-500 transition group-open:rotate-180"></i>
                    </summary>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a href="{{ route('about') }}" class="hover:underline">Tentang Kami</a></li>
                        <li><a href="{{ route('galeri') }}" class="hover:underline">Galeri</a></li>
                        <li><a href="{{ route('testimoni') }}" class="hover:underline">Testimoni</a></li>
                    </ul>
                </details>
            </div>
        </div>
        <div
            class="mx-auto mt-10 w-full max-w-7xl border-t border-black/10 px-6 pt-5 text-xs text-zinc-400 md:px-10 lg:px-12">
            © {{ now()->year }} Hijab. Hak cipta dilindungi.
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
        const mobileMenu = document.querySelector('[data-mobile-menu]');

        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                mobileMenuToggle.setAttribute('aria-expanded', String(!mobileMenu.classList.contains('hidden')));
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
