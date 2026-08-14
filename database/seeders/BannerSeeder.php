<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::truncate();

        // Hero Banners
        $heroBanners = [
            [
                'type' => 'hero',
                'title' => 'SHOP NOW',
                'image' => 'banner-1.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'hero',
                'title' => 'NEW ARRIVALS',
                'image' => 'banner-2.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'hero',
                'title' => 'DISCOVER',
                'image' => 'banner-3.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($heroBanners as $banner) {
            Banner::create($banner);
        }

        // Split Banners (Models)
        $splitBanners = [
            [
                'type' => 'split',
                'title' => 'SEAMLESS COLLECTION',
                'button_text' => 'SHOP SEAMLESS',
                'image' => 'model-1.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'split',
                'title' => 'DESIGNED TO LAYER',
                'button_text' => 'SHOP STYLES',
                'image' => 'model-2.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'split',
                'title' => 'KOLEKSI TERBARU',
                'button_text' => 'BELANJA SEKARANG',
                'image' => 'model-3.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'split',
                'title' => 'HIJAB PREMIUM',
                'button_text' => 'BELANJA SEKARANG',
                'image' => 'model-4.jpg',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($splitBanners as $banner) {
            Banner::create($banner);
        }
    }
}
