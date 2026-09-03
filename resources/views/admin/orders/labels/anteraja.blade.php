<!-- AnterAja Label -->
<div style="border: 3px solid #3B82F6; padding: 0; background: white; font-family: Arial, sans-serif;">
    <!-- Header AnterAja -->
    <div style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); padding: 20px; text-align: center;">
        <div style="font-size: 48px; font-weight: 900; color: white; letter-spacing: 2px;">AnterAja</div>
        <div style="font-size: 14px; color: white; margin-top: 5px; letter-spacing: 1px;">KIRIM PAKET JADI MUDAH</div>
    </div>

    <!-- Barcode Resi -->
    <div style="text-align: center; padding: 30px 20px; background: #EFF6FF; border-bottom: 2px dashed #3B82F6;">
        <div style="font-size: 16px; color: #1E40AF; font-weight: 600; margin-bottom: 10px;">NOMOR RESI</div>
        <svg style="width: 100%; max-width: 450px; height: 80px; margin: 0 auto;">
            @php
                $waybill = $order->waybill_id ?? '100000000000000';
                $barWidth = 3;
                $barSpacing = 2;
            @endphp
            @foreach(str_split($waybill) as $index => $char)
                <rect x="{{ $index * ($barWidth + $barSpacing) }}" y="0" width="{{ $barWidth }}" height="60" fill="#000"/>
            @endforeach
        </svg>
        <div style="font-size: 32px; font-weight: 900; color: #1E40AF; letter-spacing: 3px; margin-top: 10px; font-family: 'Courier New', monospace;">
            {{ $order->waybill_id ?? '100000000000000' }}
        </div>
    </div>

    <!-- Info Pengiriman -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-bottom: 3px solid #3B82F6;">
        <!-- Pengirim -->
        <div style="padding: 20px; border-right: 2px solid #3B82F6;">
            <div style="background: #3B82F6; color: white; padding: 8px 12px; font-weight: 700; font-size: 14px; margin-bottom: 12px; border-radius: 4px;">
                📤 DARI
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
            <div style="background: #3B82F6; color: white; padding: 8px 12px; font-weight: 700; font-size: 14px; margin-bottom: 12px; border-radius: 4px;">
                📥 KEPADA
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
    <div style="padding: 20px; background: #EFF6FF;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
            <div>
                <div style="font-size: 11px; color: #1E40AF; font-weight: 600; margin-bottom: 4px;">LAYANAN</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ $order->courier_service_name ?? 'Reguler' }}</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #1E40AF; font-weight: 600; margin-bottom: 4px;">BERAT</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ number_format($order->items->sum(fn($i) => ($i->product->weight ?? 500) * $i->quantity) / 1000, 1) }} kg</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #1E40AF; font-weight: 600; margin-bottom: 4px;">TANGGAL</div>
                <div style="font-size: 14px; font-weight: 700; color: #1F2937;">{{ $order->created_at->format('d/m/Y') }}</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #1E40AF; font-weight: 600; margin-bottom: 4px;">ORDER ID</div>
                <div style="font-size: 12px; font-weight: 700; color: #1F2937;">{{ $order->order_number }}</div>
            </div>
        </div>
    </div>

    <!-- Instruksi -->
    <div style="padding: 15px 20px; background: #DBEAFE; border-top: 2px solid #3B82F6;">
        <div style="font-size: 11px; color: #1E40AF; line-height: 1.6;">
            <strong>INSTRUKSI:</strong> Harap tempelkan label ini pada paket dengan jelas. Pastikan barcode tidak rusak atau tertutup.
        </div>
    </div>

    <!-- Footer -->
    <div style="background: #1E40AF; color: white; padding: 15px 20px; text-align: center; font-size: 11px;">
        <div style="font-weight: 600; margin-bottom: 5px;">AnterAja - Kirim Paket Jadi Mudah</div>
        <div style="opacity: 0.8;">Customer Service: 021-5021-1234 | www.anteraja.id</div>
    </div>
</div>
