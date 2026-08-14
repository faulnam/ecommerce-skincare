@extends('layouts.app')

@section('title', $product->name . ' - Hijab')
@section('og_type', 'product')
@section('og_title', $product->name . ' - Hijab')
@section('og_description'){{ Str::limit(strip_tags($product->description ?? $product->name), 160) }}@endsection
@section('og_image', $product->image_url)
@section('og_image_width', '1200')
@section('og_image_height', '1200')
@section('og_url', url()->current())
@section('og_image_alt', $product->name)
@push('og_extra')
    <meta property="product:price:amount" content="{{ $product->discounted_price }}">
    <meta property="product:price:currency" content="IDR">
    <meta property="product:availability" content="{{ $product->stock > 0 ? 'in stock' : 'out of stock' }}">
@endpush

@php
    $benefitsStr = $product->benefits ? strtolower($product->benefits) : \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 50, '');
    $playerStr = $product->player_type ? strtolower($product->player_type) : 'semua level';
    $priceStr = $product->hasActiveDiscount() ? $product->formatted_discounted_price : $product->formatted_price;
    $metaDesc = "{$product->name} — {$benefitsStr}. Cocok untuk {$playerStr}. Harga {$priceStr}. Beli di Hijab!";
    $metaDesc = \Illuminate\Support\Str::limit($metaDesc, 155, '');
@endphp
@section('meta_description', $metaDesc)

@section('content')
<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')
    <main class="pt-16 md:pt-32">
        <div class="bg-white min-h-screen">
            <div class="container mx-auto px-4 max-w-7xl">
                <!-- Breadcrumb -->
                <nav class="mb-2 text-xs">
                    <ol class="flex items-center gap-2 text-zinc-600">
                        <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                        <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                        <li><a href="{{ route('shop') }}" class="hover:text-black transition">Produk</a></li>
                        <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                        @if($product->category)
                        <li><a href="{{ route('kategori.show', $product->category_slug) }}" class="hover:text-black transition">{{ ucfirst($product->category) }}</a></li>
                        <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                        @endif
                        <li class="text-black font-medium truncate max-w-[200px]">{{ $product->name }}</li>
                    </ol>
                </nav>

                <div class="grid md:grid-cols-[auto_1fr] gap-5 lg:gap-6 py-4 items-start" x-data="productData()">
                    <!-- Product Gallery -->
                    <div class="flex flex-col gap-3 max-w-[600px]">
                        <!-- Main Image -->
                        <div class="flex-1 relative group">
                            <div class="w-full bg-zinc-50 overflow-hidden flex items-center justify-center relative aspect-square max-w-[500px]">
                                <img :src="activeImage" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover transition-all duration-500 ease-out"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100">
                                <template x-if="isCurrentDiscounted && {{ ($product->is_free_event && isset($isEligibleForFree) && $isEligibleForFree) ? 'false' : 'true' }}">
                                    <div class="absolute text-white font-bold uppercase tracking-wider z-10" style="top: 12px; left: 12px; font-size: 10px; padding: 4px 8px; background-color: #e53e3e; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" x-text="'Diskon ' + currentDiscountPercent + '%'"></div>
                                </template>
                                @php
                                    $isNewArrival = \App\Models\Product::orderBy('created_at', 'desc')->take(8)->pluck('id')->contains($product->id);
                                @endphp
                                <template x-if="!isCurrentDiscounted && {{ $isNewArrival ? 'true' : 'false' }}">
                                    <div class="absolute text-black font-bold uppercase tracking-wider z-10" style="top: 12px; left: 12px; font-size: 10px; padding: 4px 8px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        Terbaru
                                    </div>
                                </template>
                                @if(isset($isEligibleForFree) && $isEligibleForFree && $product->is_free_event)
                                    <span class="absolute left-3 top-3 bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white z-10 rounded">FREE</span>
                                @endif
                                @if($product->package_type === 'bundle')
                                    <span class="absolute left-3 bg-purple-500 px-3 py-1.5 text-xs font-semibold text-white z-10 rounded" :class="isCurrentDiscounted ? 'top-12' : 'top-3'">Bundle</span>
                                @endif
                                @if($product->isBestSeller())
                                    <span class="absolute right-3 top-3 bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white z-10 rounded">Best Seller</span>
                                @endif
                            </div>
                        </div>

                        <!-- Thumbnails -->
                        <div class="flex flex-row gap-3 overflow-x-auto pb-2">
                            <template x-for="(imageUrl, index) in currentImages" :key="index">
                                <button @click="activeImage = imageUrl"
                                        class="w-16 h-16 md:w-20 md:h-20 bg-zinc-50 transition-all duration-200 flex items-center justify-center p-1.5 overflow-hidden flex-shrink-0"
                                        :class="activeImage === imageUrl ? 'ring-2 ring-black ring-offset-2' : 'hover:bg-zinc-100'">
                                    <img :src="imageUrl" alt="Thumbnail" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Product Info - Lebih Compact -->
                    <div class="space-y-4">
                        <!-- Category Badge -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">
                                {{ $product->category_label }}
                            </span>
                            
                            @if($product->package_type === 'bundle')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200">
                                    <i class="fas fa-box-open text-[10px]"></i>
                                    Bundling
                                </span>
                            @endif
                            
                            <template x-if="isCurrentDiscounted">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200" x-text="'Discount ' + currentDiscountPercent + '%'">
                                </span>
                            </template>

                            @if($product->stock <= 0)
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-500 border border-zinc-200">
                                    Out of Stock
                                </span>
                            @endif
                        </div>

                        <!-- Product Name -->
                        <div>
                            <h1 class="text-xl md:text-2xl font-semibold text-black tracking-tight leading-tight">{{ $product->name }}</h1>
                        </div>

                        <!-- Rating & Terjual -->
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-1">
                                @php
                                    $totalSold = \App\Models\OrderItem::where('product_id', $product->id)
                                        ->whereHas('order', function($q) {
                                            $q->whereIn('status', ['completed', 'delivered']);
                                        })->sum('quantity');

                                    $reviews = \App\Models\Review::where('product_id', $product->id)
                                        ->get();
                                    $displayRating = $reviews->isNotEmpty() ? $reviews->avg('rating') : 5.0;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= floor($displayRating) ? 'text-black' : 'text-zinc-500' }} text-sm"></i>
                                @endfor
                                <span class="text-zinc-600 ml-1">{{ number_format($displayRating, 1) }}</span>
                            </div>
                            <span class="text-zinc-400">|</span>
                            <div class="text-zinc-600">
                                <i class="fas fa-box text-xs mr-1"></i>
                                <span class="font-semibold text-black">{{ $totalSold }}</span> Terjual
                            </div>
                            @if($product->formatted_weight && $product->formatted_weight !== '-')
                                <span class="text-zinc-400">|</span>
                                <div class="text-zinc-600">
                                    <i class="fas fa-weight-hanging text-xs mr-1"></i>
                                    {{ $product->formatted_weight }}
                                </div>
                            @endif
                        </div>

                        <!-- Price -->
                        <div class="space-y-1">
                            <template x-if="isFreeEventAndEligible">
                                <div>
                                    <span class="text-3xl font-bold text-zinc-400 line-through" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(originalPrice)"></span>
                                </div>
                            </template>
                            <template x-if="!isFreeEventAndEligible">
                                <div>
                                    <template x-if="isCurrentDiscounted">
                                        <div>
                                            <div class="flex items-baseline gap-3">
                                                <span class="text-3xl font-bold text-black" x-text="formatRupiah(currentPrice)"></span>
                                                <span class="text-lg text-zinc-400 line-through" x-text="formatRupiah(originalPrice)"></span>
                                            </div>
                                            <p class="text-sm text-green-600 font-medium">
                                                <i class="fas fa-tag mr-1"></i>Save <span x-text="formatRupiah(originalPrice - currentPrice)"></span>
                                            </p>
                                        </div>
                                    </template>
                                    <template x-if="!isCurrentDiscounted">
                                        <span class="text-3xl font-bold text-black" x-text="formatRupiah(currentPrice)"></span>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Product Variants -->
                        @if($product->has_variants && $product->variants->count() > 0)
                        <div class="border-t border-zinc-200 pt-4 pb-2 mb-4">
                            <!-- Size Selection -->
                            <div class="mb-4">
                                <label class="block text-[11px] font-semibold text-zinc-900 uppercase tracking-widest mb-2">Size</label>
                                <select :value="selectedSize" @change="selectedSize = $event.target.value" class="w-full border border-zinc-300 rounded-none shadow-none text-sm py-3 px-4 focus:border-black focus:ring-0 cursor-pointer appearance-none bg-white">
                                    <template x-for="size in availableSizes" :key="size">
                                        <option :value="size" x-text="size" :selected="size === selectedSize"></option>
                                    </template>
                                </select>
                            </div>
                            
                            <!-- Color Selection -->
                            <div class="mb-4">
                                <label class="block text-[11px] font-semibold text-zinc-900 uppercase tracking-widest mb-2">
                                    Color: <span class="font-normal text-zinc-600 ml-1" x-text="selectedColor"></span>
                                </label>
                                <div class="flex flex-wrap gap-3 mt-2">
                                    <template x-for="color in availableColors" :key="color.name">
                                        <div @click="selectedColor = color.name"
                                             class="cursor-pointer rounded-full p-[2px] transition-all duration-200"
                                             :class="selectedColor === color.name ? 'border border-black' : 'border border-transparent hover:border-zinc-300'">
                                            <div class="w-6 h-6 rounded-full border border-zinc-200" :style="`background-color: ${color.hex}`"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Product Description -->
                        @if($product->description)
                        <div class="border-t border-zinc-200 pt-4 pb-2">
                            <h3 class="text-[10px] font-semibold text-black uppercase tracking-wider mb-3">Description</h3>
                            <div class="prose prose-zinc max-w-none text-xs text-zinc-600 prose-h2:text-sm prose-h2:font-medium prose-h2:text-black prose-h2:mb-4 prose-h2:mt-0 prose-p:mb-6 prose-p:leading-relaxed text-justify">
                                {!! $product->description !!}
                            </div>
                        </div>
                        @endif

                        <!-- Specifications Table -->
                        @if($product->category === 'hijab')
                        <div class="border-t border-zinc-200 pt-4">
                            <h3 class="text-[10px] font-semibold text-black uppercase tracking-wider mb-3">Specifications</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <tbody>
                                        @php
                                            $primarySpecs = [
                                                'Brand' => $product->brand,
                                                'Series' => $product->series,
                                                'Shape' => $product->shape,
                                                'Balance' => $product->balance,
                                                'Weight' => $product->hijab_weight,
                                            ];
                                            $allSpecs = [
                                                'Brand' => $product->brand,
                                                'Series' => $product->series,
                                                'Shape' => $product->shape,
                                                'Balance' => $product->balance,
                                                'Weight' => $product->hijab_weight,
                                                'Level' => $product->level ? ucfirst($product->level) : null,
                                                'Play Style' => $product->play_style,
                                                'Player Type' => $product->player_type,
                                                'Core' => $product->core,
                                                'Faces' => $product->faces,
                                                'Frame' => $product->frame,
                                                'Surface' => $product->surface,
                                                'Feel' => $product->feel,
                                                'Power' => $product->power,
                                                'Control' => $product->control,
                                                'Maneuverability' => $product->maneuverability,
                                                'Comfort' => $product->comfort,
                                                'Technology' => $product->technology,
                                                'Benefits' => $product->benefits,
                                                'Suitable For' => $product->suitable_for,
                                                'Collection' => $product->collection,
                                            ];
                                        @endphp
                                        @foreach($primarySpecs as $label => $value)
                                            @if($value)
                                                <tr class="border-b border-zinc-100">
                                                    <td class="py-1.5 px-1.5 font-medium text-zinc-700 w-1/3">{{ $label }}</td>
                                                    <td class="py-1.5 px-1.5 text-zinc-600">{{ $value }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Expand/Collapse Button -->
                            <button id="toggleSpecs" onclick="toggleSpecs()" class="flex items-center gap-1 text-xs text-zinc-600 hover:text-black mt-2 transition-colors">
                                <span id="toggleText">View More</span>
                                <i id="toggleIcon" class="fas fa-chevron-down transition-transform duration-200"></i>
                            </button>

                            <!-- Additional Specifications (Hidden by default) -->
                            <div id="additionalSpecs" class="hidden overflow-x-auto mt-2">
                                <table class="w-full text-xs">
                                    <tbody>
                                        @php
                                            $additionalSpecs = array_slice($allSpecs, 5, null, true);
                                        @endphp
                                        @foreach($additionalSpecs as $label => $value)
                                            @if($value)
                                                <tr class="border-b border-zinc-100">
                                                    <td class="py-1.5 px-1.5 font-medium text-zinc-700 w-1/3">{{ $label }}</td>
                                                    <td class="py-1.5 px-1.5 text-zinc-600">{{ $value }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="border-t border-zinc-200 pt-4 space-y-2">
                            @if($product->stock > 0)
                                <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="flex w-full">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="variant_id" :value="selectedVariant ? selectedVariant.id : ''">
                                    <button type="submit" class="flex-grow bg-black px-3 py-4 text-xs font-bold tracking-widest text-white uppercase hover:bg-zinc-900 transition flex items-center justify-center gap-2" :disabled="isVariantRequiredButNotSelected || (selectedVariant && selectedVariant.stock <= 0)">
                                        <span x-text="(selectedVariant && selectedVariant.stock <= 0) ? 'Out of Stock' : (isVariantRequiredButNotSelected ? 'Select Variant' : 'Add to Cart')"></span>
                                    </button>
                                    <button type="button" onclick="event.preventDefault(); event.stopPropagation(); addToWishlist('{{ $product->slug }}', event)" class="w-14 bg-[#111] border-l border-zinc-800 text-white flex items-center justify-center hover:bg-zinc-900 transition">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full bg-zinc-200 text-zinc-500 py-3 font-semibold text-sm cursor-not-allowed">
                                    Out of Stock
                                </button>
                            @endif
                        </div>
                    </div>
                </div>



                <!-- Customer Reviews Section -->
                <section class="mt-16 pt-12 border-t border-zinc-200">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
                        <!-- Left Column - Review Summary -->
                        <div class="lg:col-span-4 space-y-8">
                            <div>
                                <p class="text-[10px] font-semibold tracking-[0.15em] text-zinc-400 uppercase mb-4">Customer Reviews</p>
                                <div class="flex items-end gap-3 mb-3">
                                    <span class="text-5xl font-light text-black">{{ $avgRating > 0 ? number_format($avgRating, 1) : '0.0' }}</span>
                                    <span class="text-xl text-zinc-400 mb-2">/5</span>
                                </div>
                                <div class="flex items-center gap-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= floor($avgRating) ? 'text-black' : 'text-zinc-200' }} text-sm"></i>
                                    @endfor
                                </div>
                                <p class="text-[11px] font-medium tracking-[0.1em] text-zinc-500 uppercase">{{ $totalReviews }} {{ $totalReviews === 1 ? 'Review' : 'Reviews' }}</p>
                            </div>

                            <!-- Rating Breakdown -->
                            <div class="space-y-3">
                                @foreach($ratingBreakdown as $star => $percent)
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-zinc-600 w-8">{{ $star }}★</span>
                                    <div class="flex-1 h-1 bg-zinc-100 overflow-hidden">
                                        <div class="h-full bg-black transition-all duration-300" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="text-xs text-zinc-500 w-10 text-right">{{ $percent }}%</span>
                                </div>
                                @endforeach
                            </div>

                        </div>

                        <!-- Right Column - Reviews List -->
                        <div class="lg:col-span-8 space-y-6 min-w-0">
                            <!-- Header -->
                            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                                <h3 class="text-[11px] font-semibold tracking-[0.15em] text-black uppercase shrink-0">Reviews {{ $totalReviews }}</h3>
                                @auth
                                    <button onclick="openReviewModal()" class="bg-black text-white px-5 py-2.5 text-[10px] font-semibold tracking-[0.1em] uppercase transition duration-200 hover:bg-white hover:text-black border border-black shrink-0">
                                        Write a Review
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="bg-black text-white px-5 py-2.5 text-[10px] font-semibold tracking-[0.1em] uppercase transition duration-200 hover:bg-white hover:text-black border border-black shrink-0">
                                        Login to Review
                                    </a>
                                @endauth
                            </div>

                            <!-- Search & Filter -->
                            <div class="flex flex-wrap items-center gap-3 mb-8">
                                <input type="text" id="reviewSearch" placeholder="Search reviews" class="flex-1 min-w-[200px] px-4 py-2.5 border border-zinc-200 text-sm focus:outline-none focus:border-zinc-400 transition">
                                <select id="reviewRatingFilter" class="w-full sm:w-auto px-4 py-2.5 border border-zinc-200 text-sm focus:outline-none focus:border-zinc-400 transition bg-white shrink-0">
                                    <option value="all">All ratings</option>
                                    <option value="5">5 stars</option>
                                    <option value="4">4 stars</option>
                                    <option value="3">3 stars</option>
                                    <option value="2">2 stars</option>
                                    <option value="1">1 star</option>
                                </select>
                            </div>

                            <!-- Reviews List -->
                            @if($reviews->count() > 0)
                            <div class="space-y-0 max-h-[600px] overflow-y-auto pr-2" id="reviewsList">
                                @foreach($reviews as $review)
                                <div class="py-8 border-b border-zinc-100 last:border-0 review-item" data-rating="{{ $review->rating }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="text-xs font-semibold tracking-[0.05em] text-black uppercase">{{ $review->reviewer_name ?? $review->user->name }}</h4>
                                                @if($review->is_verified)
                                                <span class="text-[9px] text-zinc-400 uppercase tracking-wider">· Verified Buyer</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-black' : 'text-zinc-200' }} text-xs"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-[10px] text-zinc-400">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>

                                    @if($review->comment)
                                    <p class="text-sm text-zinc-600 leading-relaxed mb-4 review-text">{{ $review->comment }}</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="py-12 text-center">
                                <i class="fas fa-star text-4xl text-zinc-200 mb-3"></i>
                                <p class="text-sm text-zinc-500">No reviews yet. Be the first to review this product!</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </section>

                <!-- Related Products -->
                @if($relatedProducts->count() > 0)
                <div class="mt-16 pt-12 border-t border-zinc-200">
                    <div class="mb-6">
                        <h2 class="text-2xl font-semibold tracking-tight text-black">Produk Terkait</h2>
                        <p class="mt-2 text-zinc-600">Produk lain yang mungkin Anda suka</p>
                    </div>

                    <div class="flex gap-4 overflow-x-auto pb-3 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden snap-x snap-mandatory">
                        @foreach($relatedProducts as $related)
                        @php
                            $soldCount = \App\Models\OrderItem::where('product_id', $related->id)
                                ->whereHas('order', function($q) {
                                    $q->whereIn('status', ['completed', 'delivered']);
                                })->sum('quantity');
                        @endphp
                        <div class="product-item group snap-start shrink-0 basis-[40%] sm:basis-[48%] md:basis-[32%] lg:basis-[18%] overflow-hidden bg-white transition duration-300 hover:-translate-y-2"
                             data-name="{{ strtolower($related->name) }}"
                             data-price="{{ $related->hasActiveDiscount() ? $related->discounted_price : $related->price }}"
                             data-discount="{{ $related->hasActiveDiscount() ? 'yes' : 'no' }}"
                             data-bundle="{{ $related->package_type === 'bundle' ? 'yes' : 'no' }}"
                             data-sold="{{ $soldCount }}">
                            <a href="{{ $related->detail_url }}" class="block relative">
                                <div class="relative aspect-square overflow-hidden">
                                    <div class="h-full w-full overflow-hidden">
                                        <img src="{{ $related->image_url }}" alt="{{ $related->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" onerror="this.onerror=null; this.removeAttribute('srcset'); this.src=this.src;" loading="lazy">
                                    </div>
                                    <!-- Tags Kiri Atas -->
                                    <div class="absolute top-2 left-2 flex flex-col gap-1 items-start z-10 pointer-events-none">
                                        @if($related->hasActiveDiscount())
                                            <span style="background:#dc2626;color:#fff;font-size:8px;font-weight:700;padding:2px 6px;">
                                                Diskon {{ $related->formatted_discount_percent }}
                                            </span>
                                        @endif
                                        @if($related->package_type === 'bundle')
                                            <span style="background:#a855f7;color:#fff;font-size:8px;font-weight:600;padding:2px 6px;">Bundle</span>
                                        @endif
                                    </div>
                                    <!-- Tag Kanan Atas -->
                                    @if($related->isBestSeller())
                                        <span class="absolute right-0 top-0 pointer-events-none z-10" style="background:#f59e0b;color:#fff;font-size:8px;font-weight:600;padding:2px 6px;">Best Seller</span>
                                    @endif
                                </div>
                                <div class="p-2 md:p-4">
                                    <h3 class="line-clamp-1 text-sm font-medium text-black">{{ $related->name }}</h3>
                                    <p class="mt-1 text-xs text-zinc-600">{{ $related->category_label }}</p>
                                    @if($related->hasActiveDiscount())
                                        <p class="mt-1 text-base font-semibold text-black">{{ $related->formatted_discounted_price }}</p>
                                        <p class="text-xs text-zinc-400 line-through">{{ $related->formatted_price }}</p>
                                    @else
                                        <p class="mt-1 text-base font-semibold text-black">{{ $related->formatted_price }}</p>
                                    @endif
                                </div>
                            </a>
                            <div class="px-2 pb-2 md:px-4 md:pb-4">
                                <div class="flex items-center gap-2">
                                    <button onclick="event.preventDefault(); event.stopPropagation(); addToCart('{{ $related->slug }}', event)" class="border border-zinc-300 bg-transparent px-2 py-1 text-[10px] font-semibold text-zinc-800 transition duration-300 hover:border-zinc-500 hover:text-zinc-950">
                                        Add to cart
                                    </button>
                                    <button onclick="event.preventDefault(); event.stopPropagation(); addToWishlist('{{ $related->slug }}', event)" class="text-zinc-400 transition duration-300 hover:text-rose-500">
                                        <i class="fas fa-heart text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

@php
$schema = [
    '@context' => 'https://schema.org/',
    '@type' => 'Product',
    'name' => $product->name,
    'image' => $product->image_url,
    'description' => strip_tags($product->description ?? $product->name),
    'brand' => [
        '@type' => 'Brand',
        'name' => $product->brand ?? 'Hijab'
    ],
    'offers' => [
        '@type' => 'Offer',
        'price' => $product->hasActiveDiscount() ? $product->discounted_price : $product->price,
        'priceCurrency' => 'IDR',
        'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        // Kebijakan retur nyata dari halaman Return & Refund: 7 hari, ongkir retur ditanggung pembeli.
        'hasMerchantReturnPolicy' => [
            '@type' => 'MerchantReturnPolicy',
            'applicableCountry' => 'ID',
            'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
            'merchantReturnDays' => 7,
            'returnMethod' => 'https://schema.org/ReturnByMail',
            'returnFees' => 'https://schema.org/ReturnFeesCustomerResponsibility'
        ]
    ]
];

if ($totalReviews > 0) {
    $schema['aggregateRating'] = [
        '@type' => 'AggregateRating',
        'ratingValue' => round($avgRating, 1),
        'reviewCount' => $totalReviews
    ];
    
    // Tambahkan detail review agar lebih kuat di mata Google
    $schema['review'] = [];
    foreach ($reviews as $rev) {
        $schema['review'][] = [
            '@type' => 'Review',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $rev->rating,
                'bestRating' => '5',
                'worstRating' => '1'
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $rev->user ? $rev->user->name : 'Anonim'
            ],
            'reviewBody' => strip_tags($rev->content ?? 'Bagus'),
            'datePublished' => $rev->created_at->format('Y-m-d')
        ];
    }
}
@endphp
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>


@endsection

@push('styles')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    #mainNavbar,
    .mobile-bottom-nav {
        display: none !important;
    }

    .np-testimonial-hero-track {
        display: flex;
        gap: 0.75rem;
        transition: transform 700ms ease;
        will-change: transform;
    }

    .np-testimonial-hero-slide {
        position: relative;
        min-width: calc(100% - 2.5rem);
        overflow: hidden;
        border-radius: 1rem;
    }

    @media (min-width: 768px) {
        .np-testimonial-hero-slide {
            min-width: calc(100% - 7rem);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productData', () => ({
            variants: @json($product->variants),
            hasVariants: {{ $product->has_variants ? 'true' : 'false' }},
            selectedVariant: null,
            selectedSize: '',
            selectedColor: '',
            baseImages: @json($product->all_images),
            currentImages: [],
            activeImage: '',
            basePrice: {{ $product->price }},
            baseDiscountedPrice: {{ $product->discounted_price ?? $product->price }},
            hasDiscount: {{ $product->hasActiveDiscount() ? 'true' : 'false' }},
            
            get availableSizes() {
                let sizes = new Set();
                this.variants.forEach(v => {
                    let parts = v.name.split('|');
                    if (parts.length >= 1) sizes.add(parts[0].trim());
                });
                return Array.from(sizes);
            },
            
            get availableColors() {
                let colors = [];
                let colorNames = new Set();
                this.variants.forEach(v => {
                    let parts = v.name.split('|');
                    if (parts.length >= 3) {
                        let cName = parts[1].trim();
                        let cHex = parts[2].trim();
                        if (!colorNames.has(cName)) {
                            colorNames.add(cName);
                            colors.push({name: cName, hex: cHex});
                        }
                    }
                });
                return colors;
            },
            
            init() {
                if (this.hasVariants && this.variants.length > 0) {
                    let firstVar = this.variants.find(v => v.stock > 0) || this.variants[0];
                    
                    // Check URL for pre-selected size/color
                    const urlParams = new URLSearchParams(window.location.search);
                    const preSelectedSize = urlParams.get('size');
                    const preSelectedColorHex = urlParams.get('colorHex');
                    
                    if (preSelectedSize || preSelectedColorHex) {
                        let match = this.variants.find(v => {
                            let parts = v.name.split('|');
                            if (parts.length < 3) return false;
                            
                            let sizeMatch = true;
                            if (preSelectedSize && parts[0].trim() !== preSelectedSize) sizeMatch = false;
                            
                            let colorMatch = true;
                            // Make sure to decode the hex which might have # encoded as %23
                            let targetHex = preSelectedColorHex;
                            if (targetHex && !targetHex.startsWith('#') && urlParams.toString().includes('%23')) {
                                targetHex = '#' + targetHex;
                            }
                            
                            if (preSelectedColorHex && parts[2].trim() !== preSelectedColorHex && parts[2].trim() !== targetHex) colorMatch = false;
                            
                            return sizeMatch && colorMatch && v.stock > 0;
                        });
                        
                        if (!match) {
                            // If no exact match (or out of stock), try to just match size OR color
                            match = this.variants.find(v => {
                                let parts = v.name.split('|');
                                if (parts.length < 3 || v.stock <= 0) return false;
                                if (preSelectedSize && parts[0].trim() === preSelectedSize) return true;
                                if (preSelectedColorHex && (parts[2].trim() === preSelectedColorHex || parts[2].trim() === targetHex)) return true;
                                return false;
                            });
                        }
                        
                        if (match) {
                            firstVar = match;
                        }
                    }
                    
                    let varImages = [];
                    if (firstVar.image_url) varImages.push(firstVar.image_url);
                    if (firstVar.image_2_url) varImages.push(firstVar.image_2_url);
                    if (firstVar.image_3_url) varImages.push(firstVar.image_3_url);
                    if (firstVar.image_4_url) varImages.push(firstVar.image_4_url);
                    
                    if (varImages.length > 0) {
                        this.currentImages = varImages;
                    } else {
                        this.currentImages = [...this.baseImages];
                    }

                    this.selectedVariant = firstVar;
                    let parts = firstVar.name.split('|');
                    if(parts.length >= 3) {
                        this.selectedSize = parts[0].trim();
                        this.selectedColor = parts[1].trim();
                    }
                } else {
                    this.currentImages = [...this.baseImages];
                }
                this.activeImage = this.currentImages[0];
                
                this.$watch('selectedSize', () => this.updateSelectedVariant());
                this.$watch('selectedColor', () => this.updateSelectedVariant());
            },
            
            updateSelectedVariant() {
                if (this.selectedSize && this.selectedColor) {
                    let match = this.variants.find(v => {
                        let parts = v.name.split('|');
                        return parts.length >= 3 && parts[0].trim() === this.selectedSize && parts[1].trim() === this.selectedColor;
                    });
                    if (match) {
                        this.selectVariant(match);
                    } else {
                        this.selectedVariant = null;
                    }
                } else {
                    this.selectedVariant = null;
                }
            },

            selectVariant(variant) {
                this.selectedVariant = variant;
                
                // Construct variant images
                let varImages = [];
                if (variant.image_url) varImages.push(variant.image_url);
                if (variant.image_2_url) varImages.push(variant.image_2_url);
                if (variant.image_3_url) varImages.push(variant.image_3_url);
                if (variant.image_4_url) varImages.push(variant.image_4_url);
                
                if (varImages.length > 0) {
                    this.currentImages = varImages;
                } else {
                    this.currentImages = [...this.baseImages];
                }
                this.activeImage = this.currentImages[0];
            },

            get currentPrice() {
                if ({{ ($product->is_free_event && $isEligibleForFree) ? 'true' : 'false' }}) {
                    return 0;
                }
                if (this.selectedVariant) {
                    return parseFloat(this.selectedVariant.discounted_price || this.selectedVariant.final_price);
                }
                if (this.hasVariants && this.variants.length > 0) {
                    return parseFloat(this.variants[0].discounted_price || this.variants[0].final_price);
                }
                return this.hasDiscount ? this.baseDiscountedPrice : this.basePrice;
            },

            get originalPrice() {
                if (this.selectedVariant) {
                    return parseFloat(this.selectedVariant.price || this.basePrice);
                }
                if (this.hasVariants && this.variants.length > 0) {
                    return parseFloat(this.variants[0].price || this.basePrice);
                }
                return this.basePrice;
            },

            get currentDiscountPercent() {
                if (this.selectedVariant && this.selectedVariant.has_active_discount) {
                    return parseFloat(this.selectedVariant.discount_percent || 0);
                }
                if (this.hasVariants && this.variants.length > 0) {
                    let maxDisc = 0;
                    this.variants.forEach(v => {
                        if (v.has_active_discount) {
                            maxDisc = Math.max(maxDisc, parseFloat(v.discount_percent || 0));
                        }
                    });
                    if (maxDisc > 0) return maxDisc;
                }
                return {{ $product->discount_percent ?: 0 }};
            },

            get isCurrentDiscounted() {
                return this.currentDiscountPercent > 0;
            },

            get isFreeEventAndEligible() {
                return {{ ($product->is_free_event && $isEligibleForFree) ? 'true' : 'false' }};
            },

            get isVariantRequiredButNotSelected() {
                return this.hasVariants && this.variants.length > 0 && !this.selectedVariant;
            },

            formatRupiah(value) {
                if (value == 0) return 'Free';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
            },
        }));
    });

    // Add to Cart Function
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: 1 })
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
                showHijabToast('An error occurred. Please try again.', 'error');
            } else {
                alert('An error occurred. Please try again.');
            }
        });
    }

    // Add to Wishlist Function
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
                showHijabToast('An error occurred. Please try again.', 'error');
            } else {
                alert('An error occurred. Please try again.');
            }
        });
    }

(function() {
    const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenuToggle.setAttribute('aria-expanded', String(!mobileMenu.classList.contains('hidden')));
        });
    }

    // Search Dropdown Toggle
    window.toggleSearchDropdown = function() {
        const dropdown = document.getElementById('searchDropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                const navbarSearchInput = document.getElementById('searchInput');
                if (navbarSearchInput) {
                    navbarSearchInput.focus();
                    // Attach autocomplete listener if not already attached
                    if (!navbarSearchInput.hasAttribute('data-autocomplete-attached')) {
                        attachAutocompleteListener(navbarSearchInput);
                        navbarSearchInput.setAttribute('data-autocomplete-attached', 'true');
                    }
                }
            }
        }
    };

    // Close search dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('searchDropdown');
        const searchButton = event.target.closest('button[onclick="toggleSearchDropdown()"]');
        
        if (dropdown && !dropdown.contains(event.target) && !searchButton) {
            dropdown.classList.add('hidden');
        }
    });

    // Autocomplete Search Function
    function attachAutocompleteListener(searchInput) {
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`/api/products/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.products && data.products.length > 0) {
                            searchResults.innerHTML = data.products.map(product => `
                                <a href="${product.url}" class="flex items-center gap-3 p-2 hover:bg-zinc-100 rounded-lg transition">
                                    <img src="${product.image}" alt="${product.name}" class="w-12 h-12 object-cover rounded">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-black truncate">${product.name}</p>
                                        <p class="text-xs text-zinc-600">${product.category}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-black">${product.price}</p>
                                </a>
                            `).join('');
                            searchResults.classList.remove('hidden');
                        } else {
                            searchResults.innerHTML = '<p class="text-sm text-zinc-500 p-2 text-center">Tidak ada produk ditemukan</p>';
                            searchResults.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }, 300);
        });
    }

    // Product Filter & Search
    const searchInput = document.getElementById('searchProduct');
    const filterDiscount = document.getElementById('filterDiscount');
    const filterPriceBtn = document.getElementById('filterPrice');
    const priceRangeFilter = document.getElementById('priceRangeFilter');
    const applyPriceBtn = document.getElementById('applyPriceFilter');
    const resetPriceBtn = document.getElementById('resetPriceFilter');
    const minPriceInput = document.getElementById('minPrice');
    const maxPriceInput = document.getElementById('maxPrice');
    const productGrid = document.getElementById('productGrid');
    const noResults = document.getElementById('noResults');

    let minPrice = 0;
    let maxPrice = Infinity;

    if (filterPriceBtn && priceRangeFilter) {
        filterPriceBtn.addEventListener('click', () => {
            priceRangeFilter.classList.toggle('hidden');
        });
    }

    if (applyPriceBtn) {
        applyPriceBtn.addEventListener('click', () => {
            minPrice = parseInt(minPriceInput.value) || 0;
            maxPrice = parseInt(maxPriceInput.value) || Infinity;
            filterProducts();
        });
    }

    if (resetPriceBtn) {
        resetPriceBtn.addEventListener('click', () => {
            minPriceInput.value = '';
            maxPriceInput.value = '';
            minPrice = 0;
            maxPrice = Infinity;
            filterProducts();
        });
    }

    function filterProducts() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const discountFilter = filterDiscount ? filterDiscount.value : '';
        const bundleFilter = document.getElementById('filterBundle') ? document.getElementById('filterBundle').value : '';
        const popularFilter = document.getElementById('filterPopular') ? document.getElementById('filterPopular').value : '';
        const products = document.querySelectorAll('.product-item');
        let visibleCount = 0;

        products.forEach(product => {
            const name = product.dataset.name;
            const price = parseInt(product.dataset.price);
            const discount = product.dataset.discount;
            const bundle = product.dataset.bundle;
            const sold = parseInt(product.dataset.sold || 0);

            const matchSearch = name.includes(searchTerm);
            const matchDiscount = !discountFilter || discount === discountFilter;
            const matchPrice = price >= minPrice && price <= maxPrice;
            const matchBundle = !bundleFilter || bundle === bundleFilter;
            const matchPopular = !popularFilter || (popularFilter === 'yes' && sold > 10);

            if (matchSearch && matchDiscount && matchPrice && matchBundle && matchPopular) {
                product.style.display = 'block';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });

        if (productGrid && noResults) {
            if (visibleCount === 0) {
                productGrid.style.display = 'none';
                noResults.classList.remove('hidden');
            } else {
                productGrid.style.display = 'grid';
                noResults.classList.add('hidden');
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterProducts);
    }

    if (filterDiscount) {
        filterDiscount.addEventListener('change', filterProducts);
    }

    const filterBundleEl = document.getElementById('filterBundle');
    if (filterBundleEl) {
        filterBundleEl.addEventListener('change', filterProducts);
    }

    const filterPopularEl = document.getElementById('filterPopular');
    if (filterPopularEl) {
        filterPopularEl.addEventListener('change', filterProducts);
    }

    // Testimonial carousel
    const testimonialShowcase = document.querySelector('[data-testimonial-hero]');
    if (testimonialShowcase) {
        const viewport = testimonialShowcase;
        const track = testimonialShowcase.querySelector('[data-testimonial-track]');
        const dots = testimonialShowcase.querySelectorAll('.np-testimonial-dot');

        if (viewport && track && dots.length > 0) {
            let currentSlide = 0;
            const totalSlides = dots.length;
            const slides = track.querySelectorAll('.np-testimonial-hero-slide');
            let intervalId;

            const getTranslateX = (slideIndex) => {
                const slide = slides[slideIndex];
                if (!slide) return 0;

                const viewportWidth = viewport.clientWidth;
                const slideWidth = slide.clientWidth;
                const centeredOffset = slide.offsetLeft - ((viewportWidth - slideWidth) / 2);
                const maxOffset = Math.max(track.scrollWidth - viewportWidth, 0);

                return Math.min(Math.max(centeredOffset, 0), maxOffset);
            };

            const setActiveSlide = (index) => {
                currentSlide = (index + totalSlides) % totalSlides;
                track.style.transform = `translateX(-${getTranslateX(currentSlide)}px)`;
                dots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('bg-white', dotIndex === currentSlide);
                    dot.classList.toggle('bg-white/45', dotIndex !== currentSlide);
                });
            };

            const startAutoplay = () => {
                intervalId = window.setInterval(() => {
                    setActiveSlide(currentSlide + 1);
                }, 3600);
            };

            const stopAutoplay = () => {
                if (intervalId) {
                    window.clearInterval(intervalId);
                }
            };

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    setActiveSlide(index);
                    stopAutoplay();
                    startAutoplay();
                });
            });

            setActiveSlide(0);
            startAutoplay();

            window.addEventListener('resize', () => setActiveSlide(currentSlide));
        }
    }
})();

// Review Modal
function openReviewModal() {
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

// Handle star rating selection
function selectRating(rating) {
    document.querySelectorAll('.star-rating i').forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-zinc-200');
            star.classList.add('text-black');
        } else {
            star.classList.remove('text-black');
            star.classList.add('text-zinc-200');
        }
    });
    document.getElementById('ratingInput').value = rating;
}

// Review Filter: Search + Rating
(function() {
    const searchInput = document.getElementById('reviewSearch');
    const ratingSelect = document.getElementById('reviewRatingFilter');
    const reviewsList = document.getElementById('reviewsList');
    if (!searchInput || !ratingSelect || !reviewsList) return;

    const items = reviewsList.querySelectorAll('.review-item');

    function filterReviews() {
        const query = searchInput.value.toLowerCase().trim();
        const rating = ratingSelect.value;

        items.forEach(item => {
            const textEl = item.querySelector('.review-text');
            const text = textEl ? textEl.textContent.toLowerCase() : '';
            const nameEl = item.querySelector('h4');
            const reviewerName = nameEl ? nameEl.textContent.toLowerCase() : '';
            const itemRating = item.getAttribute('data-rating');

            const matchesSearch = !query || text.includes(query) || reviewerName.includes(query);
            const matchesRating = rating === 'all' || itemRating === rating;

            item.style.display = (matchesSearch && matchesRating) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterReviews);
    ratingSelect.addEventListener('change', filterReviews);
})();
</script>

<!-- Review Modal -->
<div id="reviewModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-xl">
        <div class="flex items-center justify-between p-6 border-b border-zinc-200">
            <h3 class="text-lg font-semibold text-black">Write a Review</h3>
            <button onclick="closeReviewModal()" class="text-zinc-400 hover:text-black transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="reviewForm" class="p-4 space-y-3">
            <!-- Rating -->
            <div>
                <label class="block text-sm font-medium text-black mb-2">Rating *</label>
                <div class="flex gap-2 star-rating cursor-pointer">
                    <i class="fas fa-star text-2xl text-zinc-200 hover:text-black transition" onclick="selectRating(1)"></i>
                    <i class="fas fa-star text-2xl text-zinc-200 hover:text-black transition" onclick="selectRating(2)"></i>
                    <i class="fas fa-star text-2xl text-zinc-200 hover:text-black transition" onclick="selectRating(3)"></i>
                    <i class="fas fa-star text-2xl text-zinc-200 hover:text-black transition" onclick="selectRating(4)"></i>
                    <i class="fas fa-star text-2xl text-zinc-200 hover:text-black transition" onclick="selectRating(5)"></i>
                </div>
                <input type="hidden" id="ratingInput" name="rating" required>
            </div>

            <!-- Comment -->
            <div>
                <label for="comment" class="block text-sm font-medium text-black mb-2">Comment</label>
                <textarea id="comment" name="comment" rows="3" class="w-full px-4 py-2.5 border border-zinc-200 rounded-lg text-sm focus:outline-none focus:border-zinc-400 transition" placeholder="Share your experience with this product..."></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-semibold text-sm hover:bg-black/90 transition">
                Submit Review
            </button>
        </form>
    </div>
</div>

<script>
// Review form submit handler — must be after the modal HTML
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const rating = parseInt(formData.get('rating'));
    const comment = formData.get('comment');

    if (!rating || rating < 1 || rating > 5) {
        alert('Silakan pilih rating terlebih dahulu.');
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    const payload = {
        rating: rating,
        comment: comment
    };

    // Only include optional fields if they have values
    const qualityRating = formData.get('quality_rating');
    if (qualityRating) payload.quality_rating = parseInt(qualityRating);

    const sizingRating = formData.get('sizing_rating');
    if (sizingRating) payload.sizing_rating = parseInt(sizingRating);

    const usualSize = formData.get('usual_size');
    if (usualSize) payload.usual_size = usualSize;

    fetch('{{ route("customer.reviews.store", $product) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                try {
                    const error = JSON.parse(text);
                    throw error;
                } catch(e) {
                    console.error('Response text:', text);
                    throw { message: text || 'Server error: ' + response.status };
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const reviewData = data.review;
            const starsHtml = Array.from({length: 5}, (_, i) => 
                `<i class="fas fa-star ${i < reviewData.rating ? 'text-black' : 'text-zinc-200'} text-xs"></i>`
            ).join('');

            const newReviewHtml = `
                <div class="py-8 border-b border-zinc-100 last:border-0 review-item" data-rating="${reviewData.rating}">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="text-xs font-semibold tracking-[0.05em] text-black uppercase">${reviewData.user_name}</h4>
                            </div>
                            <div class="flex items-center gap-1">${starsHtml}</div>
                        </div>
                        <span class="text-[10px] text-zinc-400">Just now</span>
                    </div>
                    ${reviewData.comment ? `<p class="text-sm text-zinc-600 leading-relaxed mb-4 review-text">${reviewData.comment}</p>` : ''}
                </div>
            `;

            let reviewsList = document.getElementById('reviewsList');
            if (!reviewsList) {
                const emptyState = document.querySelector('.py-12.text-center');
                if (emptyState) {
                    const container = document.createElement('div');
                    container.className = 'space-y-0 max-h-[600px] overflow-y-auto pr-2';
                    container.id = 'reviewsList';
                    emptyState.replaceWith(container);
                    reviewsList = container;
                }
            }

            if (reviewsList) {
                reviewsList.insertAdjacentHTML('afterbegin', newReviewHtml);
            }

            closeReviewModal();
            document.getElementById('reviewForm').reset();
            document.querySelectorAll('.star-rating i').forEach(s => {
                s.classList.remove('text-black');
                s.classList.add('text-zinc-200');
            });

            alert('Review berhasil ditambahkan!');
        } else {
            alert(data.message || 'Gagal mengirim review.');
        }
    })
    .catch(error => {
        console.error('Review error:', error);
        alert(error.message || 'Terjadi kesalahan. Pastikan Anda sudah login.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Review';
    });
});

function toggleSpecs() {
    const additionalSpecs = document.getElementById('additionalSpecs');
    const toggleText = document.getElementById('toggleText');
    const toggleIcon = document.getElementById('toggleIcon');

    if (additionalSpecs.classList.contains('hidden')) {
        additionalSpecs.classList.remove('hidden');
        toggleText.textContent = 'View Less';
        toggleIcon.style.transform = 'rotate(180deg)';
    } else {
        additionalSpecs.classList.add('hidden');
        toggleText.textContent = 'View More';
        toggleIcon.style.transform = 'rotate(0deg)';
    }
}
</script>
@endpush
