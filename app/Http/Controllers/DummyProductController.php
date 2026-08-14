<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DummyProductController extends Controller
{
    public function show($slug)
    {
        // Reconstruct the name from the slug
        $name = Str::title(str_replace('-', ' ', $slug));

        $product = new Product([
            'id' => 999,
            'name' => $name,
            'slug' => $slug,
            'description' => '<p>Ini adalah deskripsi produk dummy untuk menguji tampilan halaman produk detail. Tampilannya seharusnya sudah persis seperti yang diharapkan.</p>',
            'price' => 750000,
            'discount_percent' => 10,
            'stock' => 50,
            'weight' => 500,
            'image' => 'https://images.unsplash.com/photo-1606902965551-dce093cda6e7?auto=format&fit=crop&w=600&q=80',
            'category' => 'apparel',
            'type' => 'apparel',
            'brand' => 'DummyBrand',
            'is_active' => true,
            'has_variants' => false,
            'is_free_event' => false,
            'package_type' => 'single',
        ]);

        $reviews = collect();
        $totalReviews = 0;
        $avgRating = 5.0;
        $ratingBreakdown = [
            5 => 100,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];
        $relatedProducts = collect();

        // Pass everything needed by product-detail.blade.php
        return view('pages.product-detail', compact(
            'product',
            'reviews',
            'totalReviews',
            'avgRating',
            'ratingBreakdown',
            'relatedProducts'
        ));
    }
}
