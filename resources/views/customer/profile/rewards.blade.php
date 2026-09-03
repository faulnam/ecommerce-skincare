@extends('layouts.app')

@section('title', 'Reward & Points - LUMINA')

@section('content')
@php
    $jsonPath = public_path('translation/customer.json');
    $custTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp
<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')

    <div class="mx-auto w-full max-w-7xl px-6 py-8 pt-20 md:px-10 md:py-12 md:pt-24 lg:px-12 lg:py-16">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-3xl font-semibold tracking-tight text-black sm:text-4xl">
                    <i class="fas fa-gift mr-3 text-black"></i>{{ $custTrans['menu_rewards'][$lang] ?? 'Reward & Points' }}
                </h3>
                <p class="mt-1 text-sm text-zinc-500">{{ $custTrans['rewards_subtitle'][$lang] ?? 'Kelola poin loyalitas dan lihat riwayat transaksi poin Anda' }}</p>
            </div>
            <a href="{{ route('customer.profile.index') }}" class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white px-4 py-2 text-sm font-medium text-black transition hover:bg-black hover:text-white">
                <i class="fas fa-arrow-left text-xs"></i> {{ $custTrans['btn_back_profile'][$lang] ?? 'Kembali ke Profile' }}
            </a>
        </div>


        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left: Points Summary -->
            <div class="space-y-6 lg:col-span-1">
                <!-- Total Points Card -->
                <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                    <div class="bg-black px-6 py-8 text-white">
                        <div class="mb-4 flex items-center gap-2">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                                <i class="fas fa-coins text-lg"></i>
                            </div>
                            <span class="text-sm font-medium text-white/90">{{ $custTrans['card_total_points'][$lang] ?? 'Total Point' }}</span>
                        </div>
                        <div class="mb-1 text-4xl font-bold tracking-tight">
                            {{ number_format($user->available_points) }} {{ $custTrans['label_points_suffix'][$lang] ?? 'Points' }}
                        </div>
                        <div class="text-sm text-white/80">
                            {{ str_replace(':value', $user->formatted_points_value, $custTrans['label_equivalent'][$lang] ?? 'Setara dengan ' . $user->formatted_points_value) }}
                        </div>
                        @if($user->points !== $user->available_points)
                        <div class="mt-1 text-xs text-white/60">
                            {{ str_replace(':count', number_format($user->points - $user->available_points), $custTrans['label_expired_count'][$lang] ?? number_format($user->points - $user->available_points) . ' poin kedaluwarsa') }}
                        </div>
                        @endif
                        @if($user->next_points_expiry)
                        <div class="mt-2 inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-xs text-white/90">
                            <i class="far fa-clock mr-1.5"></i>{{ $custTrans['points_expiry'][$lang] ?? 'Berlaku sampai:' }} {{ $user->next_points_expiry->format('d M Y') }}
                        </div>
                        @endif
                    </div>
                    <div class="px-6 py-5">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-sm text-zinc-500">{{ $custTrans['points_value'][$lang] ?? 'Nilai Tukar' }}</span>
                            <span class="text-sm font-semibold text-black">1 Point = Rp100</span>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-4">
                            <div class="text-xs text-zinc-500 mb-2">{{ $custTrans['rate_example_title'][$lang] ?? 'Contoh penggunaan:' }}</div>
                            <div class="text-sm text-zinc-700">
                                <div class="flex justify-between py-1">
                                    <span>100 {{ $custTrans['label_points_suffix'][$lang] ?? 'Points' }}</span>
                                    <span class="font-medium text-black">Rp10.000</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span>200 {{ $custTrans['label_points_suffix'][$lang] ?? 'Points' }}</span>
                                    <span class="font-medium text-black">Rp20.000</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span>500 {{ $custTrans['label_points_suffix'][$lang] ?? 'Points' }}</span>
                                    <span class="font-medium text-black">Rp50.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                    <div class="border-b border-black/6 bg-zinc-50 px-6 py-4">
                        <h4 class="text-sm font-semibold text-black">{{ $custTrans['section_summary'][$lang] ?? 'Ringkasan' }}</h4>
                    </div>
                    <div class="px-6 py-5">
                        @php
                            $totalEarned = $user->pointTransactions()->where('points', '>', 0)->sum('points');
                            $totalRedeemed = abs($user->pointTransactions()->where('points', '<', 0)->sum('points'));
                        @endphp
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-xl bg-zinc-50 p-4 text-center">
                                <div class="mb-1 text-2xl font-bold text-black">+{{ number_format($totalEarned) }}</div>
                                <div class="text-xs text-zinc-600">{{ $custTrans['stat_points_in'][$lang] ?? 'Point Masuk' }}</div>
                            </div>
                            <div class="rounded-xl bg-zinc-50 p-4 text-center">
                                <div class="mb-1 text-2xl font-bold text-black">-{{ number_format($totalRedeemed) }}</div>
                                <div class="text-xs text-zinc-600">{{ $custTrans['stat_points_out'][$lang] ?? 'Point Keluar' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Point History -->
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                    <div class="border-b border-black/6 bg-zinc-50 px-6 py-4">
                        <h4 class="text-lg font-semibold text-black"><i class="fas fa-history mr-2"></i>{{ $custTrans['section_history'][$lang] ?? 'Riwayat Point' }}</h4>
                    </div>

                    <div class="divide-y divide-black/6">
                        @forelse($pointTransactions as $transaction)
                            <div class="flex items-start gap-4 px-6 py-4 transition hover:bg-zinc-50/50">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $transaction->points > 0 ? 'bg-zinc-100 text-black' : 'bg-zinc-100 text-zinc-500' }}">
                                    <i class="fas {{ $transaction->points > 0 ? 'fa-plus' : 'fa-minus' }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <div class="text-sm font-semibold text-black">
                                                @if($transaction->points > 0)
                                                    +{{ number_format($transaction->points) }} {{ $custTrans['label_points_suffix'][$lang] ?? 'Points' }}
                                                @else
                                                    {{ number_format($transaction->points) }} {{ $custTrans['label_points_suffix'][$lang] ?? 'Points' }}
                                                @endif
                                            </div>
                                            <div class="mt-0.5 text-sm text-zinc-500">{{ $transaction->description ?? '-' }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-zinc-400">{{ $transaction->created_at->format('d M Y') }}</div>
                                            <div class="mt-0.5 text-xs text-zinc-400">{{ $transaction->created_at->format('H:i') }}</div>
                                        </div>
                                    </div>
                                    @if($transaction->order_id)
                                        <div class="mt-1">
                                            <a href="{{ route('customer.orders.show', $transaction->order_id) }}" class="inline-flex items-center gap-1 text-xs font-medium text-black transition hover:text-zinc-600">
                                                <i class="fas fa-receipt"></i> Order #{{ $transaction->order_id }}
                                            </a>
                                        </div>
                                    @endif
                                    <div class="mt-1 text-xs text-zinc-400">
                                        Saldo: {{ number_format($transaction->balance_before) }} &rarr; {{ number_format($transaction->balance_after) }}
                                        @if($transaction->expires_at && $transaction->points > 0)
                                            <span class="ml-2 inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-600">
                                                <i class="far fa-clock mr-1"></i>
                                                {{ $custTrans['points_expiry'][$lang] ?? 'Kedaluwarsa:' }} {{ $transaction->expires_at->format('d M Y') }}
                                            </span>
                                        @endif
                                        @if($transaction->type === 'redeemed')
                                            <span class="ml-2 inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-600">{{ $custTrans['label_redeemed_badge'][$lang] ?? 'Digunakan' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <div class="mb-3 inline-flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100">
                                    <i class="fas fa-coins text-2xl text-zinc-300"></i>
                                </div>
                                <h5 class="mb-1 text-base font-semibold text-black">{{ $custTrans['empty_history_title'][$lang] ?? 'Belum Ada Riwayat Point' }}</h5>
                                <p class="text-sm text-zinc-500">{{ $custTrans['empty_history_desc'][$lang] ?? 'Riwayat transaksi point Anda akan muncul di sini.' }}</p>
                            </div>
                        @endforelse
                    </div>

                    @if($pointTransactions->hasPages())
                        <div class="border-t border-black/6 px-6 py-4">
                            {{ $pointTransactions->links() }}
                        </div>
                    @endif
                </div>

                @if($user->next_points_expiry)
                <div class="mt-6 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="flex items-start gap-2 text-sm text-zinc-700">
                        <i class="fas fa-sync-alt mt-0.5 text-zinc-500"></i>
                        <p>{!! str_replace(':date', $user->next_points_expiry->format('d M Y'), $custTrans['label_expiry_alert'][$lang] ?? 'Setiap kali Anda menggunakan poin, tanggal expired otomatis diperbarui 6 bulan ke depan. Tanggal berlaku saat ini: <strong>' . $user->next_points_expiry->format('d M Y') . '</strong>.') !!}</p>
                    </div>
                </div>
                @endif

                <!-- How to Earn -->
                <div class="mt-6 overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                    <div class="border-b border-black/6 bg-zinc-50 px-6 py-4">
                        <h4 class="text-lg font-semibold text-black"><i class="fas fa-lightbulb mr-2"></i>{{ $custTrans['section_how_to_earn'][$lang] ?? 'Cara Mendapatkan Point' }}</h4>
                    </div>
                    <div class="px-6 py-5">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-xl border border-black/6 p-4 text-center transition hover:shadow-sm">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-50 text-black">
                                    <i class="fas fa-user-plus text-lg"></i>
                                </div>
                                <div class="text-sm font-semibold text-black">{{ $custTrans['earn_bonus_title'][$lang] ?? 'Bonus Akun' }}</div>
                                <div class="mt-1 text-xs text-zinc-500">{{ $custTrans['earn_bonus_desc'][$lang] ?? 'Dapatkan point bonus saat pertama kali mendaftar' }}</div>
                            </div>
                            <div class="rounded-xl border border-black/6 p-4 text-center transition hover:shadow-sm">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-50 text-black">
                                    <i class="fas fa-shopping-bag text-lg"></i>
                                </div>
                                <div class="text-sm font-semibold text-black">{{ $custTrans['earn_cashback_title'][$lang] ?? 'Cashback Order' }}</div>
                                <div class="mt-1 text-xs text-zinc-500">{{ $custTrans['earn_cashback_desc'][$lang] ?? 'Dapatkan cashback dari setiap pembelian yang berhasil' }}</div>
                            </div>
                            <div class="rounded-xl border border-black/6 p-4 text-center transition hover:shadow-sm">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-50 text-black">
                                    <i class="fas fa-star text-lg"></i>
                                </div>
                                <div class="text-sm font-semibold text-black">{{ $custTrans['earn_promo_title'][$lang] ?? 'Promo Khusus' }}</div>
                                <div class="mt-1 text-xs text-zinc-500">{{ $custTrans['earn_promo_desc'][$lang] ?? 'Ikuti promo tertentu untuk mendapatkan bonus point' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection