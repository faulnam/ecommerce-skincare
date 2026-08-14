<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MigrateImagesToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:images-to-r2 {--old-url= : The public URL of the old R2 bucket if migrating between buckets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate product and variant images to Cloudflare R2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image migration to R2...');
        if ($this->option('old-url')) {
            $this->info('Old URL mode enabled: ' . $this->option('old-url'));
        }

        $products = Product::all();
        $this->info("Found {$products->count()} products.");
        
        $migratedCount = 0;
        $failedCount = 0;

        foreach ($products as $product) {
            $fields = ['image', 'image_2', 'image_3', 'image_4'];
            $updated = false;

            foreach ($fields as $field) {
                if (!empty($product->$field) && $product->$field !== '0') {
                    [$newPath, $errorReason] = $this->migrateImage($product->$field, 'products');
                    
                    if ($newPath && $newPath !== $product->$field) {
                        $product->$field = $newPath;
                        $updated = true;
                        $this->line("Migrated {$field} for product ID: {$product->id}");
                    } elseif ($newPath === false) {
                        $this->error("Failed to migrate {$field} for product ID: {$product->id} - {$errorReason}");
                        $failedCount++;
                    }
                }
            }

            if ($updated) {
                $product->save();
                $migratedCount++;
            }
        }

        $variants = ProductVariant::all();
        $this->info("Found {$variants->count()} variants.");
        
        foreach ($variants as $variant) {
            if (!empty($variant->image) && $variant->image !== '0') {
                [$newPath, $errorReason] = $this->migrateImage($variant->image, 'products/variants');
                
                if ($newPath && $newPath !== $variant->image) {
                    $variant->image = $newPath;
                    $variant->save();
                    $migratedCount++;
                    $this->line("Migrated image for variant ID: {$variant->id}");
                } elseif ($newPath === false) {
                    $this->error("Failed to migrate image for variant ID: {$variant->id} - {$errorReason}");
                    $failedCount++;
                }
            }
        }

        $insights = \App\Models\Insight::all();
        $this->info("Found {$insights->count()} insights.");
        foreach ($insights as $insight) {
            if (!empty($insight->image) && $insight->image !== '0') {
                [$newPath, $errorReason] = $this->migrateImage($insight->image, 'insights');
                if ($newPath && $newPath !== $insight->image) {
                    $insight->image = $newPath;
                    $insight->save();
                    $migratedCount++;
                    $this->line("Migrated image for insight ID: {$insight->id}");
                } elseif ($newPath === false) {
                    $this->error("Failed to migrate image for insight ID: {$insight->id} - {$errorReason}");
                    $failedCount++;
                }
            }
        }

        $galleries = \App\Models\Gallery::all();
        $this->info("Found {$galleries->count()} galleries.");
        foreach ($galleries as $gallery) {
            if (!empty($gallery->image) && $gallery->image !== '0') {
                [$newPath, $errorReason] = $this->migrateImage($gallery->image, 'galleries');
                if ($newPath && $newPath !== $gallery->image) {
                    $gallery->image = $newPath;
                    $gallery->save();
                    $migratedCount++;
                    $this->line("Migrated image for gallery ID: {$gallery->id}");
                } elseif ($newPath === false) {
                    $this->error("Failed to migrate image for gallery ID: {$gallery->id} - {$errorReason}");
                    $failedCount++;
                }
            }
        }

        $catalogs = \App\Models\BrandCatalog::all();
        $this->info("Found {$catalogs->count()} brand catalogs.");
        foreach ($catalogs as $catalog) {
            if (!empty($catalog->cover_image) && $catalog->cover_image !== '0') {
                [$newPath, $errorReason] = $this->migrateImage($catalog->cover_image, 'brand_catalogs');
                if ($newPath && $newPath !== $catalog->cover_image) {
                    $catalog->cover_image = $newPath;
                    $catalog->save();
                    $migratedCount++;
                    $this->line("Migrated cover image for brand catalog ID: {$catalog->id}");
                } elseif ($newPath === false) {
                    $this->error("Failed to migrate cover image for brand catalog ID: {$catalog->id} - {$errorReason}");
                    $failedCount++;
                }
            }
        }

        $testimonials = \App\Models\Testimonial::all();
        $this->info("Found {$testimonials->count()} testimonials.");
        foreach ($testimonials as $testimonial) {
            if (!empty($testimonial->image) && $testimonial->image !== '0') {
                [$newPath, $errorReason] = $this->migrateImage($testimonial->image, 'testimonials');
                if ($newPath && $newPath !== $testimonial->image) {
                    $testimonial->image = $newPath;
                    $testimonial->save();
                    $migratedCount++;
                    $this->line("Migrated image for testimonial ID: {$testimonial->id}");
                } elseif ($newPath === false) {
                    $this->error("Failed to migrate image for testimonial ID: {$testimonial->id} - {$errorReason}");
                    $failedCount++;
                }
            }
        }

        $this->info("Migration completed! Total records updated: {$migratedCount}, Failures: {$failedCount}");
    }

    private function migrateImage($currentPath, $folder)
    {
        $currentPath = trim($currentPath);
        $content = null;
        $extension = 'jpg';
        $oldUrlBase = $this->option('old-url') ? rtrim($this->option('old-url'), '/') : null;

        if (preg_match('/^https?:\/\//i', $currentPath)) {
            // It's an external URL (Google Drive, Shopee, etc.)
            try {
                // Ignore SSL verification in case local environment has issues (like cURL error 77)
                $response = Http::timeout(30)->withOptions([
                    'verify' => false,
                ])->get($currentPath);
                
                if ($response->successful()) {
                    $content = $response->body();
                    $contentType = $response->header('Content-Type');
                    
                    if (str_contains($contentType, 'png')) {
                        $extension = 'png';
                    } elseif (str_contains($contentType, 'webp')) {
                        $extension = 'webp';
                    } elseif (str_contains($contentType, 'gif')) {
                        $extension = 'gif';
                    }
                } else {
                    return [false, "HTTP Download Status " . $response->status()];
                }
            } catch (\Exception $e) {
                return [false, "HTTP Download Exception: " . $e->getMessage()];
            }
        } else {
            // It's a local path
            $normalizedPath = ltrim(str_replace('\\', '/', $currentPath), '/');
            
            // Check if it exists in local storage (public disk)
            if (Storage::disk('public')->exists($normalizedPath)) {
                $content = Storage::disk('public')->get($normalizedPath);
                $ext = pathinfo($normalizedPath, PATHINFO_EXTENSION);
                if ($ext) {
                    $extension = $ext;
                }
            } elseif ($oldUrlBase) {
                // Try fetching from the old R2 bucket
                $fullOldUrl = $oldUrlBase . '/' . $normalizedPath;
                try {
                    $response = Http::timeout(30)->withOptions(['verify' => false])->get($fullOldUrl);
                    if ($response->successful()) {
                        $content = $response->body();
                        $contentType = $response->header('Content-Type');
                        
                        if (str_contains($contentType, 'png')) {
                            $extension = 'png';
                        } elseif (str_contains($contentType, 'webp')) {
                            $extension = 'webp';
                        } elseif (str_contains($contentType, 'gif')) {
                            $extension = 'gif';
                        } else {
                            $ext = pathinfo($normalizedPath, PATHINFO_EXTENSION);
                            if ($ext) $extension = $ext;
                        }
                    } else {
                        // Skip silently, it's either not in the old bucket or already migrated
                        return [$currentPath, null];
                    }
                } catch (\Exception $e) {
                    return [$currentPath, null];
                }
            } else {
                // If it doesn't exist locally, it might already be in R2 or broken. Skip it.
                return [$currentPath, null];
            }
        }

        // If we successfully got the image content, upload it to R2
        if ($content) {
            $filename = Str::random(40) . '.' . $extension;
            $newPath = $folder . '/' . $filename;
            
            try {
                $result = Storage::disk('r2')->put($newPath, $content);
                if ($result) {
                    return [$newPath, null];
                }
                return [false, "Storage disk R2 returned false on put()"];
            } catch (\Exception $e) {
                return [false, "R2 Upload Exception: " . $e->getMessage()];
            }
        }

        return [$currentPath, null];
    }
}
