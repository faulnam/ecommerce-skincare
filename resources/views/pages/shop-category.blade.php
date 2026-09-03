@extends('layouts.app')

@section('title', 'Katalog Produk - LUMINA Skincare')
@section('og_description', 'Jelajahi koleksi perawatan kulit LUMINA Skincare berdasarkan kategori — serum, moisturizer, cleanser, toner, dan sunscreen terbaik.')

@section('content')
<div class="min-h-screen bg-white">
    <div class="bg-zinc-900 py-16 text-white">
        <div class="mx-auto max-w-7xl px-6 md:px-10 lg:px-12">
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">Koleksi Produk</h1>
            <p class="mt-4 text-lg text-zinc-300">Temukan formula skincare terbaik untuk kesehatan kulit Anda</p>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-12 md:px-10 lg:px-12">
        <!-- Category Filter -->
        <div class="mb-8 flex flex-wrap gap-3">
            <a href="{{ route('shop.category') }}" 
                class="rounded-full px-6 py-2.5 text-sm font-semibold transition duration-300 {{ !request('category') ? 'bg-black text-white' : 'border border-black/15 bg-white text-black hover:bg-black hover:text-white' }}">
                Semua Produk
            </a>
            <a href="{{ route('shop.category', ['category' => 'serum']) }}" 
                class="rounded-full px-6 py-2.5 text-sm font-semibold transition duration-300 {{ request('category') === 'serum' ? 'bg-black text-white' : 'border border-black/15 bg-white text-black hover:bg-black hover:text-white' }}">
                Serum
            </a>
            <a href="{{ route('shop.category', ['category' => 'moisturizer']) }}" 
                class="rounded-full px-6 py-2.5 text-sm font-semibold transition duration-300 {{ request('category') === 'moisturizer' ? 'bg-black text-white' : 'border border-black/15 bg-white text-black hover:bg-black hover:text-white' }}">
                Moisturizer
            </a>
            <a href="{{ route('shop.category', ['category' => 'cleanser']) }}" 
                class="rounded-full px-6 py-2.5 text-sm font-semibold transition duration-300 {{ request('category') === 'cleanser' ? 'bg-black text-white' : 'border border-black/15 bg-white text-black hover:bg-black hover:text-white' }}">
                Cleanser
            </a>
            <a href="{{ route('shop.category', ['category' => 'sunscreen']) }}" 
                class="rounded-full px-6 py-2.5 text-sm font-semibold transition duration-300 {{ request('category') === 'sunscreen' ? 'bg-black text-white' : 'border border-black/15 bg-white text-black hover:bg-black hover:text-white' }}">
                Sunscreen
            </a>
            <a href="{{ route('shop.category', ['category' => 'bundle']) }}" 
                class="rounded-full px-6 py-2.5 text-sm font-semibold transition duration-300 {{ request('category') === 'bundle' ? 'bg-black text-white' : 'border border-black/15 bg-white text-black hover:bg-black hover:text-white' }}">
                Bundle
            </a>
        </div>

        @if($products->count() > 0)
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                @foreach($products as $product)
                    @php
                        $soldCount = \App\Models\OrderItem::where('product_id', $product->id)
                            ->whereHas('order', function($q) {
                                $q->whereIn('status', ['completed', 'delivered']);
                            })->sum('quantity');
                    @endphp
                    <a href="{{ $product->detail_url }}" class="group block overflow-hidden rounded-xl border border-black/6 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <div class="relative aspect-square overflow-hidden">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" onerror="this.onerror=null; this.removeAttribute('srcset'); this.src=this.src;" loading="lazy">
                            @if($product->hasActiveDiscount())
                                <span class="absolute left-2 top-2 rounded-full bg-rose-500 px-2 py-0.5 text-[10px] font-semibold text-white">-{{ $product->formatted_discount_percent }}</span>
                            @endif
                            @if($product->category === 'arrivals')
                                <span class="absolute left-2 {{ $product->hasActiveDiscount() ? 'top-9' : 'top-2' }} rounded-full bg-blue-500 px-2 py-0.5 text-[10px] font-semibold text-white">Latest</span>
                            @endif
                            @if($product->package_type === 'bundle')
                                <span class="absolute left-2 {{ $product->hasActiveDiscount() && $product->category === 'arrivals' ? 'top-16' : ($product->hasActiveDiscount() || $product->category === 'arrivals' ? 'top-9' : 'top-2') }} rounded-full bg-purple-500 px-2 py-0.5 text-[10px] font-semibold text-white">Bundle</span>
                            @endif
                            @if($soldCount >= 5 || $product->package_type === 'bestseller')
                                <span class="absolute right-2 top-2 rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-semibold text-white">Best Seller</span>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="line-clamp-1 text-sm font-semibold text-black">{{ $product->name }}</h3>
                            <p class="mt-1 text-xs text-zinc-600">{{ $product->category_label }}</p>
                            @if($product->hasActiveDiscount())
                                <p class="mt-1 text-base font-bold text-black">{{ $product->formatted_discounted_price }}</p>
                                <p class="text-xs text-zinc-400 line-through">{{ $product->formatted_price }}</p>
                            @else
                                <p class="mt-1 text-base font-bold text-black">{{ $product->formatted_price }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $products->links() }}
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-12 text-center">
                <i class="fas fa-box-open text-4xl text-zinc-400"></i>
                <p class="mt-4 text-lg font-medium text-zinc-600">No products found</p>
                <p class="mt-2 text-sm text-zinc-500">Try selecting a different category</p>
            </div>
        @endif
    </div>
</div>
@endsection
