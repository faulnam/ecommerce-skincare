<!-- Paxel Label -->
<div style="border: 3px solid #10B981; padding: 0; background: white; font-family: Arial, sans-serif;">
    <!-- Header Paxel -->
    <div style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); padding: 20px; text-align: center;">
        <div style="font-size: 48px; font-weight: 900; color: white; letter-spacing: 2px;">PAXEL</div>
        <div style="font-size: 14px; color: white; margin-top: 5px; letter-spacing: 1px;">SAME DAY & INSTANT DELIVERY</div>
    </div>

    <!-- Barcode Resi -->
    <div style="text-align: center; padding: 30px 20px; background: #ECFDF5; border-bottom: 2px dashed #10B981;">
        <div style="font-size: 16px; color: #065F46; font-weight: 600; margin-bottom: 10px;">TRACKING NUMBER</div>
        <svg style="width: 100%; max-width: 400px; height: 80px; margin: 0 auto;">
            @php
                $waybill = $order->waybill_id ?? 'PXL00000000AB';
                $barWidth = 3;
                $barSpacing = 2;
            @endphp
            @foreach(str_split(substr($waybill, 0, -2)) as $index => $char)
                <rect x="{{ $index * ($barWidth + $barSpacing) }}" y="0" width="{{ $barWidth }}" height="60" fill="#000"/>
            @endforeach
        </svg>
        <div style="font-size: 32px; font-weight: 900; color: #065F46; letter-spacing: 3px; margin-top: 10px; font-family: 'Courier New', monospace;">
            {{ $order->waybill_id ?? 'PXL00000000AB' }}
        </div>
    </div>

    <!-- Info Pengiriman -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-bottom: 3px solid #10B981;">
        <!-- Pengirim -->
        <div style="padding: 20px; border-right: 2px solid #10B981;">
            <div style="background: #10B981; color: white; padding: 8px 12px; font-weight: 700; font-size: 14px; margin-bottom: 12px; border-radius: 6px;">
                📤 SENDER
            </div>
            <div style="font-size: 16px; font-weight: 700; color: #1F2937; margin-bottom: 8px;">
                {{ config('branding.name', 'Hijab Store') }}
            </div>
            <div style="font-size: 13px; color: #4B5563; line-height: 1.6;">
                {{ config('branding.address', 'Kec. Tarik, Sidoarjo, Jawa Timur 61265') }}
            </div>
            <div style="font-size: 13px; color: #4B5563; margin-top: 6px;">
                📞 {{ config('branding.phone', '+62 812 3456 7890') }}
            </div>
        </div>

        <!-- Penerima -->
        <div style="padding: 20px;">
            <div style="background: #10B981; color: white; padding: 8px 12px; font-weight: 700; font-size: 14px; margin-bottom: 12px; border-radius: 6px;">
                📥 RECEIVER
            </div>
            <div style="font-size: 16px; font-weight: 700; color: #1F2937; margin-bottom: 8px;">
                {{ $order->shipping_name }}
            </div>
            <div style="font-size: 13px; color: #4B5563; line-height: 1.6;">
                {{ $order->shipping_address }}
            </div>
            <div style="font-size: 13px; color: #4B5563; margin-top: 6px;">
                📞 {{ $order->shipping_phone }}
            </div>
        </div>
    </div>

    <!-- Detail Paket -->
    <div style="padding: 20px; background: #ECFDF5;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
            <div>
                <div style="font-size: 11px; color: #065F46; font-weight: 600; margin-bottom: 4px;">SERVICE</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ $order->courier_service_name ?? 'Regular' }}</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #065F46; font-weight: 600; margin-bottom: 4px;">WEIGHT</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ number_format($order->items->sum(fn($i) => ($i->product->weight ?? 500) * $i->quantity) / 1000, 1) }} kg</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #065F46; font-weight: 600; margin-bottom: 4px;">DATE</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ $order->created_at->format('d/m/Y') }}</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #065F46; font-weight: 600; margin-bottom: 4px;">TIME</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ $order->created_at->format('H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- QR Code Section -->
    <div style="padding: 20px; text-align: center; background: white; border-top: 2px solid #10B981;">
        <div style="display: inline-block; padding: 15px; border: 2px solid #10B981; border-radius: 8px;">
            <svg width="120" height="120" viewBox="0 0 120 120">
                <!-- Simple QR Code representation -->
                <rect x="0" y="0" width="120" height="120" fill="white"/>
                @for($i = 0; $i < 12; $i++)
                    @for($j = 0; $j < 12; $j++)
                        @if(($i + $j) % 2 === 0)
                            <rect x="{{ $i * 10 }}" y="{{ $j * 10 }}" width="10" height="10" fill="#000"/>
                        @endif
                    @endfor
                @endfor
            </svg>
            <div style="font-size: 10px; color: #065F46; margin-top: 8px; font-weight: 600;">SCAN FOR TRACKING</div>
        </div>
    </div>

    <!-- Footer -->
    <div style="background: #065F46; color: white; padding: 15px 20px; text-align: center; font-size: 11px;">
        <div style="font-weight: 600; margin-bottom: 5px;">Paxel - Same Day & Instant Delivery</div>
        <div style="opacity: 0.8;">Customer Service: 1500-959 | www.paxel.co</div>
    </div>
</div>
