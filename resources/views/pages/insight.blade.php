@extends('layouts.app')

@section('title', 'Insight - LUMINA')
@section('og_description', 'Insight dan artikel seputar dunia kecantikan & skincare dari LUMINA. Tips, trik, review produk, dan berita terbaru untuk para pecinta kulit sehat.')

@push('og_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "CollectionPage",
  "url": "{{ url()->current() }}",
  "name": "{{ $lang === 'id' ? 'Insight — LUMINA' : 'Insight — LUMINA' }}",
  "description": "{{ $lang === 'id' ? 'Insight dan artikel seputar dunia kecantikan & skincare dari LUMINA. Tips, trik, review produk, dan berita terbaru.' : 'Insights and articles about the world of skincare from LUMINA. Tips, tricks, product reviews, and latest news.' }}",
  "inLanguage": "{{ $lang === 'id' ? 'id-ID' : 'en-US' }}",
  "isPartOf": { "@id": "{{ url('/') }}/#website" },
  "publisher": { "@id": "{{ url('/') }}/#organization" }
}
</script>
@endpush

@section('content')
<style>
    .mobile-bottom-nav { display: none !important; }
    #mainNavbar { display: none !important; }
</style>
<div class="bg-white text-black antialiased min-h-screen">
    @include('components.luxury-navbar')
    
    <main class="bg-white pt-6 md:pt-32 pb-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 md:px-10 lg:px-12">

            @if($insights->count() > 0)
                <!-- Grid 5 columns -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                    @foreach($insights as $insight)
                    <article class="group relative flex flex-col items-start justify-start border border-zinc-100 rounded-xl overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="relative w-full">
                            <div class="aspect-[4/3] w-full overflow-hidden bg-zinc-100 relative">
                                <img src="{{ $insight->image_url }}" alt="{{ $insight->title }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            </div>
                        </div>
                        <div class="p-3 w-full flex flex-col flex-1">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <time datetime="{{ $insight->published_at->format('Y-m-d') }}" class="text-zinc-400">{{ $insight->published_at->format('M d') }}</time>
                                <div class="flex items-center gap-x-1 text-zinc-400">
                                    <i class="fas fa-eye"></i>
                                    <span>{{ number_format($insight->views) }}</span>
                                </div>
                            </div>
                            <h3 class="text-sm font-semibold leading-tight text-black group-hover:text-zinc-600 mb-2 line-clamp-2">
                                <a href="{{ route('insight.show', $insight) }}">
                                    <span class="absolute inset-0"></span>
                                    {{ $insight->title }}
                                </a>
                            </h3>
                            <p class="mt-auto pt-1 line-clamp-3 text-xs leading-relaxed text-zinc-500">{{ $insight->excerpt ?? Str::limit(strip_tags($insight->content), 90) }}</p>
                        </div>
                    </article>
                    @endforeach
                </div>

                @if($insights->hasPages())
                    <div class="mt-12 flex justify-center">
                        {{ $insights->appends(request()->query())->links('pagination.luxury') }}
                    </div>
                @endif
            @else
                <div class="rounded-3xl border border-dashed border-zinc-300 bg-zinc-50 py-20 text-center">
                    <i class="fas fa-newspaper text-3xl text-zinc-300 mb-3"></i>
                    <h3 class="text-base font-medium text-zinc-900">Belum ada artikel</h3>
                    <p class="mt-1 text-xs text-zinc-500">Belum ada artikel insight yang dipublikasikan.</p>
                </div>
            @endif

        </div>
    </main>
</div>
@endsection
