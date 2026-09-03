@extends('layouts.app')

@section('title', 'Katalog Produk - LUMINA Skincare')
@section('og_description', 'Temukan koleksi lengkap perawatan kulit di LUMINA Skincare. Serum, moisturizer, cleanser, toner, sunscreen teruji BPOM.')

@section('content')
<style>
    html, body { overflow-x: hidden; }
</style>
<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')
    <main class="bg-white pt-0 md:pt-20 lg:pt-32">
        <!-- Page Title -->
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 md:px-10 lg:px-12 py-4 lg:hidden">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Products</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-black sm:text-3xl">Shop Our Collection</h1>
            <p class="mt-2 text-zinc-600 text-sm">Discover premium skincare formulations designed for healthy glowing skin</p>
        </div>

        <!-- Main Layout: Sidebar + Grid -->
        <div class="mx-auto max-w-7xl px-6 pb-16 md:px-10 lg:px-12">
            <div class="flex flex-col gap-8 md:flex-row">
                
                <!-- Sidebar Filters -->
                <aside class="hidden lg:block w-[240px] flex-shrink-0">
                    <div class="space-y-6" id="sidebarFilters">
                        <!-- Search -->
                        <div>
                            <h3 class="mb-3 text-sm font-semibold text-black">Search</h3>
                            <input type="text" id="searchProduct" placeholder="Search products..." class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm outline-none focus:border-black transition">
                        </div>

                        <!-- Category -->
                        <div class="border-t border-zinc-100 pt-6">
                            <button type="button" class="filter-toggle flex w-full items-center justify-between text-left">
                                <h3 class="text-sm font-semibold text-black">Category</h3>
                                <i class="fas fa-chevron-down text-xs text-zinc-400 transition-transform duration-200"></i>
                            </button>
                            <div class="filter-content mt-3 flex flex-wrap gap-2">
                                <a href="{{ request()->fullUrlWithQuery(['category' => 'serum']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('category') === 'serum' ? 'bg-black text-white border-black' : '' }}">Serum</a>
                                <a href="{{ request()->fullUrlWithQuery(['category' => 'moisturizer']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('category') === 'moisturizer' ? 'bg-black text-white border-black' : '' }}">Moisturizer</a>
                                <a href="{{ request()->fullUrlWithQuery(['category' => 'cleanser']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('category') === 'cleanser' ? 'bg-black text-white border-black' : '' }}">Cleanser</a>
                                <a href="{{ request()->fullUrlWithQuery(['category' => 'toner']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('category') === 'toner' ? 'bg-black text-white border-black' : '' }}">Toner</a>
                                <a href="{{ request()->fullUrlWithQuery(['category' => 'sunscreen']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('category') === 'sunscreen' ? 'bg-black text-white border-black' : '' }}">Sunscreen</a>
                                <a href="{{ request()->fullUrlWithQuery(['category' => 'bundle']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('category') === 'bundle' ? 'bg-black text-white border-black' : '' }}">Bundle</a>
                                @if(request()->get('category'))
                                    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-rose-600 transition hover:border-rose-600 hover:text-rose-600">Clear</a>
                                @endif
                            </div>
                        </div>

                        <!-- Brand -->
                        <div class="border-t border-zinc-100 pt-6">
                            <button type="button" class="filter-toggle flex w-full items-center justify-between text-left">
                                <h3 class="text-sm font-semibold text-black">Brand</h3>
                                <i class="fas fa-chevron-down text-xs text-zinc-400 transition-transform duration-200"></i>
                            </button>
                            <div class="filter-content mt-3 flex flex-wrap gap-2">
                                <a href="{{ request()->fullUrlWithQuery(['brand' => 'LUMINA']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('brand') === 'LUMINA' ? 'bg-black text-white border-black' : '' }}">LUMINA</a>
                                <a href="{{ request()->fullUrlWithQuery(['brand' => 'Avoskin']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('brand') === 'Avoskin' ? 'bg-black text-white border-black' : '' }}">Avoskin</a>
                                <a href="{{ request()->fullUrlWithQuery(['brand' => 'Somethinc']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('brand') === 'Somethinc' ? 'bg-black text-white border-black' : '' }}">Somethinc</a>
                                <a href="{{ request()->fullUrlWithQuery(['brand' => 'COSRX']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('brand') === 'COSRX' ? 'bg-black text-white border-black' : '' }}">COSRX</a>
                                <a href="{{ request()->fullUrlWithQuery(['brand' => 'Skintific']) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ request()->get('brand') === 'Skintific' ? 'bg-black text-white border-black' : '' }}">Skintific</a>
                                @if(request()->get('brand'))
                                    <a href="{{ request()->fullUrlWithQuery(['brand' => null]) }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-rose-600 transition hover:border-rose-600 hover:text-rose-600">Clear</a>
                                @endif
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="border-t border-zinc-100 pt-6">
                            <button type="button" class="filter-toggle flex w-full items-center justify-between text-left">
                                <h3 class="text-sm font-semibold text-black">Price</h3>
                                <i class="fas fa-chevron-down text-xs text-zinc-400 transition-transform duration-200"></i>
                            </button>
                            <div class="filter-content mt-3 flex flex-col gap-2">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterPrice" class="border-zinc-300 text-black focus:ring-black" value="" onchange="applyShopFilters()">
                                    <span class="text-sm text-zinc-600">All Prices</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterPrice" class="border-zinc-300 text-black focus:ring-black" value="low" {{ request()->get('price') === 'low' ? 'checked' : '' }} onchange="applyShopFilters()">
                                    <span class="text-sm text-zinc-600">Low to High</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterPrice" class="border-zinc-300 text-black focus:ring-black" value="high" {{ request()->get('price') === 'high' ? 'checked' : '' }} onchange="applyShopFilters()">
                                    <span class="text-sm text-zinc-600">High to Low</span>
                                </label>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="border-t border-zinc-100 pt-6">
                            <button type="button" class="filter-toggle flex w-full items-center justify-between text-left">
                                <h3 class="text-sm font-semibold text-black">Sort</h3>
                                <i class="fas fa-chevron-down text-xs text-zinc-400 transition-transform duration-200"></i>
                            </button>
                            <div class="filter-content mt-3 flex flex-col gap-2">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterSort" class="border-zinc-300 text-black focus:ring-black" value="" onchange="applyShopFilters()">
                                    <span class="text-sm text-zinc-600">Default</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterSort" class="border-zinc-300 text-black focus:ring-black" value="popular" {{ request()->get('sort') === 'popular' ? 'checked' : '' }} onchange="applyShopFilters()">
                                    <span class="text-sm text-zinc-600">Popular</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="filterSort" class="border-zinc-300 text-black focus:ring-black" value="latest" {{ request()->get('sort') === 'latest' ? 'checked' : '' }} onchange="applyShopFilters()">
                                    <span class="text-sm text-zinc-600">Latest</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Product Grid Area -->
                <div class="flex-1">
                    <!-- Mobile Filter Dropdown -->
                    <div class="lg:hidden mb-4">
                        <div class="mb-3">
                            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-700">Filter</label>
                        </div>
                        <div class="flex gap-2 overflow-x-auto pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                            <select id="filterBrand" class="px-2 py-1.5 border border-zinc-300 rounded-lg text-xs focus:outline-none focus:border-blue-500 transition bg-white shrink-0 min-w-[100px]" onchange="applyShopFilters()">
                                <option value="">Brand</option>
                                <option value="LUMINA" {{ request()->get('brand') === 'LUMINA' ? 'selected' : '' }}>LUMINA</option>
                                <option value="Avoskin" {{ request()->get('brand') === 'Avoskin' ? 'selected' : '' }}>Avoskin</option>
                                <option value="Somethinc" {{ request()->get('brand') === 'Somethinc' ? 'selected' : '' }}>Somethinc</option>
                                <option value="COSRX" {{ request()->get('brand') === 'COSRX' ? 'selected' : '' }}>COSRX</option>
                                <option value="Skintific" {{ request()->get('brand') === 'Skintific' ? 'selected' : '' }}>Skintific</option>
                            </select>
                            <select id="filterCategory" class="px-2 py-1.5 border border-zinc-300 rounded-lg text-xs focus:outline-none focus:border-blue-500 transition bg-white shrink-0 min-w-[100px]" onchange="applyShopFilters()">
                                <option value="">Category</option>
                                <option value="serum" {{ request()->get('category') === 'serum' ? 'selected' : '' }}>Serum</option>
                                <option value="moisturizer" {{ request()->get('category') === 'moisturizer' ? 'selected' : '' }}>Moisturizer</option>
                                <option value="cleanser" {{ request()->get('category') === 'cleanser' ? 'selected' : '' }}>Cleanser</option>
                                <option value="toner" {{ request()->get('category') === 'toner' ? 'selected' : '' }}>Toner</option>
                                <option value="sunscreen" {{ request()->get('category') === 'sunscreen' ? 'selected' : '' }}>Sunscreen</option>
                                <option value="bundle" {{ request()->get('category') === 'bundle' ? 'selected' : '' }}>Bundle</option>
                            </select>
                            <select id="filterPrice" class="px-2 py-1.5 border border-zinc-300 rounded-lg text-xs focus:outline-none focus:border-blue-500 transition bg-white shrink-0 min-w-[100px]" onchange="applyShopFilters()">
                                <option value="">Price</option>
                                <option value="low" {{ request()->get('price') === 'low' ? 'selected' : '' }}>Low to High</option>
                                <option value="high" {{ request()->get('price') === 'high' ? 'selected' : '' }}>High to Low</option>
                            </select>
                            <select id="filterSort" class="px-2 py-1.5 border border-zinc-300 rounded-lg text-xs focus:outline-none focus:border-blue-500 transition bg-white shrink-0 min-w-[100px]" onchange="applyShopFilters()">
                                <option value="">Sort</option>
                                <option value="popular" {{ request()->get('sort') === 'popular' ? 'selected' : '' }}>Popular</option>
                                <option value="latest" {{ request()->get('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                            </select>
                        </div>
                    </div>

                    <div id="productGridWrapper">
                    <!-- Grid -->
                    <div id="productGrid" class="grid grid-cols-3 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @if($products->count() > 0)
                        @foreach($products as $product)
                            <div class="product-item group block overflow-hidden bg-white transition duration-300 hover:-translate-y-1"
                               data-name="{{ strtolower($product->name) }}"
                               data-price="{{ $product->hasActiveDiscount() ? $product->discounted_price : $product->price }}"
                               data-brand="{{ strtolower($product->brand ?? '') }}"
                               data-category="{{ strtolower($product->category ?? '') }}"
                               data-stock="{{ $product->stock ?? 0 }}">
                                <a href="{{ $product->detail_url }}" class="block">
                                    <div class="relative aspect-square overflow-hidden">
                                        <div class="h-full w-full overflow-hidden">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" onerror="this.onerror=null; this.removeAttribute('srcset'); this.src=this.src;" loading="lazy">
                                        </div>
                                        @if($product->hasActiveDiscount())
                                            <span class="absolute left-0 top-0 bg-rose-500 px-2 py-0.5 text-[10px] font-semibold text-white pointer-events-none">-{{ $product->formatted_discount_percent }}</span>
                                        @endif
                                        @if($product->category === 'arrivals')
                                            <span class="absolute left-0 {{ $product->hasActiveDiscount() ? 'top-7' : 'top-0' }} bg-blue-500 px-2 py-0.5 text-[10px] font-semibold text-white pointer-events-none">Latest</span>
                                        @endif
                                        @if($product->package_type === 'bundle')
                                            <span class="absolute left-0 {{ $product->hasActiveDiscount() && $product->category === 'arrivals' ? 'top-14' : ($product->hasActiveDiscount() || $product->category === 'arrivals' ? 'top-7' : 'top-0') }} bg-purple-500 px-2 py-0.5 text-[10px] font-semibold text-white pointer-events-none">Bundle</span>
                                        @endif
                                        @if($product->isBestSeller())
                                            <span class="absolute right-0 top-0 bg-amber-500 px-2 py-0.5 text-[10px] font-semibold text-white pointer-events-none">Best Seller</span>
                                        @endif
                                    </div>
                                    <div class="p-3">
                                        <h3 class="line-clamp-1 text-sm font-medium text-black">{{ $product->name }}</h3>
                                        <p class="mt-1 text-xs text-zinc-600">{{ $product->category_label }}</p>
                                        @if($product->hasActiveDiscount())
                                            <p class="mt-1 text-base font-semibold text-black">{{ $product->formatted_discounted_price }}</p>
                                            <p class="text-xs text-zinc-400 line-through">{{ $product->formatted_price }}</p>
                                        @else
                                            <p class="mt-1 text-base font-semibold text-black">{{ $product->formatted_price }}</p>
                                        @endif
                                    </div>
                                </a>
                                <div class="px-2 pb-2">
                                    <div class="flex items-center gap-2">
                                        <button onclick="event.preventDefault(); event.stopPropagation(); addToCart('{{ $product->slug }}', event)" class="flex-1 border border-zinc-300 bg-transparent px-2 py-1 text-[10px] font-semibold text-zinc-800 transition duration-300 hover:border-zinc-500 hover:text-zinc-950 text-center">
                                            Add to cart
                                        </button>
                                        <button onclick="event.preventDefault(); event.stopPropagation(); addToWishlist('{{ $product->slug }}', event)" class="p-1 text-zinc-400 transition duration-300 hover:text-rose-500 shrink-0">
                                            <i class="fas fa-heart text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-12 text-center">
                            <i class="fas fa-box-open text-3xl text-zinc-400"></i>
                            <p class="mt-3 font-medium text-zinc-500">No products found</p>
                        </div>
                    @endif
                    </div>
    
                    <!-- Pagination with Numbers -->
                    @if($products->hasPages())
                        <div class="mt-10 flex justify-center">
                            <div class="flex items-center gap-2 overflow-x-auto pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                {{-- Previous --}}
                                @if($products->onFirstPage())
                                    <span class="px-2 py-1.5 text-xs text-zinc-400 cursor-not-allowed shrink-0">Previous</span>
                                @else
                                    <a href="{{ $products->previousPageUrl() }}" class="pagination-link px-2 py-1.5 text-xs text-zinc-700 hover:text-black border border-zinc-200 rounded hover:border-black transition shrink-0">Previous</a>
                                @endif
    
                                {{-- Page Numbers --}}
                                @php
                                    $startPage = max(1, $products->currentPage() - 2);
                                    $endPage = min($products->lastPage(), $products->currentPage() + 2);
                                    if ($startPage > 1) $startPage = 1;
                                    if ($endPage < $products->lastPage()) $endPage = $products->lastPage();
                                @endphp
    
                                @for($i = $startPage; $i <= $endPage; $i++)
                                    @if($i == $products->currentPage())
                                        <span class="px-2 py-1.5 text-xs bg-black text-white rounded shrink-0">{{ $i }}</span>
                                    @else
                                        <a href="{{ $products->url($i) }}" class="pagination-link px-2 py-1.5 text-xs text-zinc-700 hover:text-black border border-zinc-200 rounded hover:border-black transition shrink-0">{{ $i }}</a>
                                    @endif
                                @endfor
    
                                {{-- Next --}}
                                @if($products->hasMorePages())
                                    <a href="{{ $products->nextPageUrl() }}" class="pagination-link px-2 py-1.5 text-xs text-zinc-700 hover:text-black border border-zinc-200 rounded hover:border-black transition shrink-0">Next</a>
                                @else
                                    <span class="px-2 py-1.5 text-xs text-zinc-400 cursor-not-allowed shrink-0">Next</span>
                                @endif
                            </div>
                        </div>
                    @endif
    
                    <div id="noResults" class="hidden text-center py-12">
                        <i class="fas fa-search text-4xl text-zinc-300 mb-3"></i>
                        <p class="text-zinc-500">No products found</p>
                    </div>
                </div></div>
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

            
            fetch(`/customer/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof showHijabToast === 'function') {
                        showHijabToast('Product successfully added to cart!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        alert('Product successfully added to cart!');
                        location.reload();
                    }
                } else {
                    if (typeof showHijabToast === 'function') {
                        showHijabToast(data.message || 'Failed to add product to cart', 'error');
                    } else {
                        alert(data.message || 'Failed to add product to cart');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showHijabToast === 'function') {
                    showHijabToast('An error occurred while adding to cart', 'error');
                } else {
                    alert('An error occurred while adding to cart');
                }
            });
        }

        function addToWishlist(productId, event) {
            event.preventDefault();
            event.stopPropagation();
            
            fetch(`/customer/wishlist/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof showHijabToast === 'function') {
                        showHijabToast('Product successfully added to wishlist!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        alert('Product successfully added to wishlist!');
                        location.reload();
                    }
                } else {
                    if (typeof showHijabToast === 'function') {
                        showHijabToast(data.message || 'Product already in wishlist', 'error');
                    } else {
                        alert(data.message || 'Product already in wishlist');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showHijabToast === 'function') {
                    showHijabToast('An error occurred while adding to wishlist', 'error');
                } else {
                    alert('An error occurred while adding to wishlist');
                }
            });
        }

        function applyShopFilters(url = null) {
            const productGridWrapper = document.getElementById('productGridWrapper'); // Need to wrap product grid to replace it easily
            if (!url) {
                const brand = document.getElementById('filterBrand')?.value || '';
                const category = document.getElementById('filterCategory')?.value || '';
                const price = document.querySelector('input[name="filterPrice"]:checked')?.value || document.getElementById('filterPrice')?.value || '';
                const sort = document.querySelector('input[name="filterSort"]:checked')?.value || document.getElementById('filterSort')?.value || '';
                
                const urlObj = new URL(window.location.href);
                if (brand) urlObj.searchParams.set('brand', brand); else urlObj.searchParams.delete('brand');
                if (category) urlObj.searchParams.set('category', category); else urlObj.searchParams.delete('category');
                if (price) urlObj.searchParams.set('price', price); else urlObj.searchParams.delete('price');
                if (sort) urlObj.searchParams.set('sort', sort); else urlObj.searchParams.delete('sort');
                url = urlObj.toString();
            }

            // Update URL without refresh
            window.history.pushState(null, '', url);
            
            // Show loading state
            if(productGridWrapper) productGridWrapper.style.opacity = '0.5';

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Replace Product Grid & Pagination
                const newGridWrapper = doc.getElementById('productGridWrapper');
                if (newGridWrapper && productGridWrapper) {
                    productGridWrapper.innerHTML = newGridWrapper.innerHTML;
                    productGridWrapper.style.opacity = '1';
                }
                
                // Update sidebar filters active states
                const newSidebar = doc.getElementById('sidebarFilters');
                const oldSidebar = document.getElementById('sidebarFilters');
                if (newSidebar && oldSidebar) {
                    oldSidebar.innerHTML = newSidebar.innerHTML;
                    bindFilterEvents();
                }
            })
            .catch(err => {
                console.error(err);
                if(productGridWrapper) productGridWrapper.style.opacity = '1';
                // Fallback to reload
                window.location.href = url;
            });
        }

        function bindFilterEvents() {
            // Bind sidebar links
            document.querySelectorAll('#sidebarFilters .filter-chip').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    applyShopFilters(this.href);
                });
            });
            
            // Bind pagination links
            document.querySelectorAll('#productGridWrapper .pagination-link').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    applyShopFilters(this.href);
                });
            });
            
            // Bind sidebar radios
            document.querySelectorAll('#sidebarFilters input[type="radio"]').forEach(el => {
                el.addEventListener('change', function() {
                    applyShopFilters();
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            bindFilterEvents();
            
            // Handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                applyShopFilters(window.location.href);
            });
        });
    </script>
@endpush
