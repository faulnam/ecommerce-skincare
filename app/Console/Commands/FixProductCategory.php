<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class FixProductCategory extends Command
{
    protected $signature = 'products:fix-category {--dry-run : Preview changes without saving}';
    protected $description = 'Fix type and category fields for existing products by inferring from name';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $typeToCategory = [
            'hijab'      => 'original',
            'shoes'       => 'shoes',
            'accessories' => 'pedas',
        ];

        $products = Product::all(['id', 'name', 'type', 'category']);
        $updated = 0;

        foreach ($products as $product) {
            $name = strtolower($product->name ?? '');

            // Infer type from name if not set
            if (empty($product->type)) {
                if (str_contains($name, 'shoe') || str_contains($name, 'sepatu') || str_contains($name, 'footwear')) {
                    $product->type = 'shoes';
                } elseif (str_contains($name, 'hijab') || str_contains($name, 'hijab') || str_contains($name, 'pala') || str_contains($name, 'paddle')) {
                    $product->type = 'hijab';
                } else {
                    $product->type = 'accessories';
                }
            }

            $type = $product->type ?? 'hijab';
            $correctCategory = $typeToCategory[$type] ?? 'original';

            $needsUpdate = false;
            if (empty($product->type) || $product->category !== $correctCategory) {
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                if (!$dryRun) {
                    $product->type = $type;
                    $product->category = $correctCategory;
                    $product->save();
                }
                $updated++;
                $this->line("  {$product->name}: category '{$product->category}' -> '{$correctCategory}', type -> '{$type}'");
            }
        }

        $this->newLine();
        $this->info("Updated: {$updated}/{$products->count()} products");

        if ($dryRun) {
            $this->warn("Dry run — run without --dry-run to save.");
        }

        return self::SUCCESS;
    }
}
