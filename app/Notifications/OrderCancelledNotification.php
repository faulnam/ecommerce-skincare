<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $cancelReason;
    protected $refundAmount;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $cancelReason = null, float $refundAmount = null)
    {
        $this->order = $order;
        $this->cancelReason = $cancelReason;
        $this->refundAmount = $refundAmount;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $isAdmin = $notifiable->isAdmin();

        // Build message
        $message = $isAdmin 
            ? "Pesanan #{$this->order->order_number} dari {$this->order->user->name} telah dibatalkan"
            : "Pesanan #{$this->order->order_number} telah dibatalkan";

        // Add refund info if applicable
        if ($this->refundAmount > 0) {
            $formattedRefund = 'Rp ' . number_format($this->refundAmount, 0, ',', '.');
            $message .= $isAdmin 
                ? ". Refund: {$formattedRefund}"
                : ". Dana {$formattedRefund} akan dikembalikan dalam 1-3 hari kerja";
        }

        return [
            'title' => $isAdmin ? '❌ Pesanan Dibatalkan' : 'Pesanan Dibatalkan',
            'message' => $message,
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'cancel_reason' => $this->cancelReason,
            'refund_amount' => $this->refundAmount,
            'refund_status' => $this->order->refund_status,
            'type' => 'order_cancelled',
            'url' => $isAdmin 
                ? route('admin.orders.show', $this->order)
                : route('customer.orders.show', $this->order),
        ];
    }
}
