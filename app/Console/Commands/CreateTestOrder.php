<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTestOrder extends Command
{
    protected $signature = 'order:create-test {status=completed : Order status (completed/cancelled)} {--user=1 : User ID}';
    protected $description = 'Create test order with specified status (completed/cancelled)';

    public function handle()
    {
        $status = strtolower($this->argument('status'));
        $userId = $this->option('user');

        if (!in_array($status, ['completed', 'cancelled'])) {
            $this->error('Status must be either "completed" or "cancelled"');
            return Command::FAILURE;
        }

        if (!in_array($status, [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])) {
            $this->error('Invalid status. Use: completed or cancelled');
            return Command::FAILURE;
        }

        $this->info("Creating test order with status: {$status}");
        $this->info("User ID: {$userId}");
        $this->newLine();

        try {
            DB::beginTransaction();

            // Get user
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found");
                return Command::FAILURE;
            }

            // Get a random product
            $product = Product::first();
            if (!$product) {
                $this->error("No products found in database");
                return Command::FAILURE;
            }

            // Generate order number
            $orderNumber = 'NP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            // Calculate amounts
            $subtotal = $product->price;
            $shippingCost = 15000;
            $total = $subtotal + $shippingCost;

            // Prepare order data
            $orderData = [
                'order_number' => $orderNumber,
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'product_discount' => 0,
                'shipping_discount' => 0,
                'shipping_cost' => $shippingCost,
                'ongkir_asli' => $shippingCost,
                'diskon_ongkir' => 0,
                'ongkir_dibayar' => $shippingCost,
                'total' => $total,
                'total_pembayaran' => $total,
                'status' => $status,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => 'transfer',
                'paid_at' => now(),
                'shipping_address' => 'Jl. Test No. 123, Surabaya',
                'shipping_phone' => '081234567890',
                'shipping_name' => $user->name ?? 'Test Customer',
                'shipping_postal_code' => '61219',
                'shipping_latitude' => -7.278417,
                'shipping_longitude' => 112.632583,
                'courier_code' => 'jnt',
                'courier_name' => 'J&T Express',
                'courier_service_name' => 'EZ',
                'notes' => 'TEST ORDER - Created via command',
            ];

            // Add status-specific fields
            if ($status === Order::STATUS_COMPLETED) {
                $orderData['delivered_at'] = now()->subDays(2);
                $orderData['completed_at'] = now()->subDays(2);
                $orderData['awb_number'] = 'JP' . rand(1000000000, 9999999999);
                $orderData['biteship_order_id'] = 'BIT-' . uniqid();
            } elseif ($status === Order::STATUS_CANCELLED) {
                $orderData['cancel_reason'] = 'Test cancellation via command';
                $orderData['refund_at'] = now();
                $orderData['refund_status'] = Order::REFUND_COMPLETED;
                $orderData['refund_amount'] = $total;
            }

            // Create order
            $order = Order::create($orderData);

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price,
            ]);

            DB::commit();

            $this->info("✅ Test order created successfully!");
            $this->newLine();

            $this->table(
                ['Field', 'Value'],
                [
                    ['Order Number', $order->order_number],
                    ['Order ID', $order->id],
                    ['Status', $order->status],
                    ['Payment Status', $order->payment_status],
                    ['Total', 'Rp ' . number_format($order->total, 0, ',', '.')],
                    ['Product', $product->name],
                    ['User', $user->name ?? 'N/A'],
                    ['Created At', $order->created_at->format('Y-m-d H:i:s')],
                ]
            );

            if ($status === Order::STATUS_COMPLETED) {
                $this->newLine();
                $this->info("Additional Completed Fields:");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['AWB Number', $order->awb_number],
                        ['Biteship Order ID', $order->biteship_order_id],
                        ['Delivered At', $order->delivered_at->format('Y-m-d H:i:s')],
                        ['Completed At', $order->completed_at->format('Y-m-d H:i:s')],
                    ]
                );
            } elseif ($status === Order::STATUS_CANCELLED) {
                $this->newLine();
                $this->info("Additional Cancelled Fields:");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Cancel Reason', $order->cancel_reason],
                        ['Refund At', $order->refund_at->format('Y-m-d H:i:s')],
                        ['Refund Status', $order->refund_status],
                        ['Refund Amount', 'Rp ' . number_format($order->refund_amount, 0, ',', '.')],
                    ]
                );
            }

            $this->newLine();
            $this->info("✅ You can now view this order in the admin panel or customer dashboard.");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Failed to create test order: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
