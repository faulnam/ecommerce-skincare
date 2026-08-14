<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFilter extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the products associated with this filter.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_filter_product');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
