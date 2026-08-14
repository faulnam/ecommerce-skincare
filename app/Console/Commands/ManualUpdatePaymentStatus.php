<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ManualUpdatePaymentStatus extends Command
{
    protected $signature = 'order:update-payment-status {order_number} {status=paid}';
    protected $description = 'Manually update order payment status';

    public function handle()
    {
        $orderNumber = $this->argument('order_number');
        $status = $this->argument('status');

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            $this->error("Order {$orderNumber} not found!");
            return 1;
        }

        $this->info("Order Details:");
        $this->table(
            ['Field', 'Value'],
            [
                ['Order Number', $order->order_number],
                ['Customer', $order->user->name ?? 'N/A'],
                ['Total', $order->formatted_total],
                ['Payment Status', $order->payment_status],
                ['Order Status', $order->status],
                ['Payment Gateway', $order->payment_gateway ?? 'N/A'],
                ['Transaction ID', $order->paylabs_transaction_id ?? 'N/A'],
                ['Created At', $order->created_at->format('d M Y H:i')],
            ]
        );

        if ($order->payment_status === Order::PAYMENT_PAID) {
            $this->warn('Order sudah dibayar!');
            if (!$this->confirm('Update ulang?', false)) {
                return 0;
            }
        }

        if (!$this->confirm("\nUpdate payment status ke '{$status}'?", true)) {
            $this->info('Cancelled.');
            return 0;
        }

        $oldStatus = $order->payment_status;
        $oldOrderStatus = $order->status;

        if ($status === 'paid') {
            $order->update([
                'payment_status' => Order::PAYMENT_PAID,
                'paid_at' => now(),
                'status' => Order::STATUS_PROCESSING,
            ]);
        } else {
            $order->update([
                'payment_status' => $status,
            ]);
        }

        $this->info("\n✓ Order updated successfully!");
        $this->table(
            ['Field', 'Old Value', 'New Value'],
            [
                ['Payment Status', $oldStatus, $order->fresh()->payment_status],
                ['Order Status', $oldOrderStatus, $order->fresh()->status],
                ['Paid At', '-', $order->fresh()->paid_at?->format('d M Y H:i') ?? '-'],
            ]
        );

        return 0;
    }
}
