@extends('layouts.app')

@section('title', 'Hijab - Performa Maksimal, Game Makin Total')
@section('og_description', 'Hijab — toko perlengkapan hijab premium di Indonesia. Hijab, sepatu, tas, dan aksesoris untuk pemula hingga profesional. Belanja sekarang!')

@if(false)
@section('content')
    <section class="relative min-h-screen overflow-hidden bg-linear-to-br from-slate-950 via-slate-900 to-emerald-950 text-white">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-emerald-400/20 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-cyan-400/10 blur-3xl"></div>
        </div>

        <div class="relative mx-auto flex min-h-screen w-full max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-xl">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('home') }}" class="text-lg font-extrabold tracking-tight sm:text-xl">
                        Hijab <span class="text-emerald-400">Hijab</span>
                    </a>
                    <nav class="hidden items-center gap-1 lg:flex">
                        <a href="{{ route('home') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white">Home</a>
                        <a href="{{ route('produk.index') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white">Shop</a>
                        <a href="{{ route('galeri') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white">Collections</a>
                        <a href="{{ route('testimoni') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white">Review</a>
                    </nav>
                    <div class="flex items-center gap-2">
                        @auth
                            <a href="{{ route('customer.cart.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white/90 transition hover:bg-white/20">
                                <i class="fas fa-shopping-basket text-sm"></i>
                            </a>
                            <a href="{{ route('customer.products.index') }}" class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-bold text-slate-900 transition hover:bg-emerald-100">
                                Belanja
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-bold text-slate-900 transition hover:bg-emerald-100">
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="grid flex-1 items-center gap-10 py-10 lg:grid-cols-2 lg:py-16">
                <div>
                    <span class="inline-flex items-center rounded-full border border-emerald-400/30 bg-emerald-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-300">
                        Saas-grade sport commerce
                    </span>
                    <h1 class="mt-6 text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-7xl">
                        Experience premium
                        <span class="bg-linear-to-r from-emerald-300 via-green-400 to-cyan-300 bg-clip-text text-transparent">
                            hijab gear
                        </span>
                        in one modern platform.
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
                        Hijab menghadirkan pengalaman belanja alat hijab kelas premium: cepat, elegan, dan terkurasi untuk pemula hingga profesional.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        @auth
                            <a href="{{ route('customer.products.index') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-6 py-3 text-sm font-extrabold text-slate-900 shadow-[0_16px_40px_-14px_rgba(52,211,153,0.8)] transition hover:bg-emerald-300">
                                Belanja Sekarang
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-6 py-3 text-sm font-extrabold text-slate-900 shadow-[0_16px_40px_-14px_rgba(52,211,153,0.8)] transition hover:bg-emerald-300">
                                Belanja Sekarang
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        @endauth
                        <a href="{{ route('produk.index') }}" class="inline-flex items-center rounded-full border border-white/25 bg-white/5 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                            Lihat Produk
                        </a>
                    </div>

                    <div class="mt-10 grid grid-cols-3 gap-3 sm:gap-4">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-2xl font-black text-white sm:text-3xl">{{ $stats['total_customers'] }}+</p>
                            <p class="mt-1 text-xs font-medium text-slate-300 sm:text-sm">Pelanggan</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-2xl font-black text-white sm:text-3xl">{{ $stats['total_reviews'] }}+</p>
                            <p class="mt-1 text-xs font-medium text-slate-300 sm:text-sm">Review</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-2xl font-black text-white sm:text-3xl">{{ $stats['avg_rating'] }}</p>
                            <p class="mt-1 text-xs font-medium text-slate-300 sm:text-sm">Rating ⭐</p>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-6 rounded-4xl bg-linear-to-tr from-emerald-400/20 to-cyan-300/10 blur-2xl"></div>
                    <div class="relative rounded-4xl border border-white/15 bg-white/10 p-4 shadow-2xl backdrop-blur-xl sm:p-6">
                        <img
                            src="https://images.unsplash.com/photo-1593766827228-8737b4534aa6?w=1200"
                            alt="Perlengkapan Hijab"
                            class="h-[360px] w-full rounded-2xl object-cover sm:h-[460px]"
                        >
                        <div class="mt-4 rounded-xl border border-emerald-300/20 bg-emerald-400/10 p-4">
                            <p class="text-sm font-semibold uppercase tracking-[0.15em] text-emerald-300">Hijab Insight</p>
                            <p class="mt-2 text-sm text-slate-100 sm:text-base">"Build your game with curated hijabs, shoes, and accessories engineered for consistency."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-20">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Featured product</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Produk Unggulan</h2>
                </div>
                <a href="{{ route('produk.index') }}" class="text-sm font-bold text-slate-700 transition hover:text-emerald-600">
                    Lihat semua
                </a>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($products->take(8) as $product)
                    <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_15px_45px_-25px_rgba(15,23,42,0.25)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_55px_-25px_rgba(15,23,42,0.35)]">
                        <div class="relative">
                            <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=1000' }}" alt="{{ $product->name }}" class="h-56 w-full object-cover">
                            <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                <span class="rounded-full bg-slate-900/85 px-3 py-1 text-xs font-bold text-white">{{ $product->category_label }}</span>
                                @if($product->hasActiveDiscount())
                                    <span class="rounded-full bg-rose-500 px-3 py-1 text-xs font-bold text-white">-{{ $product->formatted_discount_percent }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="line-clamp-1 text-lg font-black tracking-tight text-slate-900">{{ $product->name }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ Str::limit(strip_tags($product->description), 88) }}</p>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <div>
                                    @if($product->hasActiveDiscount())
                                        <p class="text-lg font-black text-emerald-600">{{ $product->formatted_discounted_price }}</p>
                                        <p class="text-xs font-semibold text-slate-400 line-through">{{ $product->formatted_price }}</p>
                                    @else
                                        <p class="text-lg font-black text-emerald-600">{{ $product->formatted_price }}</p>
                                    @endif
                                </div>
                                @auth
                                    <a href="{{ $product->detail_url }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-xs font-bold uppercase tracking-wide text-white transition hover:bg-emerald-600">
                                        Beli
                                    </a>
                                @else
                                    <a href="{{ $product->detail_url }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 transition hover:border-emerald-500 hover:text-emerald-600">
                                        Detail
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                        <i class="fas fa-box-open text-3xl text-slate-400"></i>
                        <p class="mt-3 font-semibold text-slate-500">Produk segera hadir!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-20">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Testimonials</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Dipercaya pemain di seluruh Indonesia</h2>
                <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-600 sm:text-base">Review real customer dengan pengalaman belanja yang cepat, nyaman, dan profesional.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($testimonials->take(6) as $testimonial)
                    <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_14px_36px_-24px_rgba(15,23,42,0.35)] transition hover:shadow-[0_20px_40px_-22px_rgba(15,23,42,0.4)]">
                        <div class="mb-4 flex items-center gap-1 text-amber-400">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-sm {{ $i <= $testimonial->rating ? 'opacity-100' : 'opacity-30' }}"></i>
                            @endfor
                        </div>
                        <p class="text-sm leading-relaxed text-slate-700">"{{ Str::limit($testimonial->content, 130) }}"</p>
                        <div class="mt-5 flex items-center gap-3">
                            <img src="{{ $testimonial->user->avatar_url }}" alt="{{ $testimonial->user->name }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-emerald-100">
                            <div>
                                <p class="text-sm font-black text-slate-900">{{ $testimonial->user->name }}</p>
                                <p class="text-xs font-medium text-slate-500">{{ $testimonial->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                        <i class="fas fa-comments text-3xl text-slate-400"></i>
                        <p class="mt-3 font-semibold text-slate-500">Belum ada testimoni.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-slate-950 py-16 text-white">
        <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-6 px-4 text-center sm:px-6 lg:flex-row lg:px-8 lg:text-left">
            <div>
                <h3 class="text-2xl font-black tracking-tight sm:text-3xl">Siap upgrade gear hijab kamu?</h3>
                <p class="mt-2 text-sm text-slate-300 sm:text-base">Belanja sekarang dan rasakan pengalaman e-commerce olahraga yang premium.</p>
            </div>
            @auth
                <a href="{{ route('customer.products.index') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-7 py-3 text-sm font-extrabold text-slate-900 transition hover:bg-emerald-300">
                    Mulai Belanja
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-7 py-3 text-sm font-extrabold text-slate-900 transition hover:bg-emerald-300">
                    Login untuk Belanja
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            @endauth
        </div>
    </section>
@endsection

@push('styles')
    @endpush

@if(false)
    <!-- Hero Banner Section - Full Width -->
    <section class="hero-banner">
        <div class="hero-banner-overlay"></div>
        <div class="container position-relative">
            <div class="hero-topbar">
                <div class="hero-topbar-left">
                    <a href="{{ route('home') }}" class="hero-brand">Hijab<span>Hijab</span></a>
                    <nav class="hero-mini-nav d-none d-lg-flex">
                        <a href="{{ route('home') }}">Home</a>
                        <a href="{{ route('produk.index') }}">Shop</a>
                        <a href="{{ route('galeri') }}">Collections</a>
                        <a href="{{ route('testimoni') }}">Review</a>
                    </nav>
                </div>
                <div class="hero-topbar-right">
                    @auth
                        <a href="{{ route('customer.cart.index') }}" class="icon-btn d-none d-md-inline-flex" aria-label="Keranjang">
                            <i class="fas fa-shopping-basket"></i>
                        </a>
                        <a href="{{ route('customer.products.index') }}" class="hero-login-btn">Belanja</a>
                    @else
                        <a href="{{ route('login') }}" class="hero-login-btn">Log In</a>
                    @endauth
                </div>
            </div>

            <div class="row align-items-center min-vh-hero">
                <div class="col-lg-6 hero-content">
                    <div class="hero-badge-wrapper">
                        <span class="hero-badge-new">
                            <i class="fas fa-bolt me-2"></i>Gear Hijab Original & Bergaransi
                        </span>
                    </div>

                    <h1 class="hero-title-new">
                        <span class="highlight">Hijab</span>
                    </h1>

                    <h2 class="hero-tagline">Lengkapi permainan hijab Anda dengan peralatan berkualitas terbaik.<br>Produk original, pilihan lengkap, harga terbaik, dan pengiriman cepat ke seluruh Indonesia.</h2>

                    <!-- HERO IMAGE MOBILE ONLY -->
                    <div class="hero-image-mobile d-block d-lg-none text-center my-4">
                        <img src="https://images.unsplash.com/photo-1648341248072-e8f8d3dcef7b?w=900" alt="Hijab Gear" class="img-fluid">
                    </div>



                    <p class="hero-desc">
                        Temukan hijab, bola, tas, sepatu, dan aksesori hijab pilihan untuk latihan harian hingga turnamen.
                        Kualitas premium, harga kompetitif, siap kirim cepat ke seluruh Indonesia.
                    </p>

                    <div class="hero-cta">
                        @auth
                            <a href="{{ route('customer.products.index') }}" class="btn btn-hero-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Belanja Sekarang
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-hero-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Belanja Sekarang
                            </a>
                        @endauth
                        <a href="{{ route('produk.index') }}" class="btn btn-hero-outline">
                            <i class="fas fa-play-circle me-2"></i>Lihat Produk
                        </a>
                    </div>

                    <!-- Stats Row -->
                    <div class="hero-stats-row">
                        <div class="stat-box">
                            <span class="stat-num">{{ $stats['total_customers'] }}+</span>
                            <span class="stat-text">Pelanggan</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-num">{{ $stats['total_reviews'] }}+</span>
                            <span class="stat-text">Review</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-num">{{ $stats['avg_rating'] }}</span>
                            <span class="stat-text">Rating ⭐</span>
                        </div>
                    </div>
                </div>

                <!-- ❌ DESKTOP TIDAK DIUBAH -->
                <div class="col-lg-6 hero-visual d-none d-lg-block">
                    <div class="hero-image-float">
                        <img src="https://images.unsplash.com/photo-1593766827228-8737b4534aa6?w=900" alt="Perlengkapan Hijab" class="main-product-img" id="heroImage">
                        <div class="deco-circle deco-1"></div>
                        <div class="deco-circle deco-2"></div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Wave separator -->
        <div class="hero-wave">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z"
                    fill="#fafafa" />
            </svg>
        </div>
    </section>

    <section class="quick-categories-section">
        <div class="container">
            <div class="quick-categories-grid">
                <a href="{{ route('produk.index') }}" class="quick-category-card">
                    <h4>Hijab</h4>
                    <img src="https://images.unsplash.com/photo-1599058917212-d750089bc07e?w=320" alt="Hijab Hijab">
                    <span><i class="fas fa-arrow-up-right-from-square"></i></span>
                </a>
                <a href="{{ route('produk.index') }}" class="quick-category-card">
                    <h4>Bola</h4>
                    <img src="https://images.unsplash.com/photo-1517649763962-0c623066013b?w=320" alt="Bola Hijab">
                    <span><i class="fas fa-arrow-up-right-from-square"></i></span>
                </a>
                <a href="{{ route('produk.index') }}" class="quick-category-card">
                    <h4>Tas</h4>
                    <img src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=320" alt="Tas Hijab">
                    <span><i class="fas fa-arrow-up-right-from-square"></i></span>
                </a>
                <a href="{{ route('produk.index') }}" class="quick-category-card">
                    <h4>Aksesori</h4>
                    <img src="https://images.unsplash.com/photo-1547347298-4074fc3086f0?w=320" alt="Aksesori Hijab">
                    <span><i class="fas fa-arrow-up-right-from-square"></i></span>
                </a>
            </div>
        </div>
    </section>



    <!-- About Section - Clean & Minimal -->
    <section class="about-section" id="about-brief">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="about-visual">
                        <div class="about-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1599058917212-d750089bc07e?w=900" alt="Hijab Team" class="about-main-img">
                            <div class="about-pattern"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="about-content">
                        <span class="section-label">Tentang Kami</span>
                        <h2 class="section-heading">
                            Cerita di Balik <span class="text-accent">Hijab</span>
                        </h2>
                        <p class="about-text">
                            Hijab hadir untuk menjawab kebutuhan pemain hijab Indonesia akan perlengkapan yang
                            andal, modern, dan nyaman digunakan.
                            Kami memilih produk dengan standar kualitas tinggi agar pemula hingga profesional bisa bermain
                            lebih percaya diri di setiap pertandingan.
                        </p>

                        <div class="feature-grid">
                            <div class="feature-item">
                                <div class="feature-icon-box">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <div class="feature-text">
                                    <h6>Produk Terkurasi</h6>
                                    <p>Hijab, bola, sepatu & aksesori terbaik</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon-box">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </div>
                                <div class="feature-text">
                                    <h6>Layanan Profesional</h6>
                                    <p>Rekomendasi gear sesuai level permainan</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('tentang') }}" class="btn btn-dark-outline">
                            Selengkapnya <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us - Minimal Grid -->
    <section class="why-section" id="why-us">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-label">Keunggulan</span>
                <h2 class="section-heading">Mengapa Memilih Hijab?</h2>
                <p class="section-subtext">Alasan pemain hijab memilih perlengkapan dari Hijab</p>
            </div>

            <div class="why-grid">
                <div class="why-item">
                    <div class="why-icon-wrap">
                        <img src="https://images.unsplash.com/photo-1599058917212-d750089bc07e?w=320" alt="Performa Stabil">
                    </div>
                    <h5>Performa Stabil</h5>
                    <p>Gear dengan material berkualitas membantu kontrol dan power tetap konsisten sepanjang game.</p>
                </div>
                <div class="why-item">
                    <div class="why-icon-wrap">
                        <img src="https://images.unsplash.com/photo-1517649763962-0c623066013b?w=320" alt="Untuk Semua Level">
                    </div>
                    <h5>Untuk Semua Level</h5>
                    <p>Mulai dari beginner sampai tournament player, ada pilihan produk yang tepat untuk kebutuhanmu.</p>
                </div>
                <div class="why-item">
                    <div class="why-icon-wrap">
                        <img src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=320" alt="Koleksi Lengkap">
                    </div>
                    <h5>Koleksi Lengkap</h5>
                    <p>Hijab hijab, bola, tas, sepatu, hingga grip tersedia dalam satu tempat belanja praktis.</p>
                </div>
                <div class="why-item">
                    <div class="why-icon-wrap">
                        <img src="https://images.unsplash.com/photo-1547347298-4074fc3086f0?w=320" alt="Pengiriman Cepat">
                    </div>
                    <h5>Pengiriman Cepat</h5>
                    <p>Pesanan diproses cepat dengan packing aman agar gear sampai dalam kondisi prima.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products - Clean Layout -->
    <section class="products-section" id="products">
        <div class="container">
            <div class="section-header-flex">
                <div>
                    <span class="section-label">Produk</span>
                    <h2 class="section-heading mb-0">Produk Unggulan</h2>
                </div>
                <a href="{{ route('produk.index') }}" class="btn-link-arrow d-none d-md-flex">
                    Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>

            <div class="products-grid">
                @forelse($products->take(4) as $product)
                    <div class="product-item">
                        <div class="product-img-wrap">
                            <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?w=400' }}"
                                alt="{{ $product->name }}">
                            <div class="product-badge-wrap">
                                <span
                                    class="product-badge {{ $product->category == 'original' ? 'badge-green' : 'badge-orange' }}">
                                    {{ $product->category_label }}
                                </span>
                                @if ($product->hasActiveDiscount())
                                    <span
                                        class="product-badge badge-red">-{{ $product->formatted_discount_percent }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="product-meta">
                                <h5 class="product-name">{{ $product->name }}</h5>
                                <span
                                    class="product-weight-tag d-none d-sm-inline">{{ $product->formatted_weight }}</span>
                            </div>
                            <p class="product-desc-text d-none d-md-block">{{ Str::limit(strip_tags($product->description), 60) }}</p>
                            <div class="product-footer">
                                <div class="product-pricing">
                                    @if ($product->hasActiveDiscount())
                                        <span class="price-current">{{ $product->formatted_discounted_price }}</span>
                                        <small class="price-old">{{ $product->formatted_price }}</small>
                                    @else
                                        <span class="price-current">{{ $product->formatted_price }}</span>
                                    @endif
                                </div>
                                @auth
                                    <a href="{{ $product->detail_url }}" class="btn-product">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span class="d-none d-sm-inline ms-1">Beli</span>
                                    </a>
                                @else
                                    <a href="{{ $product->detail_url }}" class="btn-product btn-product-outline">
                                        <i class="fas fa-eye"></i>
                                        <span class="d-none d-sm-inline ms-1">Detail</span>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Produk segera hadir!</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-4 d-md-none">
                <a href="{{ route('produk.index') }}" class="btn btn-outline-dark">
                    Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery Section - Masonry Style -->
    @if ($galleries->count() > 0)
        <section class="gallery-section" id="gallery">
            <div class="container">
                <div class="section-header-flex">
                    <div>
                        <span class="section-label">Galeri</span>
                        <h2 class="section-heading mb-0">Momen Kami</h2>
                    </div>
                    <a href="{{ route('galeri') }}" class="btn-link-arrow d-none d-md-flex">
                        Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>

                <div class="gallery-grid">
                    @foreach ($galleries->take(6) as $index => $gallery)
                        <div class="gallery-card {{ $index == 0 ? 'gallery-card-large' : '' }}">
                            @if ($gallery->isImage())
                                <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}">
                                <div class="gallery-card-overlay">
                                    <span class="gallery-card-title">{{ $gallery->title }}</span>
                                </div>
                            @else
                                <div class="gallery-video-wrap">
                                    {!! $gallery->embed_url !!}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4 d-md-none">
                    <a href="{{ route('galeri') }}" class="btn btn-outline-dark">
                        Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    

    


@push('styles')
    <style>
        /* ========================================
           HERO BANNER - PROFESSIONAL & MINIMALIST
        ======================================== */
        .hero-banner {
            position: relative;
            background: linear-gradient(135deg, #f8fffe 0%, #e8f5e9 50%, #f1f8e9 100%);
            overflow: hidden;
            padding-top: 2rem;
        }

        .hero-banner-overlay {
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2316a34a' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .min-vh-hero {
            min-height: calc(100vh - 200px);
            padding: 3rem 0;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-topbar {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 0.875rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: relative;
            z-index: 3;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        .hero-topbar-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .hero-brand {
            font-weight: 800;
            font-size: 1.15rem;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.3px;
        }

        .hero-brand span {
            color: var(--primary);
            margin-left: 2px;
        }

        .hero-mini-nav {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .hero-mini-nav a {
            text-decoration: none;
            color: #334155;
            font-weight: 600;
            font-size: 0.86rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            transition: all .2s ease;
        }

        .hero-mini-nav a:hover {
            background: #ecfdf5;
            color: var(--primary);
        }

        .hero-topbar-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .icon-btn {
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #334155;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all .2s ease;
        }

        .icon-btn:hover {
            color: var(--primary);
            background: #ecfdf5;
            border-color: #bbf7d0;
        }

        .hero-login-btn {
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            font-size: .85rem;
            padding: .58rem 1rem;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), #22c55e);
            transition: all .2s ease;
            box-shadow: 0 8px 18px rgba(22, 163, 74, .3);
        }

        .hero-login-btn:hover {
            transform: translateY(-1px);
            color: #fff;
        }

        .quick-categories-section {
            margin-top: -2rem;
            position: relative;
            z-index: 5;
            margin-bottom: 3rem;
        }

        .quick-categories-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .quick-category-card {
            position: relative;
            background: linear-gradient(160deg, #ffffff, #f1f5f9);
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            padding: 1.1rem;
            min-height: 180px;
            overflow: hidden;
            text-decoration: none;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
            transition: all .28s ease;
        }

        .quick-category-card h4 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: .75rem;
        }

        .quick-category-card img {
            width: 100%;
            height: 105px;
            object-fit: cover;
            border-radius: .9rem;
            opacity: .95;
            transition: transform .3s ease;
        }

        .quick-category-card span {
            position: absolute;
            right: .75rem;
            bottom: .75rem;
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
            background: #0f172a;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
        }

        .quick-category-card:hover {
            transform: translateY(-5px);
            border-color: #bbf7d0;
        }

        .quick-category-card:hover img {
            transform: scale(1.04);
        }

        .hero-badge-wrapper {
            margin-bottom: 1.5rem;
        }

        @media (max-width: 991.98px) {
            .hero-topbar {
                padding: .8rem;
                margin-bottom: .5rem;
            }

            .quick-categories-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .quick-categories-section {
                margin-top: -1rem;
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 575.98px) {
            .quick-categories-grid {
                grid-template-columns: 1fr;
            }
        }

        .hero-badge-new {
            display: inline-flex;
            align-items: center;
            background: rgba(22, 163, 74, 0.1);
            color: var(--primary);
            padding: 0.625rem 1.25rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid rgba(22, 163, 74, 0.2);
        }

        .hero-title-new {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            color: var(--dark);
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }

        .hero-title-new .highlight {
            background: linear-gradient(135deg, var(--primary), #22c55e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-tagline {
            font-size: 1.25rem;
            color: var(--gray);
            font-weight: 500;
            margin-bottom: 1.5rem;
            letter-spacing: 2px;
        }

        .hero-desc {
            font-size: 1.125rem;
            color: #4b5563;
            max-width: 520px;
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, var(--primary), #15803d);
            color: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
            color: white;
        }

        .btn-hero-outline {
            background: transparent;
            color: var(--dark);
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            border: 2px solid #e5e7eb;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-hero-outline:hover {
            border-color: var(--dark);
            background: var(--dark);
            color: white;
        }

        /* Hero Stats */
        .hero-stats-row {
            display: flex;
            gap: 2.5rem;
        }

        .stat-box {
            text-align: left;
        }

        .stat-num {
            display: block;
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.2;
        }

        .stat-text {
            font-size: 0.875rem;
            color: var(--gray);
            font-weight: 500;
        }

        /* Hero Visual */
        .hero-visual {
            position: relative;
            z-index: 1;
        }

        .hero-image-float {
            position: relative;
            padding: 2rem;
        }

        .main-product-img {
            width: 100%;
            max-width: 480px;
            border-radius: 24px;
            animation: gentleFloat 6s ease-in-out infinite;
            position: relative;
            z-index: 2;
        }

        .float-badge {
            position: absolute;
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            z-index: 3;
            animation: floatBadge 4s ease-in-out infinite;
        }

        .float-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .float-badge-1 {
            top: 5%;
            left: 0;
            animation-delay: 0s;
        }

        .float-badge-2 {
            bottom: 20%;
            left: -5%;
            animation-delay: 1s;
        }

        .float-badge-3 {
            top: 20%;
            right: 0;
            animation-delay: 2s;
        }

        .deco-circle {
            position: absolute;
            border-radius: 50%;
            z-index: 1;
        }

        .deco-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.1), transparent);
            top: -50px;
            right: -50px;
        }

        .deco-2 {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), transparent);
            bottom: 0;
            left: 0;
        }

        @keyframes gentleFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes floatBadge {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-8px) scale(1.05);
            }
        }

        /* Hero Wave */
        .hero-wave {
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            line-height: 0;
        }

        .hero-wave svg {
            width: 100%;
            height: 80px;
        }

        /* Mobile Hero */
        .hero-mobile {
            background: linear-gradient(135deg, #f8fffe 0%, #e8f5e9 100%);
            padding: 2rem 0;
            text-align: center;
        }

        .hero-mobile-image img {
            max-width: 280px;
            width: 100%;
            border-radius: 20px;
        }

        /* ========================================
           SECTION COMMON STYLES
        ======================================== */
        .section-label {
            display: inline-block;
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.1), rgba(22, 163, 74, 0.05));
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            border: 1px solid rgba(22, 163, 74, 0.1);
        }

        .section-heading {
            font-size: clamp(1.75rem, 3vw, 2.5rem);
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -0.5px;
            margin-bottom: 0.75rem;
        }

        .section-subtext {
            color: var(--gray);
            font-size: 1.0625rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .section-header {
            margin-bottom: 3rem;
        }

        .section-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2rem;
        }

        .btn-link-arrow {
            color: var(--dark);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            padding: 0.5rem 0;
            border-bottom: 2px solid transparent;
        }

        .btn-link-arrow:hover {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .text-accent {
            color: var(--primary);
        }

        /* ========================================
           ABOUT SECTION
        ======================================== */
        .about-section {
            padding: 6rem 0;
            background: var(--off-white);
        }

        .about-visual {
            position: relative;
        }

        .about-img-wrapper {
            position: relative;
        }

        .about-main-img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }

        .about-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 20px;
            left: 20px;
            border: 2px solid var(--primary);
            border-radius: 20px;
            z-index: -1;
            opacity: 0.3;
        }

        .about-content {
            padding-left: 2rem;
        }

        .about-text {
            color: #4b5563;
            font-size: 1.0625rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .feature-grid {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.1);
        }

        .feature-icon-box {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-light), #dcfce7);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .feature-text h6 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .feature-text p {
            font-size: 0.875rem;
            color: var(--gray);
            margin: 0;
        }

        .btn-dark-outline {
            background: transparent;
            color: var(--dark);
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            font-weight: 600;
            border: 2px solid var(--dark);
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-dark-outline:hover {
            background: var(--dark);
            color: white;
        }

        /* ========================================
           WHY SECTION
        ======================================== */
        .why-section {
            padding: 6rem 0;
            background: white;
        }

        .why-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .why-item {
            text-align: center;
            padding: 2rem 1.5rem;
            background: #fafafa;
            border-radius: 16px;
            transition: all 0.4s ease;
            border: 1px solid transparent;
        }

        .why-item:hover {
            background: white;
            border-color: var(--primary);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(22, 163, 74, 0.1);
        }

        .why-icon-wrap {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #fff, #f0fdf4);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .why-item:hover .why-icon-wrap {
            transform: scale(1.1) rotate(5deg);
        }

        .why-icon-wrap img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .why-item h5 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
            font-size: 1.125rem;
        }

        .why-item p {
            color: var(--gray);
            font-size: 0.9375rem;
            line-height: 1.6;
            margin: 0;
        }

        /* ========================================
           PRODUCTS SECTION
        ======================================== */
        .products-section {
            padding: 6rem 0;
            background: #fafafa;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .product-item {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            transition: all 0.4s ease;
        }

        .product-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border-color: transparent;
        }

        .product-img-wrap {
            position: relative;
            /* Rasio 4:5 seperti Instagram */
            aspect-ratio: 4 / 5;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            overflow: hidden;
        }

        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .product-item:hover .product-img-wrap img {
            transform: scale(1.08);
        }

        .product-badge-wrap {
            position: absolute;
            top: 1rem;
            left: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .product-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-green {
            background: rgba(22, 163, 74, 0.1);
            color: var(--primary);
        }

        .badge-orange {
            background: rgba(249, 115, 22, 0.1);
            color: var(--accent);
        }

        .badge-red {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .product-info {
            padding: 1.25rem;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .product-name {
            font-weight: 700;
            color: var(--dark);
            font-size: 1rem;
            margin: 0;
            line-height: 1.4;
        }

        .product-weight-tag {
            background: #f3f4f6;
            padding: 0.25rem 0.625rem;
            border-radius: 50px;
            font-size: 0.6875rem;
            font-weight: 600;
            color: var(--gray);
            white-space: nowrap;
        }

        .product-desc-text {
            color: var(--gray);
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-pricing {
            display: flex;
            flex-direction: column;
        }

        .price-current {
            font-size: 1.125rem;
            font-weight: 800;
            color: var(--primary);
        }

        .price-old {
            font-size: 0.8125rem;
            color: #9ca3af;
            text-decoration: line-through;
        }

        .btn-product {
            background: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-product:hover {
            background: var(--primary-dark);
            color: white;
            transform: scale(1.05);
        }

        .btn-product-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-product-outline:hover {
            background: var(--primary);
            color: white;
        }

        /* ========================================
           GALLERY SECTION
        ======================================== */
        .gallery-section {
            padding: 6rem 0;
            background: white;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(2, 200px);
            gap: 1rem;
        }

        .gallery-card {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
        }

        .gallery-card-large {
            grid-row: span 2;
        }

        .gallery-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-card:hover img {
            transform: scale(1.1);
        }

        .gallery-card-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            display: flex;
            align-items: flex-end;
            padding: 1.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-card:hover .gallery-card-overlay {
            opacity: 1;
        }

        .gallery-card-title {
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .gallery-video-wrap {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1f2937, #374151);
        }

        .gallery-video-wrap iframe {
            width: 100%;
            height: 100%;
        }

        /* ========================================
           TESTIMONIALS SECTION
        ======================================== */
        .testimonials-section {
            padding: 6rem 0;
            background: #fafafa;
        }

        .testimonials-slider {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            padding-bottom: 1rem;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        .testimonials-slider::-webkit-scrollbar {
            height: 4px;
        }

        .testimonials-slider::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        .testimonial-item {
            flex: 0 0 340px;
            background: white;
            padding: 1.75rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            scroll-snap-align: start;
            transition: all 0.3s ease;
        }

        .testimonial-item:hover {
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(22, 163, 74, 0.1);
        }

        .testimonial-stars {
            margin-bottom: 1rem;
        }

        .testimonial-stars i {
            color: #e5e7eb;
            font-size: 0.875rem;
            margin-right: 2px;
        }

        .testimonial-stars i.active {
            color: #fbbf24;
        }

        .testimonial-text {
            color: #4b5563;
            font-size: 0.9375rem;
            line-height: 1.7;
            font-style: italic;
            margin-bottom: 1.5rem;
        }

        .testimonial-author-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .testimonial-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
        }

        .testimonial-name {
            font-weight: 700;
            color: var(--dark);
            font-size: 0.9375rem;
            margin: 0;
        }

        .testimonial-date {
            font-size: 0.8125rem;
            color: var(--gray);
        }

        /* ========================================
           CTA BANNER
        ======================================== */
        .cta-banner {
            padding: 2rem 0;
            background: var(--off-white);
        }

        .cta-inner {
            background: linear-gradient(135deg, var(--primary), #15803d);
            border-radius: 24px;
            padding: 3rem 4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .cta-content h3 {
            color: white;
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .cta-content p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            margin: 0;
        }

        .btn-cta {
            background: white;
            color: var(--primary);
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-cta:hover {
            background: var(--dark);
            color: white;
            transform: translateY(-3px);
        }

        /* ========================================
           RESPONSIVE STYLES
        ======================================== */
        @media (max-width: 1199.98px) {
            .why-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
                grid-template-rows: repeat(3, 180px);
            }

            .gallery-card-large {
                grid-row: span 1;
            }
        }

        @media (max-width: 991.98px) {
            .hero-banner {
                padding-top: 1rem;
            }

            .min-vh-hero {
                min-height: auto;
                padding: 2rem 0;
            }

            .hero-content {
                text-align: center;
            }

            .hero-desc {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-cta {
                justify-content: center;
            }

            .hero-stats-row {
                justify-content: center;
            }

            .hero-wave svg {
                height: 50px;
            }

            .about-section {
                padding: 4rem 0;
            }

            .about-content {
                padding-left: 0;
            }

            .why-section,
            .products-section,
            .gallery-section,
            .testimonials-section {
                padding: 4rem 0;
            }

            .cta-inner {
                padding: 2rem;
                flex-direction: column;
                text-align: center;
            }

            .cta-content h3 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 767.98px) {
            .hero-title-new {
                font-size: 2rem;
            }

            .hero-tagline {
                font-size: 1rem;
                letter-spacing: 1px;
            }

            .hero-desc {
                font-size: 1rem;
            }

            .hero-cta {
                flex-direction: column;
            }

            .btn-hero-primary,
            .btn-hero-outline {
                width: 100%;
                justify-content: center;
                padding: 0.875rem 1.5rem;
            }

            .hero-stats-row {
                flex-wrap: wrap;
                gap: 1.5rem;
                background: white;
                padding: 1.25rem;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            }

            .stat-box {
                flex: 1;
                min-width: 80px;
                text-align: center;
            }

            .stat-num {
                font-size: 1.5rem;
            }

            .stat-text {
                font-size: 0.75rem;
            }

            .section-heading {
                font-size: 1.5rem;
            }

            .section-header-flex {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .why-grid {
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }

            .why-item {
                padding: 1.25rem 1rem;
            }

            .why-icon-wrap {
                width: 60px;
                height: 60px;
                margin-bottom: 1rem;
            }

            .why-icon-wrap img {
                width: 45px;
                height: 45px;
            }

            .why-item h5 {
                font-size: 0.9375rem;
            }

            .why-item p {
                font-size: 0.8125rem;
            }

            .products-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .product-img-wrap {
                /* Rasio 4:5 tetap untuk mobile */
                aspect-ratio: 4 / 5;
            }

            .product-info {
                padding: 0.875rem;
            }

            .product-name {
                font-size: 0.875rem;
            }

            .price-current {
                font-size: 0.9375rem;
            }

            .btn-product {
                padding: 0.375rem 0.75rem;
                font-size: 0.75rem;
            }

            .gallery-grid {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: repeat(3, 150px);
                gap: 0.75rem;
            }

            .testimonial-item {
                flex: 0 0 280px;
                padding: 1.25rem;
            }

            .testimonial-text {
                font-size: 0.875rem;
            }

            .about-section,
            .why-section,
            .products-section,
            .gallery-section,
            .testimonials-section {
                padding: 3rem 0;
            }

            .feature-grid {
                gap: 0.75rem;
            }

            .feature-item {
                padding: 1rem;
            }

            .feature-icon-box {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .cta-inner {
                padding: 1.5rem;
                border-radius: 16px;
            }

            .cta-content h3 {
                font-size: 1.25rem;
            }

            .cta-content p {
                font-size: 0.875rem;
            }

            .btn-cta {
                width: 100%;
                justify-content: center;
                padding: 0.875rem 1.5rem;
            }
        }

        @media (max-width: 575.98px) {
            .hero-title-new {
                font-size: 1.75rem;
            }

            .hero-badge-new {
                font-size: 0.75rem;
                padding: 0.5rem 1rem;
            }

            .hero-mobile-image img {
                max-width: 220px;
            }

            .product-img-wrap {
                /* Rasio 4:5 tetap untuk small mobile */
                aspect-ratio: 4 / 5;
            }

            .product-info {
                padding: 0.75rem;
            }

            .product-name {
                font-size: 0.8125rem;
            }

            .product-badge {
                font-size: 0.5625rem;
                padding: 0.25rem 0.5rem;
            }

            .price-current {
                font-size: 0.875rem;
            }

            .btn-product {
                padding: 0.25rem 0.5rem;
                font-size: 0.6875rem;
            }

            .gallery-grid {
                grid-template-rows: repeat(3, 120px);
            }

            .testimonial-item {
                flex: 0 0 250px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Smooth parallax effect on hero image
        const heroImage = document.getElementById('heroImage');
        if (heroImage) {
            document.addEventListener('mousemove', function(e) {
                if (window.innerWidth > 991) {
                    const x = (window.innerWidth / 2 - e.pageX) / 50;
                    const y = (window.innerHeight / 2 - e.pageY) / 50;
                    heroImage.style.transform = `translateY(calc(-15px + ${y}px)) translateX(${x}px)`;
                }
            });
        }

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe elements
        document.querySelectorAll('.why-item, .product-item, .gallery-card, .testimonial-item').forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(el);
        });

        // Add animation class
        document.head.insertAdjacentHTML('beforeend', `
        <style>
            .animate-in {
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
        </style>
    `);
    </script>
@endpush

@endif

@endif

@section('title', 'Hijab | Toko Spesialis Hijab dan Peralatan Hijab')

@section('content')
    @php
        $desktopHeroImages = [
            config('filesystems.disks.r2.url').'/2.png',
            config('filesystems.disks.r2.url').'/shoes.png',
            config('filesystems.disks.r2.url').'/3.png',
        ];

        $mobileHeroSlides = collect(config('branding.home_mobile_slides', []))
            ->filter(fn ($value) => filled($value))
            ->take(3)
            ->values();

        while ($mobileHeroSlides->count() < 3) {
            $mobileHeroSlides->push($desktopHeroImages[$mobileHeroSlides->count()] ?? $desktopHeroImages[0]);
        }

        $mobileHeroSlides = $mobileHeroSlides->map(function ($path, $index) use ($desktopHeroImages) {
            $rawPath = trim((string) $path);

            if ($rawPath === '') {
                return $desktopHeroImages[$index] ?? $desktopHeroImages[0];
            }

            if (preg_match('/^(https?:)?\/\//i', $rawPath) || str_starts_with($rawPath, 'data:') || str_starts_with($rawPath, '/')) {
                return $rawPath;
            }

            return asset(ltrim($rawPath, '/'));
        })->values();
    @endphp

    <div class="bg-white text-black antialiased">
    <header class="fixed left-0 top-0 z-50 w-full border-b border-black/6 bg-white/80 backdrop-blur-xl md:sticky">
            <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-6 md:px-10 lg:px-12">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ config('filesystems.disks.r2.url').'/logo.png' }}" alt="Hijab" class="h-7 w-7 object-contain" loading="lazy">
                    <span class="text-xl font-semibold tracking-tight text-black">Hijab</span>
                </a>

                <nav class="hidden items-center gap-8 md:flex">
                    <a href="{{ route('home') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Beranda</a>
                    <a href="{{ route('produk.index') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Hijab</a>
                    <a href="{{ route('produk.index') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Sepatu</a>
                    <a href="{{ route('produk.index') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Aksesoris</a>
                    <a href="{{ route('contact') }}" class="border-b border-transparent text-sm text-black/80 transition duration-300 hover:border-black/30 hover:text-black">Kontak</a>
                </nav>

                <div class="flex items-center gap-3 text-black/80">
                    @auth
                        <a href="{{ route('customer.cart.index') }}" class="transition duration-300 hover:text-black" aria-label="Cart">
                            <i class="fas fa-shopping-bag text-sm"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="transition duration-300 hover:text-black" aria-label="Cart">
                            <i class="fas fa-shopping-bag text-sm"></i>
                        </a>
                    @endauth
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-black/15 text-black transition duration-300 hover:border-black/35 md:hidden"
                        data-mobile-menu-toggle
                        aria-label="Toggle navigation"
                        aria-expanded="false"
                    >
                        <i class="fas fa-bars text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="hidden border-t border-black/10 bg-white/95 px-6 py-4 md:hidden" data-mobile-menu>
                <nav class="flex flex-col gap-3 text-sm font-medium text-black/85">
                    <a href="{{ route('home') }}" class="rounded-lg bg-black/5 px-2 py-1.5 text-black">Beranda</a>
                    <a href="{{ route('produk.index') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">Hijab</a>
                    <a href="{{ route('produk.index') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">Sepatu</a>
                    <a href="{{ route('produk.index') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">Aksesoris</a>
                    <a href="{{ route('contact') }}" class="rounded-lg px-2 py-1.5 transition hover:bg-black/5">Kontak</a>
                </nav>
            </div>
        </header>

    <main class="pt-0 md:pt-0">

    <section class="relative -mt-16 border-b-8 border-white bg-[#f5f5f7] pt-16 md:hidden">
        <div class="px-4 py-6">
            <div class="relative overflow-hidden rounded-3xl shadow-[0_18px_38px_rgba(0,0,0,0.12)]" data-mobile-hero-carousel>
                <div class="flex transition-transform duration-500 ease-out" data-mobile-hero-track>
                    @foreach($mobileHeroSlides as $index => $slideImage)
                        <div class="w-full shrink-0">
                            <img
                                src="{{ $slideImage }}"
                                alt="Hijab Mobile Slide {{ $index + 1 }}"
                                class="h-[56vh] w-full object-cover"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                            >
                        </div>
                    @endforeach
                </div>

                <button
                    type="button"
                    class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full border border-white/60 bg-black/35 px-3 py-2 text-white backdrop-blur transition hover:bg-black/55"
                    data-mobile-hero-prev
                    aria-label="Slide sebelumnya"
                >
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full border border-white/60 bg-black/35 px-3 py-2 text-white backdrop-blur transition hover:bg-black/55"
                    data-mobile-hero-next
                    aria-label="Slide berikutnya"
                >
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>

                <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 items-center gap-2" data-mobile-hero-dots>
                    @foreach($mobileHeroSlides as $index => $slideImage)
                        <button
                            type="button"
                            class="h-2.5 w-2.5 rounded-full border border-white/80 bg-white/50 transition"
                            data-mobile-hero-dot="{{ $index }}"
                            aria-label="Ke slide {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="hidden md:block">

    <x-landing.hero-product
            id="hijab"
            title="Hijab Hijab"
            subtitle="Precision. Power. Performance."
            image="{{ config('filesystems.disks.r2.url').'/2.png' }}"
            alt="Hijab Hijab"
            primary-text="Explore"
            primary-href="{{ route('produk.index') }}"
            secondary-text="Buy Now"
            secondary-href="{{ auth()->check() ? route('customer.products.index') : route('login') }}"
        section-class="bg-[#f5f5f7] border-b-8 border-white -mt-16 pt-16 md:mt-0 md:pt-0"
        />

        <x-landing.hero-product
            id="shoes"
            title="Hijab Shoes"
            subtitle="Move faster. Play smarter."
            image="{{ config('filesystems.disks.r2.url').'/shoes.png' }}"
            alt="Hijab Shoes"
            primary-text="Explore"
            primary-href="{{ route('produk.index') }}"
            secondary-text="Buy Now"
            secondary-href="{{ auth()->check() ? route('customer.products.index') : route('login') }}"
            section-class="bg-[#f5f5f7] border-b-8 border-white"
        />

        <x-landing.hero-product
            id="apparel"
            title="Hijab Accessories"
            subtitle="Comfort meets performance."
            image="{{ config('filesystems.disks.r2.url').'/3.png' }}"
            alt="Hijab Accessories"
            primary-text="Explore"
            primary-href="{{ route('produk.index') }}"
            secondary-text="Buy Now"
            secondary-href="{{ auth()->check() ? route('customer.products.index') : route('login') }}"
            section-class="bg-[#f5f5f7] border-b-8 border-white"
        />
    </div>

        

    <section class="np-fade-section bg-white py-16 lg:py-20">
            <div class="mx-auto w-full max-w-7xl px-6 md:px-10 lg:px-12">
                <div class="rounded-3xl border border-black/5 bg-linear-to-r from-zinc-100 to-white px-8 py-14 text-center shadow-[0_8px_34px_rgba(0,0,0,0.04)] lg:px-12">
                    <h2 class="text-3xl font-semibold tracking-tight text-black sm:text-4xl lg:text-5xl">Level up your game with Hijab</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-zinc-600">Designed for players who expect precision craftsmanship and world-class performance in every detail.</p>
                    <a href="{{ auth()->check() ? route('customer.products.index') : route('login') }}"
                       class="mt-8 inline-flex rounded-full bg-[#0071e3] px-8 py-3 text-sm font-medium text-white transition duration-300 hover:scale-[1.02] hover:bg-[#0077ED]">
                        Shop Collection
                    </a>
                </div>
            </div>
        </section>

        </main>

    </div>
@endsection

@push('styles')
    
    <style>
        html {
            scroll-behavior: smooth;
        }

        .np-fade-section {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .np-fade-section.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* === ANIMASI 3D SCROLL (ORYZO.AI STYLE) === */
        .np-3d-card-target {
            transform-origin: center bottom;
            will-change: transform;
        }

        .np-3d-text-target {
            will-change: transform, opacity;
        }
    </style>
@endpush

@push('scripts')
    <!-- GSAP + ScrollTrigger untuk animasi 3D scroll -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <script>
        (function () {
            // === ANIMASI YANG SUDAH ADA (TIDAK DIUBAH) ===
            const revealEls = document.querySelectorAll('.np-fade-section');
            const heroImages = document.querySelectorAll('.np-parallax-image');
            const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
            const mobileMenu = document.querySelector('[data-mobile-menu]');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.12 });

            revealEls.forEach((el) => observer.observe(el));

            const applyParallax = () => {
                const scrollTop = window.scrollY || window.pageYOffset;
                heroImages.forEach((img, index) => {
                    const intensity = 0.04 + (index * 0.005);
                    img.style.transform = `translate3d(0, ${scrollTop * intensity}px, 0)`;
                });
            };

            window.addEventListener('scroll', applyParallax, { passive: true });
            applyParallax();

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                    mobileMenuToggle.setAttribute('aria-expanded', String(!mobileMenu.classList.contains('hidden')));
                });
            }

            // === MOBILE HERO CAROUSEL (KHUSUS MOBILE, DESKTOP TIDAK DIUBAH) ===
            const mobileCarousel = document.querySelector('[data-mobile-hero-carousel]');
            if (mobileCarousel) {
                const track = mobileCarousel.querySelector('[data-mobile-hero-track]');
                const slides = track ? Array.from(track.children) : [];
                const dots = Array.from(mobileCarousel.querySelectorAll('[data-mobile-hero-dot]'));
                const prevBtn = mobileCarousel.querySelector('[data-mobile-hero-prev]');
                const nextBtn = mobileCarousel.querySelector('[data-mobile-hero-next]');

                if (track && slides.length > 0) {
                    let currentIndex = 0;
                    let autoplayTimer = null;

                    const updateSlider = (index) => {
                        currentIndex = (index + slides.length) % slides.length;
                        track.style.transform = `translateX(-${currentIndex * 100}%)`;

                        dots.forEach((dot, dotIndex) => {
                            const isActive = dotIndex === currentIndex;
                            dot.classList.toggle('bg-white', isActive);
                            dot.classList.toggle('bg-white/50', !isActive);
                            dot.classList.toggle('scale-110', isActive);
                        });
                    };

                    const stopAutoplay = () => {
                        if (autoplayTimer) {
                            clearInterval(autoplayTimer);
                            autoplayTimer = null;
                        }
                    };

                    const startAutoplay = () => {
                        stopAutoplay();
                        autoplayTimer = setInterval(() => {
                            updateSlider(currentIndex + 1);
                        }, 4500);
                    };

                    prevBtn?.addEventListener('click', () => {
                        updateSlider(currentIndex - 1);
                        startAutoplay();
                    });

                    nextBtn?.addEventListener('click', () => {
                        updateSlider(currentIndex + 1);
                        startAutoplay();
                    });

                    dots.forEach((dot) => {
                        dot.addEventListener('click', () => {
                            updateSlider(Number(dot.dataset.mobileHeroDot || 0));
                            startAutoplay();
                        });
                    });

                    mobileCarousel.addEventListener('mouseenter', stopAutoplay);
                    mobileCarousel.addEventListener('mouseleave', startAutoplay);
                    mobileCarousel.addEventListener('touchstart', stopAutoplay, { passive: true });
                    mobileCarousel.addEventListener('touchend', startAutoplay, { passive: true });

                    updateSlider(0);
                    startAutoplay();
                }
            }

            // === ANIMASI 3D SCROLL BARU (ORYZO.AI STYLE) ===
            if (typeof gsap !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);

                const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                if (!prefersReduced) {
                    // Target semua section hero-product (id: hijab, shoes, apparel)
                    const heroSections = document.querySelectorAll('#hijab, #shoes, #apparel');

                    heroSections.forEach((section) => {
                        // Cari elemen card (div dengan max-w-5xl yang berisi img)
                        const cardWrapper = section.querySelector('.max-w-5xl:has(img)');
                        // Cari elemen text (div dengan h2 dan p)
                        const textWrapper = section.querySelector('.max-w-5xl:has(h2)');

                        if (cardWrapper) {
                            // Tambahkan class untuk styling
                            cardWrapper.classList.add('np-3d-card-target');

                            // Card: mulai miring (rotateX) lalu meluruskan saat scroll
                            gsap.fromTo(cardWrapper,
                                { 
                                    rotateX: 28, 
                                    scale: 0.88, 
                                    y: 60, 
                                    opacity: 0 
                                },
                                {
                                    rotateX: 0,
                                    scale: 1,
                                    y: 0,
                                    opacity: 1,
                                    ease: 'power3.out',
                                    scrollTrigger: {
                                        trigger: section,
                                        start: 'top 80%',
                                        end: 'center 40%',
                                        scrub: 1.4,
                                    },
                                }
                            );

                            // Parallax depth saat scroll melewati section
                            gsap.to(cardWrapper, {
                                y: -30,
                                ease: 'none',
                                scrollTrigger: {
                                    trigger: section,
                                    start: 'top bottom',
                                    end: 'bottom top',
                                    scrub: true,
                                },
                            });
                        }

                        // Text: slide up saat section masuk viewport
                        if (textWrapper) {
                            textWrapper.classList.add('np-3d-text-target');

                            gsap.fromTo(textWrapper,
                                { y: 30, opacity: 0 },
                                {
                                    y: 0,
                                    opacity: 1,
                                    duration: 0.7,
                                    ease: 'power2.out',
                                    scrollTrigger: {
                                        trigger: section,
                                        start: 'top 82%',
                                        toggleActions: 'play none none none',
                                    },
                                }
                            );
                        }
                    });

                    // Featured cards: stagger 3D rotateY
                    const featuredCards = document.querySelectorAll('.np-fade-section .group');
                    if (featuredCards.length) {
                        gsap.fromTo(featuredCards,
                            { rotateY: 12, scale: 0.92, opacity: 0, y: 40 },
                            {
                                rotateY: 0,
                                scale: 1,
                                opacity: 1,
                                y: 0,
                                duration: 0.7,
                                stagger: 0.12,
                                ease: 'power2.out',
                                scrollTrigger: {
                                    trigger: featuredCards[0].closest('section'),
                                    start: 'top 78%',
                                    toggleActions: 'play none none none',
                                },
                            }
                        );
                    }
                }
            }
        })();
    </script>
@endpush
