<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\Gallery;
use App\Models\Order;
use App\Models\Voucher;
use App\Models\BrandCatalog;
use App\Repositories\VoucherRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{

    /**
     * Show home page
     */
    public function home(Request $request)
    {
        $products = Product::active()
            ->inStock()
            ->where('is_featured', false)
            ->latest()
            ->take(16)
            ->get();

        $testimonials = Testimonial::approved()
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        $galleries = Gallery::active()
            ->ordered()
            ->take(6)
            ->get();

        // Statistik realtime
        $stats = $this->getStats();

        $sections = $this->getShopSections();

        // Shop products with server-side filtering and pagination
        // Google Drive image products appear first, Shopee format products at the end
        $shopProductsQuery = Product::active()->inStock()->where('is_featured', false)
            ->orderByRaw("CASE WHEN image LIKE '%lh3.googleusercontent.com%' THEN 0 ELSE 1 END ASC");

        if ($request->filled('brand')) {
            $shopProductsQuery->where('brand', $request->brand);
        }

        // NOTE: kolom 'level' sudah di-drop (migration 2026_07_05_000001). Param ?level= legacy
        // sengaja diabaikan agar URL lama yang masih di-crawl Google tidak 500 dan canonical->base ter-render.

        // Apply category filter from sidebar (uses 'type' or 'category' field in DB)
        if ($request->filled('filter_category')) {
            $cat = strtolower($request->filter_category);
            $typeMap = [
                'serum' => 'serum',
                'moisturizer' => 'moisturizer',
                'cleanser' => 'cleanser',
                'toner' => 'toner',
                'sunscreen' => 'sunscreen',
                'bundle' => 'bundle',
            ];
            $type = $typeMap[$cat] ?? $cat;
            $shopProductsQuery->where(function($q) use ($type, $cat) {
                $q->where('type', $type)->orWhere('category', 'like', "%{$cat}%");
            });
        }

        if ($request->filled('filter_brand')) {
            $shopProductsQuery->where('brand', $request->filter_brand);
        }

        // Advanced filters
        if ($request->filled('filter_shape')) {
            $shopProductsQuery->where('shape', $request->filter_shape);
        }
        if ($request->filled('filter_hardness')) {
            $shopProductsQuery->where('hardness', $request->filter_hardness);
        }
        if ($request->filled('filter_carbon_type')) {
            $shopProductsQuery->where('carbon_type', $request->filter_carbon_type);
        }

        // Sort by price
        if ($request->filled('filter_price')) {
            if ($request->filter_price === 'low') {
                $shopProductsQuery->orderByDiscountedPrice('asc');
            } elseif ($request->filter_price === 'high') {
                $shopProductsQuery->orderByDiscountedPrice('desc');
            }
        }

        // Sort by popularity, latest, year, or hijabful rating
        if ($request->filled('filter_sort')) {
            switch ($request->filter_sort) {
                case 'popular':
                    $shopProductsQuery->withCount('orderItems')->orderByDesc('order_items_count');
                    break;
                case 'latest':
                    $shopProductsQuery->orderBy('created_at', 'desc');
                    break;
                case 'year_asc':
                    $shopProductsQuery->orderByRaw('year IS NULL, year asc');
                    break;
                case 'year_desc':
                    $shopProductsQuery->orderByRaw('year IS NULL, year desc');
                    break;
                case 'hijabful_rating':
                    $shopProductsQuery->orderByRaw('hijabful_rating IS NULL, hijabful_rating desc');
                    break;
                default:
                    $shopProductsQuery->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $shopProductsQuery->orderBy('created_at', 'desc');
        }

        $shopProducts = $shopProductsQuery->paginate(30)->onEachSide(2)->withQueryString();
        $brands = Product::active()->whereNotNull('brand')->distinct()->pluck('brand')->sort();

        // New Arrivals (Latest) -> using product-1
        $newArrivals = Product::active()->inStock()->where('is_featured', false)->where('image', 'like', '%product-1%')->orderBy('created_at', 'desc')->take(15)->get();
        
        // Discounted Products
        $discountProducts = Product::active()->inStock()->where('is_featured', false)
            ->where(function($query) {
                $query->where('discount_percent', '>', 0)
                      ->orWhereHas('variants', function($q) {
                          $q->where('discount_percent', '>', 0);
                      });
            })->take(15)->get();

        // Fetch active vouchers for frontend
        $voucherRepository = new VoucherRepository();
        $vouchers = $voucherRepository->getActiveVouchersForFrontend(10);

        // Get user's claimed vouchers if logged in
        $userVouchers = [];
        if (auth()->check()) {
            $userVouchers = $voucherRepository->getUserVouchers(auth()->id());
        }

        // Get user's wishlist product IDs
        $userWishlistIds = [];
        if (auth()->check()) {
            $userWishlistIds = \App\Models\Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray();
        } else {
            $userWishlistIds = session()->get('guest_wishlist', []);
        }

        // Fetch free products for eligible users
        $isGuest = !auth()->check();
        $isCustomer = auth()->check() && auth()->user()->role === 'customer';
        $isEligibleCustomer = $isCustomer
            && !auth()->user()->welcome_bonus_claimed
            && !auth()->user()->orders()->whereNotIn('status', ['pending', 'cancelled', 'failed'])->exists();
        $shouldShowFreeProducts = $isGuest || $isEligibleCustomer;

        $freeProducts = collect();
        $freeEventTitle = null;
        $freeEventDescription = null;
        if ($shouldShowFreeProducts) {
            $freeProducts = Product::where('is_free_event', true)->active()->inStock()->get();
            $freeEventTitle = \App\Models\Setting::where('key', 'free_event_title')->value('value') ?? 'Pilihan Produk Gratis 🎁';
            $freeEventDescription = \App\Models\Setting::where('key', 'free_event_description')->value('value') ?? 'Untuk pembelian pertama satu akun';
        }

        // Banners
        $heroBanners = \App\Models\Banner::where('type', 'hero')->where('is_active', true)->orderBy('sort_order')->get();
        $splitBanners = \App\Models\Banner::where('type', 'split')->where('is_active', true)->orderBy('sort_order')->take(4)->get();

        return view('pages.home_luxury', [
            'products' => $products,
            'testimonials' => $testimonials,
            'galleries' => $galleries,
            'stats' => $stats,
            'sections' => $sections,
            'newArrivals' => $newArrivals,
            'shopProducts' => $discountProducts, // Updated to pass discountProducts
            'shopProductsPaginated' => $discountProducts, // Updated to pass discountProducts
            'brands' => $brands,
            'selectedBrand' => $request->brand,
            'selectedLevel' => $request->level,
            'vouchers' => $vouchers,
            'userVouchers' => $userVouchers,
            'userWishlistIds' => $userWishlistIds,
            'freeProducts' => $freeProducts,
            'shouldShowFreeProducts' => $shouldShowFreeProducts,
            'freeEventTitle' => $freeEventTitle,
            'freeEventDescription' => $freeEventDescription,
            'heroBanners' => $heroBanners,
            'splitBanners' => $splitBanners,
        ]);
    }

    /**
     * AJAX filter products for home_luxury page
     */
    public function filterProducts(Request $request)
    {
        $query = Product::active()
            ->inStock()
            ->where('is_featured', false)
            ->orderByRaw("CASE WHEN image LIKE '%lh3.googleusercontent.com%' THEN 0 ELSE 1 END ASC");

        // Category filter
        if ($request->filled('category')) {
            $category = strtolower($request->category);
            $typeMap = [
                'serum' => 'serum',
                'moisturizer' => 'moisturizer',
                'cleanser' => 'cleanser',
                'toner' => 'toner',
                'sunscreen' => 'sunscreen',
                'bundle' => 'bundle',
            ];
            $type = $typeMap[$category] ?? $category;
            $query->where(function($q) use ($type, $category) {
                $q->where('type', $type)->orWhere('category', 'like', "%{$category}%");
            });
        }

        // Brand filter
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Advanced filters
        if ($request->filled('shape')) {
            $query->where('shape', $request->shape);
        }
        if ($request->filled('hardness')) {
            $query->where('hardness', $request->hardness);
        }
        if ($request->filled('carbon_type')) {
            $query->where('carbon_type', $request->carbon_type);
        }

        // Price filter
        if ($request->filled('price')) {
            if ($request->price === 'low') {
                $query->orderByDiscountedPrice('asc');
            } elseif ($request->price === 'high') {
                $query->orderByDiscountedPrice('desc');
            }
        }

        // Sort filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'popular':
                    $query->withCount('orderItems')->orderByDesc('order_items_count');
                    break;
                case 'latest':
                    $query->latest();
                    break;
                case 'year_asc':
                    $query->orderByRaw('year IS NULL, year asc');
                    break;
                case 'year_desc':
                    $query->orderByRaw('year IS NULL, year desc');
                    break;
                case 'hijabful_rating':
                    $query->orderByRaw('hijabful_rating IS NULL, hijabful_rating desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(30)->withQueryString();

        // Get first 15 for New Arrivals section, rest for Shop section
        $newArrivals = $products->take(15);
        $shopProductsBottom = $products->slice(15, 15);

        // Add all_images to each product for multi-image support
        $newArrivals->transform(function ($product) {
            $product->all_images = $product->all_images;
            return $product;
        });
        $shopProductsBottom->transform(function ($product) {
            $product->all_images = $product->all_images;
            return $product;
        });

        return response()->json([
            'newArrivals' => $newArrivals,
            'shopProducts' => $shopProductsBottom,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
            ]
        ]);
    }

    /**
     * Show shop page with grouped manual sliders
     */
    public function shop(Request $request)
    {
        $query = Product::active()->inStock()->where('is_featured', false);

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category')) {
            $categoryMap = [
                'new-arrivals' => Product::CATEGORY_NEW_ARRIVALS,
                'hijab' => Product::CATEGORY_HIJAB,
                'shoes' => Product::CATEGORY_ACCESSORIES,
                'accessories' => Product::CATEGORY_ACCESSORIES,
            ];
            $category = $categoryMap[$request->category] ?? null;
            if ($category) {
                $query->where('category', $category);
            }
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // NOTE: kolom 'level' sudah di-drop (migration 2026_07_05_000001). Param ?level= legacy
        // sengaja diabaikan agar URL lama yang masih di-crawl Google tidak 500 dan canonical->base ter-render.

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Unified ordering: price filter takes priority over sort
        if ($request->filled('price') && in_array($request->price, ['low', 'high'])) {
            $query->orderByDiscountedPrice($request->price === 'low' ? 'asc' : 'desc');
        } elseif ($request->filled('sort')) {
            switch ($request->sort) {
                case 'popular':
                    $query->withCount('orderItems')->orderByDesc('order_items_count');
                    break;
                case 'latest':
                case 'newest':
                    $query->latest();
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(24)->onEachSide(2)->withQueryString();
        $brands = Product::active()->whereNotNull('brand')->distinct()->pluck('brand')->sort();

        return view('pages.shop', [
            'products' => $products,
            'brands' => $brands,
            'selectedBrand' => $request->brand,
            'selectedLevel' => $request->level,
            'selectedCategory' => $request->category,
            'selectedMinPrice' => $request->min_price,
            'selectedMaxPrice' => $request->max_price,
        ]);
    }

    /**
     * Build grouped sections for shop showcase
     */
    private function getShopSections(): array
    {
        $baseQuery = Product::active()->inStock()->where('is_featured', false);

        $buildSection = function (string $title, array $keywords = [], ?string $category = null) use ($baseQuery) {
            $query = (clone $baseQuery);

            if ($category) {
                $query->where('category', $category);
            }

            if (!empty($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhere('name', 'like', "%{$word}%")
                            ->orWhere('description', 'like', "%{$word}%");
                    }
                });
            }

            $items = $query->latest()->take(8)->get();

            if ($items->isEmpty()) {
                $items = (clone $baseQuery)->latest()->take(8)->get();
            }

            // Prioritize featured product as the big highlight card
            $featured = Product::active()->inStock()->where('is_featured', true)->latest()->first();

            if (!$featured) {
                // Fallback: latest product as highlight
                $featured = $items->first();
            }

            $others = $items->values();

            return [
                'title' => $title,
                'latest' => $featured,
                'others' => $others,
            ];
        };

        return [
            $buildSection('Hijab Terbaru', [], Product::CATEGORY_HIJAB),
            $buildSection('Shoes Terbaru', ['shoe', 'sepatu', 'nike', 'adidas', 'new balance', 'brooks', 'salomon'], Product::CATEGORY_ACCESSORIES),
            $buildSection('Accessories Terbaru', ['apparel', 'jersey', 'shirt', 'kaos', 'wear', 'outfit'], Product::CATEGORY_ACCESSORIES),
        ];
    }

    /**
     * Get realtime statistics
     */
    private function getStats()
    {
        // Total pelanggan yang sudah selesai order (completed)
        $totalCustomers = Order::where('status', Order::STATUS_COMPLETED)
            ->distinct('user_id')
            ->count('user_id');

        // Total review/testimoni yang diapprove
        $totalReviews = Testimonial::approved()->count();

        // Rata-rata rating - default 5.0 jika belum ada review
        if ($totalReviews > 0) {
            $avgRating = Testimonial::approved()->avg('rating');
            $avgRating = round($avgRating, 1);
        } else {
            $avgRating = 5.0; // Default rating untuk toko baru
        }

        // Persentase kepuasan (order completed vs total order non-cancelled)
        // Default 100% jika belum ada order
        $totalOrders = Order::whereNotIn('status', [Order::STATUS_CANCELLED])->count();
        $completedOrders = Order::where('status', Order::STATUS_COMPLETED)->count();
        $satisfactionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 100;

        return [
            'total_customers' => $totalCustomers,
            'total_reviews' => $totalReviews,
            'avg_rating' => $avgRating,
            'satisfaction_rate' => $satisfactionRate,
        ];
    }

    /**
     * Show about page
     */
    public function tentang()
    {
        return view('pages.about');
    }

    /**
     * Show about page
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Show insight page
     */
    public function insight(\Illuminate\Http\Request $request)
    {
        $insights = \App\Models\Insight::published()->latest()->paginate(24)->onEachSide(1);

        return view('pages.insight', compact('insights'));
    }

    /**
     * Show single insight
     */
    public function insightShow(\App\Models\Insight $insight)
    {
        if (!in_array($insight->status, ['published', 'scheduled']) || $insight->published_at > now()) {
            abort(404);
        }

        $insight->increment('views');

        // Parse content to strip links of unpublished articles
        $insight->content = preg_replace_callback(
            '/<a\s+[^>]*?href=["\'](?:[^"\']*?\/insight\/([^"\'\/?#]+)[^"\']*?)["\'][^>]*>(.*?)<\/a>/is',
            function ($matches) {
                $slug = $matches[1];
                $text = $matches[2];
                static $cache = [];
                if (!array_key_exists($slug, $cache)) {
                    $linkedInsight = \App\Models\Insight::where('slug', $slug)->first();
                    $cache[$slug] = $linkedInsight && in_array($linkedInsight->status, ['published', 'scheduled']) && $linkedInsight->published_at <= now();
                }
                return $cache[$slug] ? $matches[0] : $text;
            },
            $insight->content
        );

        $relatedInsights = \App\Models\Insight::published()
            ->where('id', '!=', $insight->id)
            ->latest()
            ->take(20)
            ->get();

            $popularArticles = \App\Models\Insight::published()
                ->where('id', '!=', $insight->id) // Assuming your current article variable is $insight
                ->orderBy('views', 'desc')   // Make sure this column name matches your database
                ->take(4)
                ->get();

        return view('pages.insight-show', compact('insight', 'relatedInsights', 'popularArticles'));
    }

    /**
     * Show brand catalog page
     */
    public function brandCatalog()
    {
        $catalogs = BrandCatalog::active()->ordered()->get();
        return view('pages.brand-catalog', compact('catalogs'));
    }

    /**
     * Show help center page
     */
    public function helpCenter()
    {
        return view('pages.help-center');
    }

    /**
     * Show privacy policy page
     */
    public function policy()
    {
        return view('pages.policy');
    }

    /**
     * Show return and refund page
     */
    public function returnRefund()
    {
        return view('pages.return-refund');
    }

    /**
     * Show guarantee page
     */
    public function guarantee()
    {
        return view('pages.guarantee');
    }

    /**
     * Show contact page
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Handle contact form submit
     */
    public function submitContact(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:2000'],
            'cf-turnstile-response' => ['required', new \App\Rules\Turnstile],
        ], [
            'cf-turnstile-response.required' => 'Verifikasi Captcha wajib diisi.',
        ]);

        $receiverEmail = (string) config('contact.receiver_email');
        $receiverName = (string) config('contact.receiver_name', config('app.name', 'Hijab Support'));

        if (empty($receiverEmail)) {
            return back()->withInput()->with('error', 'Konfigurasi email tujuan contact belum diatur. Set CONTACT_RECEIVER_EMAIL di file .env.');
        }

        try {
            Mail::to($receiverEmail, $receiverName)->send(new ContactMessageMail($payload));
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim email contact form.', [
                'error' => $exception->getMessage(),
                'receiver_email' => $receiverEmail,
            ]);

            return back()->withInput()->with('error', 'Message failed! Please Try Again!');
        }

        return back()->with('success', 'Message sent successfully!');
    }

    /**
     * Show products by category slug (SEO friendly)
     */
    public function categoryShow($slug, Request $request)
    {
        // Map slug to valid request category format
        $slugMap = [
            'hijab-hijab' => 'hijab',
            'sepatu-hijab' => 'sepatu',
            'aksesoris-hijab' => 'accessories',
        ];

        $categoryParam = $slugMap[strtolower($slug)] ?? strtolower($slug);

        // Merge into request so newArrivals can process it
        $request->merge(['category' => $categoryParam]);

        return $this->newArrivals($request);
    }

    /**
     * Show products by brand slug (SEO friendly)
     */
    public function brandShow($slug, Request $request)
    {
        // Convert slug back to brand name (e.g. bullhijab -> Bullhijab)
        $brandName = ucwords(str_replace('-', ' ', $slug));

        // Merge into request so newArrivals can process it
        $request->merge(['brand' => $brandName]);

        return $this->newArrivals($request);
    }

    /**
     * Show products list for guests
     */
    public function produkIndex(Request $request)
    {
        $query = Product::active()->inStock()->where('is_featured', false);

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Unified ordering: price filter takes priority over sort
        if ($request->filled('price') && in_array($request->price, ['low', 'high'])) {
            $query->orderByDiscountedPrice($request->price === 'low' ? 'asc' : 'desc');
        } elseif ($request->filled('sort')) {
            switch ($request->sort) {
                case 'popular':
                    $query->withCount('orderItems')->orderByDesc('order_items_count');
                    break;
                case 'latest':
                case 'newest':
                    $query->latest();
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->onEachSide(1)->withQueryString();

        return view('pages.produk.index', compact('products'));
    }

    /**
     * Show single product (Legacy fallback for 301 Redirect)
     */
    public function produkShowLegacy(Product $product)
    {
        return redirect($product->detail_url, 301);
    }

    /**
     * Show single product for guests
     */
    public function produkShow($category, Product $product)
    {
        if ($category !== $product->category_slug) {
            return redirect($product->detail_url, 301);
        }

        if (!$product->is_active) {
            abort(404);
        }

        // Get reviews with user data (max 10 untuk tampilan detail)
        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        // Total reviews untuk statistik
        $totalReviews = $product->reviews()->count();
        $avgRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;

        // Rating breakdown
        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $reviews->where('rating', $i)->count();
            $ratingBreakdown[$i] = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        }

        $relatedProducts = Product::active()
            ->inStock()
            ->where('is_featured', false)
            ->where('id', '!=', $product->id)
            ->where('category', $product->category)
            ->take(10)
            ->get();

        $testimonials = Testimonial::approved()
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        $isEligibleForFree = false;
        if (!auth()->check()) {
            $isEligibleForFree = true;
        } else {
            $isEligibleForFree = auth()->user()->role === 'customer' 
                && !auth()->user()->welcome_bonus_claimed 
                && !auth()->user()->orders()->whereNotIn('status', ['pending', 'cancelled', 'failed'])->exists();
        }

        return view('pages.product-detail', compact(
            'product',
            'relatedProducts',
            'testimonials',
            'reviews',
            'totalReviews',
            'avgRating',
            'ratingBreakdown',
            'isEligibleForFree'
        ));
    }

    /**
     * Show gallery page
     */
    public function galeri()
    {
        $galleries = Gallery::active()
            ->ordered()
            ->get();

        return view('pages.galeri', compact('galleries'));
    }

    /**
     * Show testimonials page
     */
    public function testimoni()
    {
        $testimonials = Testimonial::approved()
            ->with('user')
            ->latest()
            ->paginate(12)->onEachSide(1);

        // Statistik testimoni
        $stats = $this->getStats();

        return view('pages.testimoni', compact('testimonials', 'stats'));
    }

    /**
     * Show new arrivals page
     */
    public function newArrivals(Request $request)
    {
        $query = Product::active()
            ->inStock()
            ->where('is_featured', false);

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // NOTE: kolom 'level' sudah di-drop (migration 2026_07_05_000001). Param ?level= legacy
        // sengaja diabaikan agar URL lama yang masih di-crawl Google tidak 500 dan canonical->base ter-render.

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('category')) {
            $reqCat = strtolower($request->category);
            $categoryMap = [
                'hijab'       => 'Hijab',
                'baju'        => 'Baju',
                'tas'         => 'Tas',
                'celana'      => 'Celana',
                'sepatu'      => 'Sepatu',
                'shoes'       => 'Sepatu',
                'apparel'     => 'Baju',
                'accessories' => 'Accessories',
                'new-arrivals'=> 'New Arrivals',
            ];
            $category = $categoryMap[$reqCat] ?? null;
            if ($category) {
                $query->where('category', $category);
            }
        }

        // Advanced filters
        if ($request->filled('shape')) {
            $query->where('shape', $request->shape);
        }
        if ($request->filled('hardness')) {
            $query->where('hardness', $request->hardness);
        }
        if ($request->filled('carbon_type')) {
            $query->where('carbon_type', $request->carbon_type);
        }

        // Unified ordering: price filter takes priority over sort
        if ($request->filled('price') && in_array($request->price, ['low', 'high'])) {
            $query->orderByDiscountedPrice($request->price === 'low' ? 'asc' : 'desc');
        } elseif ($request->filled('sort')) {
            switch ($request->sort) {
                case 'popular':
                    $query->withCount('orderItems')->orderByDesc('order_items_count');
                    break;
                case 'latest':
                case 'newest':
                    $query->latest();
                    break;
                case 'year_asc':
                    $query->orderByRaw('year IS NULL, year asc');
                    break;
                case 'year_desc':
                    $query->orderByRaw('year IS NULL, year desc');
                    break;
                case 'hijabful_rating':
                    $query->orderByRaw('hijabful_rating IS NULL, hijabful_rating desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->onEachSide(2)->withQueryString();
        $brands = Product::active()->whereNotNull('brand')->distinct()->pluck('brand')->sort();

        return view('pages.new-arrivals', [
            'products' => $products,
            'search' => $request->q,
            'brands' => $brands,
            'selectedBrand' => $request->brand,
            'selectedLevel' => $request->level,
            'selectedCategory' => $request->category,
            'selectedMinPrice' => $request->min_price,
            'selectedMaxPrice' => $request->max_price,
            'selectedShape' => $request->shape,
            'selectedHardness' => $request->hardness,
            'selectedCarbonType' => $request->carbon_type,
            'selectedSort' => $request->sort,
            'selectedPrice' => $request->price,
        ]);
    }

    /**
     * Show shop category page
     */
    public function shopCategory(Request $request)
    {
        $query = Product::active()->inStock()->where('is_featured', false);

        if ($request->filled('category')) {
            $reqCat = strtolower($request->category);
            $categoryMap = [
                'hijab'       => 'Hijab',
                'baju'        => 'Baju',
                'tas'         => 'Tas',
                'celana'      => 'Celana',
                'sepatu'      => 'Sepatu',
                'shoes'       => 'Sepatu',
                'apparel'     => 'Baju',
                'accessories' => 'Accessories',
                'new-arrivals'=> 'New Arrivals',
            ];

            $category = $categoryMap[$reqCat] ?? null;
            if ($category) {
                $query->where('category', $category);
            }
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('pages.shop-category', compact('products'));
    }

    /**
     * Filter New Arrivals products via AJAX
     */
    public function filterNewArrivals(Request $request)
    {
        $baseQuery = Product::active()->inStock()->where('is_featured', false)
            ->withSum(['orderItems as sold_count' => function ($q) {
                $q->whereHas('order', function ($q) {
                    $q->whereIn('status', ['completed', 'delivered']);
                });
            }], 'quantity')
            ->withCount(['reviews as total_reviews' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->withAvg(['reviews as average_rating' => function ($q) {
                $q->where('is_approved', true);
            }], 'rating');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $baseQuery->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filter by specific category if requested
        if ($request->filled('category')) {
            $reqCat = strtolower($request->category);
            $categoryMap = [
                'hijab'       => 'Hijab',
                'baju'        => 'Baju',
                'tas'         => 'Tas',
                'celana'      => 'Celana',
                'sepatu'      => 'Sepatu',
                'shoes'       => 'Sepatu',
                'apparel'     => 'Baju',
                'accessories' => 'Accessories',
                'new-arrivals'=> 'New Arrivals',
            ];
            $dbCategory = $categoryMap[$reqCat] ?? null;
            if ($dbCategory) {
                $baseQuery->where('category', $dbCategory);
            }
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $baseQuery->where('brand', $request->brand);
        }

        // Advanced filters
        if ($request->filled('shape')) {
            $baseQuery->where('shape', $request->shape);
        }
        if ($request->filled('hardness')) {
            $baseQuery->where('hardness', $request->hardness);
        }
        if ($request->filled('carbon_type')) {
            $baseQuery->where('carbon_type', $request->carbon_type);
        }

        // Sort
        if ($request->filled('price')) {
            if ($request->price === 'low') {
                $baseQuery->orderByDiscountedPrice('asc');
            } elseif ($request->price === 'high') {
                $baseQuery->orderByDiscountedPrice('desc');
            }
        } elseif ($request->filled('sort')) {
            switch ($request->sort) {
                case 'popular':
                    $baseQuery->withCount('orderItems')->orderByDesc('order_items_count');
                    break;
                case 'year_asc':
                    $baseQuery->orderByRaw('year IS NULL, year asc');
                    break;
                case 'year_desc':
                    $baseQuery->orderByRaw('year IS NULL, year desc');
                    break;
                case 'hijabful_rating':
                    $baseQuery->orderByRaw('hijabful_rating IS NULL, hijabful_rating desc');
                    break;
                default:
                    $baseQuery->latest();
                    break;
            }
        } else {
            $baseQuery->latest();
        }

        $products = $baseQuery->take(48)->get();

        if ($products->isEmpty()) {
            return response()->json(['success' => false, 'html' => '']);
        }

        // Get user's wishlist product IDs
        $userWishlistIds = [];
        if (auth()->check()) {
            $userWishlistIds = \App\Models\Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray();
        } else {
            $userWishlistIds = session()->get('guest_wishlist', []);
        }

        // Group products by category
        $categoryGroups = [
            'hijab'      => ['label' => 'Hijab',      'icon' => 'fa-table-tennis', 'products' => []],
            'shoes'       => ['label' => 'Shoes',        'icon' => 'fa-shoe-prints',  'products' => []],
            'accessories' => ['label' => 'Accessories',  'icon' => 'fa-shopping-bag', 'products' => []],
        ];

        foreach ($products as $product) {
            $cat = $product->category;
            if ($cat === Product::CATEGORY_HIJAB) {
                $type = 'hijab';
            } elseif ($cat === Product::CATEGORY_ACCESSORIES) {
                $type = 'shoes';
            } elseif ($cat === Product::CATEGORY_ACCESSORIES) {
                $type = 'accessories';
            } else {
                $type = 'hijab';
            }

            if (isset($categoryGroups[$type])) {
                $categoryGroups[$type]['products'][] = $product;
            } else {
                $categoryGroups['hijab']['products'][] = $product;
            }
        }

        // Use section headers only when multiple categories have products
        $nonEmpty = array_filter($categoryGroups, fn($g) => count($g['products']) > 0);
        $useGrouping = count($nonEmpty) > 1;

        $html = '';

        foreach ($categoryGroups as $group) {
            if (empty($group['products'])) continue;

            if (false) {
                $count = count($group['products']);
                $html .= '<div class="col-span-full flex items-center gap-3 mt-6 mb-2">';
                $html .= '<span class="flex items-center justify-center w-8 h-8 rounded-full bg-black text-white flex-shrink-0"><i class="fas ' . $group['icon'] . ' text-xs"></i></span>';
                $html .= '<h3 class="text-sm font-bold text-black tracking-wide uppercase">' . $group['label'] . '</h3>';
                $html .= '<div class="flex-1 h-px bg-zinc-200"></div>';
                $html .= '<span class="text-xs text-zinc-400">' . $count . ' produk</span>';
                $html .= '</div>';
            }

            foreach ($group['products'] as $product) {
                $html .= view('components.product-card-luxury', [
                    'product' => $product,
                    'userWishlistIds' => $userWishlistIds ?? []
                ])->render();
            }
        }

        return response()->json([
            'success' => true,
            'html'    => $html,
            'grouped' => $useGrouping,
            'total'   => $products->count(),
        ]);
    }
}
