<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'code',
        'description',
        'type',
        'category',
        'discount_value',
        'minimum_purchase',
        'maximum_discount',
        'cashback_coin',
        'quota',
        'used',
        'start_date',
        'end_date',
        'thumbnail',
        'is_active',
        'is_displayed',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'cashback_coin' => 'integer',
        'quota' => 'integer',
        'used' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'remaining_quota',
        'quota_percentage',
        'is_expired',
        'is_not_started',
        'is_quota_finished',
        'status_badge',
        'type_label',
        'formatted_discount',
        'category_label',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function userVouchers(): HasMany
    {
        return $this->hasMany(UserVoucher::class);
    }

    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->quota - $this->used);
    }

    public function getQuotaPercentageAttribute(): int
    {
        if ($this->quota == 0) return 0;
        return min(100, (int) (($this->used / $this->quota) * 100));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    public function getIsNotStartedAttribute(): bool
    {
        return $this->start_date->isFuture();
    }

    public function getIsQuotaFinishedAttribute(): bool
    {
        return $this->used >= $this->quota;
    }

    public function getStatusBadgeAttribute(): string
    {
        if (!$this->is_active) {
            return '<span class="badge bg-secondary">Inactive</span>';
        }
        
        if ($this->is_expired) {
            return '<span class="badge bg-danger">Expired</span>';
        }
        
        if ($this->is_not_started) {
            return '<span class="badge bg-info">Segera Hadir</span>';
        }
        
        if ($this->is_quota_finished) {
            return '<span class="badge bg-warning">Habis</span>';
        }
        
        return '<span class="badge bg-success">Aktif</span>';
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'fixed' => 'Nominal Diskon',
            'percent' => 'Persentase Diskon',
            'cashback' => 'Cashback Coin',
            default => 'Unknown',
        };
    }

    public function getFormattedDiscountAttribute(): string
    {
        return match($this->type) {
            'fixed' => 'Rp ' . number_format($this->discount_value, 0, ',', '.'),
            'percent' => $this->discount_value . '%',
            'cashback' => $this->cashback_coin . ' Coin',
            default => '-',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'all' => 'Semua Kategori',
            'skincare' => 'LUMINA',
            'accessories' => 'Accessories',
            'shoes' => 'Shoes',
            'new_arrivals' => 'New Arrivals',
            default => ucfirst($this->category),
        };
    }

    public function isActive(): bool
    {
        return $this->is_active 
            && !$this->is_expired 
            && !$this->is_not_started 
            && !$this->is_quota_finished;
    }

    public function isClaimedByUser(int $userId): bool
    {
        return $this->userVouchers()->where('user_id', $userId)->exists();
    }

    public function isUsedByUser(int $userId): bool
    {
        return $this->userVouchers()
            ->where('user_id', $userId)
            ->where('is_used', true)
            ->exists();
    }

    public function calculateDiscount(float $cartTotal): float
    {
        if ($cartTotal < $this->minimum_purchase) {
            return 0;
        }

        $discount = match($this->type) {
            'fixed' => $this->discount_value,
            'percent' => ($this->discount_value / 100) * $cartTotal,
            'cashback' => 0,
            default => 0,
        };

        if ($this->type === 'percent' && $this->maximum_discount) {
            $discount = min($discount, $this->maximum_discount);
        }

        return min($discount, $cartTotal);
    }

    public static function generateCode(): string
    {
        return 'VOU-' . strtoupper(Str::random(8));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($voucher) {
            if (empty($voucher->slug)) {
                $voucher->slug = Str::slug($voucher->title);
            }
            if (empty($voucher->code)) {
                $voucher->code = self::generateCode();
            }
        });

        static::updating(function ($voucher) {
            if ($voucher->isDirty('title') && empty($voucher->slug)) {
                $voucher->slug = Str::slug($voucher->title);
            }
        });
    }
}
