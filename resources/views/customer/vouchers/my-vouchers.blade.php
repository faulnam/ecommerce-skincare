@extends('layouts.app')

@section('title', 'My Vouchers - LUMINA')

@push('styles')
<style>
    body { padding-top: 0 !important; padding-bottom: 0 !important; }
    #mainNavbar, .mobile-bottom-nav, .footer { display: none !important; }
</style>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/voucher.json');
    $vouchTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp
<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')

<div class="min-h-screen bg-zinc-50 py-8 pt-16 md:pt-20">
    <div class="mx-auto max-w-7xl px-6 md:px-10 lg:px-12">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-black">{{ $vouchTrans['page_title'][$lang] ?? 'My Vouchers' }}</h1>
            <a href="{{ route('customer.profile.index') }}" class="text-sm text-zinc-600 hover:text-black">
                <i class="fas fa-arrow-left mr-2"></i>{{ $vouchTrans['btn_back_profile'][$lang] ?? 'Back to Profile' }}
            </a>
        </div>

                <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500">{{ $vouchTrans['stat_total_vouchers'][$lang] ?? 'Total Vouchers' }}</p>
                    <p class="text-3xl font-bold text-black">{{ $userVouchers->count() }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-zinc-500">{{ $vouchTrans['stat_available'][$lang] ?? 'Available' }}</p>
                    <p class="text-2xl font-bold text-emerald-600">
                        {{ $userVouchers->where('is_used', false)->count() }}
                    </p>
                </div>
            </div>
        </div>

                <div class="space-y-4">
            @forelse($userVouchers as $userVoucher)
            @php
                $voucher = $userVoucher->voucher ?? null;
                $isUsed = $userVoucher->is_used ?? false;
                $claimedAt = $userVoucher->claimed_at ?? null;
                $usedAt = $userVoucher->used_at ?? null;
            @endphp
            @if($voucher)
            <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="mb-2 flex items-center gap-2">
                                <h3 class="text-lg font-semibold text-black">{{ $voucher->title }}</h3>
                                @if($isUsed)
                                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-600">{{ $vouchTrans['badge_used'][$lang] ?? 'Used' }}</span>
                                @else
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">{{ $vouchTrans['badge_available'][$lang] ?? 'Available' }}</span>
                                @endif
                            </div>
                            <p class="mb-3 text-sm text-zinc-600">{{ $voucher->description }}</p>
                            
                            <div class="mb-2 flex items-center gap-4 text-sm">
                                <div>
                                    <p class="text-zinc-500">{{ $vouchTrans['label_code'][$lang] ?? 'Code' }}</p>
                                    <p class="font-mono font-bold text-black">{{ $voucher->code }}</p>
                                </div>
                                <div>
                                    <p class="text-zinc-500">{{ $vouchTrans['label_discount'][$lang] ?? 'Discount' }}</p>
                                    <p class="font-bold text-black">
                                        @if($voucher->type === 'percent')
                                            {{ $voucher->discount_value }}%
                                        @elseif($voucher->type === 'fixed')
                                            Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                                        @elseif($voucher->type === 'cashback')
                                            {{ $voucher->cashback_coin }} {{ $vouchTrans['suffix_coins'][$lang] ?? 'Coins' }}
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-zinc-500">{{ $vouchTrans['label_min_purchase'][$lang] ?? 'Min. Purchase' }}</p>
                                    <p class="font-bold text-black">Rp {{ number_format($voucher->minimum_purchase, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            @if($voucher->maximum_discount)
                            <div class="mb-2 text-sm">
                                <p class="text-zinc-500">{{ $vouchTrans['label_max_discount'][$lang] ?? 'Max. Discount' }}</p>
                                <p class="font-bold text-black">Rp {{ number_format($voucher->maximum_discount, 0, ',', '.') }}</p>
                            </div>
                            @endif

                            <div class="mt-3 text-xs text-zinc-500">
                                <p>{{ $vouchTrans['time_claimed'][$lang] ?? 'Claimed:' }} {{ $claimedAt ? \Carbon\Carbon::parse($claimedAt)->format('d M Y, H:i') : '-' }}</p>
                                @if($isUsed)
                                    <p>{{ $vouchTrans['time_used'][$lang] ?? 'Used:' }} {{ $usedAt ? \Carbon\Carbon::parse($usedAt)->format('d M Y, H:i') : '-' }}</p>
                                @endif
                            </div>
                        </div>

                        @if(!$isUsed && $voucher->isActive())
                        <button onclick="copyVoucherCode('{{ $voucher->code }}')" 
                                class="ml-4 flex-shrink-0 rounded-xl bg-black px-4 py-2 text-sm font-medium text-white hover:bg-black/90 transition">
                            <i class="fas fa-copy mr-2"></i>{{ $vouchTrans['btn_copy'][$lang] ?? 'Copy' }}
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @empty
            <div class="py-16 text-center">
                <div class="mb-4 flex h-20 w-20 mx-auto items-center justify-center rounded-full bg-zinc-100">
                    <i class="fas fa-ticket-alt text-3xl text-zinc-400"></i>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-black">{{ $vouchTrans['empty_title'][$lang] ?? 'No Vouchers Yet' }}</h3>
                <p class="mb-6 text-sm text-zinc-600">{{ $vouchTrans['empty_desc'][$lang] ?? 'Claim vouchers to get discounts on your orders!' }}</p>
                <a href="{{ route('customer.vouchers.index') }}" 
                   class="inline-block rounded-xl bg-black px-6 py-3 text-sm font-medium text-white hover:bg-black/90">
                    {{ $vouchTrans['btn_browse'][$lang] ?? 'Browse Vouchers' }}
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
function copyVoucherCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        alert("{{ $vouchTrans['toast_copied'][$lang] ?? 'Voucher code copied: ' }}" + code);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
    });
}
</script>
@endpush
@endsection