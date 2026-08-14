<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerOrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        \Log::debug('CustomerOrderCreatedNotification::toMail called', [
            'order_number' => $this->order->order_number,
            'notifiable_email' => $notifiable->email ?? 'N/A',
        ]);

        $url = route('customer.orders.guest-show', $this->order);

        $message = (new MailMessage)
            ->subject('Pesanan Anda Berhasil Dibuat - ' . $this->order->order_number)
            ->greeting('Halo, ' . $this->order->shipping_name . '!')
            ->line('Pesanan Anda telah berhasil dibuat dan sedang menunggu pembayaran.')
            ->line('Nomor Pesanan: **' . $this->order->order_number . '**')
            ->line('Total Pembayaran: **' . $this->order->formatted_total . '**')
            ->line('Status: Menunggu Pembayaran')
            ->action('Lihat Detail Pesanan', $url)
            ->line('Segera lakukan pembayaran agar pesanan dapat segera diproses. Pesanan akan otomatis dibatalkan jika tidak dibayar dalam 24 jam.')
            ->line('Jika Anda membutuhkan bantuan, silakan hubungi customer service kami.')
            ->salutation('Terima kasih,' . PHP_EOL . config('branding.name', 'Hijab'));

        \Log::debug('CustomerOrderCreatedNotification::toMail MailMessage built successfully');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pesanan Berhasil Dibuat',
            'message' => "Pesanan #{$this->order->order_number} berhasil dibuat. Silakan lakukan pembayaran.",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'type' => 'order_created',
            'url' => route('customer.orders.guest-show', $this->order),
        ];
    }
}
