<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'image',
        'embed_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Type constants
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';

    /**
     * Get types
     */
    public static function types(): array
    {
        return [
            self::TYPE_IMAGE => 'Gambar',
            self::TYPE_VIDEO => 'Video (Instagram)',
        ];
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->type] ?? $this->type;
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (preg_match('/^https?:\/\//i', $this->image)) return $this->image;
        $normalizedPath = ltrim(str_replace('\\', '/', $this->image), '/');
        $r2Url = config('filesystems.disks.r2.url');
        if ($r2Url) return rtrim($r2Url, '/') . '/' . ltrim($normalizedPath, '/');
        return asset('storage/' . $normalizedPath);
    }

    /**
     * Scope for active galleries
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Check if is image
     */
    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }

    /**
     * Check if is video
     */
    public function isVideo(): bool
    {
        return $this->type === self::TYPE_VIDEO;
    }
}
