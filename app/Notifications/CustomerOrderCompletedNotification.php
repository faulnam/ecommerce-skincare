<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerOrderCompletedNotification extends Notification implements ShouldQueue
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
            ->subject('Pesanan Selesai - ' . $this->order->order_number)
            ->greeting('Halo, ' . $this->order->shipping_name . '!')
            ->line('Pesanan Anda telah selesai. Terima kasih telah berbelanja di ' . config('branding.name', 'LUMINA') . '.')
            ->line('Nomor Pesanan: **' . $this->order->order_number . '**')
            ->line('Total: **' . $this->order->formatted_total . '**')
            ->action('Lihat Detail Pesanan', $url)
            ->line('Kami harap Anda puas dengan layanan kami. Jangan ragu untuk menghubungi kami jika ada pertanyaan atau masukan.')
            ->salutation('Salam hangat,\n' . config('branding.name', 'LUMINA'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pesanan Selesai',
            'message' => "Pesanan #{$this->order->order_number} telah selesai. Terima kasih telah berbelanja!",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'type' => 'order_completed',
            'url' => route('customer.orders.guest-show', $this->order),
        ];
    }
}
