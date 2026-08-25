<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Menyuntikkan variabel secara global ke SEMUA halaman Blade sebelum di-render
        View::composer('*', function ($view) {
            // 1. Hardcode bahasa ke 'id'
            $lang = 'id';
            
            // 2. Simpan ke session agar konsisten (opsional tapi aman)
            session(['locale' => 'id']);

            // 3. Load file common.json
            $common = json_decode(@file_get_contents(public_path('translation/common.json')), true) ?? [];

            // 4. Bagikan variabel secara paksa ke seluruh Blade
            $view->with([
                'lang' => $lang,
                'common' => $common
            ]);
        });
        // Register Order Observer for push notifications
        Order::observe(OrderObserver::class);

        // Register Demo Content Observer for automatic 3-minute cleanup
        $demoTrackedModels = [
            \App\Models\Product::class,
            \App\Models\ProductVariant::class,
            \App\Models\Insight::class,
            \App\Models\Voucher::class,
            \App\Models\Gallery::class,
            \App\Models\Banner::class,
            \App\Models\Specification::class,
            \App\Models\ProductFilter::class,
            \App\Models\ShippingDiscount::class,
            \App\Models\Testimonial::class,
            \App\Models\Review::class,
            \App\Models\Order::class,
            \App\Models\BrandCatalog::class,
            \App\Models\LiveChatMessage::class,
        ];

        foreach ($demoTrackedModels as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::observe(\App\Observers\DemoContentObserver::class);
            }
        }

        // Register Notification Log Listener
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Mail\Events\MessageSent::class,
            \App\Listeners\LogSentMessage::class,
        );
    }
}
