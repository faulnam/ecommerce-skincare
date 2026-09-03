@extends('layouts.app')

@section('title', 'Privacy Policy - LUMINA')
@section('og_description', 'Kebijakan privasi LUMINA — bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi kamu.')

@push('og_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "WebPage",
  "url": "{{ url()->current() }}",
  "name": "{{ $polTrans['page_title'][$lang] ?? 'Privacy Policy' }}",
  "description": "{{ $lang === 'id' ? 'Kebijakan privasi LUMINA — bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi kamu.' : 'LUMINA privacy policy — how we collect, use, and protect your personal data.' }}",
  "inLanguage": "{{ $lang === 'id' ? 'id-ID' : 'en-US' }}",
  "isPartOf": { "@id": "{{ url('/') }}/#website" },
  "publisher": { "@id": "{{ url('/') }}/#organization" }
}
</script>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/policy.json');
    $polTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
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
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">{{ $polTrans['section_badge'][$lang] ?? 'Legal' }}</p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">{{ $polTrans['page_title'][$lang] ?? 'Privacy Policy' }}</h1>
                        <p class="mt-4 text-zinc-600">{{ $polTrans['page_subtitle'][$lang] ?? 'Last updated:' }} {{ now()->format('F d, Y') }}</p>
                    </div>
                </div>

                <div class="mx-auto mt-12 max-w-3xl rounded-2xl border border-black/10 bg-white p-6 shadow-sm md:p-8">
                    <div class="prose prose-zinc max-w-none text-sm text-zinc-600">
                        <h2 class="text-lg font-semibold text-black">{{ $polTrans['title_collect'][$lang] ?? '1. Information We Collect' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($polTrans['desc_collect'][$lang] ?? 'We collect information you provide directly to us, such as when you create an account, make a purchase, or contact our support team. This may include your name, email address, phone number, shipping address, and payment information.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $polTrans['title_use'][$lang] ?? '2. How We Use Your Information' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($polTrans['desc_use'][$lang] ?? 'We use the information we collect to process your orders, communicate with you about your account and orders, send you marketing communications (with your consent), and improve our services.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $polTrans['title_sharing'][$lang] ?? '3. Information Sharing' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($polTrans['desc_sharing'][$lang] ?? 'We do not sell your personal information to third parties. We may share your information with service providers who assist us in operating our business, such as payment processors and shipping carriers.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $polTrans['title_security'][$lang] ?? '4. Data Security' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($polTrans['desc_security'][$lang] ?? 'We implement appropriate technical and organizational measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $polTrans['title_rights'][$lang] ?? '5. Your Rights' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($polTrans['desc_rights'][$lang] ?? 'You have the right to access, correct, or delete your personal information. You may also object to the processing of your data or request data portability.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $polTrans['title_cookies'][$lang] ?? '6. Cookies' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($polTrans['desc_cookies'][$lang] ?? 'We use cookies and similar technologies to enhance your browsing experience, analyze site traffic, and personalize content.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $polTrans['title_changes'][$lang] ?? '7. Changes to This Policy' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($polTrans['desc_changes'][$lang] ?? 'We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $polTrans['title_contact'][$lang] ?? '8. Contact Us' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($polTrans['desc_contact'][$lang] ?? 'If you have any questions about this Privacy Policy, please contact us at support@luminaskincare.id.')) !!}</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection