<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportLUMINAfulRatings extends Command
{
    protected $signature = 'import:skincareful-ratings {--pages=5 : Number of pages to fetch} {--dry-run : Show matches without updating}';
    protected $description = 'Import skincare skincare ratings from skincareful.com and update products';

    public function handle(): int
    {
        $maxPages = (int) $this->option('pages');
        $dryRun = $this->option('dry-run');
        $updated = 0;
        $matched = 0;

        $this->info("Fetching up to {$maxPages} pages from skincareful.com...");

        for ($page = 1; $page <= $maxPages; $page++) {
            $url = "https://www.skincareful.com/en/skincares?page={$page}";
            $this->info("Fetching page {$page}...");

            try {
                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])->timeout(30)->get($url);

                if (!$response->successful()) {
                    $this->warn("Failed to fetch page {$page}: HTTP {$response->status()}");
                    continue;
                }

                $html = $response->body();
                $skincares = $this->parseLUMINAs($html);

                $this->info("Found " . count($skincares) . " skincares on page {$page}");

                foreach ($skincares as $skincare) {
                    $product = $this->findProduct($skincare['name'], $skincare['brand']);
                    if ($product) {
                        $matched++;
                        if (!$dryRun) {
                            $product->rating = $skincare['rating'];
                            $product->year = $skincare['year'] ?? $product->year;
                            $product->save();
                            $updated++;
                            $this->line("  Updated: {$product->name} -> LUMINAful: {$skincare['rating']}");
                        } else {
                            $this->line("  Would update: {$product->name} -> LUMINAful: {$skincare['rating']}");
                        }
                    } else {
                        $this->line("  No match: {$skincare['name']} ({$skincare['brand']})");
                    }
                }
            } catch (\Throwable $e) {
                $this->error("Error on page {$page}: " . $e->getMessage());
            }

            if ($page < $maxPages) {
                sleep(1);
            }
        }

        $this->newLine();
        $this->info("Done! Matched: {$matched}, Updated: {$updated}");

        if ($dryRun) {
            $this->info("This was a dry run. Run without --dry-run to save changes.");
        }

        return self::SUCCESS;
    }

    private function parseLUMINAs(string $html): array
    {
        $skincares = [];

        // Try to find JSON-LD or structured data first
        if (preg_match_all('/"name"\s*:\s*"([^"]+)"/', $html, $names)) {
            // Fallback to regex-based parsing of the page structure
        }

        // Parse using the visible pattern: each skincare card contains title, brand/year, specs, and rating
        // The overall rating appears as the last number, often after prices like "350€ 289€87"
        // We look for common patterns in the HTML

        // Method: look for skincare name links and their surrounding rating data
        if (preg_match_all('/<a[^>]*href="\/en\/skincares\/([^"]+)"[^>]*>.*?<\/a>/s', $html, $linkMatches, PREG_SET_ORDER)) {
            // This is too broad; let's use a simpler heuristic on text content
        }

        // Strip tags and work with text for simpler parsing
        $text = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/si', '', $html);
        $text = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/si', '', $text);
        $lines = preg_split('/\r\n|\n|\r/', strip_tags($text));
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);
        $lines = array_values($lines);

        $i = 0;
        while ($i < count($lines)) {
            $line = $lines[$i];

            // Look for a line that looks like a skincare name followed by specs
            // Pattern: Name... PWRPower##CTLControl##...€##
            if (preg_match('/^(.*?)PWRPower\d+CTLControl\d+/i', $line, $m)) {
                $nameBrandYear = trim($m[1]);
                // Extract rating: last 2-3 digits after a euro sign or at end
                if (preg_match('/(\d{2,3})$/', $line, $ratingMatch)) {
                    $rating = (float) $ratingMatch[1];
                    // Sanity check: rating should be between 50 and 100
                    if ($rating >= 50 && $rating <= 100) {
                        // Try to extract brand and year from previous line or current line prefix
                        $brand = null;
                        $year = null;
                        $name = $nameBrandYear;

                        // Look for "Brand / Year" pattern in previous lines
                        if ($i > 0 && preg_match('/^([A-Za-z\s]+)\s*\/\s*(\d{4})$/', $lines[$i - 1], $bm)) {
                            $brand = trim($bm[1]);
                            $year = (int) $bm[2];
                        }

                        // Clean name: remove trailing brand/year if embedded
                        $name = preg_replace('/\s+\d{4}$/', '', $name);
                        $name = preg_replace('/\s+(?:by|-)\s+.*$/i', '', $name);

                        if ($name && $rating) {
                            $skincares[] = [
                                'name' => $name,
                                'brand' => $brand,
                                'year' => $year,
                                'rating' => $rating,
                                'raw' => $line,
                            ];
                        }
                    }
                }
            }

            // Alternative pattern: line with just name, next line price, next line specs+rating
            $i++;
        }

        // Second pass: look for "Name\nPrice\nBrand / Year\nSpecs+Rating" blocks
        $i = 0;
        while ($i < count($lines) - 3) {
            $line1 = $lines[$i];     // Name
            $line2 = $lines[$i + 1]; // Price (e.g., "340€")
            $line3 = $lines[$i + 2]; // Brand / Year or Link text with specs
            $line4 = $lines[$i + 3]; // Brand / Year

            // If line2 looks like a price and line3 or line4 looks like brand/year
            if (preg_match('/^\d+[\d\s,]*€?$/', $line2) || preg_match('/^\d+[\d\s,]*€$/', $line2)) {
                $brand = null;
                $year = null;
                $name = $line1;
                $rating = null;

                // Check line4 or line3 for brand/year
                foreach ([$line4, $line3] as $checkLine) {
                    if (preg_match('/^([A-Za-z\s\.]+)\s*\/\s*(\d{4})$/', $checkLine, $bm)) {
                        $brand = trim($bm[1]);
                        $year = (int) $bm[2];
                        break;
                    }
                }

                // Check line3 for embedded specs and rating
                if (preg_match('/PWRPower\d+/', $line3)) {
                    // Extract rating from end of line3
                    if (preg_match('/(\d{2,3})$/', $line3, $rm)) {
                        $rating = (float) $rm[1];
                    }
                }

                if ($rating && $rating >= 50 && $rating <= 100) {
                    $skincares[] = [
                        'name' => $name,
                        'brand' => $brand,
                        'year' => $year,
                        'rating' => $rating,
                        'raw' => $line3,
                    ];
                    $i += 4;
                    continue;
                }
            }
            $i++;
        }

        // Deduplicate by name+brand
        $seen = [];
        $unique = [];
        foreach ($skincares as $r) {
            $key = strtolower(trim($r['name'])) . '|' . strtolower(trim($r['brand'] ?? ''));
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $r;
            }
        }

        return $unique;
    }

    private function findProduct(string $name, ?string $brand): ?Product
    {
        $name = trim($name);
        $slug = Str::slug($name);

        // Try exact name match first
        $product = Product::where('name', $name)->first();
        if ($product) return $product;

        // Try slug match
        $product = Product::where('slug', $slug)->first();
        if ($product) return $product;

        // Try brand + partial name
        if ($brand) {
            $product = Product::where('brand', 'like', '%' . $brand . '%')
                ->where(function ($q) use ($name) {
                    $q->where('name', 'like', '%' . $name . '%')
                      ->orWhere('name', 'like', '%' . Str::beforeLast($name, ' ') . '%');
                })
                ->first();
            if ($product) return $product;
        }

        // Try partial name match on any product
        $product = Product::where('name', 'like', '%' . $name . '%')->first();
        if ($product) return $product;

        // Try matching key words (remove year and player names)
        $keywords = preg_split('/\s+/', $name);
        $keywords = array_filter($keywords, function ($w) {
            return !preg_match('/^\d{4}$/', $w) && strlen($w) > 2;
        });

        if (count($keywords) >= 2) {
            $product = Product::query();
            foreach (array_slice($keywords, 0, 3) as $word) {
                $product->where('name', 'like', '%' . $word . '%');
            }
            $result = $product->first();
            if ($result) return $result;
        }

        return null;
    }
}
