<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'voucher_id',
        'claimed_at',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function markAsClaimed(): void
    {
        $this->claimed_at = now();
        $this->save();
        
        // Increment voucher used count
        $this->voucher->increment('used');
    }

    public function markAsUsed(): void
    {
        $this->is_used = true;
        $this->used_at = now();
        $this->save();
    }

    public function scopeClaimed($query)
    {
        return $query->whereNotNull('claimed_at');
    }

    public function scopeNotClaimed($query)
    {
        return $query->whereNull('claimed_at');
    }

    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    public function scopeNotUsed($query)
    {
        return $query->where('is_used', false);
    }

    public function scopeAvailableForUser($query, int $userId)
    {
        return $query->where('user_id', $userId)
            ->whereNotNull('claimed_at')
            ->where('is_used', false)
            ->whereHas('voucher', function ($q) {
                $q->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            });
    }
}
