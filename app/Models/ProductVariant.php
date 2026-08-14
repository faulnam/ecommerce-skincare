<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'image',
        'image_2',
        'image_3',
        'image_4',
        'stock',
        'price',
        'discount_percent',
        'discount_start',
        'discount_end',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
        'image_2_url',
        'image_3_url',
        'image_4_url',
        'has_active_discount',
        'discounted_price',
        'final_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    private function getImagePathUrl($imagePath): string
    {
        if (empty($imagePath) || $imagePath === '0') {
            return '';
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

        $r2Url = config('filesystems.disks.r2.url');

        if ($r2Url) {
            return rtrim($r2Url, '/') . '/' . ltrim($normalizedPath, '/');
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            return asset('storage/' . $normalizedPath);
        }

        return asset('storage/' . $normalizedPath);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->getImagePathUrl($this->image);
    }

    public function getImage2UrlAttribute(): string
    {
        return $this->getImagePathUrl($this->image_2);
    }

    public function getImage3UrlAttribute(): string
    {
        return $this->getImagePathUrl($this->image_3);
    }

    public function getImage4UrlAttribute(): string
    {
        return $this->getImagePathUrl($this->image_4);
    }

    public function getAllImagesAttribute(): array
    {
        $images = [];
        if ($this->image) $images[] = $this->image_url;
        if ($this->image_2) $images[] = $this->image_2_url;
        if ($this->image_3) $images[] = $this->image_3_url;
        if ($this->image_4) $images[] = $this->image_4_url;
        return $images;
    }

    public function hasActiveDiscount(): bool
    {
        if ($this->discount_percent <= 0) return false;
        
        $now = now();
        
        if ($this->discount_start && $now->lt($this->discount_start)) return false;
        if ($this->discount_end && $now->gt($this->discount_end)) return false;
        
        return true;
    }

    public function getHasActiveDiscountAttribute(): bool
    {
        return $this->hasActiveDiscount();
    }

    public function getDiscountedPriceAttribute(): float
    {
        if (!$this->hasActiveDiscount()) return $this->final_price;
        $base = $this->price ?? $this->product->price;
        return $base - ($base * ($this->discount_percent / 100));
    }

    public function getFinalPriceAttribute(): float
    {
        if ($this->hasActiveDiscount()) {
            return $this->discounted_price;
        }

        if ($this->price) {
            return $this->price;
        }

        return $this->product->hasActiveDiscount()
            ? $this->product->discounted_price
            : $this->product->price;
    }

    public function getFormattedFinalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }
}
