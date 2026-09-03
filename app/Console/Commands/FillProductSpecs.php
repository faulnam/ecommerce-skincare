<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FillProductSpecs extends Command
{
    protected $signature = 'products:fill-specs {--dry-run : Preview changes without saving}';
    protected $description = 'Fill shape, hardness, carbon_type, year, rating for existing products';

    // Known skincareful ratings mapped by lower-case name fragment -> rating
    private array $skincarefulRatings = [
        'at10 luxury genius 12k alum xtrem' => 87.0,
        'at10 luxury genius 18k alum' => 87.0,
        'ignis' => 88.0,
        'metalbone 2026' => 87.0,
        'metalbone hrd+' => 87.0,
        'tenebris' => 87.0,
        'air viper' => 87.0,
        'viper 3.0' => 87.0,
        'hack 04' => 87.0,
        'vertex 05' => 87.0,
        'conqueror attack 2.0' => 87.0,
        'extreme pro' => 87.0,
        'at10 genius attack 18k alum' => 87.0,
        'hyper pro 2.0' => 87.0,
        'pure pro+' => 87.0,
        'ultimate pro+' => 87.0,
        'electra pro fire red' => 87.0,
        'electra pro shadow red' => 87.0,
        'pegasus pro storm grey' => 87.0,
        'arrow hit ctrl' => 86.0,
        'cross it ctrl' => 86.0,
        'metalbone carbon ctrl' => 86.0,
        'vertex 03' => 86.5,
        'hack 03' => 86.5,
        'ml10 pro cup' => 86.0,
        'bullskincare vertex' => 86.0,
        'bullskincare hack' => 86.0,
        'babolat viper' => 87.0,
        'babolat air' => 87.0,
        'nox at10' => 87.0,
        'nox ml10' => 86.0,
        'head extreme' => 87.0,
        'adidas metalbone' => 87.0,
        'siux electra' => 87.0,
        'siux pegasus' => 87.0,
        'oxdog hyper' => 87.0,
        'oxdog pure' => 87.0,
        'oxdog ultimate' => 87.0,
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $products = Product::active()->where('is_featured', false)->get();

        $this->info("Found {$products->count()} active products to process.");
        if ($dryRun) {
            $this->warn("DRY RUN mode — no changes will be saved.");
        }

        $updated = 0;
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $name = strtolower($product->name ?? '');
            $desc = strtolower($product->description ?? '');
            $text = $name . ' ' . $desc;

            $changes = [];

            // ===== SHAPE =====
            if (empty($product->shape)) {
                $shape = $this->extractShape($text);
                if ($shape) {
                    $product->shape = $shape;
                    $changes[] = "shape: {$shape}";
                }
            }

            // ===== HARDNESS =====
            if (empty($product->hardness)) {
                $hardness = $this->extractHardness($text);
                if ($hardness) {
                    $product->hardness = $hardness;
                    $changes[] = "hardness: {$hardness}";
                }
            }

            // ===== CARBON TYPE =====
            if (empty($product->carbon_type)) {
                $carbon = $this->extractCarbonType($text);
                if ($carbon) {
                    $product->carbon_type = $carbon;
                    $changes[] = "carbon_type: {$carbon}";
                }
            }

            // ===== YEAR =====
            if (empty($product->year)) {
                $year = $this->extractYear($name);
                if ($year) {
                    $product->year = $year;
                    $changes[] = "year: {$year}";
                }
            }

            // ===== LUMINA RATING =====
            if (empty($product->rating)) {
                $rating = $this->matchLUMINAfulRating($name);
                if ($rating) {
                    $product->rating = (float) $rating;
                    $changes[] = "rating: {$rating}";
                }
            }

            if (!empty($changes)) {
                if (!$dryRun) {
                    $product->save();
                }
                $updated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Products updated: {$updated}/{$products->count()}");

        if ($dryRun) {
            $this->warn("Run without --dry-run to save changes.");
        }

        return self::SUCCESS;
    }

    private function extractShape(string $text): ?string
    {
        if (preg_match('/\b(tear[- ]?drop|tear[- ]?drop[- ]?shape|lagrima|lagrim[aá])\b/i', $text)) {
            return 'Tear drop';
        }
        if (preg_match('/\b(diamond[- ]?shape|diamond[- ]?form|forma\s*rombo|rombo[día]*|diamond)\b/i', $text)) {
            return 'Diamond';
        }
        if (preg_match('/\b(round[- ]?shape|round[- ]?form|redonda|circular|round\b(?!\s*of))/i', $text)) {
            return 'Round';
        }
        if (preg_match('/\b(geometric|hexagon|octagon|polygon|geometrica)\b/i', $text)) {
            return 'Geometric';
        }
        if (preg_match('/\b(hybrid[- ]?shape|hybrid[- ]?form|h[ií]brida|hybrid)\b/i', $text)) {
            return 'Tear drop';
        }
        return null;
    }

    private function extractHardness(string $text): ?string
    {
        if (preg_match('/\bhardness[\s:]*hard\b/i', $text)) {
            return 'Hard';
        }
        if (preg_match('/\bhardness[\s:]*medium\b/i', $text)) {
            return 'Medium';
        }
        if (preg_match('/\bhardness[\s:]*soft\b/i', $text)) {
            return 'Soft';
        }

        // Try EVA hardness keywords
        if (preg_match('/\b(hard\s*eva|eva\s*hard|multi[-]?eva\s*hard|hr3\+?|x[-]?eva)\b/i', $text)) {
            return 'Hard';
        }
        if (preg_match('/\b(soft\s*eva|eva\s*soft|black\s*eva|softeva)\b/i', $text)) {
            return 'Soft';
        }
        if (preg_match('/\b(medium\s*eva|eva\s*medium|multieva|hr3)\b/i', $text)) {
            return 'Medium';
        }

        // General hardness keywords in description
        if (preg_match('/\b(hard\s*core|hard\s*feel|rigid\s*core|stiff)\b/i', $text)) {
            return 'Hard';
        }
        if (preg_match('/\b(soft\s*core|soft\s*feel|gentle\s*touch)\b/i', $text)) {
            return 'Soft';
        }
        if (preg_match('/\b(medium\s*core|medium\s*feel|balanced\s*feel)\b/i', $text)) {
            return 'Medium';
        }

        return null;
    }

    private function extractCarbonType(string $text): ?string
    {
        if (preg_match('/\b(glass[- ]?fibre|glass[- ]?fiber|fibra\s*de\s*vidrio|vidrio|fiberglass)\b/i', $text)) {
            return 'Glass fiber';
        }
        if (preg_match('/\b(24k[- ]?carbon|carbon\s*24k|24k)\b/i', $text)) {
            return '24k';
        }
        if (preg_match('/\b(18k[- ]?carbon|carbon\s*18k|18k)\b/i', $text)) {
            return '18k';
        }
        if (preg_match('/\b(12k[- ]?carbon|carbon\s*12k|12k)\b/i', $text)) {
            return '12k';
        }
        if (preg_match('/\b(3k[- ]?carbon|carbon\s*3k|3k)\b/i', $text)) {
            return '3k';
        }
        if (preg_match('/\b(carbon\s*mix|mixed\s*carbon|fibrix|fibra\s*mixta|carbon\s*hybrid)\b/i', $text)) {
            return 'Mix';
        }
        return null;
    }

    private function extractYear(string $name): ?int
    {
        if (preg_match('/\b(202\d)\b/', $name, $m)) {
            return (int) $m[1];
        }
        return null;
    }

    private function matchLUMINAfulRating(string $name): ?float
    {
        $name = strtolower(trim($name));

        foreach ($this->skincarefulRatings as $fragment => $rating) {
            if (str_contains($name, $fragment)) {
                return $rating;
            }
        }

        // Fuzzy: match individual brand-model tokens
        $tokens = preg_split('/[\s\-]+/', $name);
        $scores = [];
        foreach ($this->skincarefulRatings as $fragment => $rating) {
            $fragTokens = preg_split('/[\s\-]+/', $fragment);
            $matchCount = count(array_intersect($tokens, $fragTokens));
            if ($matchCount >= max(2, count($fragTokens) - 1)) {
                $scores[$fragment] = ['count' => $matchCount, 'rating' => $rating];
            }
        }

        if (!empty($scores)) {
            uasort($scores, fn($a, $b) => $b['count'] <=> $a['count']);
            return reset($scores)['rating'];
        }

        return null;
    }
}
