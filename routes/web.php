<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProductController as AdminProduct;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonial;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\HistoryController as AdminHistory;
use App\Http\Controllers\Admin\GalleryController as AdminGallery;
use App\Http\Controllers\Admin\ProfileController as AdminProfile;
use App\Http\Controllers\Admin\UserManagementController as AdminStaff;
use App\Http\Controllers\Admin\ShippingDiscountController;
use App\Http\Controllers\Admin\NotificationLogController as AdminNotificationLog;
use App\Http\Controllers\Admin\BiteshipLogController as AdminBiteshipLog;
use App\Http\Controllers\Admin\PaylabsLogController as AdminPaylabsLog;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\ReviewController as AdminReview;
use App\Http\Controllers\Admin\BrandCatalogController as AdminBrandCatalog;
use App\Http\Controllers\Admin\SpecificationController;
use App\Http\Controllers\Admin\ProductFilterController;
use App\Http\Controllers\Admin\AdminVoucherController;
use App\Http\Controllers\Customer\VoucherController as CustomerVoucher;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController as CustomerOrder;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\TestimonialController as CustomerTestimonial;
use App\Http\Controllers\Courier\DashboardController as CourierDashboard;
use App\Http\Controllers\Courier\DeliveryController as CourierDelivery;
use App\Http\Controllers\Courier\ProfileController as CourierProfile;
use App\Http\Controllers\Courier\NotificationController as CourierNotification;
use App\Http\Controllers\BiteshipWebhookController;
use App\Http\Controllers\PakasirWebhookController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

// API Search Products
Route::get('/api/search-products', function (Illuminate\Http\Request $request) {
    $query = $request->input('q', '');

    if (strlen($query) < 2) {
        return response()->json(['products' => []]);
    }

    $products = Product::active()
        ->inStock()
        ->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('brand', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%");
        })
        ->take(8)
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image_url' => $product->image_url,
                'brand' => $product->brand,
                'category_label' => $product->category_label,
                'formatted_price' => $product->hasActiveDiscount() ? $product->formatted_discounted_price : $product->formatted_price,
                'detail_url' => $product->detail_url,
            ];
        });

    return response()->json(['products' => $products]);
})->name('api.search-products');

// API Filter New Arrivals
Route::get('/api/new-arrivals/filter', [PageController::class, 'filterNewArrivals'])->name('api.new-arrivals.filter');

Route::get('/media/products/{path}', function (string $path) {
    $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

    if (str_contains($normalizedPath, '..')) {
        abort(404);
    }

    $candidatePaths = array_values(array_unique(array_filter([
        $normalizedPath,
        !str_starts_with($normalizedPath, 'products/') ? 'products/' . ltrim($normalizedPath, '/') : null,
    ])));

    $resolvedPath = null;
    foreach ($candidatePaths as $candidate) {
        if (Storage::disk('public')->exists($candidate)) {
            $resolvedPath = $candidate;
            break;
        }
    }

    if ($resolvedPath === null) {
        $r2Url = config('filesystems.disks.r2.url');
        if ($r2Url) {
            return redirect(rtrim($r2Url, '/') . '/' . ltrim($path, '/'));
        }
        abort(404);
    }

    return response()->file(Storage::disk('public')->path($resolvedPath));
})->where('path', '.*')->name('media.product');

// Public Pages (Guest)
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/new-arrivals', [PageController::class, 'newArrivals'])->name('new-arrivals');
Route::get('/shop-category', [PageController::class, 'shopCategory'])->name('shop.category');
Route::get('/tentang', [PageController::class, 'tentang'])->name('tentang');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/insight', [PageController::class, 'insight'])->name('insight');
Route::get('/insight/{insight}', [PageController::class, 'insightShow'])->name('insight.show');
Route::get('/brand-catalog', [PageController::class, 'brandCatalog'])->name('brand-catalog');
Route::get('/shop', [PageController::class, 'shop'])->name('shop');
Route::get('/filter-products', [PageController::class, 'filterProducts'])->name('filter-products');
Route::get('/help-center', [PageController::class, 'helpCenter'])->name('help-center');
Route::get('/policy', [PageController::class, 'policy'])->name('policy');
Route::get('/return-refund', [PageController::class, 'returnRefund'])->name('return-refund');
Route::get('/guarantee', [PageController::class, 'guarantee'])->name('guarantee');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit');

// SEO Friendly URLs for Categories and Brands
Route::get('/kategori/{slug}', [PageController::class, 'categoryShow'])->name('kategori.show');
Route::get('/brand/{slug}', [PageController::class, 'brandShow'])->name('brand.show');

// SEO 301 Redirects for Legacy/404 URLs
Route::redirect('/hijab', '/kategori/hijab-hijab', 301);
Route::redirect('/shoes', '/kategori/sepatu-hijab', 301);
Route::redirect('/apparel', '/kategori/apparel', 301);
Route::redirect('/accessories', '/kategori/aksesoris-hijab', 301);
Route::redirect('/insight/5-kesalahan-umum-pemula-saat-bermain-hijab-dan-cara-mengatasinya', '/insight/5-kesalahan-umum-pemula-saat-bermain-hijab', 301);
Route::redirect('/insight/cara-membersihkan-sepatu-hijab-setelah-bermain', '/insight/cara-membersihkan-sepatu-hijab-biar-awet', 301);

Route::get('/produk', [PageController::class, 'produkIndex'])->name('produk.index');
Route::get('/produk/{category}/{product}', [PageController::class, 'produkShow'])->name('produk.show');
Route::get('/produk/{product}', [PageController::class, 'produkShowLegacy']); // Fallback legacy URL
Route::get('/dummy-produk/{slug}', [\App\Http\Controllers\DummyProductController::class, 'show'])->name('dummy.produk.show');
Route::get('/galeri', [PageController::class, 'galeri'])->name('galeri');
Route::get('/testimoni', [PageController::class, 'testimoni'])->name('testimoni');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/request-otp', [AuthController::class, 'requestRegisterOtp'])->name('register.request-otp')->middleware('throttle:3,1');
    Route::post('/register/verify-otp', [AuthController::class, 'verifyRegisterOtp'])->name('register.verify-otp')->middleware('throttle:5,1');

    // Forgot Password Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password/request-otp', [AuthController::class, 'requestPasswordResetOtp'])->name('password.request-otp')->middleware('throttle:3,1');
    Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyPasswordResetOtp'])->name('password.verify-otp')->middleware('throttle:5,1');
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

    // Firebase Authentication Route
    Route::post('/auth/firebase', [AuthController::class, 'handleFirebaseAuth'])->name('auth.firebase');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Pakasir Webhook (no auth required)
Route::post('/webhook/pakasir', [PakasirWebhookController::class, 'handleWebhook'])->name('webhook.pakasir');

// Paylabs Webhook (no auth required)
Route::post('/webhook/paylabs', [\App\Http\Controllers\PaylabsWebhookController::class, 'handleWebhook'])->name('webhook.paylabs');

// Extract Public Key (temporary route for debugging)
Route::get('/extract-public-key', function () {
    $privateKeyPath = storage_path('app/paylabs/private-key.pem');
    $publicKeyPath = storage_path('app/paylabs/public-key-correct.pem');

    $privateKeyContent = file_get_contents($privateKeyPath);
    $privateKey = openssl_pkey_get_private($privateKeyContent);

    if ($privateKey === false) {
        return response("Failed to load private key: " . openssl_error_string(), 500);
    }

    $publicKeyDetails = openssl_pkey_get_details($privateKey);
    $publicKeyPem = $publicKeyDetails['key'];

    file_put_contents($publicKeyPath, $publicKeyPem);
    openssl_free_key($privateKey);

    return response()->json([
        'success' => true,
        'message' => 'Public key extracted successfully!',
        'saved_to' => $publicKeyPath,
        'public_key' => $publicKeyPem,
    ]);
})->name('extract.public.key');

// Biteship Webhook (no auth required)
Route::post('/webhook/biteship', [BiteshipWebhookController::class, 'handleWebhook'])->name('webhook.biteship');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Store Information
    Route::get('/store-information', [\App\Http\Controllers\Admin\StoreInformationController::class, 'index'])->name('store-information.index');
    Route::put('/store-information/banner/{banner}', [\App\Http\Controllers\Admin\StoreInformationController::class, 'updateBanner'])->name('store-information.banner.update');

    // Insights
    Route::get('insights/search-products', [\App\Http\Controllers\AdminInsightController::class, 'searchProducts'])
        ->name('insights.search-products');
    Route::get('insights/search-insights', [\App\Http\Controllers\AdminInsightController::class, 'searchInsights'])
        ->name('insights.search-insights');
    Route::resource('insights', \App\Http\Controllers\AdminInsightController::class)->except(['show']);

    // Products
    Route::resource('products', AdminProduct::class)->parameters(['products' => 'product:id']);
    Route::patch('/products/{product:id}/toggle-status', [AdminProduct::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('/products/{product:id}/toggle-featured', [AdminProduct::class, 'toggleFeatured'])->name('products.toggle-featured');

    // Free Products Event
    // Route::post('free-products/settings', [\App\Http\Controllers\Admin\FreeProductController::class, 'updateSettings'])->name('free-products.settings');
    // Route::resource('free-products', \App\Http\Controllers\Admin\FreeProductController::class)->only(['index', 'create', 'store', 'destroy']);

    // Specifications & Filters
    Route::resource('specifications', SpecificationController::class)->except(['show']);
    Route::resource('filters', ProductFilterController::class)->except(['show']);

    // Orders
    Route::get('/orders', [AdminOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrder::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrder::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/verify-payment', [AdminOrder::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::patch('/orders/{order}/reject-payment', [AdminOrder::class, 'rejectPayment'])->name('orders.reject-payment');
    Route::patch('/orders/{order}/shipping', [AdminOrder::class, 'updateShipping'])->name('orders.update-shipping');
    Route::post('/orders/{order}/assign-courier', [AdminOrder::class, 'assignCourier'])->name('orders.assign-courier');
    Route::get('/orders/{order}/receipt', [AdminOrder::class, 'viewReceipt'])->name('orders.receipt');
    Route::get('/orders/{order}/print-receipt', [AdminOrder::class, 'printReceipt'])->name('orders.print-receipt');
    Route::get('/couriers', [AdminOrder::class, 'getCouriers'])->name('couriers.list');

    // Pickup & Tracking
    Route::post('/orders/{order}/request-pickup', [\App\Http\Controllers\Admin\PickupController::class, 'requestPickup'])->name('orders.request-pickup');
    Route::post('/orders/{order}/update-waybill', [\App\Http\Controllers\Admin\PickupController::class, 'updateWaybill'])->name('orders.update-waybill');
    Route::get('/orders/{order}/tracking', [\App\Http\Controllers\Admin\PickupController::class, 'getTracking'])->name('orders.tracking');
    Route::get('/orders/{order}/print-label', [\App\Http\Controllers\Admin\PickupController::class, 'printLabel'])->name('orders.print-label');
    Route::post('/orders/{order}/check-paylabs-status', [AdminOrder::class, 'checkPaylabsStatus'])->name('orders.check-paylabs-status');
    Route::post('/orders/{order}/process-refund', [AdminOrder::class, 'processRefund'])->name('orders.process-refund');
    Route::post('/orders/{order}/reject-refund', [AdminOrder::class, 'rejectRefund'])->name('orders.reject-refund');

    // Testimonials
    Route::get('/testimonials', [AdminTestimonial::class, 'index'])->name('testimonials.index');
    Route::get('/testimonials/create', [AdminTestimonial::class, 'create'])->name('testimonials.create');
    Route::post('/testimonials', [AdminTestimonial::class, 'store'])->name('testimonials.store');
    Route::patch('/testimonials/{testimonial}/approve', [AdminTestimonial::class, 'approve'])->name('testimonials.approve');
    Route::patch('/testimonials/{testimonial}/reject', [AdminTestimonial::class, 'reject'])->name('testimonials.reject');
    Route::delete('/testimonials/{testimonial}', [AdminTestimonial::class, 'destroy'])->name('testimonials.destroy');

    // Reviews
    Route::get('/reviews', [AdminReview::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [AdminReview::class, 'show'])->name('reviews.show');
    Route::patch('/reviews/{review}/approve', [AdminReview::class, 'approve'])->name('reviews.approve');
    Route::patch('/reviews/{review}/reject', [AdminReview::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [AdminReview::class, 'destroy'])->name('reviews.destroy');

    // Users
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUser::class, 'show'])->name('users.show')->withTrashed();
    Route::patch('/users/{user}/toggle-status', [AdminUser::class, 'toggleStatus'])->name('users.toggle-status')->withTrashed();
    Route::patch('/users/{user}/reset-password', [AdminUser::class, 'resetPassword'])->name('users.reset-password')->withTrashed();

    // History
    Route::get('/history', [AdminHistory::class, 'index'])->name('history.index');
    Route::get('/history/{order}', [AdminHistory::class, 'show'])->name('history.show');
    Route::get('/history-export', [AdminHistory::class, 'export'])->name('history.export');

    // Analytics
    // Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    // Route::get('/analytics/guide', [\App\Http\Controllers\Admin\AnalyticsController::class, 'guide'])->name('analytics.guide');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.mark-read');

    // Notification Logs
    Route::get('/notification-logs', [AdminNotificationLog::class, 'index'])->name('notification-logs.index');
    Route::get('/notification-logs/{log}', [AdminNotificationLog::class, 'show'])->name('notification-logs.show');

    // Biteship Logs
    Route::get('/biteship-logs', [AdminBiteshipLog::class, 'index'])->name('biteship-logs.index');
    Route::get('/biteship-logs/{log}', [AdminBiteshipLog::class, 'show'])->name('biteship-logs.show');

    // Paylabs Logs
    Route::get('/paylabs-logs', [AdminPaylabsLog::class, 'index'])->name('paylabs-logs.index');
    Route::get('/paylabs-logs/{log}', [AdminPaylabsLog::class, 'show'])->name('paylabs-logs.show');

    // Test Push Notification
    Route::get('/test-notification', function () {
        return view('admin.test-notification');
    })->name('test-notification');

    Route::post('/test-push', function () {
        $user = auth()->user();
        $webPush = app(\App\Services\WebPushService::class);

        $result = $webPush->send(
            $user,
            '🔔 Test Push Notification',
            'Ini adalah test push notification dari server. Jika Anda melihat ini, push notification berfungsi!',
            route('admin.dashboard'),
            'new_order'
        );

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Push notification terkirim!' : 'Tidak ada subscription aktif untuk user ini'
        ]);
    })->name('test-push');

    // Galleries
    Route::resource('galleries', AdminGallery::class);
    Route::patch('/galleries/{gallery}/toggle', [AdminGallery::class, 'toggle'])->name('galleries.toggle');

    // Admin Profile
    Route::get('/profile', [AdminProfile::class, 'index'])->name('profile.index');
    Route::put('/profile', [AdminProfile::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [AdminProfile::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/password', [AdminProfile::class, 'updatePassword'])->name('profile.password');

    // Staff Management (Admin & Courier)
    Route::get('/staff', [AdminStaff::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [AdminStaff::class, 'create'])->name('staff.create');
    Route::post('/staff', [AdminStaff::class, 'store'])->name('staff.store');
    Route::get('/staff/{user}/edit', [AdminStaff::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{user}', [AdminStaff::class, 'update'])->name('staff.update');
    Route::patch('/staff/{user}/toggle-active', [AdminStaff::class, 'toggleActive'])->name('staff.toggle-active');
    Route::delete('/staff/{user}', [AdminStaff::class, 'destroy'])->name('staff.destroy');

    // Shipping Discounts
    Route::resource('shipping-discounts', ShippingDiscountController::class)->except(['show']);
    Route::patch('/shipping-discounts/{shipping_discount}/toggle', [ShippingDiscountController::class, 'toggleActive'])->name('shipping-discounts.toggle');

    // Specifications
    Route::resource('specifications', \App\Http\Controllers\Admin\SpecificationController::class)->except(['show']);

    // Vouchers
    Route::resource('vouchers', AdminVoucherController::class);
    Route::patch('/vouchers/{id}/toggle-status', [AdminVoucherController::class, 'toggleStatus'])->name('vouchers.toggle-status');
    Route::patch('/vouchers/{id}/toggle-display', [AdminVoucherController::class, 'toggleDisplay'])->name('vouchers.toggle-display');

    // Brand Catalogs
    Route::resource('brand-catalogs', AdminBrandCatalog::class);
    Route::patch('/brand-catalogs/{brandCatalog}/toggle', [AdminBrandCatalog::class, 'toggle'])->name('brand-catalogs.toggle');

    // Reports
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
    Route::get('/reports/download-sales', [AdminReport::class, 'downloadSalesReport'])->name('reports.download-sales');
    Route::get('/reports/download-soldout', [AdminReport::class, 'downloadSoldOutReport'])->name('reports.download-soldout');

    // Admin Chat
    Route::get('chats', [AdminChatController::class, 'index'])->name('chats.index');
    Route::get('chats/list', [AdminChatController::class, 'getChats'])->name('chats.list');
    Route::get('chats/{chat}/messages', [AdminChatController::class, 'getMessages'])->name('chats.messages');
    Route::post('chats/{chat}/send', [AdminChatController::class, 'sendMessage'])->name('chats.send');
    Route::post('chats/{chat}/close', [AdminChatController::class, 'closeChat'])->name('chats.close');
    Route::get('chats/count-waiting', [AdminChatController::class, 'countWaiting'])->name('chats.count');
});

// Courier Routes
Route::prefix('courier')->name('courier.')->middleware(['auth', 'courier'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [CourierDashboard::class, 'index'])->name('dashboard');

    // Deliveries
    Route::get('/deliveries', [CourierDelivery::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/history', [CourierDelivery::class, 'history'])->name('deliveries.history');
    Route::get('/deliveries/{order}', [CourierDelivery::class, 'show'])->name('deliveries.show');
    Route::post('/deliveries/{order}/pickup', [CourierDelivery::class, 'pickUp'])->name('deliveries.pickup');
    Route::post('/deliveries/{order}/start', [CourierDelivery::class, 'startDelivery'])->name('deliveries.start');
    Route::post('/deliveries/{order}/delivered', [CourierDelivery::class, 'markDelivered'])->name('deliveries.delivered');

    // Location Tracking
    Route::post('/location/update', [CourierDelivery::class, 'updateLocation'])->name('location.update');

    // Profile
    Route::get('/profile', [CourierProfile::class, 'show'])->name('profile');
    Route::put('/profile', [CourierProfile::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [CourierProfile::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/password', [CourierProfile::class, 'updatePassword'])->name('profile.password');

    // Notifications
    Route::post('/notifications/mark-read', [CourierNotification::class, 'markAllAsRead'])->name('notifications.markRead');
});


// Customer Routes - Cart & Checkout (accessible without login)
Route::post('/customer/cart/add/{product}', [CartController::class, 'add'])->name('customer.cart.add');
Route::get('/customer/cart', [CartController::class, 'index'])->name('customer.cart.index');
Route::patch('/customer/cart/{cart}', [CartController::class, 'update'])->name('customer.cart.update');
Route::delete('/customer/cart/{cart}', [CartController::class, 'remove'])->name('customer.cart.remove');
Route::delete('/customer/cart', [CartController::class, 'clear'])->name('customer.cart.clear');
Route::get('/customer/cart/count', [CartController::class, 'count'])->name('customer.cart.count');

// Wishlist Routes
Route::middleware('auth')->group(function () {
    Route::get('/customer/wishlist', [\App\Http\Controllers\Customer\WishlistController::class, 'index'])->name('customer.wishlist.index');
    Route::delete('/customer/wishlist/{product}', [\App\Http\Controllers\Customer\WishlistController::class, 'remove'])->name('customer.wishlist.remove');
    Route::delete('/customer/wishlist', [\App\Http\Controllers\Customer\WishlistController::class, 'clear'])->name('customer.wishlist.clear');
    Route::get('/customer/wishlist/check/{product}', [\App\Http\Controllers\Customer\WishlistController::class, 'check'])->name('customer.wishlist.check');
});
    Route::post('/customer/wishlist/add/{product}', [\App\Http\Controllers\Customer\WishlistController::class, 'add'])->name('customer.wishlist.add');
    Route::get('/customer/wishlist/count', [\App\Http\Controllers\Customer\WishlistController::class, 'count'])->name('customer.wishlist.count');





// Guest Checkout & Payment (accessible without login)
Route::get('/customer/checkout', [CustomerOrder::class, 'checkout'])->name('customer.checkout');
Route::post('/customer/checkout', [CustomerOrder::class, 'processCheckout'])->name('customer.checkout.process')->middleware('idempotency');
Route::post('/customer/shipping/rates', [\App\Http\Controllers\Customer\ShippingController::class, 'getRates'])->name('customer.shipping.rates');

// Guest Voucher Claim (accessible without login)
Route::post('/customer/vouchers/claim', [CustomerVoucher::class, 'claim'])->name('customer.vouchers.claim')->middleware('idempotency');

// Get all claimed vouchers for checkout
Route::get('/customer/vouchers/all-claimed', [CustomerVoucher::class, 'getAllClaimedVouchers'])->name('customer.vouchers.all-claimed')->middleware('auth');

// Guest Payment Routes (accessible without login)
Route::get('/guest/payment/{order}/select-gateway', [PaymentController::class, 'selectGateway'])->name('customer.payment.select-gateway');
Route::get('/guest/payment/{order}', [PaymentController::class, 'show'])->name('customer.payment.show');
Route::post('/guest/payment/{order}/process', [PaymentController::class, 'process'])->name('customer.payment.process')->middleware('idempotency');
Route::get('/guest/payment/{order}/waiting', [PaymentController::class, 'waiting'])->name('customer.payment.waiting');
Route::get('/guest/payment/{order}/check-status', [PaymentController::class, 'checkStatus'])->name('customer.payment.check-status');
Route::get('/guest/payment/{order}/redirect', [PaymentController::class, 'redirect'])->name('customer.payment.redirect');
Route::get('/guest/payment/{order}/callback', [PakasirWebhookController::class, 'handleCallback'])->name('customer.payment.callback');

// Guest Paylabs Payment Routes
Route::get('/guest/payment-paylabs/{order}', [\App\Http\Controllers\Customer\PaylabsPaymentController::class, 'show'])->name('customer.payment.paylabs.show');
Route::post('/guest/payment-paylabs/{order}/process', [\App\Http\Controllers\Customer\PaylabsPaymentController::class, 'process'])->name('customer.payment.paylabs.process')->middleware('idempotency');
Route::get('/guest/payment-paylabs/{order}/waiting', [\App\Http\Controllers\Customer\PaylabsPaymentController::class, 'waiting'])->name('customer.payment.paylabs.waiting');
Route::get('/guest/payment-paylabs/{order}/check-status', [\App\Http\Controllers\Customer\PaylabsPaymentController::class, 'checkStatus'])->name('customer.payment.paylabs.check-status');
Route::get('/guest/payment-paylabs/{order}/callback', [\App\Http\Controllers\PaylabsWebhookController::class, 'handleCallback'])->name('customer.payment.paylabs.callback');
Route::post('/guest/payment-paylabs/{order}/simulate', [\App\Http\Controllers\Customer\PaylabsPaymentController::class, 'simulate'])->name('customer.payment.paylabs.simulate');

// Guest Order Tracking
Route::get('/customer/orders/{order}/track', [CustomerOrder::class, 'guestTrackOrder'])->name('customer.orders.guest-track');
Route::get('/customer/orders/{order}/guest-tracking', [CustomerOrder::class, 'guestGetTracking'])->name('customer.orders.guest-tracking');

// Guest Order Detail (accessible without login)
Route::get('/guest/orders/{order}', [CustomerOrder::class, 'guestShow'])->name('customer.orders.guest-show');
Route::post('/guest/orders/{order}/simulate-completed', [CustomerOrder::class, 'simulateCompleted'])->name('customer.orders.simulate-completed');

// Guest Order Lookup (accessible without login)
Route::get('/cek-pesanan', [CustomerOrder::class, 'lookupForm'])->name('customer.orders.lookup');
Route::post('/cek-pesanan', [CustomerOrder::class, 'lookupOrder'])->name('customer.orders.lookup.process');

// Customer Routes
Route::prefix('customer')->name('customer.')->middleware(['auth', 'customer'])->group(function () {
    // Products (alias to public produk route)
    Route::get('/products', function () {
        return redirect()->route('produk.index');
    })->name('products.index');

    Route::get('/products/{product}', function (Product $product) {
        return redirect($product->detail_url);
    })->name('products.show');

    // Reviews
    Route::post('/reviews/{product}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Checkout & Orders (removed - moved to guest accessible routes)
    Route::get('/orders', [CustomerOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [CustomerOrder::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/receipt', [CustomerOrder::class, 'receipt'])->name('orders.receipt');
    Route::post('/orders/{order}/payment', [CustomerOrder::class, 'uploadPayment'])->name('orders.upload-payment');
    Route::patch('/orders/{order}/cancel', [CustomerOrder::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/cancel-status', [CustomerOrder::class, 'checkCancelStatus'])->name('orders.cancel-status');
    Route::post('/orders/{order}/request-refund', [CustomerOrder::class, 'requestRefund'])->name('orders.request-refund');
    Route::patch('/orders/{order}/confirm', [CustomerOrder::class, 'confirmReceived'])->name('orders.confirm');

    // Payment Gateway (Pakasir) - untuk customer yang sudah login
    // Nama penuh: customer.auth.payment.* (tidak bentrok dengan guest: customer.payment.*)
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('auth.payment.show');
    Route::post('/payment/{order}/process', [PaymentController::class, 'process'])->name('auth.payment.process');
    Route::get('/payment/{order}/waiting', [PaymentController::class, 'waiting'])->name('auth.payment.waiting');
    Route::get('/payment/{order}/check-status', [PaymentController::class, 'checkStatus'])->name('auth.payment.check-status');
    Route::post('/payment/{order}/simulate', [PaymentController::class, 'simulatePayment'])->name('auth.payment.simulate');
    Route::get('/payment/{order}/redirect', [PaymentController::class, 'redirect'])->name('auth.payment.redirect');
    Route::get('/payment/{order}/callback', [PakasirWebhookController::class, 'handleCallback'])->name('auth.payment.callback');

    // Testimonials
    Route::post('/orders/{order}/testimonial', [CustomerTestimonial::class, 'store'])->name('testimonials.store');
    Route::put('/testimonials/{testimonial}', [CustomerTestimonial::class, 'update'])->name('testimonials.update');

    // Order Tracking
    Route::get('/orders/{order}/tracking', [CustomerOrder::class, 'getTracking'])->name('orders.tracking');
    Route::get('/orders/{order}/courier-location', [CustomerOrder::class, 'getCourierLocation'])->name('orders.courier-location');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/rewards', [ProfileController::class, 'rewards'])->name('profile.rewards');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::post('/profile/request-delete-otp', [ProfileController::class, 'requestDeleteOtp'])->name('profile.request-delete-otp');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Welcome Bonus
    Route::post('/claim-welcome-bonus', [\App\Http\Controllers\Customer\WelcomeBonusController::class, 'claimBonus'])->name('claim-welcome-bonus');

    // Vouchers (require auth)
    Route::get('/vouchers', [CustomerVoucher::class, 'index'])->name('vouchers.index');
    Route::get('/my-vouchers', [CustomerVoucher::class, 'myVouchers'])->name('vouchers.my-vouchers');
    Route::get('/vouchers/checkout-available', [CustomerVoucher::class, 'getAvailableForCheckout'])->name('vouchers.checkout-available');
    Route::post('/vouchers/validate', [CustomerVoucher::class, 'validate'])->name('vouchers.validate');

    // Notifications
    Route::get('/notifications', function () {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('customer.notifications.index', compact('notifications'));
    })->name('notifications.index');

    Route::post('/notifications/mark-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    })->name('notifications.mark-read');
});

// Chatbot Route
Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.chat');

// Live Chat Routes (Frontend)
Route::post('/live-chat/{session}/send', [LiveChatController::class, 'send'])->name('live-chat.send');
Route::get('/live-chat/{session}/poll', [LiveChatController::class, 'poll'])->name('live-chat.poll');
