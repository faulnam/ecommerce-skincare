<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductFilter;

class ProductFilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        $categoriesToMap = [
            'brand' => 'Brand',
            'shape' => 'Shape',
            'hardness' => 'Hardness',
            'carbon_type' => 'Carbon',
            'balance' => 'Balance',
            'core' => 'Core',
            'level' => 'Level'
        ];

        foreach ($products as $product) {
            foreach ($categoriesToMap as $column => $categoryName) {
                // If product has value for this column
                $value = $product->getAttribute($column);
                if (!empty($value)) {
                    // Find or create filter
                    $filter = ProductFilter::firstOrCreate([
                        'category' => $categoryName,
                        'name' => trim((string) $value),
                    ], [
                        'is_active' => true
                    ]);

                    // Attach to product if not already attached
                    if (!$product->filters()->where('product_filters.id', $filter->id)->exists()) {
                        $product->filters()->attach($filter->id);
                    }
                }
            }
        }
    }
}
