<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\CustomerOrderCreatedNotification;
use App\Notifications\CustomerPaymentReceivedNotification;
use App\Notifications\CustomerOrderCompletedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestOrderNotification extends Command
{
    protected $signature = 'test:order-notification
                            {order_number : Nomor pesanan (contoh: NP-20260610-61405)}
                            {type : Jenis notifikasi: created | paid | completed}
                            {--force : Langsung kirim tanpa konfirmasi}';

    protected $description = 'Test kirim email notifikasi pesanan ke customer';

    public function handle()
    {
        $orderNumber = $this->argument('order_number');
        $type = strtolower($this->argument('type'));

        if (!in_array($type, ['created', 'paid', 'completed'])) {
            $this->error("Type '{$type}' tidak valid. Pilih: created, paid, atau completed");
            return 1;
        }

        $order = Order::with('user')->where('order_number', $orderNumber)->first();

        if (!$order) {
            $this->error("Order {$orderNumber} tidak ditemukan!");
            return 1;
        }

        $this->info("========================================");
        $this->info("  TEST NOTIFIKASI EMAIL");
        $this->info("========================================");

        $this->table(
            ['Field', 'Value'],
            [
                ['Order Number', $order->order_number],
                ['Customer Name', $order->user->name ?? $order->shipping_name ?? 'N/A'],
                ['Customer Email', $order->user->email ?? 'N/A'],
                ['Total', $order->formatted_total ?? 'Rp ' . number_format($order->total, 0, ',', '.')],
                ['Order Status', $order->status],
                ['Payment Status', $order->payment_status],
                ['Notifikasi Type', strtoupper($type)],
                ['QUEUE', config('queue.default')],
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_FROM', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
            ]
        );

        if (!$order->user) {
            $this->warn("\nPeringatan: Order tidak memiliki user terkait.");
            $this->warn("Email tidak bisa dikirim tanpa user/notifiable.");
            return 1;
        }

        if (!$order->user->email) {
            $this->warn("\nPeringatan: User tidak memiliki email.");
            return 1;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("\nKirim email {$type} ke {$order->user->email}?", true)) {
                $this->info('Dibatalkan.');
                return 0;
            }
        }

        $this->info("\nMengirim notifikasi...");

        try {
            switch ($type) {
                case 'created':
                    $order->user->notify(new CustomerOrderCreatedNotification($order));
                    $subject = 'Pesanan Anda Berhasil Dibuat - ' . $order->order_number;
                    break;

                case 'paid':
                    $order->user->notify(new CustomerPaymentReceivedNotification($order));
                    $subject = 'Pembayaran Berhasil - ' . $order->order_number;
                    break;

                case 'completed':
                    $order->user->notify(new CustomerOrderCompletedNotification($order));
                    $subject = 'Pesanan Selesai - ' . $order->order_number;
                    break;
            }

            $this->info("\n  Email BERHASIL dikirim!");
            $this->info("  Ke     : {$order->user->email}");
            $this->info("  Subject: {$subject}");
            $this->info("\nSilakan cek inbox (dan folder Spam) di Gmail.");

            Log::info('Test notification sent via artisan command', [
                'order_number' => $order->order_number,
                'type' => $type,
                'to_email' => $order->user->email,
                'subject' => $subject,
            ]);

            return 0;

        } catch (\Throwable $e) {
            $this->error("\n  GAGAL mengirim email!");
            $this->error("  Error: {$e->getMessage()}");

            Log::error('Test notification failed', [
                'order_number' => $order->order_number,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return 1;
        }
    }
}
