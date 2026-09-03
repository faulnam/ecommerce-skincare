@extends('layouts.app')

@section('title', 'Return & Refund - LUMINA Skincare')
@section('og_description', 'Kebijakan retur dan refund LUMINA Skincare. Proses pengembalian produk yang mudah, aman, dan transparan.')

@push('og_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "WebPage",
  "url": "{{ url()->current() }}",
  "name": "{{ $rrTrans['page_title'][$lang] ?? 'Return & Refund Policy' }}",
  "description": "{{ $rrTrans['page_subtitle'][$lang] ?? 'We want you to be completely satisfied with your purchase.' }}",
  "inLanguage": "{{ $lang === 'id' ? 'id-ID' : 'en-US' }}",
  "isPartOf": { "@id": "{{ url('/') }}/#website" },
  "publisher": { "@id": "{{ url('/') }}/#organization" }
}
</script>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/return-refund.json');
    $rrTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
    $brandingPhone = config('branding.phone', '+62 812 7788 9900');
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
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">{{ $rrTrans['section_badge'][$lang] ?? 'Customer Service' }}</p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">{{ $rrTrans['page_title'][$lang] ?? 'Return & Refund Policy' }}</h1>
                        <p class="mt-4 text-zinc-600">{{ $rrTrans['page_subtitle'][$lang] ?? 'We want you to be completely satisfied with your purchase.' }}</p>
                    </div>
                </div>

                <div class="mx-auto mt-12 max-w-3xl rounded-2xl border border-black/10 bg-white p-6 shadow-sm md:p-8">
                    <div class="prose prose-zinc max-w-none text-sm text-zinc-600">
                        <h2 class="text-lg font-semibold text-black">{{ $rrTrans['title_eligibility'][$lang] ?? '1. Return Eligibility' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($rrTrans['desc_eligibility'][$lang] ?? 'You may return most new, unopened items within 7 days of delivery for a full refund. Items must be in their original packaging with all tags attached. Used, damaged, or altered products are not eligible for return.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $rrTrans['title_non_returnable'][$lang] ?? '2. Non-Returnable Items' }}</h2>
                        <p class="mt-2">{{ $rrTrans['desc_non_returnable'][$lang] ?? 'The following items cannot be returned:' }}</p>
                        <ul class="mt-2 list-disc pl-5">
                            <li>{{ $rrTrans['item_consumable'][$lang] ?? 'Produk skincare yang segelnya telah rusak atau kemasan telah dibuka' }}</li>
                            <li>{{ $rrTrans['item_custom'][$lang] ?? 'Item tester, free sample, atau produk promosi bundling diskon final' }}</li>
                            <li>{{ $rrTrans['item_sale'][$lang] ?? 'Items marked as "Final Sale" or "Clearance"' }}</li>
                            <li>{{ $rrTrans['item_vouchers'][$lang] ?? 'Gift cards and promotional vouchers' }}</li>
                        </ul>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $rrTrans['title_how_to'][$lang] ?? '3. How to Request a Return' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e(str_replace(':phone', $brandingPhone, $rrTrans['desc_how_to'][$lang] ?? 'To initiate a return, please contact our customer service team via WhatsApp at ' . $brandingPhone . ' or email at support@luminaskincare.id with your order number and reason for return. We will provide you with a return authorization and instructions.'))) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $rrTrans['title_process'][$lang] ?? '4. Refund Process' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($rrTrans['desc_process'][$lang] ?? 'Once we receive and inspect your returned item, we will notify you of the approval or rejection of your refund. If approved, your refund will be processed within 5-7 business days to your original payment method. Shipping costs for returns are the responsibility of the customer unless the item was defective or incorrect.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $rrTrans['title_exchanges'][$lang] ?? '5. Exchanges' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($rrTrans['desc_exchanges'][$lang] ?? 'We only replace items if they are defective or damaged. If you need to exchange an item for the same product, contact us with your order details and photos of the defect.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $rrTrans['title_damaged'][$lang] ?? '6. Damaged or Incorrect Items' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e($rrTrans['desc_damaged'][$lang] ?? 'If you receive a damaged or incorrect item, please contact us within 48 hours of delivery with photos. We will arrange a replacement or full refund at no additional cost, including return shipping.')) !!}</p>

                        <h2 class="mt-6 text-lg font-semibold text-black">{{ $rrTrans['title_contact'][$lang] ?? '7. Contact Us' }}</h2>
                        <p class="mt-2 text-justify">{!! nl2br(e(str_replace(':phone', $brandingPhone, $rrTrans['desc_contact'][$lang] ?? 'For any return or refund inquiries, reach out to us at support@luminaskincare.id or WhatsApp ' . $brandingPhone . '. Our team is available Monday-Saturday, 9 AM - 6 PM WIB.'))) !!}</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection