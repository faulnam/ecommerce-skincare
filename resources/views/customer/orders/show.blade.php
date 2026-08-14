@extends('layouts.app')

@section('title', 'Order Details')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
    /* ----------------------------------------- */
    /* Kill layout elements we don't want here   */
    /* ----------------------------------------- */
    body { padding-bottom: 0 !important; }
    #mainNavbar, .footer, .mobile-bottom-nav { display: none !important; }
    /* Map container with proper z-index */
    #courierMap {
        height: 450px;
        border-radius: 1rem;
        position: relative;
        z-index: 1;
    }

    /* Ensure Leaflet map doesn't override navbar */
    .leaflet-container {
        z-index: 1 !important;
    }

    .leaflet-pane {
        z-index: auto !important;
    }

    .leaflet-top,
    .leaflet-bottom {
        z-index: 2 !important;
    }

    .leaflet-control {
        z-index: 3 !important;
    }

    /* Hide Leaflet Routing Machine directions panel */
    .leaflet-routing-container,
    .leaflet-routing-alternatives-container,
    .leaflet-routing-geocoders {
        display: none !important;
    }

    /* Responsive map height */
    @media (max-width: 640px) {
        #courierMap {
            height: 250px !important;
        }
    }

    @media (min-width: 641px) and (max-width: 1024px) {
        #courierMap {
            height: 320px !important;
        }
    }

    /* Courier marker animation */
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }

    .courier-marker {
        animation: pulse 2s ease-in-out infinite;
    }
</style>
@endpush

@section('content')
@php
    $jsonPath = public_path('translation/payment.json');
    $payTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')

    <div class="min-h-screen bg-zinc-50 pb-8" style="padding-top: 140px;">
        <div class="mx-auto max-w-7xl px-6 md:px-10 lg:px-12">
            <div class="mb-6 text-sm text-zinc-500">
                <a href="{{ route('customer.orders.index') }}" class="hover:text-black transition-colors">{{ $payTrans['bc_orders'][$lang] ?? 'Orders' }}</a>
                <span class="mx-2">/</span>
                <span class="text-black font-medium">{{ $order->order_number }}</span>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold tracking-tight text-black">{{ $order->order_number }}</h2>
                            @php
                                $statusColors = [
                                    'pending_payment' => 'bg-amber-100 text-amber-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'ready_to_ship' => 'bg-indigo-100 text-indigo-800',
                                    'shipped' => 'bg-purple-100 text-purple-800',
                                    'delivered' => 'bg-teal-100 text-teal-800',
                                    'completed' => 'bg-emerald-100 text-emerald-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusColor = $statusColors[$order->status] ?? 'bg-zinc-100 text-zinc-800';

                                $displayStatusLabel = $order->biteship_order_id
                                    ? $order->shipment_stage_label
                                    : $order->status_label;
                            @endphp
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor }}">{{ $displayStatusLabel }}</span>
                        </div>
                        <p class="text-sm text-zinc-500">{{ $order->created_at->format('d F Y, H:i') }}</p>

                        @if($order->status === 'pending_payment' && !$order->isExpired())
                            <div class="mt-4 rounded-xl bg-amber-50 border border-amber-200 p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-clock text-amber-600 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-amber-900 mb-1">{{ $payTrans['detail_pay_now_title'][$lang] ?? 'Make Payment Now' }}</p>
                                        <p class="text-xs text-amber-800 mb-2">{{ $payTrans['detail_pay_now_desc'][$lang] ?? 'Order will be automatically cancelled if not paid within:' }}</p>
                                        <div class="text-lg font-bold text-amber-900 tracking-tight" id="expirationTimer">{{ $order->formatted_expiration_time }}</div>
                                    </div>
                                </div>
                            </div>
                        @elseif($order->status === 'pending_payment' && $order->isExpired())
                            <div class="mt-4 rounded-xl bg-red-50 border border-red-200 p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-red-900 mb-1">{{ $payTrans['detail_expired_title'][$lang] ?? 'Order Expired' }}</p>
                                        <p class="text-xs text-red-800">{{ $payTrans['detail_expired_desc'][$lang] ?? 'This order will be cancelled soon because payment was not made within 24 hours.' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(in_array($order->status, ['processing', 'ready_to_ship', 'shipped', 'delivered', 'completed', 'cancelled']) || $order->biteship_order_id)
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                            <h3 class="text-base font-bold text-black">{{ $payTrans['ship_status_title'][$lang] ?? 'Status Pengiriman' }}</h3>
                            @if(!empty($biteshipDetail))
                                <button
                                    type="button"
                                     onclick="toggleOrderDetailPanel()"
                                    class="inline-flex items-center gap-2 rounded-full bg-black px-4 py-2 text-xs font-semibold text-white transition hover:bg-black/85 shadow-sm">
                                    <i class="fas fa-file-alt text-[11px]"></i>
                                    <span id="orderDetailToggleLabel">{{ $payTrans['ship_btn_view_details'][$lang] ?? 'View Details' }}</span>
                                </button>
                            @endif
                        </div>

                        <div class="relative">
                            @php
                                $timeline = [
                                    ['stage' => 'sedang_diproses', 'label' => ($lang === 'id' ? 'Sedang Diproses' : 'Processing'), 'icon' => 'fa-cog'],
                                    ['stage' => 'penjemputan', 'label' => ($lang === 'id' ? 'Penjemputan' : 'Pickup'), 'icon' => 'fa-box', 'photo' => $order->pickup_photo],
                                    ['stage' => 'pengantaran', 'label' => ($lang === 'id' ? 'Pengantaran' : 'In Transit'), 'icon' => 'fa-truck', 'photo' => $order->delivery_photo],
                                    ['stage' => 'pengembalian', 'label' => ($lang === 'id' ? 'Pengembalian' : 'Returned'), 'icon' => 'fa-undo'],
                                    ['stage' => 'ditahan', 'label' => ($lang === 'id' ? 'Di Tahan' : 'On Hold'), 'icon' => 'fa-pause-circle'],
                                    ['stage' => 'selesai', 'label' => ($lang === 'id' ? 'Selesai' : 'Completed'), 'icon' => 'fa-star'],
                                ];
                                $currentStage = $order->shipment_stage;
                                $stagePositions = array_flip(array_column($timeline, 'stage'));
                                $currentIndex = $stagePositions[$currentStage] ?? 0;
                                $progressRatio = $currentIndex / max((count($timeline) - 1), 1);
                            @endphp

                            <div class="absolute top-5 left-0 right-0 h-0.5 bg-zinc-200" style="margin: 0 2.5rem;"></div>
                            <div class="absolute top-5 left-0 h-0.5 bg-black transition-all duration-500"
                                 style="width: calc((100% - 5rem) * {{ $progressRatio }}); margin-left: 2.5rem;"></div>

                            <div class="relative flex justify-between">
                                @foreach($timeline as $index => $step)
                                    @php
                                        $stepStage = $step['stage'];
                                        $isCurrent = $stepStage === $currentStage;
                                        $isPassed = $index < $currentIndex;

                                        if ($currentStage === 'ditahan' && $stepStage === 'pengembalian') {
                                            $isPassed = true;
                                        }
                                        if ($isCurrent) {
                                            $isPassed = false;
                                        }

                                        $isReached = $isPassed || $isCurrent;
                                        $hasPhoto = isset($step['photo']) && $step['photo'];

                                        $stepCircleClass = $isCurrent
                                            ? 'bg-black text-white ring-4 ring-black/20 scale-105'
                                            : ($isPassed
                                                ? 'bg-emerald-500 text-white'
                                                : 'bg-zinc-200 text-zinc-400');

                                        $stepLabelClass = $isCurrent
                                            ? 'text-black font-semibold'
                                            : ($isPassed ? 'text-emerald-700 font-medium' : 'text-zinc-400');
                                    @endphp
                                    <div class="flex flex-col items-center" style="flex: 1;">
                                        <div class="relative">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $stepCircleClass }} transition-all duration-300 shadow-sm">
                                                <i class="fas {{ $step['icon'] }} text-xs"></i>
                                            </div>
                                            @if($hasPhoto && $isReached)
                                                <button type="button" onclick="togglePhotoDropdown('photo-{{ $index }}')" class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-black text-white flex items-center justify-center text-[10px] hover:bg-zinc-800 transition shadow-sm">
                                                    <i class="fas fa-camera"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <p class="mt-3 text-[11px] tracking-tight text-center {{ $stepLabelClass }}">{{ $step['label'] }}</p>

                                        @if($hasPhoto && $isReached)
                                            <div id="photo-{{ $index }}" class="hidden absolute top-16 z-20 mt-2 w-64 rounded-xl bg-white shadow-xl border border-zinc-200 p-3">
                                                <div class="flex items-center justify-between mb-2">
                                                    <p class="text-xs font-semibold text-black">
                                                        {{ $step['stage'] === 'penjemputan' ? ($payTrans['photo_pickup'][$lang] ?? 'Foto Pickup') : ($payTrans['photo_delivered'][$lang] ?? 'Foto Delivery') }}
                                                    </p>
                                                    <button type="button" onclick="togglePhotoDropdown('photo-{{ $index }}')" class="text-zinc-400 hover:text-black">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </button>
                                                </div>
                                                <img src="{{ config('filesystems.disks.r2.url').'/' . $step['photo'] }}"
                                                     class="w-full h-auto rounded-lg object-cover border cursor-pointer hover:opacity-95 transition"
                                                     onclick="openPhotoModal('{{ config('filesystems.disks.r2.url').'/' . $step['photo'] }}', '{{ $step['stage'] === 'penjemputan' ? 'Photo Pickup' : 'Photo Delivery' }}')"
                                                     alt="Bukti Foto">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle mt-0.5 text-amber-700"></i>
                            <div>
                                <p class="text-sm font-semibold text-amber-900">{{ $payTrans['ship_return_info_title'][$lang] ?? 'Informasi Pengembalian' }}</p>
                                <p class="mt-1 text-xs text-amber-800 leading-relaxed">
                                    {{ $payTrans['ship_return_info_desc'][$lang] ?? 'Jika ingin mengajukan pengembalian, silakan hubungi admin terlebih dahulu. Pengajuan pengembalian tidak bisa dilakukan langsung dari sistem.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if(!empty($biteshipDetail))
                        <div id="orderDetailPanel" class="mt-6 hidden space-y-6">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 space-y-3 text-xs text-zinc-700">
                                    <div><p class="text-zinc-400 font-medium">Order ID</p><p class="font-mono font-semibold text-black mt-0.5">{{ $biteshipDetail['order_id'] ?? '-' }}</p></div>
                                    <div><p class="text-zinc-400 font-medium">Reference ID</p><p class="font-semibold text-black mt-0.5">{{ $biteshipDetail['reference_id'] ?? '-' }}</p></div>
                                    <div><p class="text-zinc-400 font-medium">{{ $payTrans['biteship_receipt_no'][$lang] ?? 'Receipt No.' }}</p><p class="font-semibold text-black mt-0.5">{{ $biteshipDetail['waybill_id'] ?? '-' }}</p></div>
                                    <div><p class="text-zinc-400 font-medium">Status</p><p class="font-semibold text-teal-700 mt-0.5">{{ $biteshipDetail['status_label'] ?? '-' }}</p></div>
                                    <div>
                                        <p class="text-zinc-400 font-medium">Order Date</p>
                                        <p class="font-semibold text-black mt-0.5">
                                            {{ $order->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y') }}<br>
                                            {{ $order->created_at->timezone('Asia/Jakarta')->format('H.i') }} WIB
                                        </p>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 space-y-3 text-xs text-zinc-700">
                                    <div><p class="text-zinc-400 font-medium">Courier</p><p class="font-semibold text-black mt-0.5">{{ $biteshipDetail['courier_name'] ?? '-' }}</p></div>
                                    <div><p class="text-zinc-400 font-medium">{{ $payTrans['biteship_weight'][$lang] ?? 'Weight' }}</p><p class="font-semibold text-black mt-0.5">{{ number_format((float) ($biteshipDetail['total_weight_kg'] ?? 0), 3, ',', '.') }} kg</p></div>
                                    <div><p class="text-zinc-400 font-medium">Shipping Cost</p><p class="font-semibold text-black mt-0.5">{{ $biteshipDetail['shipping_cost'] ?? '-' }}</p></div>
                                    <div><p class="text-zinc-400 font-medium">{{ $payTrans['biteship_driver_name'][$lang] ?? 'Driver Name' }}</p><p class="font-semibold text-black mt-0.5">{{ $biteshipDetail['driver_name'] ?? '-' }}</p></div>
                                    <div><p class="text-zinc-400 font-medium">{{ $payTrans['biteship_driver_phone'][$lang] ?? 'Driver Phone' }}</p><p class="font-semibold text-black mt-0.5">{{ $biteshipDetail['driver_phone'] ?? '-' }}</p></div>
                                    <div><p class="text-zinc-400 font-medium">{{ $payTrans['biteship_plate_number'][$lang] ?? 'License Plate' }}</p><p class="font-semibold text-black mt-0.5">{{ $biteshipDetail['vehicle_number'] ?? '-' }}</p></div>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="rounded-xl border border-zinc-200 p-4 bg-white">
                                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400">{{ $payTrans['biteship_addr_pickup'][$lang] ?? 'Pickup Address' }}</p>
                                    <p class="text-xs font-bold text-black">{{ data_get($biteshipDetail, 'pickup.name', '-') }}</p>
                                    <p class="text-xs text-zinc-500 mt-0.5">{{ data_get($biteshipDetail, 'pickup.phone', '-') }}</p>
                                    <p class="mt-2 text-xs text-zinc-600 leading-relaxed">{{ data_get($biteshipDetail, 'pickup.address', '-') }}</p>
                                </div>
                                <div class="rounded-xl border border-zinc-200 p-4 bg-white">
                                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400">{{ $payTrans['biteship_addr_receiver'][$lang] ?? 'Receiver Address' }}</p>
                                    <p class="text-xs font-bold text-black">{{ data_get($biteshipDetail, 'receiver.name', '-') }}</p>
                                    <p class="text-xs text-zinc-500 mt-0.5">{{ data_get($biteshipDetail, 'receiver.phone', '-') }}</p>
                                    <p class="mt-2 text-xs text-zinc-600 leading-relaxed">{{ data_get($biteshipDetail, 'receiver.address', '-') }}</p>
                                </div>
                            </div>

                            <div class="rounded-xl border border-zinc-200 p-4 bg-white">
                                <p class="mb-3 text-[10px] font-bold uppercase tracking-wider text-zinc-400">{{ $payTrans['biteship_pkg_info'][$lang] ?? 'Package Information' }}</p>
                                <div class="space-y-3">
                                    @foreach(($biteshipDetail['items'] ?? []) as $idx => $itemDetail)
                                        <div class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-600 space-y-1">
                                            <p class="font-bold text-black mb-1.5">{{ $payTrans['biteship_item_name'][$lang] ?? 'Item Name' }} {{ $idx + 1 }}: <span class="font-semibold text-zinc-800">{{ $itemDetail['name'] ?? '-' }}</span></p>
                                            <div class="grid gap-2 md:grid-cols-2 pt-1">
                                                <p>{{ $payTrans['biteship_item_weight'][$lang] ?? 'Weight' }}: <span class="font-medium text-black">{{ number_format((float) ($itemDetail['weight_kg'] ?? 0), 3, ',', '.') }} kg</span></p>
                                                <p>{{ $payTrans['biteship_item_qty'][$lang] ?? 'Quantity' }}: <span class="font-medium text-black">{{ $itemDetail['quantity'] ?? 1 }}</span></p>
                                                <p>{{ $payTrans['biteship_item_price'][$lang] ?? 'Price' }}: <span class="font-medium text-black">{{ $itemDetail['price'] ?? '-' }}</span></p>
                                                <p>{{ $payTrans['biteship_item_dim'][$lang] ?? 'Dimension' }}: <span class="font-medium text-black">{{ $itemDetail['dimension'] ?? '-' }}</span></p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-xl border border-zinc-200 p-4 text-xs space-y-1 bg-white">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">{{ $payTrans['biteship_notes'][$lang] ?? 'Notes' }}</p>
                                <p class="font-medium text-black pt-0.5">{{ $biteshipDetail['note'] ?? '-' }}</p>
                            </div>

                            <div class="rounded-xl border border-zinc-200 p-4 text-xs bg-white">
                                <p class="mb-3 text-[10px] font-bold uppercase tracking-wider text-zinc-400">{{ $payTrans['biteship_billing'][$lang] ?? 'Billing Details' }}</p>
                                <div class="flex items-center justify-between py-1 text-zinc-600">
                                    <span>Shipping Cost</span>
                                    <span class="font-semibold text-black">{{ data_get($biteshipDetail, 'billing.shipping_cost', '-') }}</span>
                                </div>
                                <div class="mt-2 border-t border-zinc-100 pt-2.5 flex items-center justify-between text-sm">
                                    <span class="font-bold text-black">{{ $payTrans['biteship_total_bill'][$lang] ?? 'Total Bill' }}</span>
                                    <span class="font-bold text-black">{{ data_get($biteshipDetail, 'billing.total', $order->formatted_total) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                @if($order->shipment_stage === 'pengantaran')
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-black">{{ $payTrans['live_track_title'][$lang] ?? 'Lacak Posisi Kurir' }}</h3>
                        <div class="flex items-center gap-2 text-xs text-emerald-600">
                            <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="font-semibold">Live</span>
                        </div>
                    </div>
                    <div id="courierMap" class="relative rounded-xl overflow-hidden shadow-inner border border-zinc-100" style="z-index: 1; height: 300px;">
                        <div class="absolute inset-0 flex items-center justify-center bg-zinc-50 z-10" id="mapLoader">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin text-2xl text-zinc-400 mb-2"></i>
                                <p class="text-xs text-zinc-500">{{ $payTrans['live_track_loading'][$lang] ?? 'Memuat peta...' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl bg-zinc-50/50 p-4 border border-zinc-200 space-y-2 text-xs">
                        <div class="flex items-center gap-2 mb-2 pb-1 border-b border-zinc-100">
                            <i class="fas fa-clock text-zinc-400"></i>
                            <span class="font-bold text-zinc-800">{{ $payTrans['live_track_est'][$lang] ?? 'Estimasi Pengiriman' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">{{ $payTrans['live_track_service'][$lang] ?? 'Layanan' }}</span>
                            <span class="font-semibold text-black">{{ $order->courier_service_name ?? 'Reguler' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500">{{ $payTrans['live_track_eta'][$lang] ?? 'Estimasi Tiba' }}</span>
                            <span class="font-semibold text-black">{{ $order->calculated_estimated_delivery }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($order->shipment_stage === 'selesai')
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                    <h3 class="mb-4 text-base font-bold text-black">{{ $payTrans['proof_title'][$lang] ?? 'Delivery Proof' }}</h3>
                    @if($order->delivery_photo)
                        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 p-2.5">
                            <img
                                src="{{ config('filesystems.disks.r2.url').'/' . $order->delivery_photo }}"
                                class="w-full h-auto rounded-lg cursor-pointer hover:opacity-95 transition shadow-sm"
                                onclick="openPhotoModal('{{ config('filesystems.disks.r2.url').'/' . $order->delivery_photo }}', 'Delivery Photo Proof')"
                                alt="Delivery Proof">
                            <p class="mt-2.5 text-center text-[11px] text-zinc-400">{{ $payTrans['proof_click_hint'][$lang] ?? 'Order is completed. Click image to enlarge.' }}</p>
                        </div>
                    @else
                        <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 text-xs text-zinc-500 leading-relaxed">
                            {{ $payTrans['proof_not_available'][$lang] ?? 'Order is completed, but delivery photo proof is not yet available.' }}
                        </div>
                    @endif
                </div>
                @endif

                @if(($order->courier_driver_name || $order->courier_id) && in_array($order->status, ['ready_to_ship', 'shipped', 'on_delivery', 'delivered', 'completed']))
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                    <h3 class="mb-4 text-base font-bold text-black">{{ $payTrans['courier_info_title'][$lang] ?? 'Courier Information' }}</h3>
                    <div class="flex items-center gap-3">
                        <img src="{{ $order->courier_driver_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($order->courier_driver_name) }}"
                             class="h-14 w-14 rounded-full object-cover border-2 border-white shadow-sm" alt="{{ $order->courier_driver_name }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="font-bold text-black text-sm truncate">{{ $order->courier_driver_name }}</p>
                                @if($order->courier_driver_rating)
                                    <span class="flex items-center gap-0.5 text-xs text-amber-500 font-medium">
                                        <i class="fas fa-star text-[10px]"></i>
                                        {{ number_format($order->courier_driver_rating, 1) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-500">{{ $order->courier_name ?? 'Ekspedisi' }}</p>
                            @if($order->courier_driver_vehicle)
                                <p class="text-[11px] text-zinc-400 mt-1 flex items-center gap-1">
                                    <i class="fas fa-motorcycle text-zinc-300"></i>
                                    <span>{{ $order->courier_driver_vehicle }} {{ $order->courier_driver_vehicle_number ? '- ' . $order->courier_driver_vehicle_number : '' }}</span>
                                </p>
                            @endif
                        </div>
                        @if($order->courier_driver_phone)
                            <a href="tel:{{ $order->courier_driver_phone }}"
                               class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition shadow-sm border border-emerald-100 flex-shrink-0">
                                <i class="fas fa-phone text-xs"></i>
                            </a>
                        @endif
                    </div>
                    @if($order->waybill_id)
                        <div class="mt-4 rounded-xl bg-zinc-50 p-3 border border-zinc-100">
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide mb-0.5">{{ $payTrans['biteship_receipt_no'][$lang] ?? 'Nomor Resi' }}</p>
                            <p class="font-mono text-xs font-bold text-black tracking-wider">{{ $order->waybill_id }}</p>
                        </div>
                    @endif
                </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                    <h3 class="mb-4 text-base font-bold text-black">{{ $payTrans['card_items_title'][$lang] ?? 'Order Items' }}</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-start gap-3">
                            <img src="{{ $item->product ? $item->product->image_url : 'https://via.placeholder.com/56' }}"
                                 class="h-14 w-14 rounded-xl object-cover border flex-shrink-0 shadow-sm bg-zinc-50" alt="{{ $item->product_name }}">
                            <div class="flex-1 min-w-0 text-xs">
                                <p class="font-bold text-zinc-900 line-clamp-2 leading-normal" style="font-size: 13px;">{{ $item->product_name }}</p>
                                <p class="text-zinc-400 mt-1 fw-medium">{{ $item->formatted_price }} × {{ $item->quantity }}</p>
                                <p class="font-bold text-black mt-1" style="font-size: 13px;">{{ $item->formatted_subtotal }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                    <h3 class="mb-4 text-base font-bold text-black">{{ $payTrans['card_address_title'][$lang] ?? 'Alamat Pengiriman' }}</h3>
                    <div class="space-y-1.5 text-xs text-zinc-600 leading-relaxed">
                        <p class="font-bold text-black" style="font-size: 13px;">{{ $order->shipping_name }}</p>
                        <p class="font-medium text-zinc-500"><i class="fas fa-phone-alt text-[10px] me-1"></i>{{ $order->shipping_phone }}</p>
                        <p class="pt-1 text-zinc-600 border-t border-zinc-100 mt-2">{{ $order->shipping_address }}</p>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                    <h3 class="mb-4 text-base font-bold text-black">{{ $payTrans['card_summary'][$lang] ?? 'Ringkasan Pembayaran' }}</h3>
                    <div class="space-y-3 text-xs border-b border-zinc-100 pb-3">
                        <div class="flex justify-between text-zinc-500">
                            <span>{{ $payTrans['label_subtotal'][$lang] ?? 'Subtotal' }}</span>
                            <span class="font-semibold text-zinc-800">{{ $order->formatted_subtotal }}</span>
                        </div>
                        @if($order->product_discount > 0)
                        <div class="flex justify-between text-emerald-600 font-medium">
                            <span>{{ $payTrans['summary_product_disc'][$lang] ?? 'Diskon Produk' }}</span>
                            <span>-{{ $order->formatted_product_discount }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-zinc-500">
                            <span>{{ $payTrans['label_shipping'][$lang] ?? 'Ongkir' }}</span>
                            <span class="font-semibold text-zinc-800">{{ data_get($biteshipDetail, 'billing.shipping_cost', $order->formatted_shipping_cost) }}</span>
                        </div>
                        @if($order->shipping_discount > 0)
                        <div class="flex justify-between text-emerald-600 font-medium">
                            <span>{{ $payTrans['summary_shipping_disc'][$lang] ?? 'Diskon Ongkir' }}</span>
                            <span>-{{ $order->formatted_shipping_discount }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="pt-3 flex justify-between font-bold text-sm text-black">
                        <span>{{ $payTrans['label_total'][$lang] ?? 'Total' }}</span>
                        <span class="text-base tracking-tight" style="color: #003C52;">{{ $order->formatted_total }}</span>
                    </div>

                    @if($order->payment_method)
                        <div class="mt-4 pt-4 border-t border-zinc-100">
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide mb-2.5">{{ $payTrans['payment_method_label'][$lang] ?? 'Metode Pembayaran' }}</p>

                            @if(strtolower($order->payment_method) === 'cod' || strtolower($order->payment_gateway) === 'cod')
                                <div class="rounded-xl bg-amber-50/50 border border-amber-200 p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 shadow-sm flex-shrink-0">
                                            <i class="fas fa-hand-holding-usd text-amber-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1 text-xs">
                                            <p class="font-bold text-amber-900" style="font-size: 13px;">Cash on Delivery (COD)</p>
                                            <p class="text-amber-700 mt-0.5">{{ $payTrans['payment_cod_subtitle'][$lang] ?? 'Bayar saat barang diterima' }}</p>
                                            <div class="space-y-1.5 mt-3 pt-2.5 border-t border-amber-200/50">
                                                <div class="flex items-center gap-2 text-amber-800 font-medium">
                                                    <i class="fas fa-check-circle text-amber-600 text-[10px]"></i>
                                                    <span>{{ $payTrans['payment_cod_hint_1'][$lang] ?? 'Siapkan uang pas' }} <strong>{{ $order->formatted_total }}</strong></span>
                                                </div>
                                                <div class="flex items-center gap-2 text-amber-800 font-medium">
                                                    <i class="fas fa-check-circle text-amber-600 text-[10px]"></i>
                                                    <span>{{ $payTrans['payment_cod_hint_2'][$lang] ?? 'Bayar langsung ke kurir' }}</span>
                                                </div>
                                                @if($order->status === 'processing' || $order->status === 'ready_to_ship')
                                                <div class="flex items-center gap-2 text-amber-700">
                                                    <i class="fas fa-clock text-amber-500 text-[10px]"></i>
                                                    <span>{{ $payTrans['payment_cod_hint_3'][$lang] ?? 'Kurir akan menghubungi Anda' }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-xl bg-zinc-50 border border-zinc-200 p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white border shadow-sm flex-shrink-0">
                                            <i class="fas fa-credit-card text-zinc-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1 text-xs">
                                            <p class="font-bold text-black" style="font-size: 13px;">
                                                {{ ucfirst($order->payment_gateway ?? 'Online Payment') }}
                                            </p>
                                            <p class="text-zinc-500 font-medium mt-0.5">
                                                {{ $order->payment_channel ? str_replace('_', ' ', ucwords($order->payment_channel, '_')) : 'Online Payment' }}
                                            </p>
                                            <div class="mt-2.5 pt-2 border-t border-zinc-200/60">
                                                @if($order->payment_status === 'paid')
                                                    <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                                                        <i class="fas fa-check-circle text-emerald-500"></i>
                                                        <span>{{ $payTrans['payment_online_p_status'][$lang] ?? 'Pembayaran berhasil' }}</span>
                                                    </div>
                                                    @if($order->paid_at)
                                                    <div class="flex items-center gap-1.5 text-zinc-400 mt-1 font-medium text-[11px]">
                                                        <i class="fas fa-calendar-check text-zinc-300"></i>
                                                        <span>{{ $order->paid_at->format('d M Y, H:i') }}</span>
                                                    </div>
                                                    @endif
                                                @elseif($order->payment_status === 'pending')
                                                    <div class="flex items-center gap-1.5 text-amber-700 font-semibold">
                                                        <i class="fas fa-clock text-amber-500"></i>
                                                        <span>{{ $payTrans['wait_title'][$lang] ?? 'Menunggu pembayaran' }}</span>
                                                    </div>
                                                @elseif($order->payment_status === 'pending_verification')
                                                    <div class="flex items-center gap-1.5 text-blue-700 font-semibold">
                                                        <i class="fas fa-hourglass-half text-blue-500"></i>
                                                        <span>{{ $payTrans['payment_online_p_verif'][$lang] ?? 'Menunggu verifikasi admin' }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="space-y-3">
                    @if($order->canUploadPaymentProof())
                    <a href="{{ route('customer.payment.paylabs.show', $order) }}"
                       class="flex w-full items-center justify-center rounded-xl bg-black py-3 text-center text-sm font-semibold text-white transition hover:bg-black/90 shadow-sm">
                        {{ $payTrans['btn_pay'][$lang] ?? 'Pay Now' }}
                    </a>
                    @endif

                    @if($order->canBeCancelled())
                    <button type="button" onclick="showCancelModal()"
                       class="flex w-full items-center justify-center rounded-xl border border-red-200 bg-white py-3 text-center text-sm font-semibold text-red-600 transition hover:bg-red-50/60 shadow-sm">
                        {{ $payTrans['cancel_btn'][$lang] ?? 'Cancel Order' }}
                    </button>
                    @endif
                    <div class="pt-2 flex gap-3">
                        <a href="{{ route('customer.orders.index') }}" 
                           class="flex-1 block w-full rounded-xl border border-zinc-200 bg-white py-3 text-center text-sm font-semibold text-black transition hover:bg-zinc-50 hover:border-zinc-300 shadow-sm">
                            {{ $payTrans['btn_back'][$lang] ?? 'Kembali' }}
                        </a>
                        
                        <form action="{{ route('customer.orders.simulate-completed', $order) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-600 border border-emerald-600 py-3 text-center text-sm font-semibold text-white transition hover:bg-emerald-700 shadow-sm">
                                Simulasi Selesai
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="photoModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" onclick="closePhotoModal()">
    <div class="relative max-w-4xl w-full mx-4" onclick="event.stopPropagation()">
        <button type="button" onclick="closePhotoModal()" class="absolute -top-10 right-0 text-white hover:text-zinc-300 transition">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <div class="bg-white rounded-2xl p-4 shadow-2xl">
            <h3 id="photoModalTitle" class="text-base font-bold text-black mb-3 border-b pb-2"></h3>
            <img id="photoModalImage" src="" class="w-full h-auto rounded-xl object-contain max-h-[75vh]" alt="Bukti Foto">
        </div>
    </div>
</div>

<div id="cancelModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" onclick="closeCancelModal(event)">
    <div class="relative mx-4 w-full max-w-md rounded-2xl bg-white p-6 md:p-8 shadow-2xl border" onclick="event.stopPropagation()">
        <div class="mb-5 flex justify-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-50 border border-red-100 shadow-sm">
                <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
            </div>
        </div>

        <h2 class="mb-2 text-center text-xl font-bold tracking-tight text-black">{{ $payTrans['cancel_modal_title'][$lang] ?? 'Cancel Order?' }}</h2>
        <p class="text-xs text-center text-zinc-500 mb-5">{{ $payTrans['cancel_modal_confirm'][$lang] ?? 'Are you sure you want to cancel this order?' }}</p>

        <div class="mb-5 text-xs">
            @if($order->requiresRefund())
            <div class="rounded-xl bg-blue-50/50 border border-blue-100 p-4 text-left">
                <h3 class="mb-2.5 flex items-center gap-1.5 font-bold text-blue-900">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    {{ $payTrans['cancel_modal_refund_info'][$lang] ?? 'Refund Information:' }}
                </h3>
                <ul class="space-y-2 text-blue-800 font-medium">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle mt-0.5 text-blue-500 text-[10px]"></i>
                        <span>{!! str_replace(':total', $order->formatted_total, $payTrans['cancel_modal_refund_hint_1'][$lang] ?? 'Amount of <strong>:total</strong> will be refunded') !!}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle mt-0.5 text-blue-500 text-[10px]"></i>
                        <span>{{ $payTrans['cancel_modal_refund_hint_2'][$lang] ?? 'Refund process takes 1-3 business days' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle mt-0.5 text-blue-500 text-[10px]"></i>
                        <span>{{ $payTrans['cancel_modal_refund_hint_3'][$lang] ?? 'Product stock will be returned' }}</span>
                    </li>
                </ul>
            </div>
            @else
            <div class="rounded-xl bg-zinc-50 border p-3.5 text-center font-medium text-zinc-500">
                <p>{{ $payTrans['cancel_modal_stock_hint'][$lang] ?? 'Product stock will be returned after cancellation.' }}</p>
            </div>
            @endif
        </div>

        <form action="{{ route('customer.orders.cancel', $order) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-5 text-xs">
                <label class="block font-semibold text-zinc-700 mb-2">{{ $payTrans['cancel_modal_reason_label'][$lang] ?? 'Cancellation Reason (Optional)' }}</label>
                <textarea name="cancel_reason" rows="3" class="w-full rounded-xl border border-zinc-200 px-3 py-2 text-zinc-800 placeholder-zinc-400 focus:border-black focus:outline-none focus:ring-4 focus:ring-black/5 transition-all" placeholder="{{ $payTrans['cancel_modal_placeholder'][$lang] ?? 'Provide cancellation reason...' }}"></textarea>
            </div>

            <div class="flex gap-3 text-sm">
                <button type="button" onclick="closeCancelModal()"
                        class="flex-1 rounded-xl border border-zinc-200 bg-white py-3 font-semibold text-zinc-800 transition hover:bg-zinc-50 shadow-sm">
                    {{ $payTrans['cancel_modal_btn_no'][$lang] ?? 'No' }}
                </button>
                <button type="submit"
                        class="flex-1 rounded-xl bg-red-600 py-3 font-semibold text-white transition hover:bg-red-700 shadow-sm">
                    {{ $payTrans['cancel_modal_btn_yes'][$lang] ?? 'Yes, Cancel' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle Photo Dropdown
function togglePhotoDropdown(id) {
    const dropdown = document.getElementById(id);
    const allDropdowns = document.querySelectorAll('[id^="photo-"]');

    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== id) {
            d.classList.add('hidden');
        }
    });

    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Toggle Order Details panel
function toggleOrderDetailPanel() {
    const panel = document.getElementById('orderDetailPanel');
    const label = document.getElementById('orderDetailToggleLabel');

    if (!panel || !label) return;

    panel.classList.toggle('hidden');
    const isHidden = panel.classList.contains('hidden');
    label.textContent = isHidden ? 'View Details' : 'Hide Details';
}

// Open Photo Modal
function openPhotoModal(imageUrl, title) {
    document.getElementById('photoModalImage').src = imageUrl;
    document.getElementById('photoModalTitle').textContent = title;
    document.getElementById('photoModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close Photo Modal
function closePhotoModal() {
    document.getElementById('photoModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Show Cancel Modal
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close Cancel Modal
function closeCancelModal(event) {
    if (!event || event.target.id === 'cancelModal') {
        document.getElementById('cancelModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('[id^="photo-"]');
    const isClickInside = event.target.closest('[id^="photo-"]') || event.target.closest('button[onclick^="togglePhotoDropdown"]');

    if (!isClickInside) {
        dropdowns.forEach(d => d.classList.add('hidden'));
    }
});

// Expiration Timer
@if($order->status === 'pending_payment' && !$order->isExpired())
let expirationSeconds = {{ $order->expiration_time }};
const timerElement = document.getElementById('expirationTimer');

function updateTimer() {
    if (expirationSeconds <= 0) {
        timerElement.textContent = 'Expired';
        timerElement.classList.add('text-red-600');
        // Reload page to show expired state
        setTimeout(() => location.reload(), 2000);
        return;
    }

    const hours = Math.floor(expirationSeconds / 3600);
    const minutes = Math.floor((expirationSeconds % 3600) / 60);
    const seconds = expirationSeconds % 60;

    timerElement.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

    expirationSeconds--;
}

// Update timer every second
setInterval(updateTimer, 1000);
updateTimer();
@endif
</script>

@if($order->shipment_stage === 'pengantaran')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script>
let map, courierMarker, destinationMarker, routingControl;
let courierPosition = null;
let updateInterval = null;
let routeCoordinates = [];
let currentRouteIndex = 0;

// Motor icon - Simple and clear
const motorIcon = L.divIcon({
    html: `
        <div class="courier-marker" style="position: relative; width: 50px; height: 50px;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 46px; height: 46px; background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); border-radius: 50%; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.5); display: flex; align-items: center; justify-content: center; border: 3px solid white;">
                <i class="fas fa-person-biking" style="color: white; font-size: 24px;"></i>
            </div>
            <div style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); background: rgba(59, 130, 246, 0.3); width: 40px; height: 8px; border-radius: 50%; filter: blur(4px);"></div>
        </div>
    `,
    className: '',
    iconSize: [50, 50],
    iconAnchor: [25, 25]
});

const destinationIcon = L.divIcon({
    html: `
        <div style="position: relative; width: 40px; height: 50px;">
            <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 36px; height: 36px; background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); border-radius: 50% 50% 50% 0; transform: translateX(-50%) rotate(-45deg); box-shadow: 0 3px 10px rgba(239, 68, 68, 0.4); border: 3px solid white;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(45deg); width: 12px; height: 12px; background: white; border-radius: 50%;"></div>
            </div>
        </div>
    `,
    className: '',
    iconSize: [40, 50],
    iconAnchor: [20, 40]
});

function initMap() {
    const destination = {
        lat: {{ $order->shipping_latitude ?? -6.2088 }},
        lng: {{ $order->shipping_longitude ?? 106.8456 }}
    };

    // Initialize map with proper z-index
    map = L.map('courierMap', {
        zoomControl: true,
        attributionControl: true
    }).setView([destination.lat, destination.lng], 14);

    // Set z-index for map container
    const mapContainer = document.getElementById('courierMap');
    if (mapContainer) {
        mapContainer.style.zIndex = '1';
    }

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Add destination marker only
    destinationMarker = L.marker([destination.lat, destination.lng], { icon: destinationIcon }).addTo(map);
    destinationMarker.bindPopup(`
        <div style="font-family: system-ui; padding: 4px; max-width: 200px;">
            <div style="font-weight: 600; margin-bottom: 4px;">📍 Alamat Tujuan</div>
            <div style="font-size: 12px; color: #666; margin-bottom: 4px;">{{ $order->shipping_name }}</div>
            <div style="font-size: 11px; color: #888;">{{ $order->shipping_address }}</div>
        </div>
    `);

    // Hide loader
    document.getElementById('mapLoader').style.display = 'none';

    // Start tracking
    updateCourierLocation();
    updateInterval = setInterval(updateCourierLocation, 3000); // Update every 3 seconds
}

function updateCourierLocation() {
    fetch('{{ route('customer.orders.courier-location', $order) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.location) {
                const newPosition = {
                    lat: parseFloat(data.location.latitude),
                    lng: parseFloat(data.location.longitude)
                };

                if (!courierMarker) {
                    // Create courier marker
                    courierMarker = L.marker([newPosition.lat, newPosition.lng], {
                        icon: motorIcon,
                        zIndexOffset: 1000
                    }).addTo(map);

                    courierMarker.bindPopup(`
                        <div style="font-family: system-ui; padding: 6px;">
                            <div style="font-weight: 600; margin-bottom: 4px;">🛵 ${data.courier.name || 'Kurir'}</div>
                            <div style="font-size: 12px; color: #666; margin-bottom: 2px;">{{ $order->courier_name ?? 'Ekspedisi' }}</div>
                            <div style="font-size: 10px; color: #10B981; margin-top: 4px;">● Sedang menuju lokasi Anda</div>
                        </div>
                    `);

                    // Create routing
                    createRoute(newPosition, {
                        lat: {{ $order->shipping_latitude ?? -6.2088 }},
                        lng: {{ $order->shipping_longitude ?? 106.8456 }}
                    });
                } else {
                    // Smooth animation to new position
                    animateMarker(courierMarker, newPosition);

                    // Update route
                    if (routingControl) {
                        map.removeControl(routingControl);
                    }
                    createRoute(newPosition, {
                        lat: {{ $order->shipping_latitude ?? -6.2088 }},
                        lng: {{ $order->shipping_longitude ?? 106.8456 }}
                    });
                }

                courierPosition = newPosition;

                // Fit bounds to show all markers
                const bounds = L.latLngBounds([
                    [newPosition.lat, newPosition.lng],
                    [{{ $order->shipping_latitude ?? -6.2088 }}, {{ $order->shipping_longitude ?? 106.8456 }}]
                ]);
                map.fitBounds(bounds, { padding: [50, 50] });
            } else {
                console.log('Courier location not available:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching courier location:', error);
        });
}

function createRoute(origin, destination) {
    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(origin.lat, origin.lng),
            L.latLng(destination.lat, destination.lng)
        ],
        routeWhileDragging: false,
        addWaypoints: false,
        draggableWaypoints: false,
        fitSelectedRoutes: false,
        showAlternatives: false,
        lineOptions: {
            styles: [{
                color: '#3B82F6',
                opacity: 0.8,
                weight: 5
            }]
        },
        createMarker: function() { return null; }, // Hide default markers
    }).addTo(map);

    // Get route info
    routingControl.on('routesfound', function(e) {
        const routes = e.routes;
        // Store route coordinates for smooth animation
        routeCoordinates = routes[0].coordinates;
    });
}

function animateMarker(marker, newPosition) {
    const startLatLng = marker.getLatLng();
    const endLatLng = L.latLng(newPosition.lat, newPosition.lng);

    let step = 0;
    const numSteps = 30;
    const delay = 100;

    function animate() {
        step++;
        if (step > numSteps) return;

        const progress = step / numSteps;
        const lat = startLatLng.lat + (endLatLng.lat - startLatLng.lat) * progress;
        const lng = startLatLng.lng + (endLatLng.lng - startLatLng.lng) * progress;

        marker.setLatLng([lat, lng]);

        if (step < numSteps) {
            setTimeout(animate, delay);
        }
    }

    animate();
}

// Initialize map when page loads
if (typeof L !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
} else {
    window.addEventListener('load', initMap);
}

// Cleanup interval on page unload
window.addEventListener('beforeunload', () => {
    if (updateInterval) {
        clearInterval(updateInterval);
    }
});
</script>
@endif
@endsection
