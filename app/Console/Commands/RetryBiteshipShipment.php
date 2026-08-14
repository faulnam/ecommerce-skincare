<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\BiteshipService;
use Illuminate\Console\Command;

class RetryBiteshipShipment extends Command
{
    protected $signature = 'biteship:retry-shipment {order_number?}';
    protected $description = 'Retry creating Biteship shipment for failed orders';

    public function handle(BiteshipService $biteship)
    {
        $orderNumber = $this->argument('order_number');

        if ($orderNumber) {
            $orders = Order::where('order_number', $orderNumber)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->whereNotNull('biteship_draft_order_id')
                ->whereNull('biteship_order_id')
                ->get();
        } else {
            // Get all paid orders with draft but no shipment
            $orders = Order::where('payment_status', Order::PAYMENT_PAID)
                ->whereNotNull('biteship_draft_order_id')
                ->whereNull('biteship_order_id')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($orders->isEmpty()) {
            $this->info('No orders need Biteship shipment retry.');
            return 0;
        }

        $this->info("Found {$orders->count()} orders to retry...\n");

        $this->table(
            ['Order Number', 'Customer', 'Courier', 'Created At'],
            $orders->map(fn($o) => [
                $o->order_number,
                $o->user->name ?? 'N/A',
                $o->courier_name ?? 'N/A',
                $o->created_at->format('d M Y H:i'),
            ])
        );

        if (!$this->confirm("\nRetry creating shipment for these orders?", true)) {
            return 0;
        }

        $success = 0;
        $failed = 0;

        foreach ($orders as $order) {
            $this->info("Processing: {$order->order_number}");

            try {
                // Create shipment from draft
                $result = $biteship->createOrderFromDraft($order->biteship_draft_order_id);

                if ($result['success']) {
                    $shipmentData = $result['data'];
                    
                    // Update order with shipment data
                    $order->update([
                        'biteship_order_id' => $shipmentData['id'],
                        'waybill_id' => $shipmentData['courier']['waybill_id'] ?? null,
                        'awb_number' => $shipmentData['courier']['waybill_id'] ?? null,
                        'label_url' => $shipmentData['courier']['link'] ?? null,
                        'courier_driver_name' => $shipmentData['courier']['driver_name'] ?? null,
                        'courier_driver_phone' => $shipmentData['courier']['driver_phone'] ?? null,
                        'courier_driver_photo' => $shipmentData['courier']['driver_photo_url'] ?? null,
                        'status' => Order::STATUS_READY_TO_SHIP,
                    ]);

                    $this->info("  ✓ Shipment created: {$shipmentData['id']}");
                    if ($shipmentData['courier']['waybill_id'] ?? null) {
                        $this->line("  Waybill: {$shipmentData['courier']['waybill_id']}");
                    }
                    $success++;
                } else {
                    $this->error("  ✗ Failed: {$result['message']}");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Exception: {$e->getMessage()}");
                $failed++;
            }

            $this->line('');
        }

        $this->info("\nSummary:");
        $this->line("  Success: {$success}");
        $this->line("  Failed: {$failed}");

        return 0;
    }
}
