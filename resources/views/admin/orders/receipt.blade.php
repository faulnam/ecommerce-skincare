<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Pesanan - {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #16a34a;
            --dark: #1f2937;
            --gray: #6b7280;
            --light-gray: #f8fafc;
            --border: #e5e7eb;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: var(--dark);
            background: #f3f4f6;
        }
        
        .receipt-wrapper {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .receipt {
            padding: 40px;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 30px;
            border-bottom: 2px solid var(--primary);
            margin-bottom: 30px;
        }
        
        .brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .brand-logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .brand-info h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }
        
        .brand-info p {
            color: var(--gray);
            font-size: 12px;
        }
        
        .receipt-title {
            text-align: right;
        }
        
        .receipt-title h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: 2px;
        }
        
        .receipt-title p {
            color: var(--gray);
            font-size: 12px;
        }
        
        /* Order Info */
        .order-meta {
            display: flex;
            justify-content: space-between;
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .order-meta-item label {
            display: block;
            font-size: 11px;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .order-meta-item span {
            font-weight: 600;
            color: var(--dark);
        }
        
        .order-meta-item span.order-number {
            color: var(--primary);
            font-size: 16px;
        }
        
        /* Address Section */
        .address-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .address-box {
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 8px;
        }
        
        .address-box h3 {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }
        
        .address-box .name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
        }
        
        .address-box .detail {
            color: var(--gray);
            font-size: 12px;
        }
        
        /* Schedule Box */
        .schedule-box {
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.05) 0%, rgba(22, 163, 74, 0.1) 100%);
            border: 1px solid rgba(22, 163, 74, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .schedule-box h3 {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .schedule-item label {
            display: block;
            font-size: 11px;
            color: var(--gray);
            margin-bottom: 4px;
        }
        
        .schedule-item span {
            font-weight: 600;
            color: var(--dark);
        }
        
        /* Courier Box */
        .courier-box {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(37, 99, 235, 0.1) 100%);
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .courier-box h3 {
            font-size: 12px;
            font-weight: 600;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        
        .courier-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .courier-avatar {
            width: 50px;
            height: 50px;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
        }
        
        .courier-detail .name {
            font-weight: 600;
            color: var(--dark);
        }
        
        .courier-detail .phone {
            color: var(--gray);
            font-size: 12px;
        }
        
        /* Table */
        .items-section h3 {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background: var(--light-gray);
            padding: 12px 15px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Summary */
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        
        .summary-box {
            width: 280px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-row.total {
            background: var(--primary);
            color: white;
            margin: 10px -15px -10px;
            padding: 15px;
            border-radius: 0 0 8px 8px;
        }
        
        .summary-row.total span {
            font-weight: 700;
            font-size: 16px;
        }
        
        .summary-wrapper {
            background: var(--light-gray);
            padding: 15px;
            border-radius: 8px;
        }
        
        /* Notes */
        .notes-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
        }
        
        .notes-box h4 {
            font-size: 12px;
            font-weight: 600;
            color: #f59e0b;
            margin-bottom: 8px;
        }
        
        .notes-box p {
            color: var(--dark);
            font-size: 12px;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding-top: 30px;
            border-top: 2px solid var(--primary);
        }
        
        .footer p {
            color: var(--gray);
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .footer .thanks {
            font-weight: 600;
            color: var(--primary);
            font-size: 14px;
            margin-top: 10px;
        }
        
        /* Print Actions */
        .print-actions {
            text-align: center;
            padding: 20px;
            background: var(--light-gray);
        }
        
        .print-actions button,
        .print-actions a {
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        
        .btn-print {
            background: var(--primary);
            color: white;
            border: none;
            margin-right: 10px;
        }
        
        .btn-print:hover {
            background: #15803d;
        }
        
        .btn-back {
            background: white;
            color: var(--gray);
            border: 1px solid var(--border);
        }
        
        .btn-back:hover {
            background: var(--light-gray);
        }
        
        @media print {
            body {
                background: white;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .receipt-wrapper {
                box-shadow: none;
                margin: 0;
            }
            
            .receipt {
                padding: 20px;
            }
            
            .print-actions {
                display: none;
            }
            
            .schedule-box,
            .courier-box,
            .notes-box,
            .summary-wrapper {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-wrapper">
        <div class="print-actions">
            <button onclick="window.print()" class="btn-print">
                🖨️ Cetak Resi
            </button>
            <a href="{{ route('admin.orders.show', $order) }}" class="btn-back">
                ← Kembali
            </a>
        </div>

        <div class="receipt">
            <!-- Header -->
            <div class="header">
                <div class="brand">
                    <img src="{{ asset(config('branding.logo', 'storage/logo.png')) }}" alt="Hijab" class="brand-logo" style="width: 60px; height: 60px; object-fit: contain;">
                    <div class="brand-info">
                        <h1>{{ config('branding.name', 'Hijab') }}</h1>
                        <p>Perlengkapan Hijab Premium</p>
                        <p>{{ config('branding.address', 'Kec. Tarik, Sidoarjo, Jawa Timur 61265') }}</p>
                    </div>
                </div>
                
            </div>
            
            <!-- Order Meta -->
            <div class="order-meta">
                <div class="order-meta-item">
                    <label>No. Pesanan</label>
                    <span class="order-number">{{ $order->order_number }}</span>
                </div>
                <div class="order-meta-item">
                    <label>Tanggal Pesanan</label>
                    <span>{{ $order->created_at->format('d F Y') }}</span>
                </div>
                <div class="order-meta-item">
                    <label>Waktu Pesanan</label>
                    <span>{{ $order->created_at->format('H:i') }} WIB</span>
                </div>
                <div class="order-meta-item">
                    <label>Metode Bayar</label>
                    <span>{{ $order->payment_method === 'cod' ? 'COD' : 'Transfer' }}</span>
                </div>
            </div>
            
            <!-- Address Section -->
            <div class="address-section">
                <div class="address-box">
                    <h3>Pengirim</h3>
                    <p class="name">Hijab Store</p>
                    <p class="detail">Kec. Tarik, Kab. Sidoarjo</p>
                    <p class="detail">Jawa Timur 61265</p>
                    <p class="detail">Telp: {{ config('branding.phone', '+62 812 3456 7890') }}</p>
                </div>
                <div class="address-box">
                    <h3>Penerima</h3>
                    <p class="name">{{ $order->shipping_name }}</p>
                    <p class="detail">{{ $order->shipping_address }}</p>
                    <p class="detail">Telp: {{ $order->shipping_phone }}</p>
                </div>
            </div>

            <!-- Schedule -->
            <div class="schedule-box">
                <h3>Jadwal Pengiriman</h3>
                <div class="schedule-grid">
                    <div class="schedule-item">
                        <label>Tanggal Kirim</label>
                        <span>{{ $order->delivery_date ? $order->formatted_delivery_date : '-' }}</span>
                    </div>
                    <div class="schedule-item">
                        <label>Jam Pengiriman</label>
                        <span>{{ $order->delivery_time_slot ?? '10:00 - 16:00' }} WIB</span>
                    </div>
                    <div class="schedule-item">
                        <label>Estimasi Jarak</label>
                        <span>{{ $order->formatted_delivery_distance }}</span>
                    </div>
                </div>
            </div>
            
            
            <!-- Items -->
            <div class="items-section">
                <h3>Detail Pesanan</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">Rp {{ number_format($item->product_price, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Summary -->
            <div class="summary-section">
                <div class="summary-wrapper">
                    <div class="summary-box">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>{{ $order->formatted_subtotal }}</span>
                        </div>
                        @if($order->product_discount > 0)
                            <div class="summary-row" style="color: #dc2626;">
                                <span>Diskon Produk</span>
                                <span>-{{ $order->formatted_product_discount }}</span>
                            </div>
                        @endif
                        <div class="summary-row">
                            <span>Ongkos Kirim @if($order->delivery_distance_km)({{ number_format($order->delivery_distance_km, 1) }} km)@endif</span>
                            <span>{{ $order->formatted_shipping_cost }}</span>
                        </div>
                        @if($order->shipping_discount > 0)
                            <div class="summary-row" style="color: #dc2626;">
                                <span>Diskon Ongkir</span>
                                <span>-{{ $order->formatted_shipping_discount }}</span>
                            </div>
                        @endif
                        <div class="summary-row total">
                            <span>TOTAL</span>
                            <span>{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            @if($order->notes)
            <div class="notes-box">
                <h4>📝 Catatan Pesanan</h4>
                <p>{{ $order->notes }}</p>
            </div>
            @endif
            
            <!-- Biteship Label -->
            @if($order->label_url || $order->waybill_id)
            <div style="margin-top: 30px; page-break-before: always;">
                @php
                    $courierCode = strtolower($order->courier_code ?? 'jnt');
                @endphp
                
                @if($courierCode === 'jnt')
                    @include('admin.orders.labels.jnt', ['order' => $order])
                @elseif($courierCode === 'anteraja')
                    @include('admin.orders.labels.anteraja', ['order' => $order])
                @elseif($courierCode === 'paxel')
                    @include('admin.orders.labels.paxel', ['order' => $order])
                @else
                    @include('admin.orders.labels.generic', ['order' => $order])
                @endif
            </div>
            @endif
            
           
        </div>
    </div>
</body>
</html>
