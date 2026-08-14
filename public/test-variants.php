<?php
// Test file untuk cek varian produk
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Debug Varian Produk</h2>";

// Cek produk yang punya has_variants = true
$products = \App\Models\Product::where('has_variants', true)->get();

echo "<h3>Produk dengan has_variants = true:</h3>";
if ($products->isEmpty()) {
    echo "<p style='color: red;'>❌ TIDAK ADA produk dengan has_variants = true</p>";
    echo "<p>Solusi: Buka Admin → Products → Edit produk → Centang 'Produk ini memiliki varian'</p>";
} else {
    foreach ($products as $product) {
        $variantCount = $product->variants()->count();
        $activeVariantCount = $product->activeVariants()->count();
        
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>ID:</strong> {$product->id}<br>";
        echo "<strong>Nama:</strong> {$product->name}<br>";
        echo "<strong>has_variants:</strong> " . ($product->has_variants ? 'true' : 'false') . "<br>";
        echo "<strong>Total Varian:</strong> {$variantCount}<br>";
        echo "<strong>Varian Aktif:</strong> {$activeVariantCount}<br>";
        
        if ($variantCount > 0) {
            echo "<h4>Detail Varian:</h4>";
            foreach ($product->variants as $variant) {
                echo "- {$variant->name} (Stock: {$variant->stock}, Active: " . ($variant->is_active ? 'Ya' : 'Tidak') . ")<br>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Produk ini has_variants = true tapi TIDAK PUNYA varian!</p>";
            echo "<p>Solusi: Buka Admin → Products → Edit produk → Tambahkan varian</p>";
        }
        echo "</div>";
    }
}

echo "<hr>";
echo "<h3>Semua Varian di Database:</h3>";
$allVariants = \App\Models\ProductVariant::with('product')->get();
if ($allVariants->isEmpty()) {
    echo "<p style='color: red;'>❌ TIDAK ADA varian di database</p>";
} else {
    foreach ($allVariants as $variant) {
        echo "- Produk: {$variant->product->name} | Varian: {$variant->name} | Stock: {$variant->stock} | Active: " . ($variant->is_active ? 'Ya' : 'Tidak') . "<br>";
    }
}
