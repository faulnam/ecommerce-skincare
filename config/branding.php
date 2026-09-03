<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brand Name
    |--------------------------------------------------------------------------
    |
    | Nama brand yang ditampilkan di seluruh aplikasi
    |
    */

    'name' => env('BRAND_NAME', 'LUMINA Skincare'),

    /*
    |--------------------------------------------------------------------------
    | Brand Tagline
    |--------------------------------------------------------------------------
    |
    | Tagline brand
    |
    */

    'tagline' => env('BRAND_TAGLINE', 'Perawatan Kulit Terpercaya untuk Kilau Alami & Sehat'),

    /*
    |--------------------------------------------------------------------------
    | Logo Settings
    |--------------------------------------------------------------------------
    |
    | Path logo relatif dari folder public/
    | Taruh file logo di public/images/
    |
    | Contoh: jika logo ada di public/images/logo.png
    | maka isi dengan 'images/logo.png'
    |
    */

    'logo' => env('BRAND_LOGO', 'images/logo.png'),

    'logo_dark' => env('BRAND_LOGO_DARK', 'images/logo.png'),

    'logo_white' => env('BRAND_LOGO_WHITE', 'images/logo.png'),
    
    'favicon' => env('BRAND_FAVICON', 'images/lumina-skincare-favicon.svg'),

    /*
    |--------------------------------------------------------------------------
    | Logo Sizes
    |--------------------------------------------------------------------------
    |
    | Ukuran default logo dalam pixel
    |
    */

    'logo_height' => [
        'navbar' => 40,
        'sidebar' => 32,
        'footer' => 36,
        'receipt' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    */

    'email' => env('BRAND_EMAIL', 'hello@luminaskincare.id'),
    'phone' => env('BRAND_PHONE', '+62 812 3456 7890'),
    'address' => env('BRAND_ADDRESS', 'Lumina Beauty Tower Lt. 5, Jl. Darmo No. 88, Surabaya, Jawa Timur'),

    /*
    |--------------------------------------------------------------------------
    | Store Location
    |--------------------------------------------------------------------------
    |
    | Koordinat lokasi toko untuk perhitungan ongkos kirim
    | Lokasi: Kec. Tarik, Kab. Sidoarjo, Jawa Timur
    |
    */

    'store_latitude' => env('STORE_LATITUDE', -7.278417),
    'store_longitude' => env('STORE_LONGITUDE', 112.632583),

    /*
    |--------------------------------------------------------------------------
    | Social Media
    |--------------------------------------------------------------------------
    */

    'social' => [
        'instagram' => env('BRAND_INSTAGRAM', '@luminaskincare.id'),
        'facebook' => env('BRAND_FACEBOOK', 'luminaskincare.official'),
        'whatsapp' => env('BRAND_WHATSAPP', '6281234567890'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Home Mobile Hero Slides
    |--------------------------------------------------------------------------
    |
    | Khusus carousel mobile di halaman home (3 slide).
    | Bisa diisi path relatif public/ (contoh: storage/home/mobile-1.jpg)
    | atau URL penuh (https://...).
    |
    */
    'home_mobile_slides' => [
        env('HOME_MOBILE_SLIDE_1', 'storage/banner-1.jpg'),
        env('HOME_MOBILE_SLIDE_2', 'storage/banner-2.jpg'),
        env('HOME_MOBILE_SLIDE_3', 'storage/banner-3.jpg'),
    ],

];
