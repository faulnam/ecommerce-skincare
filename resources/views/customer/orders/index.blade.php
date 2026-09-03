@extends('layouts.app')

@section('title', 'Order History')

@push('styles')
<style>
    body { padding-top: 0 !important; padding-bottom: 0 !important; }
    #mainNavbar, .mobile-bottom-nav, .footer { display: none !important; }
</style>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/orderhistory.json');
    $historyTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')

    <div class="min-h-screen bg-zinc-50 py-8 pt-16 md:pt-20">
        <div class="mx-auto max-w-7xl px-6 md:px-10 lg:px-12">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-black">{{ $historyTrans['page_title'][$lang] ?? 'Order History' }}</h1>
            </div>

            <div class="mb-6 rounded-2xl bg-white p-4 shadow-sm border border-zinc-100">
                <form action="{{ route('customer.orders.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row">
                    <select name="status" class="flex-1 rounded-xl border border-zinc-300 px-4 py-2 text-sm focus:border-black focus:outline-none transition-colors">
                        <option value="">{{ $historyTrans['filter_all'][$lang] ?? 'All Status' }}</option>
                        <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>{{ $historyTrans['filter_pending'][$lang] ?? 'Waiting Payment' }}</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ $historyTrans['filter_paid'][$lang] ?? 'Paid' }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ $historyTrans['filter_completed'][$lang] ?? 'Completed' }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ $historyTrans['filter_cancelled'][$lang] ?? 'Cancelled' }}</option>
                    </select>
                    <button type="submit" class="rounded-xl bg-black px-6 py-2 text-sm font-medium text-white hover:bg-black/90 transition-colors shadow-sm">
                        {{ $historyTrans['btn_filter'][$lang] ?? 'Filter' }}
                    </button>
                </form>
            </div>

            <div class="space-y-4">
                @forelse($orders as $order)
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-black tracking-tight" style="color: #003C52;">{{ $order->order_number }}</h3>
                            <p class="text-xs text-zinc-400 mt-0.5">{{ $order->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'pending_payment' => 'bg-amber-100 text-amber-800',
                                'paid' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-emerald-100 text-emerald-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $statusColor = $statusColors[$order->status] ?? 'bg-zinc-100 text-zinc-800';
                        @endphp
                        <span class="rounded-full px-3 py-1 text-[11px] font-semibold {{ $statusColor }} shadow-inner">{{ $order->status_label }}</span>
                    </div>

                    <div class="mb-4 space-y-2.5">
                        @foreach($order->items->take(2) as $item)
                        <div class="flex items-center justify-between text-xs text-zinc-700">
                            <span>{{ $item->product_name }} <span class="text-zinc-400 font-medium ml-1">×{{ $item->quantity }}</span></span>
                            <span class="font-semibold text-black">{{ $item->formatted_subtotal }}</span>
                        </div>
                        @endforeach
                        @if($order->items->count() > 2)
                            <p class="text-[11px] font-medium text-zinc-400 pt-0.5">
                                {!! str_replace(':count', $order->items->count() - 2, $historyTrans['label_other_items'][$lang] ?? '+:count other items') !!}
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between border-t border-zinc-100 pt-4 mt-2">
                        <div>
                            <p class="text-[11px] font-medium text-zinc-400 uppercase tracking-wider mb-0.5">{{ $historyTrans['label_total_pay'][$lang] ?? 'Total Payment' }}</p>
                            <p class="text-base font-bold text-black tracking-tight">{{ $order->formatted_total }}</p>
                        </div>
                        <a href="{{ route('customer.orders.show', $order) }}" 
                           class="rounded-xl border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-50 hover:text-black transition-all shadow-sm">
                            {{ $historyTrans['btn_view_details'][$lang] ?? 'View Details' }}
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-16 text-center border bg-white rounded-2xl shadow-sm border-zinc-100">
                    <div class="mb-4 flex h-16 w-16 mx-auto items-center justify-center rounded-full bg-zinc-50 border shadow-inner">
                        <i class="fas fa-shopping-bag text-2xl text-zinc-400"></i>
                    </div>
                    <h3 class="mb-1.5 text-base font-bold text-black">{{ $historyTrans['empty_title'][$lang] ?? 'No Orders Yet' }}</h3>
                    <p class="mb-6 text-xs text-zinc-500 max-w-sm mx-auto px-4">{{ $historyTrans['empty_desc'][$lang] ?? "Let's start shopping for skincare equipment!" }}</p>
                    <a href="{{ route('produk.index') }}" 
                       class="inline-block rounded-xl bg-black px-6 py-2.5 text-xs font-semibold text-white hover:bg-black/90 transition-colors shadow-sm">
                        {{ $historyTrans['btn_start_shopping'][$lang] ?? 'Start Shopping' }}
                    </a>
                </div>
                @endforelse
            </div>

            @if($orders->hasPages())
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<footer class="border-t border-black/5 bg-white py-10 text-xs text-zinc-400">
    <div class="mx-auto w-full max-w-7xl px-6 md:px-10 lg:px-12">
        <div class="space-y-2 md:hidden">
            <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                <summary class="flex cursor-pointer list-none items-center justify-between text-[11px] font-bold uppercase tracking-wider text-black">
                    {{ $historyTrans['foot_support'][$lang] ?? 'Support' }}
                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-200 group-open:rotate-180"></i>
                </summary>
                <ul class="mt-3 space-y-2 text-xs font-medium text-zinc-500">
                    <li><a href="{{ route('policy') }}" class="hover:text-black transition-colors">{{ $historyTrans['foot_policy'][$lang] ?? 'Policy' }}</a></li>
                    <li><a href="{{ route('return-refund') }}" class="hover:text-black transition-colors">{{ $historyTrans['foot_return'][$lang] ?? 'Return & Refund' }}</a></li>
                    <li><a href="{{ route('guarantee') }}" class="hover:text-black transition-colors">{{ $historyTrans['foot_guarantee'][$lang] ?? 'LUMINA Guarantee' }}</a></li>
                    <li><a href="{{ route('help-center') }}" class="hover:text-black transition-colors">{{ $historyTrans['foot_help'][$lang] ?? 'Help Center' }}</a></li>
                </ul>
            </details>
            
            <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                <summary class="flex cursor-pointer list-none items-center justify-between text-[11px] font-bold uppercase tracking-wider text-black">
                    {{ $historyTrans['foot_account'][$lang] ?? 'Account' }}
                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-200 group-open:rotate-180"></i>
                </summary>
                <ul class="mt-3 space-y-2 text-xs font-medium text-zinc-500">
                    <li><a href="{{ route('customer.profile.index') }}" class="hover:text-black transition-colors">{{ $historyTrans['foot_profile'][$lang] ?? 'Profile' }}</a></li>
                    <li><a href="{{ route('customer.orders.index') }}" class="hover:text-black transition-colors">{{ $historyTrans['page_title'][$lang] ?? 'Order History' }}</a></li>
                </ul>
            </details>
        </div>
    </div>
    <div class="mx-auto mt-8 w-full max-w-7xl border-t border-black/5 px-6 pt-5 text-[11px] font-medium text-zinc-400 md:px-10 lg:px-12">
        &copy; {{ now()->year }} LUMINA. {{ $historyTrans['foot_rights'][$lang] ?? 'All rights reserved.' }}
    </div>
</footer>
@endsection