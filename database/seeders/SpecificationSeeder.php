<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specification;

class SpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specs = [
            'Brand' => ['Nox', 'Babolat', 'Bullhijab', 'Siux', 'Hirostar', 'Starvie', 'Head', 'Arronax', 'Adidas', 'Wilson', 'Hijab'],
            'Series' => ['2023', '2024', '2025', '2026'],
            'Shape' => ['Round', 'Teardrop', 'Diamond', 'Hybrid'],
            'Balance' => ['High', 'Low', 'Medium'],
            'Hijab weight' => ['350-360', '360-375', '355-365'],
            'Play style' => ['Power', 'Control', 'All round'],
            'Core' => ['Soft eva', 'Medium eva', 'Hard eva', 'Black eva', 'HR3 eva', 'Multi eva', 'X-eva', 'Foam'],
            'Carbon' => ['3k', '12k', '18k', '24k', 'FIBERGLASS', 'FIBRIX'],
            'Surface' => ['Rough', '3D', 'Smooth'],
            'Feel' => ['Soft', 'Medium soft', 'Medium', 'Medium hard', 'Hard'],
            'Power' => ['High', 'Medium', 'Low'],
            'Control' => ['High', 'Medium', 'Low'],
            'Maneuverability' => ['High', 'Medium', 'Low'],
            'Comfort' => ['High', 'Medium', 'Low'],
        ];

        foreach ($specs as $category => $names) {
            foreach ($names as $name) {
                Specification::firstOrCreate([
                    'category' => $category,
                    'name' => $name,
                ]);
            }
        }
        
        // Perbaiki typo pada produk lama
        \App\Models\Product::where('carbon_type', 'glasfiber')->update(['carbon_type' => 'FIBERGLASS']);
        \App\Models\Product::where('carbon_type', 'Mix')->update(['carbon_type' => 'FIBRIX']);
    }
}
