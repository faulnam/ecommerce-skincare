<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'points',
        'type',
        'description',
        'balance_before',
        'balance_after',
        'expires_at',
        'consumed',
    ];

    protected $casts = [
        'points' => 'integer',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'consumed' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    public function scopeWelcomeBonus($query)
    {
        return $query->where('type', 'welcome_bonus');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function scopeEarnOrBonus($query)
    {
        return $query->whereIn('type', ['earned', 'welcome_bonus']);
    }

    public function getAvailablePointsAttribute(): int
    {
        return max(0, $this->points - $this->consumed);
    }
}
