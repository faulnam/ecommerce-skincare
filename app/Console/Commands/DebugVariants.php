<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Console\Command;

class DebugVariants extends Command
{
    protected $signature = 'debug:variants';
    protected $description = 'Debug product variants - check database status';

    public function handle()
    {
        $this->info('🔍 DEBUG VARIAN PRODUK - LUMINA SKINCARE');
        $this->newLine();

        // 1. CEK SEMUA PRODUK
        $this->info('1️⃣ SEMUA PRODUK DI DATABASE');
        $this->line(str_repeat('=', 80));
        
        $allProducts = Product::all();
        $this->info("Total Produk: {$allProducts->count()}");
        $this->newLine();

        if ($allProducts->isEmpty()) {
            $this->error('❌ TIDAK ADA PRODUK di database!');
        } else {
            $headers = ['ID', 'Nama', 'has_variants', 'Total Varian', 'Varian Aktif'];
            $rows = [];
            
            foreach ($allProducts as $p) {
                $variantCount = $p->variants()->count();
                $activeCount = $p->activeVariants()->count();
                $hasVariants = $p->has_variants ? '✅ TRUE' : '❌ FALSE';
                
                $rows[] = [
                    $p->id,
                    $p->name,
                    $hasVariants,
                    $variantCount,
                    $activeCount
                ];
            }
            
            $this->table($headers, $rows);
        }
        
        $this->newLine(2);

        // 2. CEK PRODUK DENGAN has_variants = TRUE
        $this->info('2️⃣ PRODUK DENGAN has_variants = TRUE');
        $this->line(str_repeat('=', 80));
        
        $productsWithVariants = Product::where('has_variants', true)->get();
        
        if ($productsWithVariants->isEmpty()) {
            $this->error('❌ TIDAK ADA produk dengan has_variants = true');
            $this->newLine();
            $this->warn('SOLUSI:');
            $this->line('1. Login ke Admin Panel');
            $this->line('2. Buka menu Products → Edit produk');
            $this->line('3. Centang checkbox "Produk ini memiliki varian"');
            $this->line('4. Tambahkan varian (nama, stock, harga)');
            $this->line('5. Klik Save');
        } else {
            foreach ($productsWithVariants as $product) {
                $variantCount = $product->variants()->count();
                $activeVariantCount = $product->activeVariants()->count();
                
                $this->newLine();
                $this->line("📦 {$product->name}");
                $this->line(str_repeat('-', 80));
                $this->line("ID: {$product->id}");
                $this->line("has_variants: " . ($product->has_variants ? '✅ TRUE' : '❌ FALSE'));
                $this->line("Total Varian: {$variantCount}");
                $this->line("Varian Aktif: {$activeVariantCount}");
                
                if ($variantCount > 0) {
                    $this->newLine();
                    $this->line("Detail Varian:");
                    
                    $variantHeaders = ['ID', 'Nama', 'Stock', 'Price Adj', 'Active', 'Sort'];
                    $variantRows = [];
                    
                    foreach ($product->variants as $variant) {
                        $active = $variant->is_active ? '✅ Ya' : '❌ Tidak';
                        $variantRows[] = [
                            $variant->id,
                            $variant->name,
                            $variant->stock,
                            'Rp ' . number_format($variant->price_adjustment, 0, ',', '.'),
                            $active,
                            $variant->sort_order
                        ];
                    }
                    
                    $this->table($variantHeaders, $variantRows);
                    
                    if ($activeVariantCount == 0) {
                        $this->warn('⚠️ MASALAH: Produk punya varian tapi SEMUA varian is_active = FALSE!');
                        $this->line('SOLUSI: Edit varian dan pastikan minimal 1 varian aktif');
                    }
                } else {
                    $this->warn('⚠️ MASALAH: has_variants = TRUE tapi TIDAK ADA varian di database!');
                    $this->line('SOLUSI: Tambahkan varian di Admin Panel');
                }
                
                $this->line("API Endpoint: /api/products/{$product->id}/variants");
            }
        }
        
        $this->newLine(2);

        // 3. CEK SEMUA VARIAN
        $this->info('3️⃣ SEMUA VARIAN DI DATABASE');
        $this->line(str_repeat('=', 80));
        
        $allVariants = ProductVariant::with('product')->get();
        
        if ($allVariants->isEmpty()) {
            $this->error('❌ TIDAK ADA varian di database!');
            $this->line('SOLUSI: Buat varian di Admin Panel');
        } else {
            $this->info("Total Varian: {$allVariants->count()}");
            $this->newLine();
            
            $headers = ['ID', 'Produk', 'Nama Varian', 'Stock', 'Price Adj', 'Active'];
            $rows = [];
            
            foreach ($allVariants as $variant) {
                $active = $variant->is_active ? '✅ Ya' : '❌ Tidak';
                $rows[] = [
                    $variant->id,
                    $variant->product->name,
                    $variant->name,
                    $variant->stock,
                    'Rp ' . number_format($variant->price_adjustment, 0, ',', '.'),
                    $active
                ];
            }
            
            $this->table($headers, $rows);
        }
        
        $this->newLine(2);

        // 4. KESIMPULAN
        $this->info('4️⃣ KESIMPULAN & CHECKLIST');
        $this->line(str_repeat('=', 80));
        
        $hasProductsWithVariantsFlag = Product::where('has_variants', true)->exists();
        $hasVariantsInDb = ProductVariant::exists();
        $hasActiveVariants = ProductVariant::where('is_active', true)->exists();
        
        $this->line(($hasProductsWithVariantsFlag ? '✅' : '❌') . ' Ada produk dengan has_variants = true');
        $this->line(($hasVariantsInDb ? '✅' : '❌') . ' Ada varian di database');
        $this->line(($hasActiveVariants ? '✅' : '❌') . ' Ada varian yang aktif (is_active = true)');
        
        $this->newLine();
        
        if ($hasProductsWithVariantsFlag && $hasVariantsInDb && $hasActiveVariants) {
            $this->info('✅ SEMUA OK!');
            $this->line('Varian seharusnya muncul di frontend. Jika tidak muncul, cek:');
            $this->line('1. Buka Console browser (F12) saat klik produk');
            $this->line('2. Lihat apakah ada error JavaScript');
            $this->line('3. Cek apakah API endpoint mengembalikan data yang benar');
        } else {
            $this->error('❌ ADA MASALAH!');
            $this->line('Ikuti langkah-langkah di atas untuk memperbaiki.');
        }

        return 0;
    }
}
