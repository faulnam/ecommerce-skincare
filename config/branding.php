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

    'name' => env('BRAND_NAME', 'Hijab'),

    /*
    |--------------------------------------------------------------------------
    | Brand Tagline
    |--------------------------------------------------------------------------
    |
    | Tagline brand
    |
    */

    'tagline' => env('BRAND_TAGLINE', 'Performa Maksimal, Game Makin Total'),

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

    'logo' => env('BRAND_LOGO', 'storage/logo.png'),

    'logo_dark' => env('BRAND_LOGO_DARK', 'storage/logo.png'),

    'logo_white' => env('BRAND_LOGO_WHITE', 'storage/logo.png'),
    
    'favicon' => env('BRAND_FAVICON', 'images/hijab-hijab-favicon.svg'),

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

    'email' => env('BRAND_EMAIL', 'hello@hijab.id'),
    'phone' => env('BRAND_PHONE', '+62 812 7788 9900'),
    'address' => env('BRAND_ADDRESS', 'Jl. Hijab Arena No. 21, Surabaya'),

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
        'instagram' => env('BRAND_INSTAGRAM', '@hijab.id'),
        'facebook' => env('BRAND_FACEBOOK', ''),
        'whatsapp' => env('BRAND_WHATSAPP', '6281277889900'),
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
        env('HOME_MOBILE_SLIDE_1', 'storage/2.png'),
        env('HOME_MOBILE_SLIDE_2', 'storage/shoes.png'),
        env('HOME_MOBILE_SLIDE_3', 'storage/3.png'),
    ],

];
