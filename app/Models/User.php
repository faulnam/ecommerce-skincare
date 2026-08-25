<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'is_active',
        'is_guest',
        'avatar',
        'points',
        'first_purchase_completed',
        'welcome_bonus_claimed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_guest' => 'boolean',
            'first_purchase_completed' => 'boolean',
            'welcome_bonus_claimed' => 'boolean',
            'points' => 'integer',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is developer
     */
    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user is courier
     */
    public function isCourier(): bool
    {
        return $this->role === 'courier';
    }

    /**
     * Check if user is blogger
     */
    public function isBlogger(): bool
    {
        return $this->role === 'blogger';
    }

    /**
     * Check if user is a demo account
     */
    public function isDemo(): bool
    {
        return str_starts_with($this->email, 'demo') || 
               str_contains($this->email, 'demo') || 
               str_contains(strtolower($this->name), 'demo');
    }

    /**
     * Get orders for the user (customer)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get insights authored by the user
     */
    public function insights()
    {
        return $this->hasMany(Insight::class);
    }

    /**
     * Get assigned deliveries for the courier
     */
    public function assignedDeliveries()
    {
        return $this->hasMany(Order::class, 'courier_id');
    }

    /**
     * Get active deliveries for the courier
     */
    public function activeDeliveries()
    {
        return $this->hasMany(Order::class, 'courier_id')
            ->whereIn('status', ['assigned', 'picked_up', 'on_delivery']);
    }

    /**
     * Get completed deliveries for the courier
     */
    public function completedDeliveries()
    {
        return $this->hasMany(Order::class, 'courier_id')
            ->whereIn('status', ['delivered', 'completed']);
    }

    /**
     * Get testimonials for the user
     */
    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * Get cart items for the user
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get cart with products
     */
    public function cart()
    {
        return $this->hasMany(Cart::class)->with('product');
    }

    /**
     * Get wishlist items for the user
     */
    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get wishlist with products
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class)->with('product');
    }

    /**
     * Get user vouchers
     */
    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }

    /**
     * Get vouchers through user_vouchers
     */
    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_vouchers')
            ->withPivot('claimed_at', 'is_used', 'used_at')
            ->withTimestamps();
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Return default avatar with initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=000000&color=ffffff&size=200';
    }

    /**
     * Get point transactions for the user
     */
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * Get available points (only non-expired earn/bonus transactions)
     */
    public function getAvailablePointsAttribute(): int
    {
        return $this->pointTransactions()
            ->earnOrBonus()
            ->notExpired()
            ->get()
            ->sum(fn ($t) => $t->available_points);
    }

    /**
     * Get the nearest expiry date of valid earn/bonus point transactions
     */
    public function getNextPointsExpiryAttribute(): ?\Carbon\Carbon
    {
        return $this->pointTransactions()
            ->earnOrBonus()
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->whereRaw('(points - consumed) > 0')
            ->orderBy('expires_at', 'asc')
            ->value('expires_at');
    }

    /**
     * Sync points column from valid transactions
     */
    public function syncPoints(): void
    {
        $this->points = $this->available_points;
        $this->saveQuietly();
    }

    /**
     * Add points to user balance
     */
    public function addPoints(int $points, string $type = 'earned', ?string $description = null, ?int $orderId = null): void
    {
        $balanceBefore = $this->points;
        $this->points += $points;
        $this->save();

        $payload = [
            'user_id' => $this->id,
            'order_id' => $orderId,
            'points' => $points,
            'type' => $type,
            'description' => $description,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->points,
        ];

        // Earned and welcome_bonus points expire in 6 months
        if (in_array($type, ['earned', 'welcome_bonus'])) {
            $payload['expires_at'] = now()->addMonths(6);
        }

        PointTransaction::create($payload);

        // Refresh expiry of all existing valid earn/bonus transactions
        $this->refreshEarnExpiry();
    }

    /**
     * Redeem points from user balance (FIFO consumption + refresh expiry)
     */
    public function redeemPoints(int $points, string $description = null, ?int $orderId = null): bool
    {
        if ($this->points < $points) {
            return false;
        }

        $balanceBefore = $this->points;
        $this->points -= $points;
        $this->save();

        // FIFO: consume from oldest non-expired earn/bonus transactions first
        $remaining = $points;
        $this->pointTransactions()
            ->earnOrBonus()
            ->notExpired()
            ->orderBy('created_at', 'asc')
            ->chunkById(100, function ($transactions) use (&$remaining) {
                foreach ($transactions as $transaction) {
                    if ($remaining <= 0) break;
                    $available = $transaction->available_points;
                    if ($available <= 0) continue;

                    $toConsume = min($available, $remaining);
                    $transaction->consumed += $toConsume;
                    $transaction->save();
                    $remaining -= $toConsume;
                }
            });

        PointTransaction::create([
            'user_id' => $this->id,
            'order_id' => $orderId,
            'points' => -$points,
            'type' => 'redeemed',
            'description' => $description,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->points,
        ]);

        // Refresh expiry of remaining valid earn/bonus transactions
        $this->refreshEarnExpiry();

        return true;
    }

    /**
     * Refresh expiry of all valid earn/bonus transactions to 6 months from now
     */
    public function refreshEarnExpiry(): void
    {
        $this->pointTransactions()
            ->earnOrBonus()
            ->notExpired()
            ->whereRaw('(points - consumed) > 0')
            ->update(['expires_at' => now()->addMonths(6)]);
    }

    /**
     * Calculate available points value in IDR (100 points = Rp10,000)
     */
    public function getPointsValueAttribute(): float
    {
        return ($this->available_points / 100) * 10000;
    }

    /**
     * Format available points value to IDR currency
     */
    public function getFormattedPointsValueAttribute(): string
    {
        return 'Rp ' . number_format($this->points_value, 0, ',', '.');
    }
}
