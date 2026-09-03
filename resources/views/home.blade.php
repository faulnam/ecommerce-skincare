@extends('layouts.app')

@section('title', 'LUMINA - Performa Maksimal, Game Makin Total')

@section('content')
<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #1B5E20 0%, #43A047 100%); padding: 100px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white">
                <h1 class="display-4 fw-bold mb-4">LUMINA</h1>
                <h2 class="h4 mb-4">Lengkapi permainan skincare Anda dengan peralatan berkualitas terbaik.<br>Produk original, pilihan lengkap, harga terbaik, dan pengiriman cepat ke seluruh Indonesia.</h2>
                <p class="lead mb-4">Quality skincare skincares, balls, bags, shoes, and accessories for beginners to professionals.</p>
                <div class="d-flex gap-3 mb-4">
                    @auth
                        <a href="{{ route('customer.products.index') }}" class="btn btn-warning btn-lg px-4">
                            <i class="fas fa-shopping-cart me-2"></i>Shop Now
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-warning btn-lg px-4">
                            <i class="fas fa-shopping-cart me-2"></i>Shop Now
                        </a>
                    @endauth
                    <a href="#about" class="btn btn-outline-light btn-lg px-4">Learn More</a>
                </div>
                <!-- Stats -->
                <div class="d-flex gap-4 mt-4">
                    <div class="text-center">
                        <div class="h3 fw-bold mb-0">{{ $stats['total_customers'] }}+</div>
                        <small class="text-white-50">Satisfied Customers</small>
                    </div>
                    <div class="border-start border-white-50 ps-4 text-center">
                        <div class="h3 fw-bold mb-0">{{ $stats['total_reviews'] }}+</div>
                        <small class="text-white-50">Review</small>
                    </div>
                    <div class="border-start border-white-50 ps-4 text-center">
                        <div class="h3 fw-bold mb-0">{{ $stats['avg_rating'] }}</div>
                        <small class="text-white-50">Rating ⭐</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <img src="https://images.unsplash.com/photo-1593766827228-8737b4534aa6?w=900" alt="LUMINA" class="img-fluid rounded-4 shadow-lg" style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5" id="about" style="background: linear-gradient(180deg, #F1F8E9 0%, #fff 100%);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Mengapa Memilih LUMINA?</h2>
            <p class="text-muted">Keunggulan produk perawatan kulit kami</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-leaf fa-2x text-success"></i>
                        </div>
                        <h5 class="card-title">Kualitas Teruji</h5>
                        <p class="card-text text-muted">Produk dipilih dari material premium yang tahan lama dan nyaman digunakan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-heart fa-2x text-warning"></i>
                        </div>
                        <h5 class="card-title">Untuk Semua Level</h5>
                        <p class="card-text text-muted">Pilihan gear lengkap untuk pemain pemula, intermediate, hingga kompetitif.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-table-tennis fa-2x text-info"></i>
                        </div>
                        <h5 class="card-title">Performa Maksimal</h5>
                        <p class="card-text text-muted">Bantu kontrol, power, dan kenyamanan bermain di setiap sesi latihan maupun match.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5" id="products">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Produk Kami</h2>
            <p class="text-muted">Pilihan produk perawatan kulit yang lengkap</p>
        </div>
        
        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 product-card">
                        <div class="position-relative">
                       <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?w=400' }}" 
                                 class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                            @if($product->hasActiveDiscount())
                                <span class="position-absolute top-0 end-0 m-2 badge bg-danger">-{{ $product->formatted_discount_percent }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="badge bg-{{ $product->category == 'original' ? 'success' : 'danger' }} me-1">{{ $product->category_label }}</span>
                                <span class="badge bg-secondary">{{ $product->formatted_weight }}</span>
                            </div>
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($product->description, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($product->hasActiveDiscount())
                                        <span class="h5 text-success mb-0">{{ $product->formatted_discounted_price }}</span>
                                        <small class="text-decoration-line-through text-muted d-block">{{ $product->formatted_price }}</small>
                                    @else
                                        <span class="h5 text-success mb-0">{{ $product->formatted_price }}</span>
                                    @endif
                                </div>
                                @auth
                                    <a href="{{ route('customer.products.show', $product) }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-shopping-cart me-1"></i>Pesan
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada produk tersedia.</p>
                </div>
            @endforelse
        </div>
        
        @if($products->count() > 0)
            <div class="text-center mt-5">
                @auth
                    <a href="{{ route('customer.products.index') }}" class="btn btn-success btn-lg px-5">
                        Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-success btn-lg px-5">
                        Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                @endauth
            </div>
        @endif
    </div>
</section>

<!-- About Section -->
<section class="bg-white py-12 md:py-16 border-b border-zinc-100" id="about">
    <div class="container">
        <!-- Header -->
        <div class="mx-auto max-w-3xl text-center mb-5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-muted">TENTANG KAMI</p>
            <h2 class="mt-1.5 fw-bold text-dark">
                Tentang <span class="text-success">Kami</span>
            </h2>
            <p class="mt-3 text-muted leading-relaxed max-w-2xl mx-auto">
                Kami hadir sebagai penyedia koleksi fashion skincare premium dan mitra terpercaya Anda. Kami berdedikasi menghadirkan pakaian muslimah, kerudung, tas, sepatu, dan aksesoris berkualitas tinggi untuk mendukung kenyamanan dan keanggunan Anda di setiap kesempatan.
            </p>
        </div>

        <!-- 3 Columns Cards -->
        <div class="row g-4 justify-content-center">
            <!-- Card 1: Premium Quality (Dark Green) -->
            <div class="col-md-4">
                <div class="about-card-dark rounded-4 p-4 d-flex flex-col justify-content-between overflow-hidden shadow h-100 min-h-[440px]">
                    <!-- Top Image -->
                    <div class="relative w-full h-[220px] rounded-3 overflow-hidden bg-dark d-flex align-items-center justify-content-center p-3">
                        <img src="https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=600" alt="LUMINA Dermatological Care" class="w-full h-full object-cover rounded transition hover:scale-105" loading="lazy" style="max-height: 180px;">
                    </div>
                    
                    <!-- Bottom Stats Box -->
                    <div class="mt-4 about-card-stats rounded-3 p-3 d-flex align-items-center justify-content-around">
                        <div class="text-center w-50 border-end border-success">
                            <span class="d-block h3 fw-bold about-text-emerald mb-0">100%</span>
                            <span class="d-block text-white-50 uppercase font-semibold small mt-1">Produk Original</span>
                        </div>
                        <div class="text-center w-50">
                            <span class="d-block h3 fw-bold about-text-emerald mb-0">7 Hari</span>
                            <span class="d-block text-white-50 uppercase font-semibold small mt-1">Garansi Retur</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Lime/Light Green Theme -->
            <div class="col-md-4">
                <div class="about-card-lime rounded-4 p-4 d-flex flex-col justify-content-between shadow h-100 min-h-[440px]">
                    <!-- Top Text Content -->
                    <div class="mb-4">
                        <h4 class="fw-bold leading-tight">Solusi Perlengkapan LUMINA Anda</h4>
                        <p class="mt-3 small leading-relaxed font-medium">
                            Kami menyediakan berbagai pilihan skincare, tas, sepatu, dan aksesoris skincare premium dari brand ternama dunia, dikurasi secara profesional untuk membantu Anda mendominasi lapangan.
                        </p>
                    </div>
                    
                    <!-- Bottom Image -->
                    <div class="relative w-full h-[200px] rounded-3 overflow-hidden about-card-lime-img-bg">
                        <img src="https://images.unsplash.com/photo-1599058917212-d750089bc07e?w=600" alt="LUMINA Court" class="w-full h-full object-cover transition hover:scale-105" loading="lazy" style="max-height: 180px;">
                    </div>
                </div>
            </div>

            <!-- Card 3: Support/Service (Dark Green) -->
            <div class="col-md-4">
                <div class="about-card-dark rounded-4 p-4 d-flex flex-col justify-content-between overflow-hidden shadow h-100 min-h-[440px]">
                    <!-- Top Image -->
                    <div class="relative w-full h-[200px] rounded-3 overflow-hidden bg-dark">
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600" alt="LUMINA Consultation" class="w-full h-full object-cover transition hover:scale-105" loading="lazy" style="max-height: 180px;">
                    </div>

                    <!-- Bottom Text Content -->
                    <div class="mt-4">
                        <h4 class="fw-bold leading-tight">Konsultasi Gear & Layanan Profesional</h4>
                        <p class="mt-3 small text-white-50 leading-relaxed">
                            Kami menjamin pengalaman berbelanja yang aman dan nyaman, didukung dengan konsultasi pemilihan gear yang tepat serta layanan purnajual responsif untuk memastikan Anda siap bertanding kapan saja.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
@if($galleries->count() > 0)
<section class="py-5 bg-light" id="gallery">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Galeri</h2>
            <p class="text-muted">Momen dan aktivitas kami</p>
        </div>
        
        <div class="row g-4">
            @foreach($galleries as $gallery)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 gallery-card overflow-hidden">
                        @if($gallery->isImage())
                            <div class="gallery-image-wrapper" data-bs-toggle="modal" data-bs-target="#galleryModal{{ $gallery->id }}" style="cursor: pointer;">
                                <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="card-img-top gallery-image" style="height: 250px; object-fit: cover;">
                                <div class="gallery-overlay">
                                    <i class="fas fa-search-plus fa-2x text-white"></i>
                                </div>
                            </div>
                        @else
                            <div class="ratio ratio-16x9">
                                {!! $gallery->embed_url !!}
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                @if($gallery->isImage())
                                    <span class="badge bg-primary me-2"><i class="fas fa-image me-1"></i>Gambar</span>
                                @else
                                    <span class="badge bg-danger me-2"><i class="fab fa-instagram me-1"></i>Video</span>
                                @endif
                            </div>
                            <h6 class="card-title mb-1">{{ $gallery->title }}</h6>
                            @if($gallery->description)
                                <p class="card-text small text-muted mb-0">{{ Str::limit($gallery->description, 80) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Modal for Image Gallery -->
                @if($gallery->isImage())
                <div class="modal fade" id="galleryModal{{ $gallery->id }}" tabindex="-1" aria-labelledby="galleryModalLabel{{ $gallery->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content bg-transparent border-0">
                            <div class="modal-header border-0">
                                <h5 class="modal-title text-white" id="galleryModalLabel{{ $gallery->id }}">{{ $gallery->title }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0 text-center">
                                <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="img-fluid rounded">
                            </div>
                            @if($gallery->description)
                                <div class="modal-footer border-0 justify-content-center">
                                    <p class="text-white mb-0">{{ $gallery->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, #1B5E20 0%, #43A047 100%);">
    <div class="container text-center text-white">
    <h2 class="fw-bold mb-4">Ready to Upgrade Your LUMINA Gear?</h2>
    <p class="lead mb-4">Shop now and improve your game performance with LUMINA!</p>
        @auth
            <a href="{{ route('customer.products.index') }}" class="btn btn-warning btn-lg px-5">
                <i class="fas fa-shopping-cart me-2"></i>Shop Now
            </a>
        @else
            <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5">
                <i class="fas fa-user-plus me-2"></i>Register & Shop
            </a>
        @endauth
    </div>
</section>
@endsection

@push('styles')
<style>
    .product-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    /* Gallery Styles */
    .gallery-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .gallery-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .gallery-image-wrapper {
        position: relative;
        overflow: hidden;
    }
    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .gallery-image-wrapper:hover .gallery-overlay {
        opacity: 1;
    }
    .gallery-image-wrapper:hover .gallery-image {
        transform: scale(1.05);
    }
    .gallery-image {
        transition: transform 0.3s;
    }
    
    /* Modal dark background */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.85) !important;
    }

    /* About Modern Cards CSS */
    .about-card-dark {
        background-color: #0b1f14;
        border: 1px solid rgba(22, 58, 36, 0.3);
        color: #ffffff;
        transition: all 0.3s ease;
    }
    [data-theme="dark"] .about-card-dark {
        background-color: #06130b !important;
        border-color: rgba(22, 58, 36, 0.6) !important;
    }
    .about-card-lime {
        background-color: #c5ff3b;
        color: #09090b;
        transition: all 0.3s ease;
    }
    [data-theme="dark"] .about-card-lime {
        background-color: #aae620 !important;
        color: #09090b !important;
    }
    .about-card-lime-img-bg {
        background-color: #b1e631;
    }
    [data-theme="dark"] .about-card-lime-img-bg {
        background-color: #92c422 !important;
    }
    .about-text-emerald {
        color: #10b981;
    }
    [data-theme="dark"] .about-text-emerald {
        color: #34d399 !important;
    }
    .about-card-stats {
        background: linear-gradient(to right, #0d2a1a, #071a10);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    [data-theme="dark"] .about-card-stats {
        background: linear-gradient(to right, #081a10, #040e08) !important;
        border-color: rgba(16, 185, 129, 0.1) !important;
    }
</style>
@endpush
