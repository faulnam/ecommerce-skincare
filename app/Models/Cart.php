<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    /**
     * Get user
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get unit price (variant-aware)
     */
    public function getUnitPriceAttribute(): float
    {
        $isEligibleForFree = false;
        if (!auth()->check()) {
            $isEligibleForFree = true;
        } else {
            $user = $this->user ?? auth()->user();
            $isEligibleForFree = $user && $user->role === 'customer' 
                && !$user->welcome_bonus_claimed 
                && !$user->orders()->whereNotIn('status', ['pending', 'cancelled', 'failed'])->exists();
        }

        if ($this->product && $this->product->is_free_event && $isEligibleForFree) {
            return 0;
        }

        if ($this->product_variant_id && $this->variant) {
            return $this->variant->final_price;
        }
        return $this->product->hasActiveDiscount()
            ? $this->product->discounted_price
            : $this->product->price;
    }

    /**
     * Get subtotal (uses discounted price if available)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Get subtotal without discount (original price)
     */
    public function getOriginalSubtotalAttribute(): float
    {
        $basePrice = ($this->product_variant_id && $this->variant && $this->variant->price)
            ? $this->variant->price
            : $this->product->price;
            
        return $basePrice * $this->quantity;
    }

    /**
     * Get discount amount for this cart item
     */
    public function getDiscountAmountAttribute(): float
    {
        $original = $this->original_subtotal;
        $final = $this->subtotal;
        
        return max(0, $original - $final);
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get formatted original subtotal
     */
    public function getFormattedOriginalSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->original_subtotal, 0, ',', '.');
    }

    /**
     * Get formatted discount amount
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
    }
}
