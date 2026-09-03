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
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Banner::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Hero Banners
        $heroBanners = [
            [
                'type' => 'hero',
                'title' => 'BELANJA SEKARANG',
                'image' => 'banner-1.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'hero',
                'title' => 'FORMULASI TERBARU',
                'image' => 'banner-2.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'hero',
                'title' => 'KULIT GLOWING ALAMI',
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
                'title' => 'BARRIER REPAIR ESSENTIALS',
                'button_text' => 'LIHAT KOLEKSI',
                'image' => 'model-1.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'split',
                'title' => 'DAILY UV SHIELD CARE',
                'button_text' => 'PILIH SUNSCREEN',
                'image' => 'model-2.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'split',
                'title' => 'GLOWING SERUM LAB',
                'button_text' => 'TEMUKAN SERUM',
                'image' => 'model-3.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'split',
                'title' => 'CLEAN BEAUTY ROUTINE',
                'button_text' => 'MULAI PERAWATAN',
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
