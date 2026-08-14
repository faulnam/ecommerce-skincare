<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'discount_percent',
        'max_discount',
        'min_subtotal',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_subtotal' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Scope for active discounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Check if discount is currently active
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;
        
        $now = now();
        
        if ($this->start_date && $now->lt($this->start_date)) return false;
        if ($this->end_date && $now->gt($this->end_date)) return false;
        
        return true;
    }

    /**
     * Calculate discount for shipping cost
     */
    public function calculateDiscount(float $shippingCost, float $subtotal): float
    {
        $shippingCost = max(0, $shippingCost);
        $subtotal = max(0, $subtotal);

        // Check if meets minimum subtotal requirement
        if ($this->min_subtotal && $subtotal < $this->min_subtotal) {
            return 0;
        }

        $discount = $shippingCost * (max(0, (float) $this->discount_percent) / 100);

        // Apply max discount cap
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        // Diskon ongkir tidak boleh melebihi ongkir asli.
        return max(0, min($shippingCost, (float) $discount));
    }

    /**
     * Get formatted discount percent
     */
    public function getFormattedDiscountAttribute(): string
    {
        return number_format($this->discount_percent, 0) . '%';
    }

    /**
     * Get formatted max discount
     */
    public function getFormattedMaxDiscountAttribute(): ?string
    {
        if ($this->max_discount) {
            return 'Rp ' . number_format($this->max_discount, 0, ',', '.');
        }
        return null;
    }

    /**
     * Get formatted min subtotal
     */
    public function getFormattedMinSubtotalAttribute(): ?string
    {
        if ($this->min_subtotal) {
            return 'Rp ' . number_format($this->min_subtotal, 0, ',', '.');
        }
        return null;
    }
}
