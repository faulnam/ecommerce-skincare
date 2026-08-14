<!-- J&T Express Label -->
<div style="border: 3px solid #EF4444; padding: 0; background: white; font-family: Arial, sans-serif;">
    <!-- Header J&T -->
    <div style="background: #EF4444; padding: 20px; text-align: center;">
        <div style="font-size: 48px; font-weight: 900; color: white; letter-spacing: 3px;">J&T Express</div>
        <div style="font-size: 14px; color: white; margin-top: 5px; letter-spacing: 1px;">PENGIRIMAN CEPAT & TERPERCAYA</div>
    </div>

    <!-- Barcode Resi -->
    <div style="text-align: center; padding: 30px 20px; background: #FEF2F2; border-bottom: 2px dashed #EF4444;">
        <div style="font-size: 16px; color: #991B1B; font-weight: 600; margin-bottom: 10px;">NOMOR RESI</div>
        <svg style="width: 100%; max-width: 400px; height: 80px; margin: 0 auto;">
            @php
                $waybill = $order->waybill_id ?? 'JT000000000000';
                $barWidth = 3;
                $barSpacing = 2;
                $totalWidth = strlen($waybill) * ($barWidth + $barSpacing);
            @endphp
            @foreach(str_split($waybill) as $index => $char)
                <rect x="{{ $index * ($barWidth + $barSpacing) }}" y="0" width="{{ $barWidth }}" height="60" fill="#000"/>
            @endforeach
        </svg>
        <div style="font-size: 32px; font-weight: 900; color: #991B1B; letter-spacing: 3px; margin-top: 10px; font-family: 'Courier New', monospace;">
            {{ $order->waybill_id ?? 'JT000000000000' }}
        </div>
    </div>

    <!-- Info Pengiriman -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-bottom: 3px solid #EF4444;">
        <!-- Pengirim -->
        <div style="padding: 20px; border-right: 2px solid #EF4444;">
            <div style="background: #EF4444; color: white; padding: 8px 12px; font-weight: 700; font-size: 14px; margin-bottom: 12px;">
                📤 PENGIRIM
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
            <div style="background: #EF4444; color: white; padding: 8px 12px; font-weight: 700; font-size: 14px; margin-bottom: 12px;">
                📥 PENERIMA
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
    <div style="padding: 20px; background: #FEF2F2;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <div style="font-size: 11px; color: #991B1B; font-weight: 600; margin-bottom: 4px;">LAYANAN</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ $order->courier_service_name ?? 'EZ (Reguler)' }}</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #991B1B; font-weight: 600; margin-bottom: 4px;">BERAT</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ number_format($order->items->sum(fn($i) => ($i->product->weight ?? 500) * $i->quantity) / 1000, 1) }} kg</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #991B1B; font-weight: 600; margin-bottom: 4px;">TANGGAL</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ $order->created_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="background: #1F2937; color: white; padding: 15px 20px; text-align: center; font-size: 11px;">
        <div style="font-weight: 600; margin-bottom: 5px;">J&T Express - Pengiriman Cepat & Terpercaya</div>
        <div style="opacity: 0.8;">Customer Service: 021-8066-1888 | www.jet.co.id</div>
    </div>
</div>
