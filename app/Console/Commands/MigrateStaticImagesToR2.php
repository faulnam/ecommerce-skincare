<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class MigrateStaticImagesToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:static-images-to-r2 {--old-url= : The public URL of the old R2 bucket}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate static website images to Cloudflare R2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oldUrlBase = $this->option('old-url') ? rtrim($this->option('old-url'), '/') : 'https://cdn.skincare.com';

        $this->info('Starting static image migration from ' . $oldUrlBase);

        $staticFiles = [
            'logo.png',
            'fiks1.jpg',
            'fiks2.png',
            'bg.png',
            '2.png',
            'shoes.png',
            '3.png',
            'home/hero-player.jpg',
            'home/feature-skincares.jpg',
            'home/feature-support.jpg',
            'seabank.png',
            'danamon.png',
            'permata.jpg',
            'bri.png',
            'bni.png',
            'btn.jpeg',
            'cimb.png',
            'ocbc.png',
            'mega.png',
            'dana.jpg',
            'gopay.webp',
            'shopeepay.png',
            'ovo.png',
            'linkaja.webp',
            'nobu.png',
            // Brand logos
            'babolat.jpeg',
            'head.jpeg',
            'bullskincare.jpeg',
            'nox.jpeg',
            'adidas.jpeg',
            'starvie.jpeg',
            'siux.jpeg',
            'drop-shot.jpeg',
            'kuikma.jpeg',
            'wilson.jpeg',
            'dunlop.jpeg',
            'joma.jpeg'
        ];

        $migratedCount = 0;
        $failedCount = 0;

        foreach ($staticFiles as $path) {
            $this->info("Processing: {$path}");
            $fullUrl = $oldUrlBase . '/' . $path;

            try {
                $response = Http::timeout(30)->withOptions(['verify' => false])->get($fullUrl);
                
                if ($response->successful()) {
                    $content = $response->body();
                    $result = Storage::disk('r2')->put($path, $content);
                    
                    if ($result) {
                        $this->line("<info>Success:</info> {$path} migrated.");
                        $migratedCount++;
                    } else {
                        $this->error("Failed to save {$path} to R2 disk.");
                        $failedCount++;
                    }
                } else {
                    $this->error("Failed to download {$path} - HTTP Status: " . $response->status());
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->error("Exception for {$path} - " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("Static migration completed! Successfully migrated: {$migratedCount}, Failures: {$failedCount}");
    }
}
