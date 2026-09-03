@extends('layouts.app')

@section('title', 'LUMINA Skincare | Solusi Perawatan Kulit Sehat & Glowing')

@section('content')
<style>
    /* Hide default scrollbars for horizontal lists */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .color-swatch {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid #e5e5e5;
        cursor: pointer;
    }
    .color-swatch.active {
        outline: 1px solid #000;
        outline-offset: 2px;
    }

    .product-card img {
        transition: transform 0.5s ease;
    }
    .product-card:hover img {
        transform: scale(1.05);
    }
</style>

@push('styles')
<style>
    body { padding-top: 0 !important; }
    
</style>
@endpush

<!-- Custom Navbar -->
@include('components.luxury-navbar')

<!-- HERO SECTION -->
<section class="relative w-full" style="height: 85vh; background-color: #e3e3e3;">
    <!-- Hero Image Carousel -->
    <picture>
        <source media="(max-width: 768px)" srcset="{{ asset('storage/model-1.jpg') }}" id="hero-source-mobile">
        <source media="(min-width: 769px)" srcset="{{ $heroBanners->first()->image_url ?? '' }}" id="hero-source-desktop">
        <img id="hero-image" src="{{ $heroBanners->first()->image_url ?? '' }}" alt="Hero" class="absolute inset-0 w-full h-full object-cover object-center filter" style="filter: brightness(0.9); transition: opacity 0.1s;">
    </picture>
    
    <!-- Black Blink Overlay -->
    <div id="blink-overlay" class="absolute inset-0 bg-black pointer-events-none" style="opacity: 0; transition: opacity 0.15s ease-in-out; z-index: 10;"></div>
    


    <!-- Center Content -->
    <div class="absolute inset-0 flex flex-col items-center justify-end text-center px-4 pointer-events-none z-20" style="padding-bottom: 70px;">
        <a id="shop-now-btn" href="{{ route('produk.index') }}" class="pointer-events-auto text-white hover:bg-white hover:text-black font-bold uppercase" style="border: 1px solid rgba(255, 255, 255, 0.5); background-color: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); font-size: 13px; letter-spacing: 0.2em; padding: 16px 60px; transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s ease, background-color 0.3s, color 0.3s; transform: translateY(0); opacity: 1;">
            {{ $heroBanners->first()->title ?? 'SHOP NOW' }}
        </a>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute left-1/2 transform -translate-x-1/2 w-12 h-12 bg-white rounded-full flex items-center justify-center z-20 cursor-pointer hover:bg-gray-50 transition-colors" style="bottom: -24px; box-shadow: 0 4px 14px rgba(0,0,0,0.25);">
        <i class="fas fa-chevron-down text-gray-400" style="font-size: 12px; margin-top: -6px;"></i>
    </div>

    <!-- Carousel Dots -->
    <div class="absolute flex space-x-2 z-20" style="bottom: 48px; right: 48px;" id="hero-dots">
        @foreach($heroBanners as $index => $banner)
        <div class="rounded-full bg-white {{ $index === 0 ? 'opacity-100' : 'opacity-50' }} cursor-pointer hero-dot" style="width: 6px; height: 6px;" data-index="{{ $index }}"></div>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Carousel Logic
        const desktopImages = [
            @foreach($heroBanners as $banner)
            "{{ $banner->image_url }}",
            @endforeach
        ];

        const mobileImages = [
            "{{ asset('storage/model-1.jpg') }}",
            "{{ asset('storage/product-1-skincare.jpg') }}",
            "{{ asset('storage/product-1-baju.jpg') }}"
        ];
        const titles = [
            @foreach($heroBanners as $banner)
            "{{ $banner->title }}",
            @endforeach
        ];
        let currentIndex = 0;
        const heroImg = document.getElementById('hero-image');
        const blinkOverlay = document.getElementById('blink-overlay');
        const dots = document.querySelectorAll('.hero-dot');
        const shopNowBtn = document.getElementById('shop-now-btn');
        let autoPlayInterval;
        
        // Initial button animation on load
        shopNowBtn.style.transition = 'none';
        shopNowBtn.style.opacity = '0';
        shopNowBtn.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            shopNowBtn.style.transition = 'transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s ease, background-color 0.3s, color 0.3s';
            shopNowBtn.style.opacity = '1';
            shopNowBtn.style.transform = 'translateY(0)';
        }, 100);

        function updateDots() {
            dots.forEach((dot, index) => {
                if(index === currentIndex) {
                    dot.style.opacity = '1';
                } else {
                    dot.style.opacity = '0.5';
                }
            });
        }
        
        function changeImage(index) {
            if(index === currentIndex) return;
            
            // Blink to black
            blinkOverlay.style.opacity = '1';

            // Animate button out (instantly)
            shopNowBtn.style.transition = 'none';
            shopNowBtn.style.opacity = '0';
            shopNowBtn.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                currentIndex = index;
                document.getElementById('hero-source-mobile').srcset = mobileImages[currentIndex];
                document.getElementById('hero-source-desktop').srcset = desktopImages[currentIndex];
                heroImg.src = desktopImages[currentIndex]; // fallback
                updateDots();
                
                // Remove blink after image source is updated
                setTimeout(() => {
                    blinkOverlay.style.opacity = '0';
                    shopNowBtn.textContent = titles[currentIndex];
                    
                    // Animate button in
                    shopNowBtn.style.transition = 'transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s ease, background-color 0.3s, color 0.3s';
                    shopNowBtn.style.opacity = '1';
                    shopNowBtn.style.transform = 'translateY(0)';
                }, 150);
            }, 150); // Wait for blink to become fully black
        }
        
        function startAutoPlay() {
            autoPlayInterval = setInterval(() => {
                let nextIndex = (currentIndex + 1) % desktopImages.length;
                changeImage(nextIndex);
            }, 5000);
        }

        function resetAutoPlay() {
            clearInterval(autoPlayInterval);
            startAutoPlay();
        }
        
        // Init Auto play
        startAutoPlay();
        
        // Click dots
        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                const idx = parseInt(dot.getAttribute('data-index'));
                changeImage(idx);
                resetAutoPlay();
            });
        });

        // Navbar Scroll Effect
        const navbar = document.getElementById('custom-navbar');
        const topMarquee = document.getElementById('top-marquee');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 35) {
                navbar.style.top = '0px';
                navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                navbar.style.paddingTop = '10px';
                navbar.style.paddingBottom = '10px';
                if (topMarquee) topMarquee.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.top = '31px';
                navbar.style.backgroundColor = 'transparent';
                navbar.style.boxShadow = 'none';
                navbar.style.paddingTop = '15px';
                navbar.style.paddingBottom = '15px';
                if (topMarquee) topMarquee.style.transform = 'translateY(0)';
            }
        });
        // Carousel Scrolling Logic
        function setupCarousel(carouselId, leftBtnId, rightBtnId) {
            const carousel = document.getElementById(carouselId);
            const leftBtn = document.getElementById(leftBtnId);
            const rightBtn = document.getElementById(rightBtnId);
            
            if (carousel && leftBtn && rightBtn) {
                leftBtn.addEventListener('click', () => {
                    carousel.scrollBy({ left: -300, behavior: 'smooth' });
                });
                rightBtn.addEventListener('click', () => {
                    carousel.scrollBy({ left: 300, behavior: 'smooth' });
                });
                
                // Update button visibility based on scroll position
                const updateButtons = () => {
                    leftBtn.style.opacity = carousel.scrollLeft > 0 ? '1' : '0.5';
                    leftBtn.style.pointerEvents = carousel.scrollLeft > 0 ? 'auto' : 'none';
                    
                    const maxScrollLeft = carousel.scrollWidth - carousel.clientWidth;
                    rightBtn.style.opacity = carousel.scrollLeft >= maxScrollLeft - 5 ? '0.5' : '1';
                    rightBtn.style.pointerEvents = carousel.scrollLeft >= maxScrollLeft - 5 ? 'none' : 'auto';
                };
                
                carousel.addEventListener('scroll', updateButtons);
                updateButtons(); // initial state
            }
        }
        
        setupCarousel('product-carousel', 'scroll-left', 'scroll-right');
        setupCarousel('voucher-carousel', 'voucher-scroll-left', 'voucher-scroll-right');
    });

    // Custom Toast Notification for Vouchers
    function claimVoucher(code) {
        navigator.clipboard.writeText(code);
        
        // Create toast container if not exists
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-5 right-5 z-50 flex flex-col gap-2';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'bg-white border-l-4 border-green-500 shadow-lg rounded px-4 py-3 flex items-center transform transition-all duration-300 translate-x-full opacity-0';
        toast.style.fontFamily = "'Inter', sans-serif";
        toast.style.minWidth = "250px";
        
        toast.innerHTML = `
            <div class="flex-shrink-0 text-green-500 mr-3">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-800">Berhasil Diklaim!</h4>
                <p class="text-xs text-gray-500 mt-0.5">Kode <span class="font-mono font-bold text-black">${code}</span> telah disalin.</p>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
</script>
@endpush

@if(isset($vouchers) && $vouchers->count() > 0)
<!-- EXCLUSIVE VOUCHERS -->
<section class="py-6 bg-white max-w-[1440px] mx-auto px-6 md:px-14 relative group">
    
    <!-- Left/Right controls for vouchers (positioned outside card area) -->
    <button id="voucher-scroll-left" class="absolute bg-white rounded-full flex items-center justify-center text-black shadow-md z-10 cursor-pointer hover:bg-gray-50 transition-all" style="width: 36px; height: 36px; top: 50%; left: 8px; transform: translateY(-50%); border: 1px solid #e5e7eb;" aria-label="Previous Voucher">
        <i class="fas fa-chevron-left text-xs"></i>
    </button>
    <button id="voucher-scroll-right" class="absolute bg-white rounded-full flex items-center justify-center text-black shadow-md z-10 cursor-pointer hover:bg-gray-50 transition-all" style="width: 36px; height: 36px; top: 50%; right: 8px; transform: translateY(-50%); border: 1px solid #e5e7eb;" aria-label="Next Voucher">
        <i class="fas fa-chevron-right text-xs"></i>
    </button>

    <div id="voucher-carousel" class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth hide-scrollbar py-2" style="gap: 18px;">
        @foreach($vouchers as $voucher)
        <div class="snap-start relative flex bg-white border border-gray-200 shadow-sm transition-all hover:shadow-md shrink-0 overflow-hidden" style="min-width: 350px; width: 350px; flex: 0 0 auto; border-radius: 12px;">
            <!-- Left Section (Voucher Details) -->
            <div class="flex-1 p-4 flex flex-col justify-between" style="background-color: #ffffff;">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="inline-block text-[10px] font-bold tracking-wider uppercase px-2.5 py-0.5 rounded-full" style="background-color: #EBF3EE; color: #2D4C41; font-family: 'Inter', sans-serif;">
                            @if($voucher->type === 'percent')
                                DISKON {{ intval($voucher->discount_value) }}%
                            @elseif($voucher->type === 'fixed')
                                POTONGAN RP {{ number_format($voucher->discount_value / 1000, 0) }}K
                            @else
                                SPESIAL VOUCHER
                            @endif
                        </span>
                    </div>
                    <h3 class="text-[14px] font-bold text-gray-900 leading-tight truncate mb-1" style="font-family: 'Inter', sans-serif;" title="{{ $voucher->title }}">{{ $voucher->title }}</h3>
                    <p class="text-xs text-gray-500 mb-2" style="font-family: 'Inter', sans-serif;">Min. belanja Rp{{ number_format($voucher->minimum_purchase, 0, ',', '.') }}</p>
                </div>
                
                <div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mb-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full transition-all" style="background-color: #2D4C41; width: {{ max(10, min(100, $voucher->quota_percentage)) }}%;"></div>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-gray-400" style="font-family: 'Inter', sans-serif;">
                        <span>Sisa: <strong class="text-gray-600 font-semibold">{{ $voucher->remaining_quota }}</strong></span>
                        <span>Berlaku s/d {{ $voucher->end_date->format('d.m.Y') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Stub Divider with clean styling -->
            <div class="w-[1px] my-2 border-l border-dashed border-gray-300 relative shrink-0"></div>
            
            <!-- Right Section (Stub & Action) -->
            <div class="w-24 shrink-0 flex flex-col items-center justify-center p-3 text-center" style="background-color: #FAF8F5;">
                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400 mb-1" style="font-family: 'Inter', sans-serif;">KODE</span>
                <span class="font-mono font-bold text-[11px] text-gray-900 bg-white border border-gray-200 rounded px-1.5 py-0.5 mb-2.5 tracking-wider select-all shadow-2xs w-full block truncate">
                    {{ $voucher->code }}
                </span>
                <button onclick="claimVoucher('{{ $voucher->code }}')" class="inline-flex items-center justify-center gap-1 w-full py-1.5 rounded-md text-[11px] font-semibold text-white transition-all hover:opacity-90 active:scale-95 shadow-xs cursor-pointer" style="background-color: #2D4C41; font-family: 'Inter', sans-serif;" title="Klaim Kode Voucher">
                    <i class="far fa-copy text-[10px]"></i> Klaim
                </button>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

<!-- PRODUCT CAROUSEL 1 -->
<section class="pt-0 pb-16 md:pt-4 md:pb-24 max-w-[1440px] mx-auto relative px-4 md:px-12 group" style="padding-bottom: 20px;">
    <!-- Left/Right controls -->
    <button id="scroll-left" class="absolute bg-white rounded-full flex items-center justify-center text-black shadow-md z-10 cursor-pointer hidden md:flex hover:bg-gray-50 opacity-0 group-hover:opacity-100 transition-opacity" style="width: 40px; height: 40px; top: 40%; left: 16px; transform: translateY(-50%); border: 1px solid #eee;">
        <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
    </button>
    <button id="scroll-right" class="absolute bg-white rounded-full flex items-center justify-center text-black shadow-md z-10 cursor-pointer hidden md:flex hover:bg-gray-50 opacity-0 group-hover:opacity-100 transition-opacity" style="width: 40px; height: 40px; top: 40%; right: 16px; transform: translateY(-50%); border: 1px solid #eee;">
        <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
    </button>
    
    <!-- Product List -->
    <div id="product-carousel" class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth pb-4" style="gap: 24px; -ms-overflow-style: none; scrollbar-width: none;">
        <style>
            #product-carousel::-webkit-scrollbar { display: none; }
            .product-item:hover .size-popup { opacity: 1 !important; }
        </style>
        @foreach($newArrivals as $product)
        @php
            $detailUrl = $product->detail_url;
        @endphp

        <div class="product-item cursor-pointer flex-none snap-start flex flex-col relative" style="width: calc(25% - 18px); min-width: 280px;" onclick="window.location.href='{{ $detailUrl }}'">
            <div class="relative w-full bg-gray-200 overflow-hidden mb-4" style="aspect-ratio: 3 / 4;">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                
                @if($product->hasActiveDiscount())
                <div class="absolute text-white font-bold uppercase tracking-wider z-10" style="top: 12px; left: 12px; font-size: 10px; padding: 4px 8px; background-color: #e53e3e; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    Diskon {{ $product->formatted_discount_percent }}
                </div>
                @endif
                <div class="absolute text-black font-bold uppercase tracking-wider z-10" style="{{ $product->hasActiveDiscount() ? 'top: 42px;' : 'top: 12px;' }} left: 12px; font-size: 10px; padding: 4px 8px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    Terbaru
                </div>
                
                @if(!$product->inStock())
                <div class="absolute text-white font-bold uppercase tracking-wider" style="top: 0; left: 0; font-size: 10px; padding: 4px 12px; background-color: #555;">
                    Sold Out
                </div>
                @endif
                
                <button class="absolute text-white hover:text-gray-200 transition-colors z-10" style="top: 12px; right: 12px;" onclick="event.preventDefault(); event.stopPropagation(); addToWishlist('{{ $product->slug }}', event, this)" data-in-wishlist="{{ in_array($product->id, $userWishlistIds ?? []) ? 'true' : 'false' }}">
                    <i class="fas fa-heart {{ in_array($product->id, $userWishlistIds ?? []) ? 'text-rose-500' : '' }}" style="font-size: 16px; filter: drop-shadow(0px 1px 2px rgba(0,0,0,0.5));"></i>
                </button>
                
                <!-- Sizes on Hover -->
                <div class="size-popup absolute flex justify-between bg-white shadow-sm" style="bottom: 16px; left: 16px; right: 16px; opacity: 0; transition: opacity 0.3s ease;">
                    @php
                        $sizes = collect();
                        if ($product->has_variants) {
                            foreach($product->variants as $variant) {
                                $parts = explode('|', $variant->name);
                                if(count($parts) >= 1) $sizes->push($parts[0]);
                            }
                        }
                        $uniqueSizes = $sizes->unique();
                    @endphp
                    @forelse($uniqueSizes->take(4) as $size)
                        @php
                            $displaySize = trim($size);
                            if (preg_match('/(\d+\s*(?:ml|gr|g|oz))/i', $displaySize, $m)) {
                                $displaySize = strtolower(str_replace(' ', '', $m[1]));
                            } else {
                                $displaySize = preg_replace('/^(Travel|Regular|Jumbo|Mini|Full)\s*/i', '', $displaySize);
                            }
                        @endphp
                        <div class="flex-1 text-center py-2 text-xs font-semibold hover:bg-gray-100" style="border-right: 1px solid #f3f4f6;" onclick="event.stopPropagation(); window.location.href='{{ $detailUrl }}?size={{ urlencode(trim($size)) }}'">{{ $displaySize }}</div>
                    @empty
                        <div class="flex-1 text-center py-2 text-xs font-semibold hover:bg-gray-100">All Size</div>
                    @endforelse
                </div>
            </div>
            
            <div class="flex-1 flex flex-col justify-start text-left px-1">
                <h3 class="text-gray-800 leading-snug mb-1 line-clamp-1" style="font-size: 13px; font-family: Arial, sans-serif;">{{ $product->name }}</h3>
                <p class="text-gray-800 mb-3" style="font-size: 13px; font-family: Arial, sans-serif;">{{ $product->formatted_price }}</p>
                <div class="flex mt-auto" style="gap: 8px;">
                    @php
                        $colors = collect();
                        if ($product->has_variants) {
                            foreach($product->variants as $variant) {
                                $parts = explode('|', $variant->name);
                                if (count($parts) >= 3) {
                                    $colors->push(trim($parts[2]));
                                }
                            }
                        }
                    @endphp
                    @foreach($colors->unique() as $color)
                        <div class="rounded-full cursor-pointer" style="width: 16px; height: 16px; background-color: {{ $color }}; border: 2px solid white; box-shadow: 0 0 0 1px #d1d5db;" onclick="event.stopPropagation(); window.location.href='{{ $detailUrl }}?colorHex={{ urlencode(trim($color)) }}'"></div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const setupCarousel = (carouselId, leftBtnId, rightBtnId) => {
            const carousel = document.getElementById(carouselId);
            const btnLeft = document.getElementById(leftBtnId);
            const btnRight = document.getElementById(rightBtnId);
            if (carousel && btnLeft && btnRight) {
                btnLeft.addEventListener('click', () => {
                    carousel.scrollBy({ left: -300, behavior: 'smooth' });
                });
                btnRight.addEventListener('click', () => {
                    carousel.scrollBy({ left: 300, behavior: 'smooth' });
                });
            }
        };

        setupCarousel('product-carousel', 'scroll-left', 'scroll-right');
        setupCarousel('product-carousel-2', 'scroll-left-2', 'scroll-right-2');
    });

    function showToast(message, type) {
        const existing = document.querySelector('.np-toast-msg');
        if (existing) existing.remove();

        const div = document.createElement('div');
        div.className = 'np-toast-msg';
        const colors = {
            success:  { bg: '#f0fdf4', color: '#166534', border: '#bbf7d0' },
            removed:  { bg: '#fef2f2', color: '#991b1b', border: '#fecaca' },
            error:    { bg: '#fef2f2', color: '#991b1b', border: '#fecaca' },
        };
        const c = colors[type] || colors.success;
        div.innerHTML = `<span style="flex:1">${message}</span><span style="opacity:.5;font-size:18px;line-height:1">&times;</span>`;
        div.style.cssText = `position:fixed;top:20px;right:20px;z-index:99999;padding:14px 18px;border-radius:12px;font-size:13px;font-weight:500;display:flex;align-items:center;gap:10px;box-shadow:0 8px 30px rgba(0,0,0,.12);cursor:pointer;background:${c.bg};color:${c.color};border:1px solid ${c.border};animation:toastIn .4s cubic-bezier(.34,1.56,.64,1)`;
        div.onclick = () => { div.style.animation = 'toastOut .3s ease forwards'; setTimeout(() => div.remove(), 300); };
        document.body.appendChild(div);
        setTimeout(() => { div.style.animation = 'toastOut .3s ease forwards'; setTimeout(() => div.remove(), 300); }, 3000);
    }

    function addToWishlist(productId, event, btn) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        @guest
        window.location.href = '/login';
        return;
        @endguest


        const button = btn || event.target.closest('button');
        const icon = button.querySelector('i');
        const inWishlist = button.getAttribute('data-in-wishlist') === 'true';

        if (icon) {
            icon.classList.remove('fa-heart');
            icon.classList.add('fa-spinner', 'fa-spin');
        }

        if (inWishlist) {
            fetch(`/customer/wishlist/${productId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    button.setAttribute('data-in-wishlist', 'false');
                    button.classList.remove('text-rose-500');
                    if (icon) { icon.classList.remove('fa-spinner','fa-spin','text-rose-500'); icon.classList.add('fa-heart'); }
                    showToast('Favorit dihapus', 'removed');
                } else {
                    showToast(data.message || 'Gagal menghapus dari wishlist', 'error');
                    if (icon) { icon.classList.remove('fa-spinner','fa-spin'); icon.classList.add('fa-heart'); }
                }
            })
            .catch(err => {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                if (icon) { icon.classList.remove('fa-spinner','fa-spin'); icon.classList.add('fa-heart'); }
            });
        } else {
            fetch(`/customer/wishlist/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    button.setAttribute('data-in-wishlist', 'true');
                    button.classList.add('text-rose-500');
                    if (icon) { icon.classList.remove('fa-spinner','fa-spin'); icon.classList.add('fa-heart','text-rose-500'); }
                    showToast('Favorit masuk', 'success');
                } else {
                    showToast(data.message || 'Gagal menambahkan produk ke wishlist', 'error');
                    if (icon) { icon.classList.remove('fa-spinner','fa-spin'); icon.classList.add('fa-heart'); }
                }
            })
            .catch(err => {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                if (icon) { icon.classList.remove('fa-spinner','fa-spin'); icon.classList.add('fa-heart'); }
            });
        }
    }
</script>
@endpush

<!-- CATEGORY STRIP -->
<section style="padding: 20px 20px 80px 20px; background-color: #ffffff; max-width: 1400px; margin: 0 auto;">
        @php
            $categories = [
                ['name' => 'CLEANSER', 'img' => asset('storage/Atas.png'), 'filter' => 'cleanser'],
                ['name' => 'TONER & ESSENCE', 'img' => asset('storage/Tengah.png'), 'filter' => 'toner'],
                ['name' => 'SERUM & TREATMENT', 'img' => asset('storage/Accecories.png'), 'filter' => 'serum'],
                ['name' => 'MOISTURIZER & SUNSCREEN', 'img' => asset('storage/Bawah.png'), 'filter' => 'moisturizer']
            ];
        @endphp

        <style>
            .cat-item { transition: transform 0.3s ease; }
            .cat-item:hover { transform: translateY(-8px); }
            
            .cat-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
                align-items: flex-end;
                gap: 20px;
            }
            
            .cat-link {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-decoration: none;
                flex: 1 1 20%;
                min-width: 120px;
            }
            
            .cat-img-wrapper {
                width: 100%;
                max-width: 200px;
                height: 220px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 40px;
                margin-left: auto;
                margin-right: auto;
            }
            
            @media (max-width: 768px) {
                .cat-link {
                    flex: 1 1 45%;
                }
                .cat-img-wrapper {
                    height: 180px;
                    margin-bottom: 15px;
                }
            }
        </style>

        <div class="cat-container">
            @foreach($categories as $cat)
            <a href="{{ route('produk.index') }}?category={{ $cat['filter'] }}" class="cat-link">
                <div class="cat-item cat-img-wrapper">
                    <img src="{{ $cat['img'] }}" alt="{{ $cat['name'] }}" style="max-width: 100%; max-height: 100%; object-fit: contain; mix-blend-mode: multiply; filter: contrast(1.1);">
                </div>
                <span style="font-size: 11px; font-weight: 600; color: #000; letter-spacing: 0.15em; font-family: Arial, sans-serif; text-align: center;">{{ $cat['name'] }}</span>
            </a>
            @endforeach
        </div>
</section>

<!-- SPLIT BANNERS 1 (Flush) -->
@if(isset($splitBanners) && $splitBanners->count() >= 2)
<section style="display: flex; flex-wrap: wrap; width: 100%;">
    <!-- Banner Left -->
    <div onclick="window.location.href='{{ $splitBanners[0]->link ?? route('produk.index') }}'" style="position: relative; flex: 1 1 50%; min-width: 300px; aspect-ratio: 3 / 4; min-height: 95vh; overflow: hidden; cursor: pointer;" onmouseover="this.querySelector('img').style.transform='scale(1.05)'" onmouseout="this.querySelector('img').style.transform='scale(1)'">
        <img src="{{ $splitBanners[0]->image_url }}" alt="{{ $splitBanners[0]->title }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.7s ease;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.5) 45%, rgba(0, 0, 0, 0.15) 75%, transparent 100%); pointer-events: none;"></div>
        <div style="position: absolute; bottom: 45px; left: 0; width: 100%; text-align: center; display: flex; flex-direction: column; align-items: center; padding: 0 20px;">
            <h3 style="color: #fff; font-size: 22px; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 24px; font-family: Arial, sans-serif;">{{ $splitBanners[0]->title }}</h3>
            <button style="background-color: #fff; color: #000; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; padding: 14px 40px; border: none; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#000'; this.style.color='#fff';" onmouseout="this.style.backgroundColor='#fff'; this.style.color='#000';">{{ $splitBanners[0]->button_text }}</button>
        </div>
    </div>
    
    <!-- Banner Right -->
    <div onclick="window.location.href='{{ $splitBanners[1]->link ?? route('produk.index') }}'" style="position: relative; flex: 1 1 50%; min-width: 300px; aspect-ratio: 3 / 4; min-height: 95vh; overflow: hidden; cursor: pointer;" onmouseover="this.querySelector('img').style.transform='scale(1.05)'" onmouseout="this.querySelector('img').style.transform='scale(1)'">
        <img src="{{ $splitBanners[1]->image_url }}" alt="{{ $splitBanners[1]->title }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.7s ease;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.5) 45%, rgba(0, 0, 0, 0.15) 75%, transparent 100%); pointer-events: none;"></div>
        <div style="position: absolute; bottom: 45px; left: 0; width: 100%; text-align: center; display: flex; flex-direction: column; align-items: center; padding: 0 20px;">
            <h3 style="color: #fff; font-size: 22px; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 24px; font-family: Arial, sans-serif;">{{ $splitBanners[1]->title }}</h3>
            <button style="background-color: #fff; color: #000; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; padding: 14px 40px; border: none; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#000'; this.style.color='#fff';" onmouseout="this.style.backgroundColor='#fff'; this.style.color='#000';">{{ $splitBanners[1]->button_text }}</button>
        </div>
    </div>
</section>
@endif

<!-- PRODUCT CAROUSEL 2 (BEST SELLERS / DISCOUNT) -->
<section class="py-16 md:py-24 max-w-[1440px] mx-auto relative px-4 md:px-12">
    <!-- Left/Right controls -->
    <button id="scroll-left-2" class="absolute bg-white rounded-full flex items-center justify-center text-black shadow-md z-10 cursor-pointer hidden md:flex hover:bg-gray-50" style="width: 40px; height: 40px; top: 40%; left: 0px; transform: translateY(-50%); border: 1px solid #eee;">
        <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
    </button>
    <button id="scroll-right-2" class="absolute bg-white rounded-full flex items-center justify-center text-black shadow-md z-10 cursor-pointer hidden md:flex hover:bg-gray-50" style="width: 40px; height: 40px; top: 40%; right: 0px; transform: translateY(-50%); border: 1px solid #eee;">
        <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
    </button>
    
    <!-- Product List -->
    <div id="product-carousel-2" class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth pb-4" style="gap: 24px; -ms-overflow-style: none; scrollbar-width: none;">
        <style>
            #product-carousel-2::-webkit-scrollbar { display: none; }
        </style>
        @foreach($shopProductsPaginated as $product)
        @php
            $detailUrl2 = $product->detail_url;
        @endphp

        <div class="product-item cursor-pointer flex-none snap-start flex flex-col relative" style="width: calc(25% - 18px); min-width: 280px;" onclick="window.location.href='{{ $detailUrl2 }}'">
            <div class="relative w-full bg-gray-200 overflow-hidden mb-4" style="aspect-ratio: 3 / 4;">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                
                @if($product->hasActiveDiscount())
                <div class="absolute text-white font-bold uppercase tracking-wider z-10" style="top: 12px; left: 12px; font-size: 10px; padding: 4px 8px; background-color: #e53e3e; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    Diskon {{ $product->formatted_discount_percent }}
                </div>
                @endif
                
                @if(!$product->inStock())
                <div class="absolute text-white font-bold uppercase tracking-wider" style="top: 0; left: 0; font-size: 10px; padding: 4px 12px; background-color: #555;">
                    Sold Out
                </div>
                @endif
                
                <button class="absolute text-white hover:text-gray-200 transition-colors z-10" style="top: 12px; right: 12px;" onclick="event.preventDefault(); event.stopPropagation(); addToWishlist('{{ $product->slug }}', event, this)" data-in-wishlist="{{ in_array($product->id, $userWishlistIds ?? []) ? 'true' : 'false' }}">
                    <i class="fas fa-heart {{ in_array($product->id, $userWishlistIds ?? []) ? 'text-rose-500' : '' }}" style="font-size: 16px; filter: drop-shadow(0px 1px 2px rgba(0,0,0,0.5));"></i>
                </button>
                
                <!-- Sizes on Hover -->
                <div class="size-popup absolute flex justify-between bg-white shadow-sm" style="bottom: 16px; left: 16px; right: 16px; opacity: 0; transition: opacity 0.3s ease;">
                    @php
                        $sizes2 = collect();
                        if ($product->has_variants) {
                            foreach($product->variants as $variant) {
                                $parts = explode('|', $variant->name);
                                if(count($parts) >= 1) $sizes2->push($parts[0]);
                            }
                        }
                        $uniqueSizes2 = $sizes2->unique();
                    @endphp
                    @forelse($uniqueSizes2->take(4) as $size)
                        @php
                            $displaySize2 = trim($size);
                            if (preg_match('/(\d+\s*(?:ml|gr|g|oz))/i', $displaySize2, $m2)) {
                                $displaySize2 = strtolower(str_replace(' ', '', $m2[1]));
                            } else {
                                $displaySize2 = preg_replace('/^(Travel|Regular|Jumbo|Mini|Full)\s*/i', '', $displaySize2);
                            }
                        @endphp
                        <div class="flex-1 text-center py-2 text-xs font-semibold hover:bg-gray-100" style="border-right: 1px solid #f3f4f6;" onclick="event.stopPropagation(); window.location.href='{{ $detailUrl2 }}?size={{ urlencode(trim($size)) }}'">{{ $displaySize2 }}</div>
                    @empty
                        <div class="flex-1 text-center py-2 text-xs font-semibold hover:bg-gray-100">All Size</div>
                    @endforelse
                </div>
            </div>
            
            <div class="flex-1 flex flex-col justify-start text-left px-1">
                <h3 class="text-gray-800 leading-snug mb-1 line-clamp-1" style="font-size: 13px; font-family: Arial, sans-serif;">{{ $product->name }}</h3>
                <p class="text-gray-800 mb-3" style="font-size: 13px; font-family: Arial, sans-serif;">{{ $product->formatted_price }}</p>
                <div class="flex mt-auto" style="gap: 8px;">
                    @php
                        $colors2 = collect();
                        if ($product->has_variants) {
                            foreach($product->variants as $variant) {
                                $parts = explode('|', $variant->name);
                                if (count($parts) >= 3) {
                                    $colors2->push(trim($parts[2]));
                                }
                            }
                        }
                    @endphp
                    @foreach($colors2->unique() as $color)
                        <div class="rounded-full cursor-pointer" style="width: 16px; height: 16px; background-color: {{ $color }}; border: 2px solid white; box-shadow: 0 0 0 1px #d1d5db;" onclick="event.stopPropagation(); window.location.href='{{ $detailUrl2 }}?colorHex={{ urlencode(trim($color)) }}'"></div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- TESTIMONIAL MARQUEE SECTION -->
<style>
    @keyframes scrollTestimonial {
        0% { transform: translateX(0); }
        100% { transform: translateX(calc(-50% - 1rem)); }
    }
    .testimonial-marquee-wrapper {
        overflow: hidden;
        width: 100%;
        position: relative;
        padding: 2rem 0;
    }
    .testimonial-marquee-wrapper::before,
    .testimonial-marquee-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 150px;
        z-index: 2;
        pointer-events: none;
    }
    .testimonial-marquee-wrapper::before {
        left: 0;
        background: linear-gradient(to right, white, transparent);
    }
    .testimonial-marquee-wrapper::after {
        right: 0;
        background: linear-gradient(to left, white, transparent);
    }
    .testimonial-track {
        display: flex;
        gap: 2rem;
        width: max-content;
        animation: scrollTestimonial 40s linear infinite;
    }
    .testimonial-track:hover {
        animation-play-state: paused;
    }
    .testimonial-card {
        width: 350px;
        background: #fff;
        padding: 2rem;
        border: 1px solid #f3f4f6;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        white-space: normal;
        flex-shrink: 0;
    }
    .testimonial-stars {
        color: #111;
        font-size: 14px;
        margin-bottom: 1rem;
    }
    .testimonial-text {
        font-family: 'Inter', sans-serif;
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .testimonial-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #111;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-family: 'Inter', sans-serif;
    }
    .testimonial-name {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        color: #111;
        font-size: 0.9rem;
    }
    .testimonial-role {
        font-family: 'Inter', sans-serif;
        color: #9ca3af;
        font-size: 0.8rem;
    }
</style>
<section class="py-16 md:py-24 bg-white overflow-hidden">
    <div class="text-center mb-12 px-4">
        <h2 class="font-bold text-black" style="font-size: clamp(2rem, 3.5vw, 2.5rem); line-height: 1.1; font-family: 'Inter', sans-serif; letter-spacing: -0.02em;">
            Apa Kata Mereka
        </h2>
        <p class="text-gray-500 mt-4" style="font-family: 'Inter', sans-serif;">Ulasan jujur dari pelanggan setia mengenai hasil nyata perawatan kulit bersama LUMINA Skincare.</p>
    </div>
    
    <div class="testimonial-marquee-wrapper">
        <div class="testimonial-track">
            <!-- Testimonial Items (Original Set) -->
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Centella Calming Serum-nya penyelamat saat kulit lagi iritasi dan kemerahan parah! Dalam 3 hari pemakaian kemerahan langsung reda."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">A</div>
                    <div>
                        <div class="testimonial-name">Aisyah R.</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Ceramide Barrier Moisture Gel teksturnya seringan air dan cepat meresap. Bikin kulit kenyal dan lembap seharian tanpa minyak berlebih."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">F</div>
                    <div>
                        <div class="testimonial-name">Fatima Z.</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Sunscreen SPF 50+ ini juara banget! No whitecast, nggak bikin kusam atau pedih di mata. Sangat nyaman untuk re-apply setiap siang."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">N</div>
                    <div>
                        <div class="testimonial-name">Nisa Kamila</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★☆</div>
                <p class="testimonial-text">"Serum Niacinamide 10%-nya benar-benar ampuh memudarkan bekas jerawat PIE. Kulit wajah kelihatan jauh lebih cerah dan glowing natural."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">D</div>
                    <div>
                        <div class="testimonial-name">Dina S.</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Paket 4-Step Routine sangat praktis dan hemat. Skin barrier yang tadinya reaktif sekarang jauh lebih sehat dan tahan banting."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">R</div>
                    <div>
                        <div class="testimonial-name">Rina M.</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>

            <!-- Duplicate Set for Infinite Scroll -->
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Centella Calming Serum-nya penyelamat saat kulit lagi iritasi dan kemerahan parah! Dalam 3 hari pemakaian kemerahan langsung reda."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">A</div>
                    <div>
                        <div class="testimonial-name">Aisyah R.</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Ceramide Barrier Moisture Gel teksturnya seringan air dan cepat meresap. Bikin kulit kenyal dan lembap seharian tanpa minyak berlebih."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">F</div>
                    <div>
                        <div class="testimonial-name">Fatima Z.</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Sunscreen SPF 50+ ini juara banget! No whitecast, nggak bikin kusam atau pedih di mata. Sangat nyaman untuk re-apply setiap siang."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">N</div>
                    <div>
                        <div class="testimonial-name">Nisa Kamila</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★☆</div>
                <p class="testimonial-text">"Serum Niacinamide 10%-nya benar-benar ampuh memudarkan bekas jerawat PIE. Kulit wajah kelihatan jauh lebih cerah dan glowing natural."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">D</div>
                    <div>
                        <div class="testimonial-name">Dina S.</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Paket 4-Step Routine sangat praktis dan hemat. Skin barrier yang tadinya reaktif sekarang jauh lebih sehat dan tahan banting."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">R</div>
                    <div>
                        <div class="testimonial-name">Rina M.</div>
                        <div class="testimonial-role">Verified Buyer</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="mx-auto text-center py-16 px-6 sm:py-24" style="max-width: 950px;">
    <h2 class="font-bold tracking-tight text-black" style="font-size: clamp(2rem, 4vw, 3rem); line-height: 1.2; margin-bottom: 1rem; font-family: 'Inter', sans-serif;">
        Waktunya Menyayangi Kulitmu & Tampil <br class="hidden sm:block">
        <span style="color: #2D4C41; font-style: italic;">Glowing Alami</span>
    </h2>
    <p class="text-gray-500 leading-relaxed mx-auto" style="max-width: 800px; font-size: 1rem; margin-bottom: 2.5rem; font-family: 'Inter', sans-serif;">
        Temukan rangkaian skincare premium dengan formulasi dermatologis teruji klinis dan bersertifikasi BPOM. Dapatkan jaminan 100% original serta konsultasi gratis bersama Beauty Advisor kami.
    </p>
    <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
        <a href="{{ route('produk.index') }}" class="inline-flex items-center justify-center rounded-full text-white font-medium transition-all" style="background-color: #18181b; padding: 14px 32px; font-size: 0.95rem; min-width: 180px; font-family: 'Inter', sans-serif; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
            Belanja Sekarang <span style="margin-left: 8px;">&rarr;</span>
        </a>
        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full text-black font-medium transition-all" style="background-color: #fff; padding: 14px 32px; font-size: 0.95rem; border: 1px solid #e4e4e7; min-width: 180px; font-family: 'Inter', sans-serif;" onmouseover="this.style.backgroundColor='#f4f4f5';" onmouseout="this.style.backgroundColor='#fff';">
            Masuk Akun <span style="margin-left: 8px; color: #71717a;">&rarr;</span>
        </a>
    </div>
</section>

@endsection
