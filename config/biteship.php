<?php

return [
    'api_key' => env('BITESHIP_API_KEY'),
    'sandbox' => env('BITESHIP_SANDBOX', true),
    'use_mock' => env('BITESHIP_USE_MOCK', false),
    'base_url' => env('BITESHIP_BASE_URL', 'https://api.biteship.com/v1'),
    
    // Origin address (your store)
    'origin' => [
    'latitude' => env('BITESHIP_ORIGIN_LAT', env('BRANDING_STORE_LATITUDE', -7.278417)),
    'longitude' => env('BITESHIP_ORIGIN_LNG', env('BRANDING_STORE_LONGITUDE', 112.632583)),
        'postal_code' => env('BITESHIP_ORIGIN_POSTAL_CODE', env('BRANDING_STORE_POSTAL_CODE', '61219')),
    ],
    
    // Supported couriers
    'couriers' => [
        'jne' => 'JNE',
        'jnt' => 'J&T Express',
        'anteraja' => 'AnterAja',
        'spx' => 'Shopee Express',
        'paxel' => 'Paxel',
        'gosend' => 'GoSend',
        'grabexpress' => 'GrabExpress',
    ],

    // Operational time rules per courier service (WIB).
    // Jika current time di luar range, service disembunyikan di checkout.
    // Catatan: cutoff Same Day berikut mengikuti behavior/provider rule Biteship
    // yang umum untuk on-demand courier (contoh error code 40002037 pada Grab Same Day).
    // Apabila provider mengubah aturan per area, sesuaikan nilai ini sesuai respons resmi terbaru.
    'service_operational_hours' => [
        'grab:same_day' => ['start' => '09:00', 'end' => '14:00'],
        'grabexpress:same_day' => ['start' => '09:00', 'end' => '14:00'],
        'gojek:same_day' => ['start' => '09:00', 'end' => '14:00'],
        'gosend:same_day' => ['start' => '09:00', 'end' => '14:00'],
    ],
];
