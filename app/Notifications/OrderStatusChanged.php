<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    protected $order;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
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
        // Determine URL based on user role
        $url = route('customer.orders.show', $this->order);
        if ($notifiable->role === 'admin') {
            $url = route('admin.orders.show', $this->order);
        } elseif ($notifiable->role === 'courier') {
            $url = route('courier.orders.show', $this->order);
        }

        return [
            'title' => 'Status Pesanan Berubah',
            'message' => "Status pesanan #{$this->order->order_number} telah berubah menjadi: {$this->order->status_label}",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
            'type' => 'status_changed',
            'url' => $url,
        ];
    }
}
