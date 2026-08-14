@once
    <style>
        .product-item:hover .size-popup { opacity: 1 !important; }
    </style>
@endonce

<div class="product-item group cursor-pointer flex flex-col relative bg-white transition duration-300 hover:-translate-y-1" onclick="window.location.href='{{ $product->detail_url }}'">
    <div class="relative w-full bg-gray-200 overflow-hidden mb-4" style="aspect-ratio: 3 / 4;">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" onerror="this.onerror=null; this.removeAttribute('srcset'); this.src=this.src;" loading="lazy">
        
        @if($product->hasActiveDiscount())
        <div class="absolute text-white font-bold uppercase tracking-wider z-10 text-[8px] px-1.5 py-0.5" style="top: 12px; left: 12px; background-color: #e53e3e; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
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
        <div class="size-popup absolute flex justify-between bg-white shadow-sm opacity-0 transition-opacity duration-300 ease-in-out group-hover:opacity-100" style="bottom: 16px; left: 16px; right: 16px;">
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
                <div class="flex-1 text-center py-2 text-xs font-semibold hover:bg-gray-100" style="border-right: 1px solid #f3f4f6;" onclick="event.stopPropagation(); window.location.href='{{ $product->detail_url }}?size={{ urlencode(trim($size)) }}'">{{ trim($size) }}</div>
            @empty
                <div class="flex-1 text-center py-2 text-xs font-semibold hover:bg-gray-100" onclick="event.stopPropagation(); window.location.href='{{ $product->detail_url }}'">All Size</div>
            @endforelse
        </div>
    </div>
    
    <div class="flex-1 flex flex-col justify-start text-left px-1 pb-4">
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
                <div class="rounded-full cursor-pointer" style="width: 16px; height: 16px; background-color: {{ $color }}; border: 2px solid white; box-shadow: 0 0 0 1px #d1d5db;" onclick="event.stopPropagation(); window.location.href='{{ $product->detail_url }}?colorHex={{ urlencode(trim($color)) }}'"></div>
            @endforeach
        </div>
    </div>
</div>
