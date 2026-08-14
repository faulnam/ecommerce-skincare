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

        // Register Notification Log Listener
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Mail\Events\MessageSent::class,
            \App\Listeners\LogSentMessage::class,
        );
    }
}
