<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{
    protected WebPushService $webPush;

    public function __construct(WebPushService $webPush)
    {
        $this->webPush = $webPush;
    }

    /**
     * Called when payment proof is uploaded
     */
    public function paymentUploaded(Order $order): void
    {
        try {
            // Send push to all admins
            $this->webPush->sendToAdmins(
                '💳 Bukti Pembayaran Baru',
                "Customer {$order->user->name} mengupload bukti pembayaran untuk pesanan #{$order->order_number}",
                route('admin.orders.show', $order),
                'payment_uploaded'
            );

            Log::info("Push notification sent for payment upload #{$order->order_number}");
        } catch (\Exception $e) {
            Log::error("Failed to send push for payment upload: " . $e->getMessage());
        }
    }
}
