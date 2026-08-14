<?php

use App\Http\Controllers\Api\CourierLocationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PushNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Notification check (requires web auth via session)
Route::middleware('web')->group(function () {
    Route::get('/notifications/check', [NotificationController::class, 'check']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    
    // Push notification endpoints
    Route::get('/push/vapid-key', [PushNotificationController::class, 'getVapidKey']);
    Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe']);
    Route::post('/push/unsubscribe', [PushNotificationController::class, 'unsubscribe']);
    
    // Product variants (public)
    Route::get('/products/{product}/variants', function (\App\Models\Product $product) {
        $activeVariants = $product->activeVariants;
        return response()->json([
            'has_variants' => $activeVariants->isNotEmpty(),
            'variants' => $activeVariants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'stock' => $variant->stock,
                    'price_adjustment' => $variant->price_adjustment,
                    'price' => $variant->formatted_final_price,
                    'image' => $variant->image_url,
                ];
            }),
        ]);
    });
    
    // Product search (public)
    Route::get('/products/search', function () {
        $query = request()->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['products' => []]);
        }
        
        $products = \App\Models\Product::where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit(10)
            ->get();
            
        return response()->json([
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category_label,
                    'price' => $product->formatted_price,
                    'image' => $product->image_url,
                    'url' => $product->detail_url,
                ];
            })
        ]);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    // Courier location tracking
    Route::post('/courier/location', [CourierLocationController::class, 'update']);
    Route::post('/courier/location/stop', [CourierLocationController::class, 'stopTracking']);
    Route::get('/orders/{order}/tracking', [CourierLocationController::class, 'getForOrder']);
});
