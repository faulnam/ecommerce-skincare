<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Insight;
use App\Models\BrandCatalog;
use Illuminate\Support\Facades\Http;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate split sitemaps (index, pages, products, insights, brands) with db query';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('  🗺️  Sitemap Generator — skincare.com');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        try {
            $this->info('▶ Generating Sitemap Pages...');
            $this->generatePagesSitemap();

            $this->info('▶ Generating Sitemap Products...');
            $this->generateProductsSitemap();

            $this->info('▶ Generating Sitemap Insights...');
            $this->generateInsightsSitemap();

            $this->info('▶ Generating Sitemap Brands...');
            $this->generateBrandsSitemap();

            $this->info('▶ Generating Main Sitemap Index...');
            $this->generateSitemapIndex();

            $this->newLine();
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info('  ✅ Sitemap berhasil digenerate!');
            $this->line('  Output: ' . public_path('sitemap.xml'));
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->error('  ❌ Gagal generate sitemap!');
            $this->error('  Error: ' . $e->getMessage());
            $this->error('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            return self::FAILURE;
        }
    }

    private function generatePagesSitemap(): void
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(url('/')))
            ->add(Url::create(url('/shop')))
            ->add(Url::create(url('/insight')))
            ->add(Url::create(url('/brand-catalog')))
            ->add(Url::create(url('/new-arrivals')))
            ->add(Url::create(url('/produk')))
            ->add(Url::create(url('/tentang')))
            ->add(Url::create(url('/contact')))
            ->add(Url::create(url('/galeri')))
            ->add(Url::create(url('/testimoni')));

        $sitemap->writeToFile(public_path('sitemap_pages.xml'));
    }

    private function generateProductsSitemap(): void
    {
        $sitemap = Sitemap::create();
        $products = Product::where('is_active', true)->get();

        foreach ($products as $product) {
            $url = Url::create(url("/produk/{$product->slug}"));
            $lastMod = $product->updated_at ?? $product->created_at;
            if ($lastMod) {
                $url->setLastModificationDate($lastMod);
            }
            $sitemap->add($url);
        }

        $sitemap->writeToFile(public_path('sitemap_products.xml'));
    }

    private function generateInsightsSitemap(): void
    {
        $sitemap = Sitemap::create();
        $insights = Insight::where('status', 'published')->get();

        foreach ($insights as $insight) {
            $url = Url::create(url("/insight/{$insight->slug}"));
            $lastMod = $insight->updated_at ?? $insight->created_at;
            if ($lastMod) {
                $url->setLastModificationDate($lastMod);
            }
            $sitemap->add($url);
        }

        $sitemap->writeToFile(public_path('sitemap_insights.xml'));
    }

    private function generateSitemapIndex(): void
    {
        SitemapIndex::create()
            ->add(url('/sitemap_pages.xml'))
            ->add(url('/sitemap_products.xml'))
            ->add(url('/sitemap_insights.xml'))
            ->writeToFile(public_path('sitemap.xml'));
    }
}
