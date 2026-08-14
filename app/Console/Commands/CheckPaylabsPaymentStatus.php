<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\PaylabsService;
use Illuminate\Console\Command;

class CheckPaylabsPaymentStatus extends Command
{
    protected $signature = 'paylabs:check-status {order_number?}';
    protected $description = 'Check Paylabs payment status and update order';

    public function handle(PaylabsService $paylabs)
    {
        $orderNumber = $this->argument('order_number');

        if ($orderNumber) {
            $orders = Order::where('order_number', $orderNumber)
                ->where('payment_gateway', 'paylabs')
                ->whereNotNull('paylabs_transaction_id')
                ->get();
        } else {
            // List all pending Paylabs orders first
            $this->info('\n=== PENDING PAYLABS ORDERS ===\n');
            
            $pendingOrders = Order::where('payment_gateway', 'paylabs')
                ->where('payment_status', Order::PAYMENT_PENDING)
                ->whereNotNull('paylabs_transaction_id')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->get();

            if ($pendingOrders->isEmpty()) {
                $this->info('No pending Paylabs orders found.');
                return 0;
            }

            $this->table(
                ['Order Number', 'Customer', 'Amount', 'Created At'],
                $pendingOrders->map(fn($o) => [
                    $o->order_number,
                    $o->user->name ?? 'N/A',
                    $o->formatted_total,
                    $o->created_at->format('d M Y H:i'),
                ])
            );

            if (!$this->confirm('\nCheck status for all pending orders?', true)) {
                return 0;
            }

            $orders = $pendingOrders;
        }

        if ($orders->isEmpty()) {
            $this->info('No pending Paylabs orders found.');
            return 0;
        }

        $this->info("\nChecking {$orders->count()} orders...\n");

        foreach ($orders as $order) {
            $this->info("Checking order: {$order->order_number}");
            
            $result = $paylabs->checkStatus($order->paylabs_transaction_id);

            if (!$result['success']) {
                $this->error("  Failed: {$result['message']}");
                continue;
            }

            $status = $result['data']['status'] ?? 'pending';
            $rawStatus = $result['data']['raw_status'] ?? $status;

            $this->line("  Status: {$status} (raw: {$rawStatus})");

            if (in_array($status, ['paid', 'success']) || $status === '02') {
                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'paid_at' => now(),
                    'status' => Order::STATUS_PROCESSING,
                ]);
                $this->info("  ✓ Order updated to PAID");
            } else {
                $this->line("  - Still pending");
            }
        }

        $this->info('\nDone!');
        return 0;
    }
}
