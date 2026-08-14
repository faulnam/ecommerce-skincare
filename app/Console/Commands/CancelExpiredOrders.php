<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCancelledNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel orders with pending payment status that are older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired orders...');

        // Get all expired pending payment orders
        $expiredOrders = Order::expiredPendingPayment()->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders found.');
            return 0;
        }

        $cancelledCount = 0;

        foreach ($expiredOrders as $order) {
            try {
                $order->update([
                    'status' => Order::STATUS_CANCELLED,
                    'cancel_reason' => 'Otomatis dibatalkan: Pembayaran tidak dilakukan dalam 24 jam',
                ]);

                // Restore stock
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->restoreStock($item->quantity);
                    }
                }

                // Send notifications
                // Notify admins
                $admins = User::where('role', 'admin')->get();
                if ($admins->isNotEmpty()) {
                    Notification::send($admins, new OrderCancelledNotification(
                        $order, 
                        'Otomatis dibatalkan: Pembayaran tidak dilakukan dalam 24 jam',
                        null
                    ));
                }

                // Notify customer
                if ($order->user) {
                    $order->user->notify(new OrderCancelledNotification(
                        $order,
                        'Otomatis dibatalkan: Pembayaran tidak dilakukan dalam 24 jam',
                        null
                    ));
                }

                $cancelledCount++;

                $this->line("✓ Cancelled order: {$order->order_number}");

                Log::info("Auto-cancelled expired order", [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'created_at' => $order->created_at,
                    'expired_at' => $order->created_at->addHours(24),
                ]);

            } catch (\Exception $e) {
                $this->error("✗ Failed to cancel order {$order->order_number}: {$e->getMessage()}");
                
                Log::error("Failed to auto-cancel expired order", [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Successfully cancelled {$cancelledCount} expired order(s).");

        return 0;
    }
}
