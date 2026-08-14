@extends('layouts.app')

@section('title', 'Shopping Cart - Hijab')

@section('content')
@php
    $jsonPath = public_path('translation/shoppingcart.json');
    $cartTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')
    <div class="mx-auto w-full max-w-7xl px-6 py-8 pt-32 md:px-10 md:py-12 md:pt-32 lg:px-12 lg:py-24">
        <h3 class="mb-6 text-3xl font-semibold tracking-tight text-black sm:text-4xl flex items-center">
            <i class="fas fa-shopping-cart mr-3 text-black"></i>{{ $cartTrans['cart_title'][$lang] ?? 'Shopping Cart' }}
        </h3>
    
    @if($cartItems->count() > 0)
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-4 overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-black/6 bg-zinc-50 px-6 py-4">
                        <span class="text-sm font-medium text-black">
                            {!! str_replace(':count', $cartItems->count(), $cartTrans['label_items_count'][$lang] ?? $cartItems->count() . ' Item') !!}
                        </span>
                        <form action="{{ route('customer.cart.clear') }}" method="POST" 
                              onsubmit="return confirm('{{ $cartTrans['prompt_clear_cart'][$lang] ?? 'Clear cart?' }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full border border-rose-600 bg-white px-4 py-1.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50 flex items-center gap-1">
                                <i class="fas fa-trash text-[10px]"></i>{{ $cartTrans['btn_clear_cart'][$lang] ?? 'Clear Cart' }}
                            </button>
                        </form>
                    </div>
                    <div>
                        @foreach($cartItems as $item)
                            <div class="border-b border-black/6 p-6 last:border-0">
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <div class="relative mx-auto shrink-0 sm:mx-0">
                                        <img src="{{ ($item->variant && $item->variant->image_url) ? $item->variant->image_url : ($item->product ? $item->product->image_url : 'https://via.placeholder.com/80') }}" 
                                             alt="{{ $item->product->name }}" class="h-20 w-20 rounded-xl object-cover border" onerror="this.onerror=null; this.removeAttribute('srcset'); this.src=this.src;">
                                        @if($item->product->hasActiveDiscount())
                                            <span class="absolute left-0 top-0 rounded-br-lg rounded-tl-lg bg-rose-500 px-1.5 py-0.5 text-[10px] font-semibold text-white shadow-sm">-{{ $item->product->formatted_discount_percent }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex flex-1 flex-col">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0 flex-1">
                                                <h6 class="text-base font-semibold text-black truncate">{{ $item->product->name }}</h6>
                                                @if($item->variant)
                                                    @php
                                                        $vParts = explode('|', $item->variant->name);
                                                        $vName = count($vParts) >= 2 ? trim($vParts[0]) . ' - ' . trim($vParts[1]) : $item->variant->name;
                                                    @endphp
                                                    <p class="mt-0.5 text-xs text-zinc-500 flex items-center gap-1">
                                                        <i class="fas fa-tag text-[10px]"></i>{{ $cartTrans['label_variant'][$lang] ?? 'Varian:' }} <span class="font-semibold text-zinc-700">{{ $vName }}</span>
                                                    </p>
                                                @endif
                                                @php
                                                    $isEligibleForFree = false;
                                                    if (!auth()->check()) {
                                                        $isEligibleForFree = true;
                                                    } else {
                                                        $user = auth()->user();
                                                        $isEligibleForFree = $user && $user->role === 'customer' 
                                                            && !$user->welcome_bonus_claimed 
                                                            && !$user->orders()->whereNotIn('status', ['pending', 'cancelled', 'failed'])->exists();
                                                    }
                                                    $isFree = $item->product->is_free_event && $isEligibleForFree;
                                                @endphp
                                                @if($isFree)
                                                    <p class="mt-1 text-sm font-semibold text-zinc-400 line-through">{{ $item->product->formatted_price }}</p>
                                                @elseif($item->variant)
                                                    <p class="mt-1 text-sm font-semibold text-emerald-600">{{ $item->variant->formatted_final_price }}</p>
                                                @elseif($item->product->hasActiveDiscount())
                                                    <p class="mt-1 text-sm font-semibold text-emerald-600">{{ $item->product->formatted_discounted_price }}</p>
                                                    <p class="text-xs text-zinc-400 line-through">{{ $item->product->formatted_price }}</p>
                                                @else
                                                    <p class="mt-1 text-sm font-semibold text-emerald-600">{{ $item->product->formatted_price }}</p>
                                                @endif
                                            </div>
                                            <form action="{{ auth()->check() ? route('customer.cart.remove', $item->id) : route('customer.cart.remove', $item->id) }}" method="POST" class="shrink-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-zinc-400 transition hover:text-rose-600 p-1">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-3 border-t border-dashed border-zinc-100">
                                            <form action="{{ auth()->check() ? route('customer.cart.update', $item->id) : route('customer.cart.update', $item->id) }}" method="POST" class="inline-flex">
                                                @csrf
                                                @method('PATCH')
                                                <div class="flex items-center gap-1 bg-zinc-50 border border-black/10 rounded-lg p-0.5 shadow-inner">
                                                    <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="flex h-7 w-7 items-center justify-center rounded-md bg-white border text-black font-semibold shadow-sm transition hover:bg-zinc-100 disabled:opacity-50" 
                                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                                                    <input type="text" class="h-7 w-10 bg-transparent text-center text-xs font-bold text-zinc-800" value="{{ $item->quantity }}" readonly>
                                                    @php
                                                        $maxStock = $item->variant ? $item->variant->stock : $item->product->stock;
                                                    @endphp
                                                    <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="flex h-7 w-7 items-center justify-center rounded-md bg-white border text-black font-semibold shadow-sm transition hover:bg-zinc-100 disabled:opacity-50"
                                                            {{ $item->quantity >= $maxStock ? 'disabled' : '' }}>+</button>
                                                </div>
                                            </form>
                                            
                                            @if($isFree)
                                                <strong class="text-base font-bold text-zinc-400 line-through tracking-tight">{{ is_object($item) && method_exists($item, 'getAttribute') ? $item->formatted_original_subtotal : 'Rp ' . number_format($item->original_subtotal ?? 0, 0, ',', '.') }}</strong>
                                            @else
                                                <strong class="text-base font-bold text-black tracking-tight">{{ is_object($item) && method_exists($item, 'getAttribute') ? $item->formatted_subtotal : 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</strong>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <a href="{{ route('home') }}#products" class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-6 py-2.5 text-sm font-semibold text-zinc-800 transition hover:bg-black hover:text-white shadow-sm">
                    <i class="fas fa-arrow-left text-xs"></i>{{ $cartTrans['btn_continue_shopping'][$lang] ?? 'Lanjut Belanja' }}
                </a>
            </div>
            
            <div class="lg:col-span-1">
                <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                    <div class="border-b border-black/6 bg-black px-6 py-4 flex items-center">
                        <h4 class="text-lg font-bold text-white tracking-tight"><i class="fas fa-receipt mr-2 text-zinc-400 text-sm"></i>{{ $cartTrans['summary_title'][$lang] ?? 'Summary' }}</h4>
                    </div>
                    <div class="px-6 py-6 text-xs">
                        <div class="flex justify-between border-b border-black/6 pb-3 text-zinc-500 font-medium">
                            <span>{{ $cartTrans['summary_total_items'][$lang] ?? 'Total Items' }}</span>
                            <span class="font-bold text-black">{{ $cartItems->sum('quantity') }} pcs</span>
                        </div>
                        @php
                            $totalDiscount = 0;
                            $originalTotal = 0;
                            foreach($cartItems as $item) {
                                if(is_object($item) && method_exists($item, 'getAttribute')) {
                                    $totalDiscount += $item->discount_amount ?? 0;
                                    $originalTotal += $item->original_subtotal ?? $item->subtotal;
                                } else {
                                    $product = $item->product;
                                    $variant = $item->variant ?? null;
                                    $qty = $item->quantity;
                                    
                                    $basePrice = ($variant && $variant->price) ? $variant->price : $product->price;
                                    $finalPrice = $variant ? $variant->final_price : ($product->hasActiveDiscount() ? $product->discounted_price : $product->price);
                                    
                                    $originalTotal += $basePrice * $qty;
                                    $totalDiscount += max(0, $basePrice - $finalPrice) * $qty;
                                }
                            }
                        @endphp
                        @if($totalDiscount > 0)
                            <div class="flex justify-between border-b border-black/6 py-3 text-zinc-500 font-medium">
                                <span>{{ $cartTrans['summary_normal_price'][$lang] ?? 'Harga Normal' }}</span>
                                <span class="text-zinc-400 line-through font-semibold">Rp {{ number_format($originalTotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between border-b border-black/6 py-3 text-rose-600 font-semibold">
                                <span>{{ $cartTrans['summary_product_discount'][$lang] ?? 'Diskon Produk' }}</span>
                                <span>-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-3 text-zinc-500 font-medium border-b border-zinc-100 mb-4">
                            <span>{{ $cartTrans['summary_subtotal'][$lang] ?? 'Subtotal' }}</span>
                            <span class="font-bold text-black">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="mb-6 flex justify-between items-center">
                            <span class="text-sm font-bold text-black">{{ $cartTrans['summary_total'][$lang] ?? 'Total' }}</span>
                            <strong class="text-xl font-bold tracking-tight text-emerald-600">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                        
                        <div class="space-y-2.5 text-sm">
                            @if(auth()->check())
                                <a href="{{ route('customer.checkout') }}" class="flex w-full items-center justify-center rounded-full bg-black py-3 text-center font-semibold text-white transition hover:bg-black/90 shadow-sm">
                                    <i class="fas fa-credit-card mr-2 text-xs text-zinc-400"></i>{{ $cartTrans['btn_checkout'][$lang] ?? 'Checkout' }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="flex w-full items-center justify-center rounded-full bg-black py-3 text-center font-semibold text-white transition hover:bg-black/90 shadow-sm">
                                    <i class="fas fa-sign-in-alt mr-2 text-xs text-white"></i>Login Dulu
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State View Component -->
        <div class="py-20 text-center border rounded-2xl bg-white shadow-sm border-zinc-100">
            <div class="mb-5 flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-zinc-50 border shadow-inner text-zinc-300">
                <i class="fas fa-shopping-cart text-2xl"></i>
            </div>
            <h4 class="mb-1.5 text-xl font-bold text-black tracking-tight">{{ $cartTrans['empty_title'][$lang] ?? 'Shopping Cart is Empty' }}</h4>
            <p class="mb-6 text-xs text-zinc-500 max-w-sm mx-auto px-4 leading-relaxed">{{ $cartTrans['empty_desc'][$lang] ?? "Let's start shopping for Hijab equipment!" }}</p>
            <a href="{{ route('home') }}#products" class="inline-flex items-center gap-2 rounded-full bg-black px-8 py-2.5 text-xs font-semibold text-white transition hover:bg-black/90 shadow-sm">
                <i class="fas fa-shopping-bag text-[10px]"></i>{{ $cartTrans['btn_start_shopping'][$lang] ?? 'Start Shopping' }}
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