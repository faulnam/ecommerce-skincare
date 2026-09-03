@extends('layouts.app')

@php
    $jsonPath = public_path('translation/about.json');
    $about = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@section('title', $about['meta_title'][$lang] ?? 'Tentang Kami - LUMINA Skincare')
@section('og_description', $about['meta_description'][$lang] ?? 'Kenali kami lebih dekat — brand skincare premium Indonesia yang mengutamakan kualitas, keamanan BPOM, dan kepuasan pelanggan.')

@push('og_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "AboutPage",
  "url": "{{ url()->current() }}",
  "name": "{{ $about['meta_title'][$lang] ?? 'Tentang Kami - LUMINA Skincare' }}",
  "description": "{{ $about['hero_desc'][$lang] ?? 'Kami menyediakan formulasi skincare teruji klinis dan terdaftar resmi BPOM untuk kilau kulit sehat alami.' }}",
  "inLanguage": "id-ID",
  "isPartOf": { "@id": "{{ url('/') }}/#website" },
  "publisher": { "@id": "{{ url('/') }}/#organization" }
}
</script>
@endpush

@section('content')
    @include('components.luxury-navbar')

    <style>
        /* Marquee Animation */
        .marquee-container {
            display: flex;
            overflow: hidden;
            user-select: none;
            width: 100%;
            mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
        }

        .marquee-content {
            display: flex;
            align-items: center;
            width: max-content;
            flex-shrink: 0;
            animation: marquee 45s linear infinite; /* Slower for more premium feel */
            will-change: transform;
            white-space: nowrap;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            perspective: 1000px;
            transform: translate3d(0, 0, 0);
            -webkit-font-smoothing: antialiased;
        }

        @keyframes marquee {
            0% {
                transform: translate3d(0, 0, 0);
            }
            100% {
                transform: translate3d(-50%, 0, 0);
            }
        }

        .marquee-item {
            flex: 0 0 auto; /* Auto-width to allow perfect uniform padding gaps */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.2vw; /* Fluid mobile padding */
            box-sizing: border-box;
            overflow: visible;
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
        }

        .marquee-item:hover {
            transform: scale(1.1);
        }

        .marquee-item img {
            width: auto;
            height: 8vw; /* Fluid height scaling on mobile */
            object-fit: contain;
            display: block;
        }

        /* Specific responsive height override for Head logo to match visual weight */
        .marquee-item img[alt="Head"] {
            height: 10vw; /* Mobile height */
        }

        @media (min-width: 768px) {
            .marquee-item {
                padding: 0 2.2vw; /* Fluid tablet padding */
            }
            .marquee-item img {
                height: 6vw; /* Fluid height scaling on tablet */
            }
            .marquee-item img[alt="Head"] {
                height: 8vw; /* Tablet height */
            }
        }

        @media (min-width: 1024px) {
            .marquee-item {
                padding: 0 3.8vw; /* Fluid desktop padding */
            }
            .marquee-item img {
                height: 2.8vw; /* Fluid height scaling on desktop */
                max-height: 52px;
            }
            .marquee-item img[alt="Head"] {
                height: 4.2vw; /* Desktop height */
                max-height: 80px;
            }
        }

        /* Light/Dark logo behavior */
        .logo-dark {
            display: none !important;
        }
        [data-theme="dark"] .logo-dark {
            display: block !important;
        }
        [data-theme="dark"] .logo-light {
            display: none !important;
        }

        /* Dark mode overrides for about page components */
        [data-theme="dark"] body {
            background-color: #09090b;
            color: #f4f4f5;
        }
        [data-theme="dark"] .bg-white {
            background-color: #09090b !important;
        }
        [data-theme="dark"] .bg-zinc-50 {
            background-color: #18181b !important;
        }
        [data-theme="dark"] .text-black {
            color: #f4f4f5 !important;
        }
        [data-theme="dark"] .text-zinc-600 {
            color: #a1a1aa !important;
        }
        [data-theme="dark"] .border-black\/10 {
            border-color: rgba(255, 255, 255, 0.15) !important;
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

    <main class="bg-white" style="padding-top: 140px;">
        <!-- About Section -->
        <section class="bg-white pb-8 md:pb-10 border-b border-zinc-100" id="about-section">
            <div class="mx-auto w-full max-w-[1200px] px-6 md:px-10 lg:px-12">
                
                <div class="mx-auto max-w-3xl text-center mb-8">
                    <h2 class="text-3xl font-bold tracking-tight text-black sm:text-4xl">{{ $about['hero_title'][$lang] ?? 'Tentang Kami' }}</h2>
                    <p class="mt-4 text-sm sm:text-base text-zinc-500 leading-relaxed max-w-2xl mx-auto">
                        {{ $about['hero_desc'][$lang] ?? 'Kami hadir sebagai mitra terpercaya Anda dalam merawat kesehatan dan kecantikan kulit. Kami berdedikasi untuk menghadirkan serum, toner, moisturizer, dan pembersih wajah dengan standar dermatologi internasional.' }}
                    </p>
                </div>                @php
                    $aboutProducts = \App\Models\Product::active()
                        ->where(function($q) {
                            $q->whereNotNull('image')->where('image', '!=', '0')->where('image', '!=', '');
                        })
                        ->inRandomOrder()
                        ->take(3)
                        ->get();
                        
                    $img1 = isset($aboutProducts[0]) ? $aboutProducts[0]->image_url : asset('storage/model-1.jpg');
                    $img2 = isset($aboutProducts[1]) ? $aboutProducts[1]->image_url : asset('storage/model-2.jpg');
                    $img3 = isset($aboutProducts[2]) ? $aboutProducts[2]->image_url : asset('storage/model-3.jpg');
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 px-4 md:px-0 max-w-6xl mx-auto">
                    <!-- Column 1 -->
                    <div class="flex flex-col gap-6">
                        <div class="w-full flex-1 overflow-hidden rounded-[2rem] bg-zinc-100 min-h-[300px]">
                            <img src="{{ $img1 }}" class="h-full w-full object-cover hover:scale-105 transition-transform duration-500" alt="Skincare Product">
                        </div>
                        <div class="flex flex-row items-center justify-center rounded-[2rem] bg-[#111827] py-6 px-4 h-[120px] shrink-0">
                            <div class="flex-1 border-r border-zinc-700/50 text-center">
                                <h3 class="text-3xl font-black tracking-tighter text-white">10K+</h3>
                                <p class="text-[9px] font-semibold uppercase tracking-[0.2em] text-zinc-400 mt-1">Happy Customers</p>
                            </div>
                            <div class="flex-1 text-center">
                                <h3 class="text-3xl font-black tracking-tighter text-white">100%</h3>
                                <p class="text-[9px] font-semibold uppercase tracking-[0.2em] text-zinc-400 mt-1">BPOM Certified</p>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="flex flex-col gap-6">
                        <div class="rounded-[2rem] bg-white p-8 border border-zinc-100 shadow-sm shrink-0 flex flex-col justify-center min-h-[160px]">
                            <h3 class="text-xl font-bold text-black mb-3">{{ $about['card2_title'][$lang] ?? 'Curated Formulations' }}</h3>
                            <p class="text-sm text-zinc-500 leading-relaxed font-medium">
                            {{ $about['card2_desc'][$lang] ?? 'Discover our curated collection of active ingredients, dermatologically tested serums, and nourishing moisturizers.' }}
                            </p>
                        </div>
                        <div class="w-full flex-1 overflow-hidden rounded-[2rem] bg-zinc-200 min-h-[260px]">
                            <img src="{{ $img2 }}" class="h-full w-full object-cover hover:scale-105 transition-transform duration-500" alt="Skincare Treatment">
                        </div>
                    </div>

                    <!-- Column 3 -->
                    <div class="flex flex-col rounded-[2rem] bg-[#111827] p-6 gap-6">
                        <div class="w-full h-[180px] shrink-0 overflow-hidden rounded-[1.5rem] bg-zinc-800">
                            <img src="{{ $img3 }}" class="h-full w-full object-cover hover:scale-105 transition-transform duration-500" alt="Beauty Ritual">
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <h3 class="text-xl font-bold text-white leading-tight">{{ $about['card3_title'][$lang] ?? 'Expert Skin Consultation' }}</h3>
                            <p class="mt-3 text-sm text-zinc-400 leading-relaxed">
                                {{ $about['card3_desc'][$lang] ?? 'Shop with confidence — our beauty advisors and skincare specialists help you build the ideal daily routine for your unique skin barrier.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const marqueeContent = document.getElementById('marqueeContent');
        if (marqueeContent) {
            marqueeContent.innerHTML += marqueeContent.innerHTML;
        }
    });
</script>
@endpush
@endsection