<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerPaymentReceivedNotification extends Notification implements ShouldQueue
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
        $url = route('customer.orders.guest-show', $this->order);

        return (new MailMessage)
            ->subject('Pembayaran Berhasil - ' . $this->order->order_number)
            ->greeting('Halo, ' . $this->order->shipping_name . '!')
            ->line('Pembayaran untuk pesanan Anda telah berhasil diterima.')
            ->line('Nomor Pesanan: **' . $this->order->order_number . '**')
            ->line('Total: **' . $this->order->formatted_total . '**')
            ->line('Status Pesanan: Diproses')
            ->action('Lihat Detail Pesanan', $url)
            ->line('Pesanan Anda sedang dipersiapkan untuk pengiriman. Kami akan menginformasikan kembali saat pesanan dikirim.')
            ->line('Terima kasih telah berbelanja di ' . config('branding.name', 'LUMINA') . '!')
            ->salutation('Salam,\n' . config('branding.name', 'LUMINA'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pembayaran Berhasil',
            'message' => "Pembayaran untuk pesanan #{$this->order->order_number} telah berhasil diterima. Pesanan sedang diproses.",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'type' => 'payment_received',
            'url' => route('customer.orders.guest-show', $this->order),
        ];
    }
}
