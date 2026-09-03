@extends('layouts.app')

@section('title', 'Help Center - LUMINA')
@section('og_description', 'Pusat bantuan LUMINA. Temukan jawaban untuk pertanyaan seputar pemesanan, pengiriman, pembayaran, dan produk kami.')

@push('og_extra')
@php
    $hcJson = json_decode(@file_get_contents(public_path('translation/helpcenter.json')), true) ?? [];
@endphp
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "FAQPage",
  "url": "{{ url()->current() }}",
  "name": "{{ $hcJson['page_title'][$lang] ?? 'Help Center' }}",
  "description": "{{ $hcJson['page_subtitle'][$lang] ?? 'Find quick answers about orders, shipping, and payment.' }}",
  "inLanguage": "{{ $lang === 'id' ? 'id-ID' : 'en-US' }}",
  "isPartOf": { "@id": "{{ url('/') }}/#website" },
  "publisher": { "@id": "{{ url('/') }}/#organization" },
  "mainEntity": [
    {
      "@type": "Question",
      "name": "{{ $hcJson['title_track'][$lang] ?? 'How to track order' }}",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "{{ $hcJson['desc_track'][$lang] ?? 'Log in to your account, open order history menu, then select order to view status.' }}"
      }
    },
    {
      "@type": "Question",
      "name": "{{ $hcJson['title_payment'][$lang] ?? 'Payment Information' }}",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "{{ $hcJson['desc_payment'][$lang] ?? 'We support bank transfers and online payment gateway.' }}"
      }
    },
    {
      "@type": "Question",
      "name": "{{ $hcJson['title_return'][$lang] ?? 'Return policy' }}",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "{{ $hcJson['desc_return'][$lang] ?? 'Return requests can be made within 7 days after product is received.' }}"
      }
    },
    {
      "@type": "Question",
      "name": "{{ $hcJson['title_need_help'][$lang] ?? 'Still need help?' }}",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "{{ $hcJson['desc_need_help'][$lang] ?? 'Our support team is ready to help you through the contact page.' }}"
      }
    }
  ]
}
</script>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/helpcenter.json');
    $helpTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
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
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">{{ $helpTrans['section_badge'][$lang] ?? 'Support' }}</p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">{{ $helpTrans['page_title'][$lang] ?? 'Help Center' }}</h1>
                        <p class="mt-4 text-zinc-600">{{ $helpTrans['page_subtitle'][$lang] ?? 'Need help with orders, shipping, or payment? Find quick answers here.' }}</p>
                    </div>

                    <div class="mx-auto mt-12 grid max-w-5xl gap-5 md:grid-cols-2">
                        <article class="rounded-2xl border border-black/10 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-black">{{ $helpTrans['title_track'][$lang] ?? 'How to track order' }}</h2>
                            <p class="mt-3 text-sm leading-relaxed text-zinc-600">{{ $helpTrans['desc_track'][$lang] ?? 'Log in to your account, open order history menu, then select order to view pickup, shipping, and completion status.' }}</p>
                        </article>
                        <article class="rounded-2xl border border-black/10 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-black">{{ $helpTrans['title_payment'][$lang] ?? 'Payment Information' }}</h2>
                            <p class="mt-3 text-sm leading-relaxed text-zinc-600">{{ $helpTrans['desc_payment'][$lang] ?? 'We support bank transfers, online payment gateway.' }}</p>
                        </article>
                        <article class="rounded-2xl border border-black/10 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-black">{{ $helpTrans['title_return'][$lang] ?? 'Return policy' }}</h2>
                           <p class="mt-3 text-sm leading-relaxed text-zinc-600">{{ $helpTrans['desc_return'][$lang] ?? 'Return requests can be made maximum 7 days after product is received, as long as product hasn\'t been used and packaging is still complete.' }}</p>
                        </article>
                        <article class="rounded-2xl border border-black/10 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-black">{{ $helpTrans['title_need_help'][$lang] ?? 'Still need help?' }}</h2>
                            <p class="mt-3 text-sm leading-relaxed text-zinc-600">{{ $helpTrans['desc_need_help'][$lang] ?? 'Our support team is ready to help you through the contact page for technical questions or product consultation.' }}</p>
                            <a href="{{ route('contact') }}" class="mt-4 inline-flex rounded-full bg-black px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800">{{ $helpTrans['btn_contact'][$lang] ?? 'Contact Us' }}</a>
                       </article>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection