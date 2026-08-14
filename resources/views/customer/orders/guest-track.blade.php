@extends('layouts.app')

@php
    $jsonPath = public_path('translation/tracking.json');
    $trackTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@section('title', ($trackTrans['meta_title'][$lang] ?? 'Track Order #') . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold
                    @if($order->status === 'pending_payment') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                    @elseif($order->status === 'completed') bg-green-100 text-green-800
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $order->status_label }}
                </span>
            </div>
        </div>

        @if($order->status === 'pending_payment')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-semibold text-yellow-800 mb-1">{{ $trackTrans['payment_wait_title'][$lang] ?? 'Waiting for Payment' }}</h3>
                    <p class="text-sm text-yellow-700">{{ $trackTrans['payment_wait_desc'][$lang] ?? 'Please select a payment method to continue your order.' }}</p>
                    <a href="{{ route('customer.payment.show', $order) }}" 
                       class="inline-block mt-3 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                        {{ $trackTrans['btn_pay'][$lang] ?? 'Pay Now' }}
                    </a>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">{{ $trackTrans['recipient_title'][$lang] ?? 'Recipient Information' }}</h3>
                <p class="text-sm text-gray-600">{{ $order->shipping_name }}</p>
                <p class="text-sm text-gray-600">{{ $order->shipping_phone }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ $order->shipping_address }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">{{ $trackTrans['shipping_title'][$lang] ?? 'Shipping Information' }}</h3>
                @if($order->courier_name)
                <p class="text-sm text-gray-600">{{ $order->courier_name }} - {{ $order->courier_service_name }}</p>
                @endif
                @if($order->waybill_id)
                <p class="text-sm text-gray-600">{{ $trackTrans['label_receipt'][$lang] ?? 'Tracking Number:' }} <span class="font-mono font-semibold">{{ $order->waybill_id }}</span></p>
                @endif
                @if($order->estimated_delivery_date)
                <p class="text-sm text-gray-600">{{ $trackTrans['estimate_label'][$lang] ?? 'Estimate:' }} {{ $order->estimated_delivery_date }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">{{ $trackTrans['products_ordered_title'][$lang] ?? 'Products Ordered' }}</h2>
        <div class="space-y-4">
            @foreach($order->items as $item)
            <div class="flex items-center gap-4 pb-4 border-b last:border-b-0">
                @if($item->product && $item->product->image)
                <img src="{{ config('filesystems.disks.r2.url').'/' . $item->product->image }}" 
                     alt="{{ $item->product_name }}"
                     class="w-20 h-20 object-cover rounded-lg">
                @else
                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                @endif
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800">{{ $item->product_name }}</h3>
                    <p class="text-sm text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->product_price, 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 pt-4 border-t">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ $trackTrans['summary_subtotal'][$lang] ?? 'Subtotal' }}</span>
                    <span class="text-gray-800">{{ $order->formatted_subtotal }}</span>
                </div>
                @if($order->product_discount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ $trackTrans['summary_product_disc'][$lang] ?? 'Product Discount' }}</span>
                    <span class="text-green-600">-{{ $order->formatted_product_discount }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ $trackTrans['summary_shipping_cost'][$lang] ?? 'Shipping Cost' }}</span>
                    <span class="text-gray-800">{{ $order->formatted_shipping_cost }}</span>
                </div>
                @if($order->shipping_discount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ $trackTrans['summary_shipping_disc'][$lang] ?? 'Shipping Discount' }}</span>
                    <span class="text-green-600">-{{ $order->formatted_shipping_discount }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold pt-2 border-t">
                    <span class="text-gray-800">{{ $trackTrans['summary_total'][$lang] ?? 'Total' }}</span>
                    <span class="text-gray-900">{{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($order->waybill_id && $biteshipDetail)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">{{ $trackTrans['timeline_title'][$lang] ?? 'Track Delivery' }}</h2>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Status</span>
                <span class="font-semibold text-gray-800">{{ $biteshipDetail['status_label'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">{{ $trackTrans['label_courier'][$lang] ?? 'Courier' }}</span>
                <span class="font-semibold text-gray-800">{{ $biteshipDetail['courier_name'] }}</span>
            </div>
            @if($biteshipDetail['driver_name'] !== '-')
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">{{ $trackTrans['biteship_driver'][$lang] ?? 'Driver' }}</span>
                <span class="font-semibold text-gray-800">{{ $biteshipDetail['driver_name'] }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection