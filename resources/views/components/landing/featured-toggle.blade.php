

@php
    $cardFallbackImage = '/images/logo.png';
@endphp

<section class="np-fade-section {{ $sectionClass }}" data-featured-toggle @if($sectionId) id="{{ $sectionId }}" @endif>
    <div class="mx-auto w-full max-w-7xl px-6 md:px-10 lg:px-12">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-blue-400">{{ $subtitle }}</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-white sm:text-4xl">{{ $title }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/70">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/70">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

    <div class="np-layout-grid mt-10 grid grid-cols-2 gap-4" data-grid>
            @forelse($products as $index => $product)
                <button
                    type="button"
                    class="np-layout-item group flex h-full w-full flex-col overflow-hidden rounded-2xl border border-white/10 bg-[#111827] pb-6 text-start transition"
                    data-product-trigger
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ e($product->name) }}"
                    data-product-category="{{ e($product->category_label) }}"
                    data-product-description="{{ e(\Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 180)) }}"
                    data-product-image="{{ $product->image_url ?: $cardFallbackImage }}"
                    data-product-price="{{ $product->hasActiveDiscount() ? $product->formatted_discounted_price : $product->formatted_price }}"
                    data-product-old-price="{{ $product->hasActiveDiscount() ? $product->formatted_price : '' }}"
                >
                    <div class="relative aspect-[4/5] overflow-hidden bg-zinc-950">
                        <img
                            src="{{ $product->image_url ?: $cardFallbackImage }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            onerror="this.onerror=null;this.src='{{ $cardFallbackImage }}';"
                            loading="lazy"
                        >
                        @if($product->has_variants)
                            <span class="absolute right-2 top-2 z-20 inline-flex items-center rounded-full bg-black/85 px-2 py-1 text-[10px] font-semibold text-white shadow">Varian</span>
                        @endif
                        @if($product->hasActiveDiscount())
                            <span class="absolute left-2 top-2 z-20 inline-flex items-center gap-1 rounded-full bg-rose-500 px-2 py-1 text-[10px] font-semibold text-white shadow">
                                <i class="fas fa-tag text-[9px]"></i>
                                -{{ $product->formatted_discount_percent }}
                            </span>
                        @endif
                        <div class="absolute inset-0 z-10 bg-slate-950/10 transition duration-300 group-hover:bg-slate-950/0"></div>
                    </div>
                    <div class="mt-auto grid grid-cols-[1fr_auto] items-end gap-3 px-4">
                        <div class="min-h-11">
                            <h3 class="line-clamp-1 text-sm font-semibold capitalize tracking-tight text-white">{{ $product->name }}</h3>
                            <p class="mt-1 line-clamp-1 text-[11px] text-white/50">{{ $product->category_label }}</p>
                        </div>
                        <p class="self-end text-xs tabular-nums leading-none tracking-tight text-white/70">
                            @if($product->hasActiveDiscount())
                                <span class="text-[11px] font-semibold text-emerald-300 sm:text-xs">{{ $product->formatted_discounted_price }}</span>
                                <span class="ml-1 text-[9px] text-white/40 line-through sm:text-[10px]">{{ $product->formatted_price }}</span>
                            @else
                                {{ $product->formatted_price }}
                            @endif
                        </p>
                    </div>
                </button>
            @empty
                <div class="rounded-xl border border-dashed border-white/20 bg-white/5 p-10 text-center text-white/60">Produk belum tersedia.</div>
            @endforelse
        </div>
    </div>
</section>