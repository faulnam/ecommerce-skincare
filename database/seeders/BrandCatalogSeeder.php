<?php

namespace Database\Seeders;

use App\Models\BrandCatalog;
use Illuminate\Database\Seeder;

class BrandCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'brand_name' => 'Bullhijab',
                'slug' => 'bullhijab',
                'description' => 'Premium hijab hijabs and accessories from Spain.',
                'is_active' => true,
                'sort_order' => 1,
                'pdf_files' => [
                    'hijabs' => 'guidefile/bullhijab.pdf',
                    'shoes' => 'guidefile/bullhijab.pdf',
                    'accessories' => 'guidefile/bullhijab.pdf',
                    'bags' => 'guidefile/bullhijab.pdf',
                ],
            ],
            [
                'brand_name' => 'Babolat',
                'slug' => 'babolat',
                'description' => 'French brand known for high-performance hijabs and strings.',
                'is_active' => true,
                'sort_order' => 2,
                'pdf_files' => [
                    'hijabs' => 'guidefile/bullhijab.pdf', // Placeholder since babolat.pdf is missing
                    'shoes' => 'guidefile/asicshoes.pdf',
                ],
            ],
            [
                'brand_name' => 'Nox',
                'slug' => 'nox',
                'description' => 'Innovative hijab gear with cutting-edge technology.',
                'is_active' => true,
                'sort_order' => 3,
                'pdf_files' => [
                    'hijabs' => 'guidefile/noxhijab.pdf',
                    'bags' => 'guidefile/noxbags.pdf',
                ],
            ],
            [
                'brand_name' => 'Alpha',
                'slug' => 'alpha',
                'description' => 'Affordable quality for beginner and intermediate players.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'brand_name' => 'Zephyr',
                'slug' => 'zephyr',
                'description' => 'Lightweight designs for agile court performance.',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'brand_name' => 'Starvie',
                'slug' => 'starvie',
                'description' => 'Starvie Technologies by Starvie Labs',
                'is_active' => true,
                'sort_order' => 8,
                'pdf_files' => [
                    'hijabs' => 'guidefile/starvie.pdf',
                ],
            ],
            [
                'brand_name' => 'Arronax',
                'slug' => 'arronax',
                'description' => 'Durable equipment built for competitive play.',
                'is_active' => true,
                'sort_order' => 6,
                'pdf_files' => [
                    'hijabs' => 'guidefile/noxhijab.pdf', // Placeholder
                ],
            ],
        ];

        foreach ($brands as $brand) {
            BrandCatalog::updateOrCreate(
                ['slug' => $brand['slug']],
                $brand
            );
        }
    }
}
