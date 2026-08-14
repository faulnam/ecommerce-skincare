<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'rating',
        'content',
        'image',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Get user
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get order
     */
    public function order()
    {
        return $this->belongsTo(Order::class)->withDefault();
    }

    /**
     * Scope for approved testimonials
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for pending testimonials
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
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
     * Get star rating HTML
     */
    public function getStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="fas fa-star text-warning"></i>';
            } else {
                $stars .= '<i class="far fa-star text-warning"></i>';
            }
        }
        return $stars;
    }
}
