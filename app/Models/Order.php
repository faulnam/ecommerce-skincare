<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Order extends Model
{
    use HasFactory;

    protected static ?bool $hasBiteshipOrderIdColumn = null;

    protected $fillable = [
        'order_number',
        'user_id',
        'courier_id',
        'courier_code',
        'courier_name',
        'courier_service_name',
        'estimated_delivery_date',
        'biteship_order_id',
        'biteship_draft_order_id',
        'awb_number',
    'biteship_tracking_status',
    'biteship_status_stage',
        'waybill_id',
        'label_url',
        'assigned_at',
        'subtotal',
        'product_discount',
        'shipping_discount',
        'shipping_cost',
    'ongkir_asli',
    'diskon_ongkir',
    'ongkir_dibayar',
        'total',
    'total_pembayaran',
        'status',
        'payment_status',
    'payment_gateway',
    'payment_channel',
    'paylabs_transaction_id',
    'payment_data',
        'payment_method',
        'payment_proof',
        'payment_verified_at',
        'paid_at',
        'cod_verified',
        'cod_verified_at',
        'shipping_address',
        'shipping_phone',
        'shipping_name',
        'shipping_postal_code',
        'shipping_latitude',
        'shipping_longitude',
        'delivery_distance_minutes',
        'delivery_distance_km',
        'delivery_date',
        'delivery_time_slot',
        'picked_up_at',
        'on_delivery_at',
        'delivered_at',
        'completed_at',
        'delivery_notes',
        'pickup_photo',
        'delivery_photo',
        'notes',
        'cancel_reason',
        'refund_at',
        'refund_amount',
        'refund_status',
        'refund_transaction_id',
        'refund_note',
        'courier_driver_name',
        'courier_driver_phone',
        'courier_driver_photo',
        'courier_driver_rating',
        'courier_driver_vehicle',
        'courier_driver_vehicle_number',
        'pickup_time',
        'voucher_id',
        'voucher_discount',
        'points_used',
        'points_discount',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'product_discount' => 'decimal:2',
        'shipping_discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    'ongkir_asli' => 'decimal:2',
    'diskon_ongkir' => 'decimal:2',
    'ongkir_dibayar' => 'decimal:2',
        'total' => 'decimal:2',
    'total_pembayaran' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'shipping_latitude' => 'decimal:8',
        'shipping_longitude' => 'decimal:8',
        'delivery_distance_minutes' => 'integer',
        'delivery_distance_km' => 'decimal:2',
        'delivery_date' => 'date',
        'payment_verified_at' => 'datetime',
        'paid_at' => 'datetime',
        'cod_verified' => 'boolean',
        'cod_verified_at' => 'datetime',
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'on_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'refund_at' => 'datetime',
        'pickup_time' => 'datetime',
        'courier_driver_rating' => 'decimal:2',
    ];

    // Waktu tunggu pembatalan (dalam menit) - customer punya 5 menit untuk membatalkan setelah order
    const CANCEL_WAIT_MINUTES = 5;

    // Refund status
    const REFUND_PENDING   = 'pending';    // Menunggu approval admin
    const REFUND_APPROVED  = 'approved';   // Disetujui admin, sedang diproses
    const REFUND_PROCESSING = 'processing'; // Sedang diproses ke payment gateway
    const REFUND_COMPLETED = 'completed';  // Dana sudah dikembalikan
    const REFUND_FAILED    = 'failed';     // Gagal
    const REFUND_REJECTED  = 'rejected';   // Ditolak admin

    // Jam operasional pengiriman
    const DELIVERY_START_HOUR = 10; // 10:00
    const DELIVERY_END_HOUR = 16;   // 16:00

    // Status pesanan
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PROCESSING = 'processing';
    const STATUS_READY_TO_SHIP = 'ready_to_ship';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Legacy status (untuk backward compatibility)
    const STATUS_PAID = 'paid';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_PICKED_UP = 'picked_up';
    const STATUS_ON_DELIVERY = 'on_delivery';

    // Stage status Biteship untuk tampilan customer
    const BITESHIP_STAGE_PROCESSING = 'sedang_diproses';
    const BITESHIP_STAGE_PICKUP = 'penjemputan';
    const BITESHIP_STAGE_DELIVERY = 'pengantaran';
    const BITESHIP_STAGE_RETURN = 'pengembalian';
    const BITESHIP_STAGE_ON_HOLD = 'ditahan';
    const BITESHIP_STAGE_FINISHED = 'selesai';

    // Status pembayaran
    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PENDING = 'pending_verification';
    const PAYMENT_PAID = 'paid';

    /**
     * Boot function to auto-generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                // Format: NP-YYYYMMDD-XXXXX
                // NP = LUMINA
                // YYYYMMDD = Tanggal
                // XXXXX = 5 digit random
                $order->order_number = 'NP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            }
        });
    }

    /**
     * Get user
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get courier
     */
    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id')->withTrashed();
    }

    /**
     * Get order items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get testimonial
     */
    public function testimonial()
    {
        return $this->hasOne(Testimonial::class);
    }

    /**
     * Cek apakah kolom biteship_order_id tersedia di tabel orders.
     */
    public static function supportsBiteshipOrderId(): bool
    {
        if (static::$hasBiteshipOrderIdColumn !== null) {
            return static::$hasBiteshipOrderIdColumn;
        }

        try {
            static::$hasBiteshipOrderIdColumn = Schema::hasColumn('orders', 'biteship_order_id');
        } catch (\Throwable $e) {
            static::$hasBiteshipOrderIdColumn = false;
        }

        return static::$hasBiteshipOrderIdColumn;
    }

    /**
     * Scope order manual/non-Biteship untuk dashboard/admin.
     */
    public function scopeWithoutBiteshipOrder(Builder $query): Builder
    {
        if (!static::supportsBiteshipOrderId()) {
            return $query;
        }

        return $query->whereNull('biteship_order_id');
    }

    /**
     * Scope pencarian order berdasarkan biteship_order_id yang aman saat kolom belum ada.
     */
    public function scopeWhereBiteshipOrderId(Builder $query, string $biteshipOrderId): Builder
    {
        if (!static::supportsBiteshipOrderId()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('biteship_order_id', $biteshipOrderId);
    }

    /**
     * Accessor for delivery_address (alias for shipping_address)
     */
    public function getDeliveryAddressAttribute(): string
    {
        return $this->shipping_address;
    }

    /**
     * Accessor for delivery_latitude (alias for shipping_latitude)
     */
    public function getDeliveryLatitudeAttribute()
    {
        return $this->shipping_latitude;
    }

    /**
     * Accessor for delivery_longitude (alias for shipping_longitude)
     */
    public function getDeliveryLongitudeAttribute()
    {
        return $this->shipping_longitude;
    }

    /**
     * Accessor for delivery_time (alias for delivery_time_slot)
     */
    public function getDeliveryTimeAttribute(): ?string
    {
        return $this->delivery_time_slot;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::STATUS_PROCESSING => 'Diproses',
            self::STATUS_READY_TO_SHIP => 'Siap Pickup',
            self::STATUS_SHIPPED => 'Dikirim',
            self::STATUS_DELIVERED => 'Sampai',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            // Legacy status
            self::STATUS_PAID => 'Siap Pickup',
            self::STATUS_ASSIGNED => 'Siap Pickup',
            self::STATUS_PICKED_UP => 'Dikirim',
            self::STATUS_ON_DELIVERY => 'Dikirim',
            default => 'Unknown'
        };
    }

    /**
     * Get label stage pengiriman berbasis update Biteship.
     */
    public function getShipmentStageLabelAttribute(): string
    {
        return match($this->shipment_stage) {
            self::BITESHIP_STAGE_PROCESSING => 'Sedang Diproses',
            self::BITESHIP_STAGE_PICKUP => 'Penjemputan',
            self::BITESHIP_STAGE_DELIVERY => 'Pengantaran',
            self::BITESHIP_STAGE_RETURN => 'Pengembalian',
            self::BITESHIP_STAGE_ON_HOLD => 'Di Tahan',
            self::BITESHIP_STAGE_FINISHED => 'Selesai',
            default => 'Sedang Diproses',
        };
    }

    /**
     * Stage pengiriman aktif untuk UI customer.
     */
    public function getShipmentStageAttribute(): string
    {
        if (!empty($this->biteship_status_stage)) {
            return (string) $this->biteship_status_stage;
        }

        return self::mapOrderStatusToShipmentStage((string) $this->status);
    }

    /**
     * Normalisasi status mentah Biteship ke stage business.
     */
    public static function normalizeBiteshipStage(?string $trackingStatus): ?string
    {
        $status = strtolower(trim((string) $trackingStatus));

        if ($status === '') {
            return null;
        }

        $pickupStatuses = [
            'allocated', 'picking_up', 'picked',
            'pickup', 'penjemputan',
        ];

        $processingStatuses = [
            'created', 'confirmed', 'pending',
            'processing', 'diproses', 'sedang_diproses',
        ];

        $deliveryStatuses = [
            'dropping_off', 'out_for_delivery', 'on_delivery', 'delivering',
            'delivery', 'pengantaran',
        ];

        $returnStatuses = [
            'returning', 'returned', 'return', 'pengembalian', 'disposed',
        ];

        $onHoldStatuses = [
            'on_hold', 'hold', 'held', 'ditahan', 'di_tahan',
        ];

        $finishedStatuses = [
            'delivered', 'completed', 'done', 'finished', 'selesai',
        ];

        return match (true) {
            in_array($status, $processingStatuses, true) => self::BITESHIP_STAGE_PROCESSING,
            in_array($status, $pickupStatuses, true) => self::BITESHIP_STAGE_PICKUP,
            in_array($status, $deliveryStatuses, true) => self::BITESHIP_STAGE_DELIVERY,
            in_array($status, $returnStatuses, true) => self::BITESHIP_STAGE_RETURN,
            in_array($status, $onHoldStatuses, true) => self::BITESHIP_STAGE_ON_HOLD,
            in_array($status, $finishedStatuses, true) => self::BITESHIP_STAGE_FINISHED,
            default => null,
        };
    }

    /**
     * Mapping status order internal ke stage pengiriman customer.
     */
    public static function mapOrderStatusToShipmentStage(string $orderStatus): string
    {
        return match ($orderStatus) {
            self::STATUS_COMPLETED => self::BITESHIP_STAGE_FINISHED,
            self::STATUS_PENDING_PAYMENT, self::STATUS_PROCESSING => self::BITESHIP_STAGE_PROCESSING,
            self::STATUS_READY_TO_SHIP, self::STATUS_ASSIGNED => self::BITESHIP_STAGE_PICKUP,
            self::STATUS_SHIPPED, self::STATUS_DELIVERED, self::STATUS_ON_DELIVERY, self::STATUS_PICKED_UP => self::BITESHIP_STAGE_DELIVERY,
            self::STATUS_CANCELLED => self::BITESHIP_STAGE_RETURN,
            default => self::BITESHIP_STAGE_PROCESSING,
        };
    }

    /**
     * Mapping status tracking Biteship ke status order internal.
     */
    public static function mapBiteshipTrackingToOrderStatus(?string $trackingStatus): ?string
    {
        $status = strtolower(trim((string) $trackingStatus));

        if ($status === '') {
            return null;
        }

        $statusMap = [
            'confirmed' => self::STATUS_PROCESSING,
            'allocated' => self::STATUS_READY_TO_SHIP,
            'picking_up' => self::STATUS_READY_TO_SHIP,
            'picked' => self::STATUS_SHIPPED,
            'dropping_off' => self::STATUS_SHIPPED,
            'out_for_delivery' => self::STATUS_SHIPPED,
            'on_delivery' => self::STATUS_SHIPPED,
            'delivered' => self::STATUS_DELIVERED,
            'completed' => self::STATUS_COMPLETED,
            'done' => self::STATUS_COMPLETED,
            'cancelled' => self::STATUS_CANCELLED,
            'rejected' => self::STATUS_CANCELLED,
            'disposed' => self::STATUS_CANCELLED,
            'returned' => self::STATUS_CANCELLED,
            'returning' => self::STATUS_CANCELLED,
            'on_hold' => self::STATUS_READY_TO_SHIP,
        ];

        return $statusMap[$status] ?? null;
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_UNPAID => 'Belum Dibayar',
            self::PAYMENT_PENDING => 'Menunggu Verifikasi',
            self::PAYMENT_PAID => 'Lunas',
            default => 'Unknown'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING_PAYMENT => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_READY_TO_SHIP => 'primary',
            self::STATUS_SHIPPED => 'primary',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            // Legacy status
            self::STATUS_PAID => 'info',
            self::STATUS_ASSIGNED => 'primary',
            self::STATUS_PICKED_UP => 'info',
            self::STATUS_ON_DELIVERY => 'primary',
            default => 'secondary'
        };
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_UNPAID => 'danger',
            self::PAYMENT_PENDING => 'warning',
            self::PAYMENT_PAID => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Get total amount (alias for total)
     */
    public function getTotalAmountAttribute(): float
    {
        $canonicalTotal = null;

        if (array_key_exists('total_pembayaran', $this->attributes) && $this->attributes['total_pembayaran'] !== null) {
            $canonicalTotal = (float) $this->attributes['total_pembayaran'];
        }

        $legacyTotal = (float) ($this->attributes['total'] ?? 0);

        // Fallback ke kolom legacy `total` bila canonical masih default 0 tapi total legacy sudah valid.
        if ($canonicalTotal !== null) {
            if ($canonicalTotal > 0) {
                return $canonicalTotal;
            }

            if ($legacyTotal > 0) {
                return $legacyTotal;
            }

            return max(0, $canonicalTotal);
        }

        return max(0, $legacyTotal);
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get formatted shipping cost
     */
    public function getFormattedShippingCostAttribute(): string
    {
        $shippingPaid = $this->ongkir_dibayar;

        if ($shippingPaid === null) {
            $shippingPaid = $this->shipping_cost;
        }

        return 'Rp ' . number_format((float) $shippingPaid, 0, ',', '.');
    }

    /**
     * Get formatted product discount
     */
    public function getFormattedProductDiscountAttribute(): string
    {
        return 'Rp ' . number_format($this->product_discount ?? 0, 0, ',', '.');
    }

    /**
     * Get formatted shipping discount
     */
    public function getFormattedShippingDiscountAttribute(): string
    {
        $shippingDiscount = $this->diskon_ongkir;

        if ($shippingDiscount === null) {
            $shippingDiscount = $this->shipping_discount ?? 0;
        }

        return 'Rp ' . number_format((float) $shippingDiscount, 0, ',', '.');
    }

    /**
     * Get total discount
     */
    public function getTotalDiscountAttribute(): float
    {
        $shippingDiscount = $this->diskon_ongkir;

        if ($shippingDiscount === null) {
            $shippingDiscount = $this->shipping_discount ?? 0;
        }

        return (float) ($this->product_discount ?? 0) + (float) $shippingDiscount;
    }

    /**
     * Get formatted total discount
     */
    public function getFormattedTotalDiscountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_discount, 0, ',', '.');
    }

    /**
     * Check if payment is COD
     */
    public function isCod(): bool
    {
        return $this->payment_method === 'cod';
    }

    /**
     * Check if COD payment can be verified (by courier)
     */
    public function canVerifyCod(): bool
    {
        return $this->isCod() 
            && $this->status === self::STATUS_DELIVERED 
            && !$this->cod_verified;
    }

    /**
     * Check if order can be cancelled
     * Rules:
     * 1. Can only cancel if status is 'processing'
     * 2. Cannot cancel if already cancelled or completed
     * 3. Cannot cancel if already shipped, delivered, or ready_to_ship
     */
    public function canBeCancelled(): bool
    {
        // Can only cancel if status is processing
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if order was paid via payment gateway (non-COD)
     */
    public function isPaidViaGateway(): bool
    {
        return !$this->isCod()
            && $this->payment_status === self::PAYMENT_PAID
            && $this->paid_at !== null;
    }

    /**
     * Get remaining seconds before cancel window expires
     * Returns 0 if cancel window has expired
     */
    public function getCancelCountdownSeconds(): int
    {
        $targetTime = $this->created_at->addMinutes(self::CANCEL_WAIT_MINUTES);
        $remaining = now()->diffInSeconds($targetTime, false);

        return max(0, (int) $remaining);
    }

    /**
     * Get cancel countdown as attribute (for blade)
     */
    public function getCancelCountdownAttribute(): int
    {
        return $this->getCancelCountdownSeconds();
    }

    /**
     * Check if cancel window is still open
     */
    public function isCancelWindowOpen(): bool
    {
        return $this->getCancelCountdownSeconds() > 0;
    }

    /**
     * Cancel order with refund handling
     */
    public function cancelOrder(string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        // Check if refund is needed (non-COD and paid)
        $needsRefund = $this->requiresRefund();

        $updateData = [
            'status' => self::STATUS_CANCELLED,
            'cancel_reason' => $reason ?? 'Dibatalkan oleh customer',
        ];

        // If refund is needed, set refund status
        if ($needsRefund) {
            $updateData['refund_status'] = self::REFUND_PENDING;
            $updateData['refund_amount'] = $this->total;
            $updateData['refund_at'] = now();
        }

        $this->update($updateData);

        return true;
    }

    /**
     * Check if order requires refund on cancellation
     */
    public function requiresRefund(): bool
    {
        return $this->isPaidViaGateway() && $this->total > 0;
    }

    /**
     * Get refund amount (full refund)
     */
    public function getRefundAmount(): float
    {
        return (float) $this->total;
    }

    /**
     * Check if payment proof can be uploaded
     */
    public function canUploadPaymentProof(): bool
    {
        return $this->status === self::STATUS_PENDING_PAYMENT 
            && $this->payment_status !== self::PAYMENT_PAID;
    }

    /**
     * Check if testimonial can be given
     */
    public function canGiveTestimonial(): bool
    {
        return $this->status === self::STATUS_COMPLETED 
            && !$this->testimonial;
    }

    /**
     * Check if courier can be assigned
     */
    public function canAssignCourier(): bool
    {
        return $this->status === self::STATUS_PROCESSING && !$this->courier_id;
    }

    /**
     * Check if order needs courier action
     */
    public function needsCourierAction(): bool
    {
        return in_array($this->status, [
            self::STATUS_ASSIGNED,
            self::STATUS_PICKED_UP,
            self::STATUS_ON_DELIVERY
        ]);
    }

    /**
     * Assign courier to order
     */
    public function assignCourier(int $courierId): bool
    {
        if (!$this->canAssignCourier()) {
            return false;
        }

        $this->update([
            'courier_id' => $courierId,
            'assigned_at' => now(),
            'status' => self::STATUS_ASSIGNED,
        ]);

        return true;
    }

    /**
     * Update delivery status by courier
     */
    public function updateDeliveryStatus(string $status, ?string $notes = null): bool
    {
        $validTransitions = [
            self::STATUS_ASSIGNED => self::STATUS_PICKED_UP,
            self::STATUS_PICKED_UP => self::STATUS_ON_DELIVERY,
            self::STATUS_ON_DELIVERY => self::STATUS_DELIVERED,
        ];

        if (!isset($validTransitions[$this->status]) || $validTransitions[$this->status] !== $status) {
            return false;
        }

        $updateData = ['status' => $status];

        if ($notes) {
            $updateData['delivery_notes'] = $notes;
        }

        // Set timestamp based on status
        switch ($status) {
            case self::STATUS_PICKED_UP:
                $updateData['picked_up_at'] = now();
                break;
            case self::STATUS_ON_DELIVERY:
                $updateData['on_delivery_at'] = now();
                break;
            case self::STATUS_DELIVERED:
                $updateData['delivered_at'] = now();
                break;
        }

        $this->update($updateData);
        return true;
    }

    /**
     * Complete order (called when customer confirms receipt)
     */
    public function completeOrder(): bool
    {
        if ($this->status !== self::STATUS_DELIVERED) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        // Award loyalty points (1% of transaction amount)
        if ($this->user && $this->total_amount > 0) {
            $pointsEarned = (int) floor($this->total_amount / 10000); // 1 point per Rp10,000 spent
            
            if ($pointsEarned > 0) {
                $this->user->addPoints(
                    $pointsEarned,
                    'earned',
                    "Earned from order #{$this->order_number}",
                    $this->id
                );
            }
        }

        return true;
    }

    /**
     * Get payment proof URL
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        if ($this->payment_proof) {
            return asset('storage/' . $this->payment_proof);
        }
        return null;
    }

    /**
     * Calculate shipping cost based on distance in minutes
     * 1 KM = Rp 1.500
     */
    public static function calculateShippingCost(float $distanceKm): int
    {
        // Setiap 1 KM = Rp 1.500
        return (int) ceil($distanceKm) * 1500;
    }

    /**
     * Calculate distance in KM between two coordinates
     * Using Haversine formula
     */
    public static function calculateDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // dalam km

        return max(1, round($distance, 2)); // Minimal 1 km
    }

    /**
     * Calculate distance in minutes (for backward compatibility)
     * Using average speed of 30 km/h
     * @deprecated Use calculateDistanceKm instead
     */
    public static function calculateDistanceMinutes(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $distance = self::calculateDistanceKm($lat1, $lon1, $lat2, $lon2);

        // Konversi ke menit dengan asumsi kecepatan rata-rata 30 km/jam
        $minutes = ($distance / 30) * 60;

        return max(10, (int) ceil($minutes)); // Minimal 10 menit
    }

    /**
     * Get formatted delivery distance
     */
    public function getFormattedDeliveryDistanceAttribute(): string
    {
        if ($this->delivery_distance_km) {
            return $this->delivery_distance_km . ' km';
        }
        if ($this->delivery_distance_minutes) {
            return $this->delivery_distance_minutes . ' menit';
        }
        return '-';
    }

    /**
     * Get formatted delivery date
     */
    public function getFormattedDeliveryDateAttribute(): string
    {
        if ($this->delivery_date) {
            return $this->delivery_date->translatedFormat('l, d F Y');
        }
        return '-';
    }

    /**
     * Calculate delivery date based on order time
     * Delivery hours: 10:00 - 16:00
     * If ordered outside these hours, delivery is next day
     */
    public static function calculateDeliveryDate(): array
    {
        $now = now();
        $currentHour = (int) $now->format('H');
        
        // Jam operasional pengiriman: 10:00 - 16:00
        $deliveryDate = $now->copy();
        
        // Jika order di luar jam operasional (sebelum jam 10 atau setelah jam 16)
        // atau sudah terlalu dekat dengan jam tutup, kirim besok
        if ($currentHour < self::DELIVERY_START_HOUR || $currentHour >= self::DELIVERY_END_HOUR) {
            $deliveryDate = $now->copy()->addDay();
        }
        
        // Skip hari Minggu (jika perlu)
        // if ($deliveryDate->isSunday()) {
        //     $deliveryDate->addDay();
        // }
        
        return [
            'date' => $deliveryDate->format('Y-m-d'),
            'time_slot' => '10:00 - 16:00',
            'is_today' => $deliveryDate->isToday(),
            'formatted' => $deliveryDate->translatedFormat('l, d F Y'),
        ];
    }

    /**
     * Check if can deliver today
     */
    public static function canDeliverToday(): bool
    {
        $currentHour = (int) now()->format('H');
        return $currentHour >= self::DELIVERY_START_HOUR && $currentHour < self::DELIVERY_END_HOUR;
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING_PAYMENT);
    }

    /**
     * Scope for processing orders
     */
    public function scopeProcessing($query)
    {
        return $query->whereIn('status', [self::STATUS_PROCESSING, self::STATUS_SHIPPED]);
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for expired pending payment orders (>24 hours)
     */
    public function scopeExpiredPendingPayment($query)
    {
        return $query->where('status', self::STATUS_PENDING_PAYMENT)
                    ->where('created_at', '<', now()->subHours(24));
    }

    /**
     * Check if order is expired (pending payment > 24 hours)
     */
    public function isExpired(): bool
    {
        if ($this->status !== self::STATUS_PENDING_PAYMENT) {
            return false;
        }

        return $this->created_at->addHours(24)->isPast();
    }

    /**
     * Get remaining time before expiration (in seconds)
     */
    public function getExpirationTimeRemaining(): int
    {
        if ($this->status !== self::STATUS_PENDING_PAYMENT) {
            return 0;
        }

        $expiresAt = $this->created_at->addHours(24);
        $remaining = now()->diffInSeconds($expiresAt, false);

        return max(0, (int) $remaining);
    }

    /**
     * Get expiration time as attribute
     */
    public function getExpirationTimeAttribute(): int
    {
        return $this->getExpirationTimeRemaining();
    }

    /**
     * Get formatted expiration time
     */
    public function getFormattedExpirationTimeAttribute(): string
    {
        $seconds = $this->getExpirationTimeRemaining();
        
        if ($seconds <= 0) {
            return 'Expired';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    /**
     * Get calculated estimated delivery date
     * Fallback jika estimated_delivery_date kosong (untuk order lama)
     */
    public function getCalculatedEstimatedDeliveryAttribute(): string
    {
        // Jika sudah ada data tersimpan, gunakan itu
        if ($this->estimated_delivery_date) {
            return $this->estimated_delivery_date;
        }

        // Fallback: hitung ulang berdasarkan courier_service_name
        $serviceType = 'regular'; // default
        
        if ($this->courier_service_name) {
            $serviceName = strtolower($this->courier_service_name);
            if (str_contains($serviceName, 'instant')) {
                $serviceType = 'instant';
            } elseif (str_contains($serviceName, 'same day') || str_contains($serviceName, 'sameday')) {
                $serviceType = 'sameday';
            } elseif (str_contains($serviceName, 'express')) {
                $serviceType = 'express';
            }
        }

        // Estimasi hari berdasarkan service type
        $minDays = 2;
        $maxDays = 3;

        switch ($serviceType) {
            case 'instant':
                return '2-4 jam';
            case 'sameday':
                return 'Hari ini';
            case 'express':
                $minDays = 1;
                $maxDays = 2;
                break;
            default:
                $minDays = 2;
                $maxDays = 3;
                break;
        }

        // Convert ke format tanggal
        $startDate = $this->created_at->addDays($minDays);
        $endDate = $this->created_at->addDays($maxDays);

        if ($startDate->month === $endDate->month) {
            return $startDate->format('d') . ' – ' . $endDate->format('d F');
        } else {
            return $startDate->format('d M') . ' – ' . $endDate->format('d M');
        }
    }
}
