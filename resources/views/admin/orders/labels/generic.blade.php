<!-- Generic Label -->
<div style="border: 3px solid #6B7280; padding: 0; background: white; font-family: Arial, sans-serif;">
    <!-- Header -->
    <div style="background: #374151; padding: 20px; text-align: center;">
        <div style="font-size: 42px; font-weight: 900; color: white; letter-spacing: 2px;">
            {{ strtoupper($order->courier_name ?? 'EKSPEDISI') }}
        </div>
        <div style="font-size: 14px; color: white; margin-top: 5px; letter-spacing: 1px;">LAYANAN PENGIRIMAN</div>
    </div>

    <!-- Barcode Resi -->
    <div style="text-align: center; padding: 30px 20px; background: #F3F4F6; border-bottom: 2px dashed #6B7280;">
        <div style="font-size: 16px; color: #374151; font-weight: 600; margin-bottom: 10px;">NOMOR RESI</div>
        <svg style="width: 100%; max-width: 400px; height: 80px; margin: 0 auto;">
            @php
                $waybill = $order->waybill_id ?? 'TRACK000000000';
                $barWidth = 3;
                $barSpacing = 2;
            @endphp
            @foreach(str_split($waybill) as $index => $char)
                <rect x="{{ $index * ($barWidth + $barSpacing) }}" y="0" width="{{ $barWidth }}" height="60" fill="#000"/>
            @endforeach
        </svg>
        <div style="font-size: 32px; font-weight: 900; color: #374151; letter-spacing: 3px; margin-top: 10px; font-family: 'Courier New', monospace;">
            {{ $order->waybill_id ?? 'TRACK000000000' }}
        </div>
    </div>

    <!-- Info Pengiriman -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-bottom: 3px solid #6B7280;">
        <!-- Pengirim -->
        <div style="padding: 20px; border-right: 2px solid #6B7280;">
            <div style="background: #6B7280; color: white; padding: 8px 12px; font-weight: 700; font-size: 14px; margin-bottom: 12px;">
                📤 PENGIRIM
            </div>
            <div style="font-size: 16px; font-weight: 700; color: #1F2937; margin-bottom: 8px;">
                {{ config('branding.name', 'LUMINA Store') }}
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
            <div style="background: #6B7280; color: white; padding: 8px 12px; font-weight: 700; font-size: 14px; margin-bottom: 12px;">
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
    <div style="padding: 20px; background: #F3F4F6;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <div style="font-size: 11px; color: #374151; font-weight: 600; margin-bottom: 4px;">LAYANAN</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ $order->courier_service_name ?? 'Reguler' }}</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #374151; font-weight: 600; margin-bottom: 4px;">BERAT</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ number_format($order->items->sum(fn($i) => ($i->product->weight ?? 500) * $i->quantity) / 1000, 1) }} kg</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #374151; font-weight: 600; margin-bottom: 4px;">TANGGAL</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ $order->created_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="background: #1F2937; color: white; padding: 15px 20px; text-align: center; font-size: 11px;">
        <div style="font-weight: 600; margin-bottom: 5px;">{{ $order->courier_name ?? 'Ekspedisi' }} - Layanan Pengiriman Terpercaya</div>
        <div style="opacity: 0.8;">Terima kasih telah menggunakan layanan kami</div>
    </div>
</div>
