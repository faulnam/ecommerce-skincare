<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Varian Produk</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .box { border: 2px solid #ccc; padding: 15px; margin: 15px 0; border-radius: 8px; }
        .success { background: #d4edda; border-color: #28a745; }
        .warning { background: #fff3cd; border-color: #ffc107; }
        .danger { background: #f8d7da; border-color: #dc3545; }
        .info { background: #d1ecf1; border-color: #17a2b8; }
        h2 { color: #333; }
        h3 { color: #666; margin-top: 0; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔍 Debug Varian Produk - Hijab</h1>
    
    <?php
    // 1. CEK SEMUA PRODUK
    echo "<div class='box info'>";
    echo "<h2>1️⃣ Semua Produk di Database</h2>";
    $allProducts = \App\Models\Product::all();
    echo "<p><strong>Total Produk:</strong> " . $allProducts->count() . "</p>";
    
    if ($allProducts->isEmpty()) {
        echo "<p class='danger'>❌ TIDAK ADA PRODUK di database!</p>";
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Nama</th><th>has_variants</th><th>Total Varian</th><th>Varian Aktif</th></tr>";
        foreach ($allProducts as $p) {
            $variantCount = $p->variants()->count();
            $activeCount = $p->activeVariants()->count();
            $hasVariants = $p->has_variants ? '✅ TRUE' : '❌ FALSE';
            echo "<tr>";
            echo "<td>{$p->id}</td>";
            echo "<td>{$p->name}</td>";
            echo "<td>{$hasVariants}</td>";
            echo "<td>{$variantCount}</td>";
            echo "<td>{$activeCount}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // 2. CEK PRODUK DENGAN has_variants = TRUE
    echo "<div class='box'>";
    echo "<h2>2️⃣ Produk dengan has_variants = TRUE</h2>";
    $productsWithVariants = \App\Models\Product::where('has_variants', true)->get();
    
    if ($productsWithVariants->isEmpty()) {
        echo "<div class='danger'>";
        echo "<p>❌ TIDAK ADA produk dengan has_variants = true</p>";
        echo "<p><strong>Solusi:</strong></p>";
        echo "<ol>";
        echo "<li>Login ke Admin Panel</li>";
        echo "<li>Buka menu Products → Edit produk</li>";
        echo "<li>Centang checkbox 'Produk ini memiliki varian'</li>";
        echo "<li>Tambahkan varian (nama, stock, harga)</li>";
        echo "<li>Klik Save</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        foreach ($productsWithVariants as $product) {
            $variantCount = $product->variants()->count();
            $activeVariantCount = $product->activeVariants()->count();
            
            $boxClass = ($variantCount > 0 && $activeVariantCount > 0) ? 'success' : 'warning';
            
            echo "<div class='box {$boxClass}'>";
            echo "<h3>📦 {$product->name}</h3>";
            echo "<p><strong>ID:</strong> {$product->id}</p>";
            echo "<p><strong>has_variants:</strong> " . ($product->has_variants ? '✅ TRUE' : '❌ FALSE') . "</p>";
            echo "<p><strong>Total Varian:</strong> {$variantCount}</p>";
            echo "<p><strong>Varian Aktif:</strong> {$activeVariantCount}</p>";
            
            if ($variantCount > 0) {
                echo "<h4>Detail Varian:</h4>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Nama</th><th>Stock</th><th>Price Adj</th><th>Active</th><th>Sort</th></tr>";
                foreach ($product->variants as $variant) {
                    $active = $variant->is_active ? '✅ Ya' : '❌ Tidak';
                    echo "<tr>";
                    echo "<td>{$variant->id}</td>";
                    echo "<td>{$variant->name}</td>";
                    echo "<td>{$variant->stock}</td>";
                    echo "<td>Rp " . number_format($variant->price_adjustment, 0, ',', '.') . "</td>";
                    echo "<td>{$active}</td>";
                    echo "<td>{$variant->sort_order}</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                if ($activeVariantCount == 0) {
                    echo "<div class='warning'>";
                    echo "<p>⚠️ <strong>MASALAH:</strong> Produk punya varian tapi SEMUA varian is_active = FALSE!</p>";
                    echo "<p><strong>Solusi:</strong> Edit varian dan pastikan minimal 1 varian aktif</p>";
                    echo "</div>";
                }
            } else {
                echo "<div class='warning'>";
                echo "<p>⚠️ <strong>MASALAH:</strong> has_variants = TRUE tapi TIDAK ADA varian di database!</p>";
                echo "<p><strong>Solusi:</strong> Tambahkan varian di Admin Panel</p>";
                echo "</div>";
            }
            
            // Test API endpoint
            echo "<h4>🔗 Test API Endpoint:</h4>";
            echo "<p><a href='/api/products/{$product->id}/variants' target='_blank'>/api/products/{$product->id}/variants</a></p>";
            
            echo "</div>";
        }
    }
    echo "</div>";
    
    // 3. CEK SEMUA VARIAN
    echo "<div class='box info'>";
    echo "<h2>3️⃣ Semua Varian di Database</h2>";
    $allVariants = \App\Models\ProductVariant::with('product')->get();
    
    if ($allVariants->isEmpty()) {
        echo "<div class='danger'>";
        echo "<p>❌ TIDAK ADA varian di database!</p>";
        echo "<p><strong>Solusi:</strong> Buat varian di Admin Panel</p>";
        echo "</div>";
    } else {
        echo "<p><strong>Total Varian:</strong> " . $allVariants->count() . "</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Produk</th><th>Nama Varian</th><th>Stock</th><th>Price Adj</th><th>Active</th></tr>";
        foreach ($allVariants as $variant) {
            $active = $variant->is_active ? '✅ Ya' : '❌ Tidak';
            echo "<tr>";
            echo "<td>{$variant->id}</td>";
            echo "<td>{$variant->product->name}</td>";
            echo "<td>{$variant->name}</td>";
            echo "<td>{$variant->stock}</td>";
            echo "<td>Rp " . number_format($variant->price_adjustment, 0, ',', '.') . "</td>";
            echo "<td>{$active}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // 4. KESIMPULAN
    echo "<div class='box info'>";
    echo "<h2>4️⃣ Kesimpulan & Checklist</h2>";
    
    $hasProductsWithVariantsFlag = \App\Models\Product::where('has_variants', true)->exists();
    $hasVariantsInDb = \App\Models\ProductVariant::exists();
    $hasActiveVariants = \App\Models\ProductVariant::where('is_active', true)->exists();
    
    echo "<ul>";
    echo "<li>" . ($hasProductsWithVariantsFlag ? "✅" : "❌") . " Ada produk dengan has_variants = true</li>";
    echo "<li>" . ($hasVariantsInDb ? "✅" : "❌") . " Ada varian di database</li>";
    echo "<li>" . ($hasActiveVariants ? "✅" : "❌") . " Ada varian yang aktif (is_active = true)</li>";
    echo "</ul>";
    
    if ($hasProductsWithVariantsFlag && $hasVariantsInDb && $hasActiveVariants) {
        echo "<div class='success'>";
        echo "<h3>✅ SEMUA OK!</h3>";
        echo "<p>Varian seharusnya muncul di frontend. Jika tidak muncul, cek:</p>";
        echo "<ol>";
        echo "<li>Buka Console browser (F12) saat klik produk</li>";
        echo "<li>Lihat apakah ada error JavaScript</li>";
        echo "<li>Cek apakah API endpoint mengembalikan data yang benar</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div class='danger'>";
        echo "<h3>❌ ADA MASALAH!</h3>";
        echo "<p>Ikuti langkah-langkah di atas untuk memperbaiki.</p>";
        echo "</div>";
    }
    echo "</div>";
    ?>
    
    <hr>
    <p><small>Debug file: public/debug-variants.php | Dibuat untuk troubleshooting varian produk</small></p>
</body>
</html>
