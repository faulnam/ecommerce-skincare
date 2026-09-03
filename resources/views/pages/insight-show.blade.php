@extends('layouts.app')

@section('title', $insight->meta_title ?? $insight->title)
@section('og_type', 'article')
@section('og_title', $insight->meta_title ?? $insight->title)
@section('og_description'){{ Str::limit(strip_tags($insight->meta_description ?? $insight->content), 160) }}@endsection
@section('og_image', $insight->image ? $insight->image_url : asset('images/logo copy.png'))
@section('og_url', url()->current())
@section('og_image_alt', $insight->alt_image ?? $insight->title)
@push('og_extra')
    <meta property="article:published_time" content="{{ $insight->published_at->toIso8601String() }}">
    <meta property="article:author" content="{{ $insight->author }}">
    <meta property="article:section" content="LUMINA">
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "NewsArticle",
  "url": "{{ url()->current() }}",
  "headline": "{{ $insight->title }}",
  "description": "{{ Str::limit(strip_tags($insight->excerpt ?? $insight->content), 160) }}",
  "datePublished": "{{ $insight->published_at->toIso8601String() }}",
  "dateModified": "{{ ($insight->updated_at ?? $insight->published_at)->toIso8601String() }}",
  "author": {
    "@type": "Person", 
    "name": "{{ $insight->author }}"
  },
  "publisher": { "@id": "{{ url('/') }}/#organization" },
  "isPartOf": { "@id": "{{ url('/') }}/#website" },
  @if($insight->image)
  "image": {
    "@type": "ImageObject",
    "url": "{{ $insight->image_url }}",
    "caption": "{{ $insight->title }}"
  },
  @endif
  "articleSection": "LUMINA",
  "inLanguage": "{{ $lang === 'id' ? 'id-ID' : 'en-US' }}"
}
</script>
@endpush

@section('content')
<style>
    .mobile-bottom-nav { display: none !important; }
    #mainNavbar { display: none !important; }

    /* Modern Editorial Typography for the Article */
    .prose {
        font-size: 1.05rem;
    }
    .prose img {
        border-radius: 1rem;
        margin-top: 2.5rem;
        margin-bottom: 2.5rem;
        width: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .prose h2 {
        font-size: 1.75rem;
        font-weight: 800;
        margin-top: 3rem;
        margin-bottom: 1.25rem;
        color: #111827;
        letter-spacing: -0.025em;
    }
    .prose h3 {
        font-size: 1.35rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #1f2937;
    }
    .prose p {
        margin-bottom: 1.5rem;
        color: #374151;
        line-height: 1.8;
    }
    .prose a {
        color: #f43f5e;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
    }
    .prose a:hover {
        text-decoration: underline;
        color: #e11d48;
    }
    .prose blockquote {
        border-left: 4px solid #111827;
        padding-left: 1.5rem;
        font-style: italic;
        color: #4b5563;
        margin-top: 2rem;
        margin-bottom: 2rem;
        background: #f9fafb;
        padding: 1.5rem;
        border-radius: 0 1rem 1rem 0;
    }
</style>

<div class="bg-white text-black antialiased min-h-screen">
    @include('components.luxury-navbar')

    <main class="bg-white pt-6 md:pt-32 pb-16">
        <article class="mx-auto w-full max-w-7xl px-4 sm:px-6 md:px-10 lg:px-12">

            <div class="flex flex-col lg:flex-row gap-10 lg:gap-16">

                <div class="lg:w-8/12 xl:w-8/12">

                    <header class="mb-8">
                        <div class="flex items-center gap-x-3 text-xs mb-5 uppercase tracking-wider font-bold text-blue-600">
                            <span>Insight & News</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                            <div class="flex items-center gap-1.5 text-zinc-500">
                                <i class="fas fa-eye text-zinc-400"></i>
                                <span>{{ number_format($insight->views) }} views</span>
                            </div>
                        </div>

                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight text-black mb-6 leading-[1.15]">{{ $insight->title }}</h1>

                        <div class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-zinc-100">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-full bg-zinc-800 flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr($insight->author, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-black">{{ $insight->author }}</p>
                                    <div class="flex items-center gap-2 text-xs text-zinc-500 mt-0.5">
                                        <span>LUMINA Team</span>
                                        <span>•</span>
                                        <time datetime="{{ $insight->published_at->format('Y-m-d') }}">{{ $insight->published_at->format('M d, Y') }}</time>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="h-10 w-10 flex items-center justify-center rounded-full bg-zinc-50 border border-zinc-200 text-zinc-600 hover:bg-[#1877F2] hover:text-white hover:border-[#1877F2] transition duration-300">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($insight->title) }}" target="_blank" class="h-10 w-10 flex items-center justify-center rounded-full bg-zinc-50 border border-zinc-200 text-zinc-600 hover:bg-black hover:text-white hover:border-black transition duration-300">
                                    <i class="fa-brands fa-x-twitter"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($insight->title . ' ' . request()->fullUrl()) }}" target="_blank" class="h-10 w-10 flex items-center justify-center rounded-full bg-zinc-50 border border-zinc-200 text-zinc-600 hover:bg-[#25D366] hover:text-white hover:border-[#25D366] transition duration-300">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->fullUrl()) }}&title={{ urlencode($insight->title) }}" target="_blank" class="h-10 w-10 flex items-center justify-center rounded-full bg-zinc-50 border border-zinc-200 text-zinc-600 hover:bg-[#0A66C2] hover:text-white hover:border-[#0A66C2] transition duration-300">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </header>

                    <div class="mb-10 w-full">
                        @if($insight->image)
                        <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-zinc-100 shadow-lg border border-zinc-100">
                            <img src="{{ $insight->image_url }}" alt="{{ $insight->title }}" class="absolute inset-0 w-full h-full object-cover">
                        </div>
                        @else
                        <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-zinc-50 border border-zinc-200 flex items-center justify-center shadow-sm">
                            <i class="fas fa-newspaper text-6xl text-zinc-300"></i>
                        </div>
                        @endif
                        <p class="text-[10px] text-zinc-400 mt-3 text-right uppercase tracking-wider font-medium">Image Credit: LUMINA</p>
                    </div>

                    <div class="prose max-w-none text-zinc-800">
                        {!! $insight->content !!}
                    </div>

                    <div class="mt-12 pt-6 border-t border-zinc-200">
                        <div class="flex flex-wrap gap-2">
                            <span class="px-4 py-1.5 bg-zinc-100 text-zinc-600 text-xs font-bold uppercase tracking-wider rounded-full hover:bg-zinc-200 transition cursor-pointer">LUMINA</span>
                            <span class="px-4 py-1.5 bg-zinc-100 text-zinc-600 text-xs font-bold uppercase tracking-wider rounded-full hover:bg-zinc-200 transition cursor-pointer">Sports</span>
                            <span class="px-4 py-1.5 bg-zinc-100 text-zinc-600 text-xs font-bold uppercase tracking-wider rounded-full hover:bg-zinc-200 transition cursor-pointer">News</span>
                        </div>
                    </div>

                </div>

                <aside class="lg:w-4/12 xl:w-4/12">
                    <div class="sticky top-28">

                        <div class="mb-6 flex items-center justify-between border-b border-black pb-3">
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-2 bg-rose-500 rounded-full animate-pulse"></div>
                                <h2 class="text-xl font-bold tracking-tight text-black">Artikel Terkait</h2>
                            </div>

                            <div class="flex items-center gap-2" id="sidebarNav" style="display: none;">
                                <button type="button" id="sidebarPrev" class="h-7 w-7 rounded-full border border-zinc-200 flex items-center justify-center text-zinc-500 hover:text-black hover:border-black hover:bg-zinc-50 transition disabled:opacity-30 disabled:cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </button>
                                <button type="button" id="sidebarNext" class="h-7 w-7 rounded-full border border-zinc-200 flex items-center justify-center text-zinc-500 hover:text-black hover:border-black hover:bg-zinc-50 transition disabled:opacity-30 disabled:cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        @if($relatedInsights->count() > 0)
                            <div class="flex flex-col gap-5 min-h-[360px]" id="sidebarList">
                                @foreach($relatedInsights as $index => $related)
                                <a href="{{ route('insight.show', $related) }}" class="sidebar-item group flex items-start gap-4 pb-2 border-b border-zinc-100 last:border-0 last:pb-0 transition-opacity duration-300" data-index="{{ $index }}">

                                    <div class="w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 rounded-xl overflow-hidden bg-zinc-100 relative shadow-sm border border-zinc-100">
                                        <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                    </div>

                                    <div class="flex-1 flex flex-col pt-1">
                                        <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-blue-600 transition duration-300 line-clamp-3">
                                            {{ $related->title }}
                                        </h3>

                                        <div class="mt-auto pt-3 flex items-center gap-2 text-[11px] text-zinc-500 font-medium uppercase tracking-wider">
                                            <time datetime="{{ $related->published_at->format('Y-m-d') }}">{{ $related->published_at->format('M d') }}</time>
                                            <span class="w-1 h-1 rounded-full bg-zinc-300"></span>
                                            <div class="flex items-center gap-1.5">
                                                <i class="fas fa-eye"></i>
                                                <span>{{ number_format($related->views) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 bg-zinc-50 border border-zinc-100 rounded-2xl text-center">
                                <i class="fas fa-inbox text-3xl text-zinc-300 mb-3"></i>
                                <p class="text-sm text-zinc-500">Belum ada artikel terkait lainnya.</p>
                            </div>
                        @endif

                        <div class="mt-8 rounded-2xl bg-zinc-900 p-8 text-center text-white relative overflow-hidden shadow-xl">
                            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                            <div class="absolute -left-10 -bottom-10 h-32 w-32 rounded-full bg-blue-500/20 blur-2xl"></div>

                            <h3 class="text-lg font-bold mb-2 relative z-10">LUMINA Dermatological Care</h3>
                            <p class="text-xs text-zinc-400 mb-6 relative z-10">Jelajahi koleksi terbaru dari Bullskincare, Nox, dan Babolat dengan penawaran spesial hari ini.</p>
                            <a href="{{ route('home') }}" class="inline-block bg-white text-black px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-zinc-200 transition relative z-10">
                                Belanja Sekarang
                            </a>
                        </div>

                    </div>
                </aside>

            </div>
        </article>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.sidebar-item');
        if(items.length === 0) return;

        // Config: How many items to show per page
        const itemsPerPage = 3;
        const totalPages = Math.ceil(items.length / itemsPerPage);
        let currentPage = 1;

        const btnPrev = document.getElementById('sidebarPrev');
        const btnNext = document.getElementById('sidebarNext');
        const navWrapper = document.getElementById('sidebarNav');

        // Only show arrows if there are enough items to require pagination
        if(totalPages > 1) {
            navWrapper.style.display = 'flex';
        }

        // Function to handle switching pages
        function updateSidebarView() {
            items.forEach((item, index) => {
                const pageOfItem = Math.floor(index / itemsPerPage) + 1;

                if(pageOfItem === currentPage) {
                    item.style.display = 'flex';
                    // slight delay to allow display to register before fading in
                    setTimeout(() => item.classList.remove('opacity-0'), 20);
                } else {
                    item.classList.add('opacity-0');
                    item.style.display = 'none';
                }
            });

            // Update button states
            btnPrev.disabled = currentPage === 1;
            btnNext.disabled = currentPage === totalPages;
        }

        // Click Listeners
        if(btnPrev) {
            btnPrev.addEventListener('click', () => {
                if(currentPage > 1) {
                    currentPage--;
                    updateSidebarView();
                }
            });
        }

        if(btnNext) {
            btnNext.addEventListener('click', () => {
                if(currentPage < totalPages) {
                    currentPage++;
                    updateSidebarView();
                }
            });
        }

        // Initialize First View immediately without delay
        updateSidebarView();
    });
</script>
@endsection
