@extends('layouts.app')

@section('title', 'LUMINA Guarantee - LUMINA')
@section('og_description', 'LUMINA Guarantee — jaminan produk original dan layanan terpercaya dari LUMINA. Belanja tenang, kualitas terjamin.')

@push('og_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "WebPage",
  "url": "{{ url()->current() }}",
  "name": "{{ $guaTrans['page_title'][$lang] ?? 'LUMINA Guarantee' }}",
  "description": "{{ $guaTrans['page_subtitle'][$lang] ?? 'Shop with confidence knowing every product is backed by our commitment.' }}",
  "inLanguage": "{{ $lang === 'id' ? 'id-ID' : 'en-US' }}",
  "isPartOf": { "@id": "{{ url('/') }}/#website" },
  "publisher": { "@id": "{{ url('/') }}/#organization" },
  "speakable": {
    "@type": "SpeakableSpecification",
    "cssSelector": ["h1", "h2"]
  }
}
</script>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/guarantee.json');
    $guaTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp
    <style>
        #marqueeBar { 
            display: block !important; 
            visibility: visible !important;
            opacity: 1 !important;
            z-index: 60 !important;
            position: fixed !important;
            top: 0 !important;
        }
        .mobile-bottom-nav { display: none !important; }
        #mainNavbar { display: none !important; }
    </style>
    <div class="bg-white text-black antialiased">
        @include('components.luxury-navbar')
        <main class="pt-0 md:pt-20">
            <section class="bg-[#f8fafc] pt-4 pb-14 lg:pt-4 lg:pb-16">
                <div class="mx-auto w-full max-w-7xl px-6 md:px-10 lg:px-12">
                    <div class="mx-auto max-w-3xl text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">{{ $guaTrans['section_badge'][$lang] ?? 'Our Promise' }}</p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">{{ $guaTrans['page_title'][$lang] ?? 'LUMINA Guarantee' }}</h1>
                        <p class="mt-4 text-zinc-600">{{ $guaTrans['page_subtitle'][$lang] ?? 'Shop with confidence knowing every product is backed by our commitment.' }}</p>
                    </div>

                    <div class="mx-auto mt-12 max-w-3xl rounded-2xl border border-black/10 bg-white p-6 shadow-sm md:p-8">
                        <div class="prose prose-zinc max-w-none text-sm text-zinc-600">
                            <div class="mb-6 flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-black text-white">
                                    <i class="fas fa-shield-alt text-base"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-black">{!! nl2br(e($guaTrans['title_authentic'][$lang] ?? '100% Authentic Products')) !!}</h2>
                            </div>
                            <p class="mt-2 text-justify">{!! nl2br(e($guaTrans['desc_authentic'][$lang] ?? 'Every product sold at LUMINA is 100% authentic and sourced directly from authorized distributors or the brands themselves. We never sell counterfeit or replica items.')) !!}</p>

                            <div class="mb-6 mt-8 flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-black text-white">
                                    <i class="fas fa-check-circle text-base"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-black">{!! nl2br(e($guaTrans['title_inspection'][$lang] ?? 'Quality Inspection')) !!}</h2>
                            </div>
                            <p class="mt-2 text-justify">{!! nl2br(e($guaTrans['desc_inspection'][$lang] ?? 'All skincares, shoes, and gear are inspected by our team before shipping. We check for defects, verify string tension accuracy, and ensure every item meets our standards.')) !!}</p>

                            <div class="mb-6 mt-8 flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-black text-white">
                                    <i class="fas fa-undo text-base"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-black">{!! nl2br(e($guaTrans['title_return'][$lang] ?? '7-Day Return')) !!}</h2>
                            </div>
                            <p class="mt-2 text-justify">{!! nl2br(e($guaTrans['desc_return_1'][$lang] ?? 'Not satisfied? Return your unused, unopened purchase within 7 days for a full refund. See our')) !!} <a href="{{ route('return-refund') }}" class="underline hover:text-black">{!! nl2br(e($guaTrans['desc_return_link'][$lang] ?? 'Return & Refund Policy')) !!}</a> {!! nl2br(e($guaTrans['desc_return_2'][$lang] ?? 'for full details.')) !!}</p>

                            <div class="mb-6 mt-8 flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-black text-white">
                                    <i class="fas fa-tools text-base"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-black">{!! nl2br(e($guaTrans['title_stringing'][$lang] ?? 'Stringing Warranty')) !!}</h2>
                            </div>
                            <p class="mt-2 text-justify">{!! nl2br(e($guaTrans['desc_stringing'][$lang] ?? 'LUMINAs strung by LUMINA come with a 30-day stringing warranty. If the strings break within 30 days under normal playing conditions, we will restring your skincare free of charge.')) !!}</p>

                            <div class="mb-6 mt-8 flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-black text-white">
                                    <i class="fas fa-headset text-base"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-black">{!! nl2br(e($guaTrans['title_support'][$lang] ?? 'Expert Support')) !!}</h2>
                            </div>
                            <p class="mt-2 text-justify">{!! nl2br(e($guaTrans['desc_support'][$lang] ?? 'Our team consists of skincare enthusiasts and certified stringers. Whether you need advice on skincare selection, string tension, or shoe sizing, we are here to help you make the right choice.')) !!}</p>

                            <div class="mb-6 mt-8 flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-black text-white">
                                    <i class="fas fa-lock text-base"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-black">{!! nl2br(e($guaTrans['title_secure'][$lang] ?? 'Secure Shopping')) !!}</h2>
                            </div>
                            <p class="mt-2 text-justify">{!! nl2br(e($guaTrans['desc_secure'][$lang] ?? 'Your payment and personal information are protected with industry-standard encryption. We partner with trusted payment gateways to ensure every transaction is safe.')) !!}</p>

                            <div class="mb-6 mt-8 flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-black text-white">
                                    <i class="fas fa-question-circle text-base"></i>
                                </div>
                                <h2 class="text-lg font-semibold text-black">{!! nl2br(e($guaTrans['title_questions'][$lang] ?? 'Support')) !!}</h2>
                            </div>
                            <p class="mt-2 text-justify">{!! nl2br(e($guaTrans['desc_questions_1'][$lang] ?? 'If you have further questions about products and warranties, please contact our team:\nEmail:')) !!} support@luminaskincare.id {!! nl2br(e($guaTrans['desc_questions_or'][$lang] ?? '\nWhatsApp: +62 812 7788 9900\nWe\'re ready to help!')) !!}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection