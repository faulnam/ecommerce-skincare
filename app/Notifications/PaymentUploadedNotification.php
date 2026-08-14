<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentUploadedNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
        return [
            'title' => 'Bukti Pembayaran Baru',
            'message' => "Customer {$this->order->user->name} telah mengupload bukti pembayaran untuk pesanan #{$this->order->order_number}",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'type' => 'payment_uploaded',
            'url' => route('admin.orders.show', $this->order),
        ];
    }
}
