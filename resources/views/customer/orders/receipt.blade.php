<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $jsonPath = public_path('translation/orderreceipt.json');
        $receiptTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
    @endphp
    <title>{{ $receiptTrans['meta_title'][$lang] ?? 'Order Receipt - ' }}{{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #003C52; /* Diselaraskan dengan tema Deep Teal profesional milikmu */
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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            overflow: hidden;
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
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }
        
        .brand-info p {
            color: var(--gray);
            font-size: 12px;
        }
        
        /* Order Info */
        .order-meta {
            display: flex;
            justify-content: space-between;
            background: var(--light-gray);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 1px solid var(--border);
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
            border-radius: 12px;
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
            background: rgba(0, 60, 82, 0.02);
            border: 1px solid rgba(0, 60, 82, 0.1);
            border-radius: 12px;
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
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        
        td {
            padding: 14px 15px;
            border-bottom: 1px solid var(--border);
            color: #374151;
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
            width: 300px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            color: #4b5563;
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-row.total {
            background: var(--primary);
            color: white;
            margin: 12px -15px -15px;
            padding: 16px;
            border-radius: 0 0 12px 12px;
        }
        
        .summary-row.total span {
            font-weight: 700;
            font-size: 16px;
        }
        
        .summary-wrapper {
            background: var(--light-gray);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        
        /* Notes */
        .notes-box {
            background: rgba(245, 158, 11, 0.03);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 30px;
        }
        
        .notes-box h4 {
            font-size: 12px;
            font-weight: 600;
            color: #d97706;
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
            font-size: 13px;
            margin-top: 10px;
        }
        
        /* Print Actions */
        .print-actions {
            text-align: center;
            padding: 20px;
            background: var(--light-gray);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        
        .print-actions button,
        .print-actions a {
            padding: 10px 24px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }
        
        .btn-print {
            background: var(--primary);
            color: white;
            border: none;
        }
        
        .btn-print:hover {
            background: #002533;
        }
        
        .btn-back {
            background: white;
            color: var(--gray);
            border: 1px solid var(--border);
        }
        
        .btn-back:hover {
            background: var(--light-gray);
            color: var(--dark);
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
                max-width: 100%;
            }
            
            .receipt {
                padding: 10px;
            }
            
            .print-actions {
                display: none;
            }
            
            .schedule-box,
            .notes-box,
            .summary-wrapper,
            th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        @media (max-width: 768px) {
            .receipt {
                padding: 20px;
            }
            
            .header {
                flex-direction: column;
                gap: 20px;
            }
            
            .order-meta {
                flex-wrap: wrap;
                gap: 15px;
            }
            
            .order-meta-item {
                flex: 1 1 45%;
            }
            
            .address-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .schedule-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px 8px;
            }
            
            .summary-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-wrapper">
        <div class="print-actions">
            <button onclick="window.print()" class="btn-print">
                🖨️ {{ $receiptTrans['btn_print'][$lang] ?? 'Print Receipt' }}
            </button>
            <a href="{{ route('customer.orders.show', $order) }}" class="btn-back">
                {{ $receiptTrans['btn_back'][$lang] ?? 'Back' }}
            </a>
        </div>

        <div class="receipt">
            <div class="header">
                <div class="brand">
                    <img src="{{ asset(config('branding.logo', 'storage/logo.png')) }}" alt="Hijab" class="brand-logo" style="width: 60px; height: 60px; object-fit: contain;">
                    <div class="brand-info">
                        <h1>{{ config('branding.name', 'Hijab') }}</h1>
                        <p>{{ $receiptTrans['tagline'][$lang] ?? 'Premium Hijab Equipment' }}</p>
                        <p>{{ config('branding.address', 'Kec. Tarik, Sidoarjo, Jawa Timur 61265') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="order-meta">
                <div class="order-meta-item">
                    <label>{{ $receiptTrans['meta_order_no'][$lang] ?? 'Order No.' }}</label>
                    <span class="order-number">{{ $order->order_number }}</span>
                </div>
                <div class="order-meta-item">
                    <label>{{ $receiptTrans['meta_order_date'][$lang] ?? 'Order Date' }}</label>
                    <span>{{ $order->created_at->format('d F Y') }}</span>
                </div>
                <div class="order-meta-item">
                    <label>{{ $receiptTrans['meta_order_time'][$lang] ?? 'Order Time' }}</label>
                    <span>{{ $order->created_at->format('H:i') }} WIB</span>
                </div>
                <div class="order-meta-item">
                    <label>{{ $receiptTrans['meta_payment_method'][$lang] ?? 'Payment Method' }}</label>
                    <span>{{ $order->payment_method === 'cod' ? ($receiptTrans['pay_cod'][$lang] ?? 'COD') : ($receiptTrans['pay_transfer'][$lang] ?? 'Transfer') }}</span>
                </div>
            </div>
            
            <div class="address-section">
                <div class="address-box">
                    <h3>{{ $receiptTrans['label_sender'][$lang] ?? 'Sender' }}</h3>
                    <p class="name">Hijab Store</p>
                    <p class="detail">Kec. Tarik, Kab. Sidoarjo</p>
                    <p class="detail">Jawa Timur 61265</p>
                    <p class="detail">{{ $receiptTrans['label_phone'][$lang] ?? 'Phone:' }} {{ config('branding.phone', '+62 812 3456 7890') }}</p>
                </div>
                <div class="address-box">
                    <h3>{{ $receiptTrans['label_recipient'][$lang] ?? 'Recipient' }}</h3>
                    <p class="name">{{ $order->shipping_name }}</p>
                    <p class="detail">{{ $order->shipping_address }}</p>
                    <p class="detail">{{ $receiptTrans['label_phone'][$lang] ?? 'Phone:' }} {{ $order->shipping_phone }}</p>
                </div>
            </div>

            <div class="schedule-box">
                <h3>{{ $receiptTrans['sched_title'][$lang] ?? 'Delivery Schedule' }}</h3>
                <div class="schedule-grid">
                    <div class="schedule-item">
                        <label>{{ $receiptTrans['sched_date'][$lang] ?? 'Shipping Date' }}</label>
                        <span>{{ $order->delivery_date ? $order->formatted_delivery_date : '-' }}</span>
                    </div>
                    <div class="schedule-item">
                        <label>{{ $receiptTrans['sched_time'][$lang] ?? 'Delivery Time' }}</label>
                        <span>{{ $order->delivery_time_slot ?? '10:00 - 16:00' }} WIB</span>
                    </div>
                    <div class="schedule-item">
                        <label>{{ $receiptTrans['sched_distance'][$lang] ?? 'Estimated Distance' }}</label>
                        <span>{{ $order->formatted_delivery_distance }}</span>
                    </div>
                </div>
            </div>
            
            <div class="items-section">
                <h3>{{ $receiptTrans['table_title'][$lang] ?? 'Order Details' }}</h3>
                <table>
                    <thead>
                        <tr>
                            <th>{{ $receiptTrans['th_product'][$lang] ?? 'Product' }}</th>
                            <th class="text-center">{{ $receiptTrans['th_qty'][$lang] ?? 'Qty' }}</th>
                            <th class="text-right">{{ $receiptTrans['th_price'][$lang] ?? 'Price' }}</th>
                            <th class="text-right">{{ $receiptTrans['th_subtotal'][$lang] ?? 'Subtotal' }}</th>
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
            
            <div class="summary-section">
                <div class="summary-wrapper">
                    <div class="summary-box">
                        <div class="summary-row">
                            <span>{{ $receiptTrans['summary_subtotal'][$lang] ?? 'Subtotal' }}</span>
                            <span>{{ $order->formatted_subtotal }}</span>
                        </div>
                        @if($order->product_discount > 0)
                            <div class="summary-row" style="color: #dc2626;">
                                <span>{{ $receiptTrans['summary_product_disc'][$lang] ?? 'Diskon Produk' }}</span>
                                <span>-{{ $order->formatted_product_discount }}</span>
                            </div>
                        @endif
                        <div class="summary-row">
                            <span>{{ $receiptTrans['summary_shipping'][$lang] ?? 'Ongkos Kirim' }} @if($order->delivery_distance_km)({{ number_format($order->delivery_distance_km, 1) }} km)@endif</span>
                            <span>{{ $order->formatted_shipping_cost }}</span>
                        </div>
                        @if($order->shipping_discount > 0)
                            <div class="summary-row" style="color: #dc2626;">
                                <span>{{ $receiptTrans['summary_shipping_disc'][$lang] ?? 'Diskon Ongkir' }}</span>
                                <span>-{{ $order->formatted_shipping_discount }}</span>
                            </div>
                        @endif
                        <div class="summary-row total">
                            <span>{{ $receiptTrans['summary_total'][$lang] ?? 'TOTAL' }}</span>
                            <span>{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($order->notes)
            <div class="notes-box">
                <h4>📝 {{ $receiptTrans['notes_title'][$lang] ?? 'Catatan Pesanan' }}</h4>
                <p>{{ $order->notes }}</p>
            </div>
            @endif
            
            <div class="footer">
                <p>{{ $receiptTrans['footer_thanks'][$lang] ?? 'Terima kasih telah berbelanja di Hijab' }}</p>
                <p class="thanks">{{ $receiptTrans['footer_wish'][$lang] ?? 'Semoga perlengkapan barunya bikin game kamu makin total! 🎾' }}</p>
            </div>
        </div>
    </div>
</body>
</html>