@extends('layouts.app')

@section('title', $newarrivals['page_new_arrivals']['meta_title'][$lang] ?? 'New Arrivals - LUMINA Skincare')
@section('og_description', $newarrivals['page_new_arrivals']['meta_description'][$lang] ?? 'Produk skincare terbaru di LUMINA Skincare. Dapatkan koleksi serum, toner, moisturizer, dan sunscreen terkini sebelum kehabisan.')

@push('og_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "CollectionPage",
  "url": "{{ url()->current() }}",
  "name": "{{ $lang === 'id' ? 'Produk Terbaru — LUMINA Skincare' : 'New Arrivals — LUMINA Skincare' }}",
  "description": "{{ $lang === 'id' ? 'Temukan rangkaian produk perawatan kulit premium terbaru kami.' : 'Discover our newest premium skincare collection.' }}",
  "inLanguage": "{{ $lang === 'id' ? 'id-ID' : 'en-US' }}",
  "isPartOf": { "@id": "{{ url('/') }}/#website" },
  "publisher": { "@id": "{{ url('/') }}/#organization" }
}
</script>
@endpush

@section('content')
@php
    $newarrivals = json_decode(@file_get_contents(public_path('translation/newarrivals.json')), true) ?? [];
@endphp
<style>
    html, body { overflow-x: hidden; }

    /* Mobile Filter Overlay */
    #mobileFilterOverlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        height: 100dvh !important;
        z-index: 99999 !important;
        isolation: isolate !important;
        transform: translateZ(0) !important;
        overscroll-behavior: contain !important;
    }
    #mobileFilterPanel {
        z-index: 100000 !important;
        position: absolute !important;
        isolation: isolate !important;
    }
    body.filter-open {
        overflow: hidden !important;
        touch-action: none !important;
        position: fixed !important;
        width: 100% !important;
        height: 100% !important;
        left: 0 !important;
        right: 0 !important;
    }
</style>
<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')
    <main class="bg-white pt-0 md:!pt-20 lg:!pt-32">
        <!-- Page Title -->
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 md:px-10 lg:px-12 py-4 lg:hidden">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">{{ $newarrivals['page_new_arrivals']['badge'][$lang] ?? 'New Arrivals' }}</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-black sm:text-3xl">{{ $newarrivals['page_new_arrivals']['title'][$lang] ?? 'Latest Products' }}</h1>
            <p class="mt-2 text-zinc-600 text-sm">{{ $newarrivals['page_new_arrivals']['subtitle'][$lang] ?? 'Discover our newest premium skincare equipment' }}</p>
        </div>

        <!-- Main Layout: Sidebar + Grid -->
        <div class="mx-auto max-w-7xl px-6 pb-16 md:px-10 lg:px-12">
            <div class="flex flex-col gap-8 md:flex-row">
                
                <!-- Sidebar Filters -->
                <aside class="hidden lg:block w-[240px] flex-shrink-0">
                    <div class="space-y-6">
                        <!-- Header / Reset Filter -->
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                            <h2 class="text-base font-bold text-black">Filters</h2>
                            <button type="button" onclick="resetMobileFilter()" class="text-xs font-semibold text-rose-500 hover:text-rose-600 transition">Reset All</button>
                        </div>

                        <!-- Search -->
                        <div class="pt-2">
                            <h3 class="mb-3 text-sm font-semibold text-black">{{ $newarrivals['page_new_arrivals']['search_label'][$lang] ?? 'Search' }}</h3>
                            <input type="text" id="searchProduct" placeholder="{{ $newarrivals['page_new_arrivals']['search_placeholder'][$lang] ?? 'Search products...' }}" class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm outline-none focus:border-black transition">
                        </div>

                        <!-- Category -->
                        <div class="border-t border-zinc-100 pt-6">
                            <button type="button" class="filter-toggle flex w-full items-center justify-between text-left">
                                <h3 class="text-sm font-semibold text-black">Category</h3>
                                <i class="fas fa-chevron-down text-xs text-zinc-400 transition-transform duration-200"></i>
                            </button>
                            <div class="filter-content mt-3 flex flex-col gap-2">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterCategory" class="border-zinc-300 text-black focus:ring-black" value="" onchange="applyCategory()">
                                    <span class="text-sm text-zinc-600">Semua Kategori</span>
                                </label>
                                @foreach(['serum' => 'Serum', 'moisturizer' => 'Moisturizer', 'cleanser' => 'Cleanser', 'toner' => 'Toner', 'sunscreen' => 'Sunscreen'] as $val => $label)
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterCategory" class="border-zinc-300 text-black focus:ring-black" value="{{ $val }}" {{ request()->get('category') === $val ? 'checked' : '' }} onchange="applyCategory()">
                                    <span class="text-sm text-zinc-600">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="border-t border-zinc-100 pt-6">
                            <button type="button" class="filter-toggle flex w-full items-center justify-between text-left">
                                <h3 class="text-sm font-semibold text-black">{{ $newarrivals['product_section']['filter_titles']['price'][$lang] ?? 'Price' }}</h3>
                                <i class="fas fa-chevron-down text-xs text-zinc-400 transition-transform duration-200"></i>
                            </button>
                            <div class="filter-content mt-3 flex flex-col gap-2">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterPrice" class="border-zinc-300 text-black focus:ring-black" value="" onchange="applyPrice()">
                                    <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['price_options']['all'][$lang] ?? 'All Prices' }}</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterPrice" class="border-zinc-300 text-black focus:ring-black" value="low" {{ request()->get('price') === 'low' ? 'checked' : '' }} onchange="applyPrice()">
                                    <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['price_options']['low_to_high'][$lang] ?? 'Low to High' }}</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterPrice" class="border-zinc-300 text-black focus:ring-black" value="high" {{ request()->get('price') === 'high' ? 'checked' : '' }} onchange="applyPrice()">
                                    <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['price_options']['high_to_low'][$lang] ?? 'High to Low' }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="border-t border-zinc-100 pt-6">
                            <button type="button" class="filter-toggle flex w-full items-center justify-between text-left">
                                <h3 class="text-sm font-semibold text-black">{{ $newarrivals['product_section']['filter_titles']['sort'][$lang] ?? 'Sort' }}</h3>
                                <i class="fas fa-chevron-down text-xs text-zinc-400 transition-transform duration-200"></i>
                            </button>
                            <div class="filter-content mt-3 flex flex-col gap-2">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterSort" class="border-zinc-300 text-black focus:ring-black" value="" onchange="applyFilters()">
                                    <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['sort_options']['default'][$lang] ?? 'Default' }}</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterSort" class="border-zinc-300 text-black focus:ring-black" value="popular" {{ request()->get('sort') === 'popular' ? 'checked' : '' }} onchange="applyFilters()">
                                    <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['sort_options']['popular'][$lang] ?? 'Popular' }}</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterSort" class="border-zinc-300 text-black focus:ring-black" value="latest" {{ request()->get('sort') === 'latest' ? 'checked' : '' }} onchange="applyFilters()">
                                    <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['sort_options']['latest'][$lang] ?? 'Latest' }}</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterSort" class="border-zinc-300 text-black focus:ring-black" value="year_desc" {{ request()->get('sort') === 'year_desc' ? 'checked' : '' }} onchange="applySort()">
                                    <span class="text-sm text-zinc-600">Year (Newest)</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterSort" class="border-zinc-300 text-black focus:ring-black" value="year_asc" {{ request()->get('sort') === 'year_asc' ? 'checked' : '' }} onchange="applySort()">
                                    <span class="text-sm text-zinc-600">Year (Oldest)</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterSort" class="border-zinc-300 text-black focus:ring-black" value="rating" {{ request()->get('sort') === 'rating' ? 'checked' : '' }} onchange="applySort()">
                                    <span class="text-sm text-zinc-600">LUMINA Rating</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Product Grid Area -->
                <div class="flex-1">
                    <!-- Mobile Search + Filter -->
                    <div class="lg:hidden mb-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                                <input type="text" id="mobileSearchProduct" placeholder="{{ $newarrivals['page_new_arrivals']['search_placeholder'][$lang] ?? 'Search products...' }}" value="{{ request()->get('q', '') }}" class="w-full rounded-lg border border-zinc-200 pl-8 pr-3 py-2 text-sm outline-none focus:border-black transition" onkeypress="if(event.key==='Enter') applyMobileSearch()">
                            </div>
                            <button type="button" onclick="openMobileFilter()" class="relative flex items-center justify-center gap-1.5 px-3 py-2 border border-zinc-200 rounded-lg text-xs font-medium text-zinc-700 active:bg-zinc-100 transition shrink-0">
                                <i class="fas fa-filter text-[11px]"></i>
                                <span>Filter</span>
                                <span id="mobileFilterCount" class="hidden bg-black text-white text-[8px] px-1.5 py-0 rounded-full font-bold">0</span>
                            </button>
                        </div>
                    </div>

                    <!-- Grid -->
                    <div id="productGrid" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4">
                        @forelse($products as $product)
                            <x-product-card-luxury :product="$product" :userWishlistIds="$userWishlistIds ?? []" />
                        @empty
                            <div class="col-span-full rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-12 text-center">
                                <i class="fas fa-box-open text-3xl text-zinc-400"></i>
                                <p class="mt-3 font-medium text-zinc-500">{{ $newarrivals['page_new_arrivals']['empty_state'][$lang] ?? 'No new arrivals yet' }}</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="mt-10 flex justify-center" id="paginationContainer">
                            {{ $products->onEachSide(1)->appends(request()->query())->links('pagination.luxury') }}
                        </div>
                    @endif

                    <div id="noResults" class="hidden text-center py-12">
                        <i class="fas fa-search text-4xl text-zinc-300 mb-3"></i>
                        <p class="text-zinc-500">{{ $newarrivals['common']['no_products_found'][$lang] ?? 'No products found' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Full-Screen Filter Overlay -->
        <div id="mobileFilterOverlay" class="lg:hidden hidden">
            <div class="absolute inset-0 bg-black/60 transition-opacity" onclick="closeMobileFilter()"></div>
            <div class="absolute inset-x-0 bottom-0 top-0 bg-white flex flex-col min-h-0 transform transition-transform duration-300 translate-y-full" id="mobileFilterPanel">
                <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100 bg-white shrink-0">
                    <h2 class="text-base font-semibold text-black">{{ $newarrivals['product_section']['filter_titles']['category'][$lang] ?? 'Filter' }}</h2>
                    <button type="button" onclick="closeMobileFilter()" class="w-10 h-10 flex items-center justify-center bg-zinc-100 rounded-full text-zinc-600 hover:bg-zinc-200 hover:text-black active:bg-zinc-300 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-4 py-4 pb-4 space-y-6 min-h-0">
                    <!-- Category -->
                    <div>
                        <h3 class="text-sm font-semibold text-black mb-3">Category</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobileCategory" value="" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" onchange="updateMobileFilterCount()" {{ !request()->get('category') ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">Semua Kategori</span>
                            </label>
                            @foreach(['serum' => 'Serum', 'moisturizer' => 'Moisturizer', 'cleanser' => 'Cleanser', 'toner' => 'Toner', 'sunscreen' => 'Sunscreen'] as $val => $label)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobileCategory" value="{{ $val }}" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" onchange="updateMobileFilterCount()" {{ request()->get('category') === $val ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="border-t border-zinc-100 pt-5">
                        <h3 class="text-sm font-semibold text-black mb-3">{{ $newarrivals['product_section']['filter_titles']['price'][$lang] ?? 'Price' }}</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobilePrice" value="" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" {{ !request()->get('price') ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['price_options']['all'][$lang] ?? 'All Prices' }}</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobilePrice" value="low" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" {{ request()->get('price') === 'low' ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['price_options']['low_to_high'][$lang] ?? 'Low to High' }}</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobilePrice" value="high" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" {{ request()->get('price') === 'high' ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['price_options']['high_to_low'][$lang] ?? 'High to Low' }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="border-t border-zinc-100 pt-5">
                        <h3 class="text-sm font-semibold text-black mb-3">{{ $newarrivals['product_section']['filter_titles']['sort'][$lang] ?? 'Sort' }}</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobileSort" value="" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" {{ !request()->get('sort') ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['sort_options']['default'][$lang] ?? 'Default' }}</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobileSort" value="popular" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" {{ request()->get('sort') === 'popular' ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['sort_options']['popular'][$lang] ?? 'Popular' }}</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobileSort" value="latest" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" {{ request()->get('sort') === 'latest' ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">{{ $newarrivals['product_section']['sort_options']['latest'][$lang] ?? 'Latest' }}</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobileSort" value="year_desc" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" {{ request()->get('sort') === 'year_desc' ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">Year (Newest)</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobileSort" value="year_asc" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" {{ request()->get('sort') === 'year_asc' ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">Year (Oldest)</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="mobileSort" value="rating" class="w-4 h-4 text-black border-zinc-300 focus:ring-black" {{ request()->get('sort') === 'rating' ? 'checked' : '' }}>
                                <span class="text-sm text-zinc-600">LUMINA Rating</span>
                            </label>
                        </div>
                    </div>
                    <div class="h-32 shrink-0"></div>
                </div>

                <div class="absolute bottom-0 inset-x-0 bg-white border-t border-zinc-100 px-4 py-3 flex items-center gap-3">
                    <button type="button" onclick="resetMobileFilter()" class="flex-1 py-2.5 border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 active:bg-zinc-100 transition">Reset</button>
                    <button type="button" onclick="applyMobileFilter()" class="flex-1 py-2.5 bg-black rounded-lg text-sm font-medium text-white active:bg-zinc-800 transition">Apply</button>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('styles')
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .animate-marquee {
            animation: marquee 20s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .np-fade-in {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .np-fade-in.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .filter-chip.active {
            background-color: #000;
            color: #fff;
            border-color: #000;
        }
        body.filter-open {
            overflow: hidden !important;
            position: fixed;
            width: 100%;
        }

        @keyframes toastIn { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes toastOut { to { opacity: 0; transform: translateX(40px); } }
        @keyframes badgePop {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .badge-pop { animation: badgePop 0.4s cubic-bezier(.34,1.56,.64,1); }
    </style>
@endpush

@push('scripts')
    <script>
        function addToCart(productId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        @guest
        window.location.href = '/login';
        return;
        @endguest


            const button = event.target.closest('button');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch(`/customer/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const currentCount = parseInt(data.cart_count || 0);
                    updateCartBadge(currentCount);
                    showToast('Masuk keranjang', 'success');

                    if (button) {
                        button.innerHTML = '✓ Added';
                        setTimeout(() => {
                            button.disabled = false;
                            button.textContent = "{{ $newarrivals['common']['cart_btn_loading_added'][$lang] ?? 'Add to Cart' }}";
                        }, 1500);
                    }
                } else {
                    showToast(data.message || "{{ $newarrivals['common']['cart_error'][$lang] ?? 'Failed to add product to cart' }}", 'error');
                    if (button) {
                        button.disabled = false;
                        button.textContent = "{{ $newarrivals['common']['cart_btn_loading_added'][$lang] ?? 'Add to Cart' }}";
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast("{{ $newarrivals['common']['cart_error_generic'][$lang] ?? 'Something went wrong. Please try again.' }}", 'error');
                if (button) {
                    button.disabled = false;
                    button.textContent = "{{ $newarrivals['common']['cart_btn_loading_added'][$lang] ?? 'Add to Cart' }}";
                }
            });
        }

        function animateBadge(badgeElement) {
            if (!badgeElement) return;
            badgeElement.classList.remove('badge-pop');
            void badgeElement.offsetWidth;
            badgeElement.classList.add('badge-pop');
            setTimeout(() => badgeElement.classList.remove('badge-pop'), 500);
        }

        function updateCartBadge(newCount) {
            document.querySelectorAll('.cart-badge').forEach(badge => {
                if (newCount > 0) {
                    badge.textContent = newCount > 9 ? '9+' : newCount;
                    badge.classList.remove('hidden');
                    animateBadge(badge);
                } else {
                    badge.classList.add('hidden');
                }
            });
        }

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

        function updateWishlistBadge(count) {
            document.querySelectorAll('.wishlist-badge, .hamburger-wishlist-badge').forEach(b => {
                if (count > 0) {
                    b.textContent = count > 9 ? '9+' : count;
                    b.classList.remove('hidden');
                    animateBadge(b);
                } else {
                    b.classList.add('hidden');
                }
            });
        }

        function addToWishlist(productId, event, btn) {
            event.preventDefault();
            event.stopPropagation();

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
                        updateWishlistBadge(parseInt(data.wishlist_count || 0));
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
                    console.error('Error:', err);
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
                        updateWishlistBadge(parseInt(data.wishlist_count || 0));
                        button.setAttribute('data-in-wishlist', 'true');
                        button.classList.add('text-rose-500');
                        if (icon) { icon.classList.remove('fa-spinner','fa-spin'); icon.classList.add('fa-heart','text-rose-500'); }
                        showToast('Favorit masuk', 'success');
                    } else if (data.message && data.message.includes('sudah ada')) {
                        button.setAttribute('data-in-wishlist', 'true');
                        button.classList.add('text-rose-500');
                        if (icon) { icon.classList.remove('fa-spinner','fa-spin'); icon.classList.add('fa-heart','text-rose-500'); }
                        showToast('Produk sudah ada di wishlist', 'success');
                    } else {
                        showToast(data.message || 'Gagal menambahkan produk ke wishlist', 'error');
                        if (icon) { icon.classList.remove('fa-spinner','fa-spin'); icon.classList.add('fa-heart'); }
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                    if (icon) { icon.classList.remove('fa-spinner','fa-spin'); icon.classList.add('fa-heart'); }
                });
            }
        }



                window._activeFilters = { 
            category: {!! json_encode(request()->get('category')) !!}
        };

        window.applyCategory = function() {
            const val = document.querySelector('input[name="filterCategory"]:checked')?.value || null;
            window._activeFilters['category'] = val;
            fetchFiltered();
        };

        window.applyPrice = function() { fetchFiltered(); };
        window.applySort = function() { fetchFiltered(); };
        window.applyFilters = function() { fetchFiltered(); };

        function fetchFiltered() {
            const params = new URLSearchParams();
            if (window._activeFilters.category) params.append('category', window._activeFilters.category);
            
            const price = document.querySelector('input[name="filterPrice"]:checked')?.value || '';
            const sort = document.querySelector('input[name="filterSort"]:checked')?.value || '';
            const search = document.getElementById('searchProduct')?.value || document.getElementById('mobileSearchProduct')?.value || '';

            if (price) params.append('price', price);
            if (sort) params.append('sort', sort);
            if (search) params.append('q', search);

            const productGrid = document.getElementById('productGrid');
            const paginationContainer = document.getElementById('paginationContainer');
            if (paginationContainer) paginationContainer.classList.add('hidden');
            if (productGrid) productGrid.innerHTML = '<div class="flex items-center justify-center w-full py-12 col-span-full"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';

            fetch(`/api/new-arrivals/filter?${params.toString()}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.html) {
                        if (productGrid) productGrid.innerHTML = data.html;
                    } else {
                        const noMsg = '<div class="col-span-full text-center py-12"><i class="fas fa-search text-4xl text-zinc-300 mb-3"></i><p class="text-zinc-500 mt-3">Tidak ada produk yang ditemukan</p></div>';
                        if (productGrid) productGrid.innerHTML = noMsg;
                    }
                })
                .catch(err => console.error(err));
        }

        // Mobile Filter Overlay
        let _scrollY = 0;

        window.openMobileFilter = function() {
            const overlay = document.getElementById('mobileFilterOverlay');
            const panel = document.getElementById('mobileFilterPanel');
            if (overlay && panel) {
                if (!overlay._originalParent) {
                    overlay._originalParent = overlay.parentNode;
                }
                if (overlay.parentNode !== document.body) {
                    document.body.appendChild(overlay);
                }
                _scrollY = window.scrollY || window.pageYOffset;
                document.body.classList.add('filter-open');
                document.body.style.top = `-${_scrollY}px`;
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    panel.classList.remove('translate-y-full');
                    panel.classList.add('translate-y-0');
                }, 10);
            }
        };

        window.closeMobileFilter = function() {
            const overlay = document.getElementById('mobileFilterOverlay');
            const panel = document.getElementById('mobileFilterPanel');
            if (overlay && panel) {
                panel.classList.remove('translate-y-0');
                panel.classList.add('translate-y-full');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    if (overlay._originalParent && overlay.parentNode !== overlay._originalParent) {
                        overlay._originalParent.appendChild(overlay);
                    }
                    document.body.classList.remove('filter-open');
                    document.body.style.top = '';
                    window.scrollTo(0, _scrollY);
                }, 300);
            }
        };

        window.updateMobileFilterCount = function() {
            const checked = document.querySelectorAll('#mobileFilterPanel input[type="checkbox"]:checked, #mobileFilterPanel input[type="radio"]:checked');
            // don't count the "all prices" or "default sort" if they have empty value
            let count = 0;
            checked.forEach(cb => { if(cb.value) count++; });
            const countEl = document.getElementById('mobileFilterCount');
            if (countEl) {
                countEl.textContent = count;
                countEl.classList.toggle('hidden', count === 0);
            }
        };

        window.applyMobileFilter = function() {
            const category = document.querySelector('input[name="mobileCategory"]:checked')?.value || '';
            const price = document.querySelector('input[name="mobilePrice"]:checked')?.value || '';
            const sort = document.querySelector('input[name="mobileSort"]:checked')?.value || '';
            const search = document.getElementById('mobileSearchProduct')?.value || '';

            // Set active filters
            window._activeFilters.category = category || null;

            // Update desktop radio inputs to match mobile
            const desktopPrice = document.querySelector(`input[name="filterPrice"][value="${price}"]`);
            if (desktopPrice) desktopPrice.checked = true;

            const desktopSort = document.querySelector(`input[name="filterSort"][value="${sort}"]`);
            if (desktopSort) desktopSort.checked = true;

            const desktopCategory = document.querySelector(`input[name="filterCategory"][value="${category}"]`);
            if (desktopCategory) desktopCategory.checked = true;

            const searchProduct = document.getElementById('searchProduct');
            if (searchProduct) searchProduct.value = search;

            closeMobileFilter();
            fetchFiltered();
        };

        window.resetMobileFilter = function() {
            const mobileSearchProduct = document.getElementById('mobileSearchProduct');
            if (mobileSearchProduct) mobileSearchProduct.value = '';
            
            // clear state
            window._activeFilters = { category: null };
            document.querySelectorAll('input[name="filterPrice"][value=""]').forEach(r => r.checked = true);
            document.querySelectorAll('input[name="filterSort"][value=""]').forEach(r => r.checked = true);
            document.querySelectorAll('input[name="filterCategory"][value=""]').forEach(r => r.checked = true);
            
            document.querySelectorAll('input[name="mobilePrice"][value=""]').forEach(r => r.checked = true);
            document.querySelectorAll('input[name="mobileSort"][value=""]').forEach(r => r.checked = true);
            document.querySelectorAll('input[name="mobileCategory"][value=""]').forEach(r => r.checked = true);

            if (document.getElementById('searchProduct')) document.getElementById('searchProduct').value = '';

            updateMobileFilterCount();

            fetchFiltered();
            closeMobileFilter();
        };

        window.applyMobileSearch = function() {
            fetchFiltered();
        };

        // Search input listeners
        document.getElementById('searchProduct')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                fetchFiltered();
            }
        });

        updateMobileFilterCount();
        
        // AJAX Pagination Interceptor
        document.addEventListener('click', function(e) {
            const link = e.target.closest('#paginationContainer a');
            if (link) {
                e.preventDefault();
                const productGrid = document.getElementById('productGrid');
                if (productGrid) {
                    productGrid.style.opacity = '0.5';
                }
                
                fetch(link.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newGrid = doc.getElementById('productGrid');
                    const newPagination = doc.getElementById('paginationContainer');
                    
                    if (newGrid && productGrid) {
                        productGrid.innerHTML = newGrid.innerHTML;
                        productGrid.style.opacity = '1';
                    }
                    
                    const currentPagination = document.getElementById('paginationContainer');
                    if (newPagination && currentPagination) {
                        currentPagination.innerHTML = newPagination.innerHTML;
                    }
                    
                    window.history.pushState({}, '', link.href);
                    
                    if (productGrid) productGrid.scrollIntoView({behavior: 'smooth', block: 'start'});
                })
                .catch(err => {
                    console.error('Pagination fetch error', err);
                    window.location.href = link.href; // fallback
                });
            }
        });
</script>
@endpush