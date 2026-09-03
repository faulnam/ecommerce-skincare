@extends('layouts.app')

@php
    $jsonPath = public_path('translation/brandcatalog.json');
    $brandCatalog = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@section('title', $brandCatalog['meta_title'][$lang] ?? 'Brand Catalog')
@section('og_description', $brandCatalog['meta_description'][$lang] ?? 'Katalog brand skincare pilihan di LUMINA. Temukan produk dari brand-brand terpercaya dan terbaik di dunia kecantikan & skincare.')

@section('content')
    @include('components.luxury-navbar')

    <main class="pt-0 md:pt-16">
         <section class="bg-white pt-8 pb-14 lg:pt-10 lg:pb-16">
            <div class="mx-auto w-full max-w-7xl px-6 md:px-10 lg:px-12">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">{{ $brandCatalog['badge'][$lang] ?? 'Our Partners' }}</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">{{ $brandCatalog['hero_title'][$lang] ?? 'Brand Catalog' }}</h1>
                    <p class="mt-4 text-zinc-600">{{ $brandCatalog['hero_desc'][$lang] ?? 'Explore official catalogs from premium skincare brands we carry.' }}</p>
                </div>

                @if($catalogs->isEmpty())
                    <div class="mt-12 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100">
                            <i class="fas fa-book text-zinc-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-zinc-700">{{ $brandCatalog['empty_title'][$lang] ?? 'No catalogs yet' }}</h3>
                        <p class="mt-1 text-sm text-zinc-500">{{ $brandCatalog['empty_desc'][$lang] ?? 'Brand catalogs will appear here once available.' }}</p>
                    </div>
                @else
                    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($catalogs as $catalog)
                            <div class="group flex h-full flex-col rounded-2xl border border-black/10 bg-white p-5 shadow-sm transition hover:shadow-md">
                                <a href="{{ route('brand.show', ['slug' => \Illuminate\Support\Str::slug($catalog->brand_name)]) }}" class="relative mb-4 flex h-40 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-zinc-50 block">
                                    @if($catalog->cover_image)
                                        <img src="{{ $catalog->cover_image_url }}" alt="{{ $catalog->brand_name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    @else
                                        <div class="flex flex-col items-center justify-center text-zinc-400">
                                            <i class="fas fa-image text-3xl"></i>
                                            <span class="mt-2 text-xs">{{ $brandCatalog['no_cover'][$lang] ?? 'No cover' }}</span>
                                        </div>
                                    @endif
                                </a>

                                <!-- Content -->
                                <div class="flex-grow">
                                    <a href="{{ route('brand.show', ['slug' => \Illuminate\Support\Str::slug($catalog->brand_name)]) }}">
                                        <h2 class="text-xl font-bold text-zinc-900 transition-colors group-hover:text-black hover:underline">{{ $catalog->brand_name }}</h2>
                                    </a>
                                    @if($catalog->description)
                                        <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-zinc-500">
                                            {{ $catalog->description }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="mt-auto border-t border-zinc-100 pt-6">
                                    @php
                                        $hasAnyPdf = $catalog->pdf_path || collect($catalog->pdf_files ?? [])->filter()->isNotEmpty();
                                    @endphp
                                    
                                    @if($hasAnyPdf)
                                        <div class="flex items-stretch gap-2" data-catalog-id="{{ $catalog->id }}">
                                            <div class="relative min-w-0 flex-grow">
                                                <select class="catalog-category-select w-full appearance-none overflow-hidden text-ellipsis rounded-xl border border-zinc-200 bg-white py-2.5 pl-4 pr-10 text-sm font-semibold text-zinc-700 transition-all hover:border-zinc-300 focus:border-black focus:outline-none focus:ring-4 focus:ring-black/5">
                                                    @if($catalog->pdf_path)
                                                        <option value="legacy" data-url="{{ $catalog->pdf_url }}">Full Catalog</option>
                                                    @endif
                                                    @foreach(\App\Models\BrandCatalog::$categories as $key => $label)
                                                        @if($catalog->hasCategoryPdf($key))
                                                            <option value="{{ $key }}" data-url="{{ $catalog->getCategoryPdfUrl($key) }}">{{ $label }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-400">
                                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                                </div>
                                            </div>
                                            
                                            <a href="#" class="catalog-download-btn inline-flex items-center justify-center gap-2 rounded-xl bg-black px-5 py-2.5 text-sm font-semibold text-white transition-all hover:bg-zinc-800 hover:shadow-lg active:scale-95"
                                               target="_blank" download>
                                                <i class="fas fa-download text-[11px]"></i>
                                                <span>Download</span>
                                            </a>
                                        </div>
                                    @else
                                        <div class="flex w-full">
                                            <div class="inline-flex w-auto items-center justify-center gap-2 rounded-full border border-zinc-100 bg-zinc-100 px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-zinc-400">
                                                <i class="fas fa-clock text-[10px]"></i>
                                                {{ $brandCatalog['status_soon'][$lang] ?? 'Catalog Coming Soon' }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- SEO Footer Text -->
            <div class="mx-auto mt-16 max-w-4xl px-6 text-center text-sm text-zinc-500 md:px-10 lg:px-12">
                <p>
                    Sebagai toko produk perawatan kulit terpercaya, LUMINA menyediakan e-katalog resmi dari berbagai merek produk perawatan kulit terkemuka. 
                    Anda dapat mengunduh katalog PDF untuk melihat spesifikasi detail skincare skincare, sepatu, bola, dan aksesori terbaru. 
                    Setiap brand di atas memiliki dedikasi tinggi terhadap olahraga skincare, memastikan performa dan durabilitas terbaik di lapangan.
                </p>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "ItemList",
  "name": "LUMINA Official Brand Catalogs",
  "description": "Download e-katalog resmi dari berbagai brand skincare dunia seperti Nox, Bullskincare, Babolat.",
  "itemListElement": [
    @foreach($catalogs as $index => $catalog)
    {
      "@@type": "ListItem",
      "position": {{ $index + 1 }},
      "item": {
        "@@type": "Brand",
        "name": "{{ $catalog->brand_name }}",
        "url": "{{ route('brand.show', ['slug' => \Illuminate\Support\Str::slug($catalog->brand_name)]) }}"
      }
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.catalog-category-select').forEach(function(select) {
        const wrapper = select.closest('[data-catalog-id]');
        const btn = wrapper.querySelector('.catalog-download-btn');

        function updateUrl() {
            const selected = select.options[select.selectedIndex];
            const url = selected.getAttribute('data-url');
            if (url) {
                btn.setAttribute('href', url);
                // Set the download attribute to the filename to ensure it saves correctly
                const filename = url.substring(url.lastIndexOf('/') + 1);
                btn.setAttribute('download', filename);
            }
        }

        select.addEventListener('change', updateUrl);
        updateUrl();
    });
});
</script>
@endpush

