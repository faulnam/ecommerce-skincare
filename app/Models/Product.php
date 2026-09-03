<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'discount_percent',
        'discount_start',
        'discount_end',
        'stock',
        'weight',
        'image',
        'image_2',
        'image_3',
        'image_4',
        'category',
        'type',
        'brand',
        'level',
        'package_type',
        'is_active',
        'is_featured',
        'series',
        'shape',
        'hardness',
        'carbon_type',
        'balance',
        'hijab_weight',
        'play_style',
        'player_type',
        'core',
        'faces',
        'frame',
        'surface',
        'feel',
        'power',
        'control',
        'maneuverability',
        'comfort',
        'technology',
        'benefits',
        'suitable_for',
        'collection',
        'year',
        'hijabful_rating',
        'bundle_type',
        'has_variants',
        'package_weight',
        'is_free_event',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'weight' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_free_event' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function getCategorySlugAttribute()
    {
        return Str::slug($this->category ?: 'umum');
    }

    public function getDetailUrlAttribute()
    {
        return route('produk.show', [
            'category' => $this->category_slug,
            'product' => $this->slug ?? $this->id
        ]);
    }

    // Categories
    const CATEGORY_SERUM = 'Serum';
    const CATEGORY_MOISTURIZER = 'Moisturizer';
    const CATEGORY_CLEANSER = 'Cleanser';
    const CATEGORY_TONER = 'Toner';
    const CATEGORY_TREATMENT = 'Treatment';
    const CATEGORY_SUNSCREEN = 'Sunscreen';
    const CATEGORY_BUNDLE = 'Paket Bundel';
    // Legacy aliases
    const CATEGORY_HIJAB = 'Serum';
    const CATEGORY_ACCESSORIES = 'Sunscreen';
    const CATEGORY_NEW_ARRIVALS = 'Serum';

    // Levels / Skin Types
    const LEVEL_BEGINNER = 'beginner';
    const LEVEL_INTERMEDIATE = 'intermediate';
    const LEVEL_PRO = 'pro';

    public static function levels(): array
    {
        return [
            self::LEVEL_BEGINNER => 'All Skin Types',
            self::LEVEL_INTERMEDIATE => 'Sensitive & Dry',
            self::LEVEL_PRO => 'Oily & Acne Prone',
        ];
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_SERUM => 'Serum & Treatment',
            self::CATEGORY_MOISTURIZER => 'Moisturizer',
            self::CATEGORY_CLEANSER => 'Cleanser',
            self::CATEGORY_TONER => 'Toner & Essence',
            self::CATEGORY_SUNSCREEN => 'Sunscreen',
            self::CATEGORY_BUNDLE => 'Paket Bundel',
        ];
    }

    // Package Types
    const PACKAGE_SINGLE = 'single';
    const PACKAGE_BUNDLE = 'bundle';

    /**
     * Check if product is best seller (sold more than 3 times)
     */
    public function isBestSeller(): bool
    {
        if (array_key_exists('sold_count', $this->attributes)) {
            return ($this->attributes['sold_count'] ?? 0) > 3;
        }

        $soldCount = \App\Models\OrderItem::where('product_id', $this->id)
            ->whereHas('order', function($q) {
                $q->whereIn('status', ['completed', 'delivered']);
            })->sum('quantity');
        
        return $soldCount > 3;
    }

    // Weight options in grams
    public static function weightOptions(): array
    {
        return [
            300 => '300 gram',
            500 => '500 gram',
            800 => '800 gram',
            1000 => '1 kg',
            1500 => '1.5 kg',
        ];
    }

    /**
     * Boot function to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        // Removed auto-slug update on updating - handled in controller
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->has_variants && $this->variants->count() > 0) {
            $minPrice = $this->variants->min(function($v) {
                return $v->price ?? $this->price;
            });
            return 'Rp ' . number_format($minPrice, 0, ',', '.');
        }
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get actual weight (prioritize package_weight)
     */
    public function getWeightAttribute($value)
    {
        return $this->package_weight ?: $value;
    }

    /**
     * Get formatted weight
     */
    public function getFormattedWeightAttribute(): string
    {
        $w = $this->weight;
        if (!$w) return '-';
        if ($w >= 1000) {
            return ($w / 1000) . ' kg';
        }
        return $w . ' gram';
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        $cat = $this->category;
        if (strtolower($cat) === 'pedas') {
            return 'Accessories';
        }
        return self::categories()[$cat] ?? $cat;
    }

    /**
     * Get package type label
     */
    public function getPackageTypeLabelAttribute(): string
    {
        $labels = [
            'single' => 'Produk Satuan',
            'bundle' => 'Paket Hemat',
        ];
        return $labels[$this->package_type] ?? 'Produk Satuan';
    }

    /**
     * Check if product is in stock
     */
    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Reduce stock
     */
    public function reduceStock(int $quantity): bool
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Restore stock
     */
    public function restoreStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    /**
     * Get product variants
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get active product variants
     */
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    /**
     * Get order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get cart items
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get testimonials for this product
     */
    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * Get filters for this product
     */
    public function filters()
    {
        return $this->belongsToMany(ProductFilter::class, 'product_filter_product');
    }

    /**
     * Get reviews for this product
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get average rating for this product
     */
    public function getAverageRatingAttribute()
    {
        if (array_key_exists('average_rating', $this->attributes)) {
            return $this->attributes['average_rating'] ?? 0;
        }
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    /**
     * Get formatted average rating
     */
    public function getFormattedRatingAttribute()
    {
        return number_format($this->average_rating, 1);
    }

    /**
     * Get total reviews count
     */
    public function getTotalReviewsAttribute()
    {
        if (array_key_exists('total_reviews', $this->attributes)) {
            return $this->attributes['total_reviews'] ?? 0;
        }
        return $this->reviews()->where('is_approved', true)->count();
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        $query->where('is_active', true);

        $isEventActive = \App\Models\Setting::where('key', 'free_event_active')->value('value') !== '0';
        if (!$isEventActive) {
            $query->where('is_free_event', false);
        }

        return $query;
    }

    /**
     * Scope for in-stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to order by discounted price
     */
    public function scopeOrderByDiscountedPrice($query, $direction = 'asc')
    {
        $now = now()->toDateTimeString();
        $discountExp = "
            CASE 
                WHEN discount_percent > 0 
                AND (discount_start IS NULL OR discount_start <= '{$now}') 
                AND (discount_end IS NULL OR discount_end >= '{$now}') 
                THEN price - (price * (discount_percent / 100)) 
                ELSE price 
            END
        ";
        
        return $query->orderByRaw("$discountExp $direction");
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image) && $this->has_variants && $this->variants->count() > 0) {
            $firstVariantWithImage = $this->variants->where('image', '!=', null)->where('image', '!=', '')->first();
            if ($firstVariantWithImage) {
                return $firstVariantWithImage->image_url;
            }
        }
        return $this->getImagePathUrl($this->image);
    }

    /**
     * Get image 2 URL
     */
    public function getImage2UrlAttribute(): string
    {
        return $this->getImagePathUrl($this->image_2);
    }

    /**
     * Get image 3 URL
     */
    public function getImage3UrlAttribute(): string
    {
        return $this->getImagePathUrl($this->image_3);
    }

    /**
     * Get image 4 URL
     */
    public function getImage4UrlAttribute(): string
    {
        return $this->getImagePathUrl($this->image_4);
    }

    /**
     * Get all images as array
     */
    public function getAllImagesAttribute(): array
    {
        $images = [];
        if ($this->image) $images[] = $this->image_url;
        if ($this->image_2) $images[] = $this->image_2_url;
        if ($this->image_3) $images[] = $this->image_3_url;
        if ($this->image_4) $images[] = $this->image_4_url;
        return $images;
    }

    /**
     * Helper method to get image URL from path
     */
    private function getImagePathUrl($imagePath): string
    {
        if (empty($imagePath) || $imagePath === '0') {
            return '/images/logo.png';
        }

        $rawPath = trim((string) $imagePath);
        $normalizedPath = ltrim(str_replace('\\', '/', $rawPath), '/');

        if ($normalizedPath !== '' && preg_match('/^https?:\/\//i', $normalizedPath)) {
            return $normalizedPath;
        }

        $knownPrefixes = [
            'storage/app/public/',
            'public/storage/',
            'storage/',
        ];

        foreach ($knownPrefixes as $prefix) {
            if (str_contains($normalizedPath, $prefix)) {
                $parts = explode($prefix, $normalizedPath, 2);
                $normalizedPath = ltrim($parts[1] ?? '', '/');
                break;
            }
        }

        if (!str_starts_with($normalizedPath, 'products/') && str_contains($normalizedPath, '/products/')) {
            $parts = explode('/products/', $normalizedPath, 2);
            $normalizedPath = 'products/' . ltrim($parts[1] ?? '', '/');
        }

        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        $candidatePaths = array_values(array_unique(array_filter([
            $normalizedPath,
            $normalizedPath !== '' && !str_starts_with($normalizedPath, 'products/') ? 'products/' . ltrim($normalizedPath, '/') : null,
        ])));

        foreach ($candidatePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                $cleanPath = str_starts_with($path, 'products/') ? substr($path, 9) : $path;
                $encodedPath = implode('/', array_map('rawurlencode', explode('/', $cleanPath)));

                return '/media/products/' . $encodedPath;
            }
        }

        return '/images/logo.png';
    }

    /**
     * Check if product has active discount
     */
    public function hasActiveDiscount(): bool
    {
        if ($this->has_variants && $this->variants->count() > 0) {
            return $this->variants->contains(function($v) {
                return $v->hasActiveDiscount();
            });
        }

        if ($this->discount_percent <= 0) return false;
        
        $now = now();
        
        if ($this->discount_start && $now->lt($this->discount_start)) return false;
        if ($this->discount_end && $now->gt($this->discount_end)) return false;
        
        return true;
    }

    /**
     * Get discounted price
     */
    public function getDiscountedPriceAttribute(): float
    {
        if (!$this->hasActiveDiscount()) return $this->price;
        return $this->price - ($this->price * ($this->discount_percent / 100));
    }

    /**
     * Get formatted discounted price
     */
    public function getFormattedDiscountedPriceAttribute(): string
    {
        if ($this->has_variants && $this->variants->count() > 0) {
            $minPrice = $this->variants->min(function($v) {
                return $v->final_price;
            });
            return 'Rp ' . number_format($minPrice, 0, ',', '.');
        }
        return 'Rp ' . number_format($this->discounted_price, 0, ',', '.');
    }

    /**
     * Get discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        if (!$this->hasActiveDiscount()) {
            return 0;
        }
        
        return $this->price * ($this->discount_percent / 100);
    }

    /**
     * Get formatted discount amount
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
    }

    /**
     * Get formatted savings amount (alias for discount amount)
     */
    public function getFormattedSavingsAmountAttribute(): string
    {
        return $this->formatted_discount_amount;
    }

    /**
     * Get formatted discount percent
     */
    public function getFormattedDiscountPercentAttribute(): string
    {
        if ($this->has_variants && $this->variants->count() > 0) {
            $maxDiscount = $this->variants->max(function($v) {
                return $v->hasActiveDiscount() ? $v->discount_percent : 0;
            });
            if ($maxDiscount > 0) {
                return number_format($maxDiscount, 0) . '%';
            }
        }
        return number_format($this->discount_percent, 0) . '%';
    }

    /**
     * Generate responsive image attributes (srcset and sizes) for R2 images
     */
    public static function generateResponsiveSrcset(?string $url): string
    {
        if (empty($url)) return '';
        
        // Only apply to R2 cloudflare URLs
        if (!str_contains($url, 'r2.cloudflarestorage.com') && !str_contains($url, config('filesystems.disks.r2.url', 'r2.cloudflarestorage.com'))) {
            return '';
        }

        // Remove query parameters like ?v=2
        $base = explode('?', $url)[0];
        $info = pathinfo($base);
        
        // If it's already a responsive variant or not supported, skip
        if (preg_match('/-\d+w$/', $info['filename']) || !in_array(strtolower($info['extension'] ?? ''), ['jpg', 'jpeg', 'png', 'webp'])) {
            return '';
        }

        $dir = $info['dirname'];
        $name = $info['filename'];
        
        $srcset = "
            {$dir}/{$name}-300w.webp 300w,
            {$dir}/{$name}-600w.webp 600w,
            {$dir}/{$name}-900w.webp 900w,
            {$dir}/{$name}-1200w.webp 1200w
        ";
        
        $sizes = "(max-width: 600px) 100vw, (max-width: 900px) 50vw, 33vw";
        
        return 'srcset="' . trim(preg_replace('/\s+/', ' ', $srcset)) . '" sizes="' . $sizes . '"';
    }
}
