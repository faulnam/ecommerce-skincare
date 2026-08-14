<?php

return [
    'merchant_id' => env('PAYLABS_MERCHANT_ID'),
    'api_key' => env('PAYLABS_API_KEY'),
    'sandbox' => env('PAYLABS_SANDBOX', true),
    'mock_mode' => env('PAYLABS_MOCK_MODE', false),
    
    // API URLs
    'base_url' => env('PAYLABS_BASE_URL', 'https://api.paylabs.co.id'),

    // HTTP Client Settings
    'timeout' => (int) env('PAYLABS_TIMEOUT', 30),
    'connect_timeout' => (int) env('PAYLABS_CONNECT_TIMEOUT', 10),
    'verify_ssl' => env('PAYLABS_VERIFY_SSL', true),
    'private_key_path' => env('PAYLABS_PRIVATE_KEY_PATH', storage_path('app/paylabs/private-key.pem')),
    'public_key_path' => env('PAYLABS_PUBLIC_KEY_PATH', storage_path('app/paylabs/public-key.pem')),
    
    // Payment Methods
    'payment_methods' => [
        // VA belum diaktifkan oleh Paylabs untuk Merchant ID 011367
        // Hubungi CS Paylabs untuk aktivasi
        'va' => [
            'VA_BCA'     => 'BCA Virtual Account',
            'VA_BNI'     => 'BNI Virtual Account',
            'VA_BRI'     => 'BRI Virtual Account',
            'VA_MANDIRI' => 'Mandiri Virtual Account',
            'VA_PERMATA' => 'Permata Virtual Account',
            'VA_CIMB'    => 'CIMB Niaga Virtual Account',
        ],
        'qris'    => ['QRIS' => 'QRIS'],
        // E-Wallet dan Retail belum ditest, nonaktifkan dulu
        'ewallet' => [
            'EWALLET_OVO'       => 'OVO',
            'EWALLET_DANA'      => 'DANA',
            'EWALLET_GOPAY'     => 'GoPay',
            'EWALLET_SHOPEEPAY' => 'ShopeePay',
            'EWALLET_LINKAJA'   => 'LinkAja',
        ],
        'retail' => [
            'RETAIL_ALFAMART'  => 'Alfamart',
            'RETAIL_INDOMARET' => 'Indomaret',
        ],
    ],
    
    // Callback URLs
    'callback_url' => env('PAYLABS_CALLBACK_URL', rtrim((string) env('APP_URL'), '/') . '/webhook/paylabs'),
    'return_url' => env('PAYLABS_RETURN_URL', rtrim((string) env('APP_URL'), '/') . '/customer/payment-paylabs/{order_id}/callback'),

    // Webhook Verification
    'webhook' => [
        'verify_signature' => env('PAYLABS_VERIFY_SIGNATURE', false),
        'signature_header' => env('PAYLABS_SIGNATURE_HEADER', 'X-Paylabs-Signature'),
        // Optional dedicated secret for webhook. Fallback to api_key when null.
        'secret' => env('PAYLABS_WEBHOOK_SECRET'),
    ],
];
