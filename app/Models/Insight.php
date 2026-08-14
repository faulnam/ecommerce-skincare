<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Insight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'image',
        'alt_image',
        'author',
        'status',
        'published_at',
        'views',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Boot function to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($insight) {
            if (empty($insight->slug)) {
                $insight->slug = Str::slug($insight->title);
            }
            if (empty($insight->published_at) && $insight->status === 'published') {
                $insight->published_at = now();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope for published insights
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->orWhere(function ($q) {
                         $q->where('status', 'scheduled')
                           ->where('published_at', '<=', now());
                     });
    }

    /**
     * Get the author user of the insight.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/logo.png'); // Default fallback
        }

        if (preg_match('/^https?:\/\//i', $this->image) || str_starts_with($this->image, '/')) {
            return $this->image;
        }

        $normalizedPath = ltrim(str_replace('\\', '/', $this->image), '/');

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
            return rtrim($r2Url, '/') . '/' . ltrim($normalizedPath, '/') . '?v=2';
        }

        return asset('storage/' . $normalizedPath);
    }
}
