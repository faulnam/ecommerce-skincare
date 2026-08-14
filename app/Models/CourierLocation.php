<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'heading',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'float',
        'speed' => 'float',
        'heading' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Get the courier (user)
     */
    public function courier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope for active locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific order
     */
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Get latest location for a courier
     */
    public static function getLatestForCourier($courierId)
    {
        return static::where('user_id', $courierId)
            ->active()
            ->latest()
            ->first();
    }

    /**
     * Get latest location for an order
     */
    public static function getLatestForOrder($orderId)
    {
        return static::where('order_id', $orderId)
            ->active()
            ->latest()
            ->first();
    }
}
