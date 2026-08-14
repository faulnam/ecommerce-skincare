@extends('layouts.app')

@section('title', 'My Wishlist - Hijab')

@section('content')
@php
    $jsonPath = public_path('translation/wishlist.json');
    $wishTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp
<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')
    <div class="mx-auto w-full max-w-7xl px-6 pb-8 pt-32 md:px-10 md:pb-12 md:pt-32 lg:px-12 lg:pb-16 lg:pt-40">
        <h3 class="mb-6 text-3xl font-semibold tracking-tight text-black sm:text-4xl">
            <i class="fas fa-heart mr-3 text-rose-500"></i>{{ $wishTrans['page_title'][$lang] ?? 'My Wishlist' }}
        </h3>
        
        @if($wishlistItems->count() > 0)
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-4 overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-black/6 bg-zinc-50 px-6 py-4">
                        <span class="text-sm font-medium text-black">{{ $wishlistItems->count() }} {{ $wishTrans['label_item'][$lang] ?? 'Item' }}</span>
                        <form action="{{ route('customer.wishlist.clear') }}" method="POST" 
                              onsubmit="return confirm('{{ $wishTrans['prompt_clear'][$lang] ?? 'Clear wishlist?' }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full border border-rose-600 bg-white px-4 py-1.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50">
                                <i class="fas fa-trash mr-1"></i>{{ $wishTrans['btn_clear'][$lang] ?? 'Clear Wishlist' }}
                            </button>
                        </form>
                    </div>
                    <div>
                        @foreach($wishlistItems as $item)
                            <div class="border-b border-black/6 p-6 last:border-0">
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <div class="relative mx-auto shrink-0 sm:mx-0">
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="h-20 w-20 rounded-xl object-cover">
                                        @if($item->product->hasActiveDiscount())
                                            <span class="absolute left-0 top-0 rounded-br-lg rounded-tl-lg bg-rose-500 px-1.5 py-0.5 text-[10px] font-semibold text-white">-{{ $item->product->formatted_discount_percent }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex flex-1 flex-col">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h6 class="text-base font-semibold text-black">{{ $item->product->name }}</h6>
                                                <p class="mt-0.5 text-xs text-zinc-500">{{ $item->product->category_label }}</p>
                                                @if($item->product->hasActiveDiscount())
                                                    <p class="mt-1 text-sm font-medium text-emerald-600">{{ $item->product->formatted_discounted_price }}</p>
                                                    <p class="text-xs text-zinc-400 line-through">{{ $item->product->formatted_price }}</p>
                                                @else
                                                    <p class="mt-1 text-sm font-medium text-emerald-600">{{ $item->product->formatted_price }}</p>
                                                @endif
                                            </div>
                                            <form action="{{ route('customer.wishlist.remove', $item->product->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 transition hover:text-rose-700">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <form action="{{ route('customer.cart.add', $item->product) }}" method="POST" class="inline-flex">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="flex items-center gap-2 rounded-full border border-black/10 bg-white px-4 py-2 text-sm font-medium text-black transition hover:bg-black hover:text-white">
                                                    <i class="fas fa-shopping-cart text-xs"></i>
                                                    <span>{{ $wishTrans['btn_add_cart'][$lang] ?? 'Tambah ke Keranjang' }}</span>
                                                </button>
                                            </form>
                                            
                                            <a href="{{ $item->detail_url }}" class="text-sm font-medium text-black underline decoration-black/30 underline-offset-4 hover:decoration-black transition">
                                                {{ $wishTrans['btn_view_detail'][$lang] ?? 'Lihat Detail' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <a href="{{ route('home') }}#products" class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white px-6 py-2.5 text-sm font-medium text-black transition hover:bg-black hover:text-white">
                    <i class="fas fa-arrow-left"></i>{{ $wishTrans['btn_continue'][$lang] ?? 'Lanjut Belanja' }}
                </a>
            </div>
            
            <div class="lg:col-span-1">
                <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                    <div class="border-b border-black/6 bg-black px-6 py-4">
                        <h4 class="text-lg font-semibold text-white"><i class="fas fa-receipt mr-2"></i>{{ $wishTrans['summary_title'][$lang] ?? 'Ringkasan' }}</h4>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex justify-between border-b border-black/6 pb-3 text-sm">
                            <span class="text-zinc-600">{{ $wishTrans['summary_total_item'][$lang] ?? 'Total Item' }}</span>
                            <span class="font-medium text-black">{{ $wishlistItems->count() }} pcs</span>
                        </div>
                        @php
                            $totalPrice = 0;
                            $originalTotal = 0;
                            foreach($wishlistItems as $item) {
                                $price = $item->product->hasActiveDiscount() ? $item->product->discounted_price : $item->product->price;
                                $totalPrice += $price;
                                $originalTotal += $item->product->price;
                            }
                            $totalDiscount = $originalTotal - $totalPrice;
                        @endphp
                        @if($totalDiscount > 0)
                            <div class="flex justify-between border-b border-black/6 py-3 text-sm">
                                <span class="text-zinc-600">{{ $wishTrans['summary_normal_price'][$lang] ?? 'Harga Normal' }}</span>
                                <span class="text-zinc-400 line-through">Rp {{ number_format($originalTotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between border-b border-black/6 py-3 text-sm text-rose-600">
                                <span>{{ $wishTrans['summary_discount'][$lang] ?? 'Diskon Produk' }}</span>
                                <span>-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-3 text-sm">
                            <span class="text-zinc-600">{{ $wishTrans['summary_subtotal'][$lang] ?? 'Subtotal' }}</span>
                            <span class="font-medium text-black">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="mb-6 flex justify-between border-t border-black/6 pt-4">
                            <strong class="text-lg text-black">{{ $wishTrans['summary_total'][$lang] ?? 'Total' }}</strong>
                            <strong class="text-lg text-emerald-600">Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
                        </div>
                        
                        <a href="{{ route('customer.cart.index') }}" class="block w-full rounded-full bg-black px-6 py-3 text-center text-sm font-medium text-white transition hover:bg-black/90">
                            <i class="fas fa-shopping-bag mr-2"></i>{{ $wishTrans['btn_view_cart'][$lang] ?? 'View Cart' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
            <div class="py-20 text-center">
                <i class="fas fa-heart mb-6 text-6xl text-zinc-300"></i>
                <h4 class="mb-2 text-2xl font-semibold text-black">{{ $wishTrans['empty_title'][$lang] ?? 'Wishlist is Empty' }}</h4>
                <p class="mb-6 text-zinc-600">{{ $wishTrans['empty_desc'][$lang] ?? "No favorite products yet? Let's start adding!" }}</p>
                <a href="{{ route('home') }}#products" class="inline-flex items-center gap-2 rounded-full bg-black px-8 py-3 text-sm font-medium text-white transition hover:bg-black/90">
                    <i class="fas fa-shopping-bag"></i>{{ $wishTrans['btn_start'][$lang] ?? 'Start Shopping' }}
                </a>
            </div>
        @endif
    </div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
        const mobileMenu = document.querySelector('[data-mobile-menu]');

        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', function () {
                mobileMenu.classList.toggle('hidden');
                mobileMenuToggle.setAttribute('aria-expanded', String(!mobileMenu.classList.contains('hidden')));
            });
        }
    });

    // Hamburger Menu Toggle
    (function() {
        const btn = document.getElementById('hamburgerMenuBtnCustom');
        const dropdown = document.getElementById('hamburgerMenuDropdownCustom');
        const wrapper = document.getElementById('hamburgerMenuWrapperCustom');
        if (!btn || !dropdown || !wrapper) return;
        btn.addEventListener('click', function(e){ e.stopPropagation(); dropdown.classList.toggle('hidden'); });
        document.addEventListener('click', function(e){ if(!wrapper.contains(e.target)) dropdown.classList.add('hidden'); });
    })();

    // Mega Dropdown Hover Control
    (function() {
        const dropdownContainers = document.querySelectorAll('[data-dropdown]');
        let activeDropdown = null;
        let hoverTimeout = null;

        dropdownContainers.forEach(container => {
            const dropdown = container.querySelector('.absolute');

            container.addEventListener('mouseenter', () => {
                if (hoverTimeout) {
                    clearTimeout(hoverTimeout);
                    hoverTimeout = null;
                }

                dropdownContainers.forEach(otherContainer => {
                    if (otherContainer !== container) {
                        const otherDropdown = otherContainer.querySelector('.absolute');
                        if (otherDropdown) {
                            otherDropdown.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]');
                            otherDropdown.classList.remove('visible', 'opacity-100', 'translate-y-0');
                        }
                    }
                });

                if (dropdown) {
                    dropdown.classList.remove('invisible', 'opacity-0', 'translate-y-[-10px]');
                    dropdown.classList.add('visible', 'opacity-100', 'translate-y-0');
                }
                activeDropdown = container;
            });

            container.addEventListener('mouseleave', () => {
                hoverTimeout = setTimeout(() => {
                    if (dropdown) {
                        dropdown.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]');
                        dropdown.classList.remove('visible', 'opacity-100', 'translate-y-0');
                    }
                    activeDropdown = null;
                }, 100);
            });

            if (dropdown) {
                dropdown.addEventListener('mouseenter', () => {
                    if (hoverTimeout) {
                        clearTimeout(hoverTimeout);
                        hoverTimeout = null;
                    }
                });

                dropdown.addEventListener('mouseleave', () => {
                    hoverTimeout = setTimeout(() => {
                        dropdown.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]');
                        dropdown.classList.remove('visible', 'opacity-100', 'translate-y-0');
                        activeDropdown = null;
                    }, 100);
                });
            }
        });
    })();

    // Modern Search Overlay
    (function() {
        const overlay = document.getElementById('searchOverlay');
        const panel = document.getElementById('searchPanel');
        const toggleBtn = document.getElementById('searchToggleBtn');
        const closeBtn = document.getElementById('searchCloseBtn');
        const backdrop = document.getElementById('searchBackdrop');
        const input = document.getElementById('searchInput');
        const loading = document.getElementById('searchLoading');
        const initialState = document.getElementById('searchInitial');
        const emptyState = document.getElementById('searchEmpty');
        const resultsList = document.getElementById('searchResults');

        if (!overlay || !toggleBtn || !input) return;

        let debounceTimer;
        let currentController;

        function openOverlay() {
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => {
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');
                panel.classList.remove('-translate-y-4');
                panel.classList.add('translate-y-0');
                setTimeout(() => input.focus(), 50);
            });
        }

        function closeOverlay() {
            overlay.classList.add('opacity-0');
            overlay.classList.remove('opacity-100');
            panel.classList.add('-translate-y-4');
            panel.classList.remove('translate-y-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
                input.value = '';
                initialState.classList.remove('hidden');
                emptyState.classList.add('hidden');
                resultsList.classList.add('hidden');
            }, 300);
        }

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        function renderResults(products) {
            resultsList.innerHTML = products.map(p => `
                <a href="${escapeHtml(p.detail_url)}" class="flex items-center gap-4 px-5 py-3 transition hover:bg-zinc-50">
                    <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-lg bg-zinc-100">
                        <img src="${escapeHtml(p.image_url)}" alt="${escapeHtml(p.name)}" class="h-full w-full object-cover" onerror="this.onerror=null; this.removeAttribute('srcset'); this.src=this.src;" loading="lazy">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-zinc-900">${escapeHtml(p.name)}</p>
                        <div class="mt-0.5 flex items-center gap-2 text-xs text-zinc-500">
                            ${p.brand ? `<span class="font-medium">${escapeHtml(p.brand)}</span>` : ''}
                            ${p.brand && p.category_label ? '<span class="text-zinc-300">·</span>' : ''}
                            ${p.category_label ? `<span>${escapeHtml(p.category_label)}</span>` : ''}
                        </div>
                    </div>
                    <p class="flex-shrink-0 text-sm font-semibold text-zinc-900">${escapeHtml(p.formatted_price)}</p>
                </a>
            `).join('');
        }

        async function performSearch(query) {
            if (currentController) currentController.abort();
            currentController = new AbortController();
            loading.classList.remove('hidden');
            try {
                const res = await fetch(`/api/search-products?q=${encodeURIComponent(query)}`, {
                    signal: currentController.signal,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                loading.classList.add('hidden');
                if (data.products && data.products.length > 0) {
                    renderResults(data.products);
                    initialState.classList.add('hidden');
                    emptyState.classList.add('hidden');
                    resultsList.classList.remove('hidden');
                } else {
                    initialState.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                    resultsList.classList.add('hidden');
                }
            } catch (err) {
                if (err.name !== 'AbortError') {
                    loading.classList.add('hidden');
                    console.error('Search error:', err);
                }
            }
        }

        input.addEventListener('input', function() {
            const q = this.value.trim();
            clearTimeout(debounceTimer);
            if (q.length < 2) {
                loading.classList.add('hidden');
                if (currentController) currentController.abort();
                initialState.classList.remove('hidden');
                emptyState.classList.add('hidden');
                resultsList.classList.add('hidden');
                return;
            }
            debounceTimer = setTimeout(() => performSearch(q), 250);
        });

        toggleBtn.addEventListener('click', openOverlay);
        closeBtn.addEventListener('click', closeOverlay);
        backdrop.addEventListener('click', closeOverlay);

        const navSearchInputEl = document.getElementById('navSearchInput');
        if (navSearchInputEl) {
            navSearchInputEl.addEventListener('focus', function() {
                openOverlay();
                const q = this.value.trim();
                if (q.length >= 2) {
                    input.value = q;
                    performSearch(q);
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !overlay.classList.contains('hidden')) {
                closeOverlay();
            }
        });
    })();
</script>
@endpush