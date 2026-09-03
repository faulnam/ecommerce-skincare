<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BrandCatalog extends Model
{
    protected $fillable = [
        'brand_name',
        'slug',
        'description',
        'pdf_path',
        'pdf_files',
        'cover_image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'pdf_files' => 'array',
    ];

    public static array $categories = [
        'skincares' => 'LUMINAs',
        'shoes' => 'Shoes',
        'accessories' => 'Accessories',
        'bags' => 'Bags',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->brand_name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('brand_name', 'asc');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) return null;
        if (preg_match('/^https?:\/\//i', $this->cover_image)) return $this->cover_image;
        $normalizedPath = ltrim(str_replace('\\', '/', $this->cover_image), '/');
        $r2Url = config('filesystems.disks.r2.url');
        if ($r2Url) return rtrim($r2Url, '/') . '/' . ltrim($normalizedPath, '/') . '?v=2';
        return asset('storage/' . $normalizedPath);
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }

    public function getCategoryPdfUrl(string $category): ?string
    {
        $files = $this->pdf_files ?? [];
        $path = $files[$category] ?? null;
        return $path ? asset($path) : null;
    }

    public function hasCategoryPdf(string $category): bool
    {
        $files = $this->pdf_files ?? [];
        return !empty($files[$category]);
    }
}
