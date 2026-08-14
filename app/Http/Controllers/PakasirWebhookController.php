<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\CustomerPaymentReceivedNotification;
use App\Services\PakasirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PakasirWebhookController extends Controller
{
    protected PakasirService $pakasirService;

    public function __construct(PakasirService $pakasirService)
    {
        $this->pakasirService = $pakasirService;
    }

    /**
     * Handle webhook from Pakasir
     * IMPORTANT: Always return 200 OK so Pakasir marks webhook as completed
     */
    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        
        Log::info('Pakasir webhook received', $data);

        // Always return 200 OK first, then process
        // This ensures Pakasir receives success response
        
        try {
            $this->processWebhook($data);
        } catch (\Exception $e) {
            Log::error('Pakasir webhook processing error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }

        // ALWAYS return 200 OK - required by Pakasir
        return response()->json([
            'status' => 'ok',
            'message' => 'Webhook received'
        ], 200);
    }

    /**
     * Process the webhook data
     */
    protected function processWebhook(array $data): void
    {
        // Get order ID from various possible fields
        $orderId = $data['order_id'] ?? $data['orderId'] ?? $data['reference'] ?? null;
        $amount = $data['amount'] ?? $data['total'] ?? 0;
        $status = $data['status'] ?? '';
        $paymentMethod = $data['payment_method'] ?? $data['method'] ?? 'online';
        $completedAt = $data['completed_at'] ?? $data['paid_at'] ?? null;
        $project = $data['project'] ?? '';

        Log::info('Pakasir webhook processing', [
            'order_id' => $orderId,
            'amount' => $amount,
            'status' => $status,
            'project' => $project
        ]);

        if (!$orderId) {
            Log::warning('Pakasir webhook missing order_id', $data);
            return;
        }

        // Find order by order_number
        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            Log::warning('Pakasir webhook order not found', ['order_id' => $orderId]);
            return;
        }

        // Check if status is completed/paid
        $paidStatuses = ['completed', 'paid', 'success', 'settlement'];
        if (!in_array(strtolower($status), $paidStatuses)) {
            Log::info('Pakasir webhook status not paid', [
                'order_id' => $orderId,
                'status' => $status
            ]);
            return;
        }

        // Update order status to paid
        if ($order->status === Order::STATUS_PENDING_PAYMENT) {
            $order->update([
                'status' => Order::STATUS_PROCESSING,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => $paymentMethod,
                'paid_at' => $completedAt ? now()->parse($completedAt) : now(),
            ]);

            // Notify customer via email
            if ($order->user) {
                $order->user->notify(new CustomerPaymentReceivedNotification($order));
            }

            Log::info('Pakasir payment completed via webhook', [
                'order_id' => $orderId,
                'order_number' => $order->order_number,
                'amount' => $amount,
                'payment_method' => $paymentMethod
            ]);
        } else {
            Log::info('Pakasir webhook received but order already processed', [
                'order_id' => $orderId,
                'current_status' => $order->status
            ]);
        }
    }

    /**
     * Handle redirect after payment (callback URL)
     */
    public function handleCallback(Request $request, Order $order)
    {
        // Check payment status via API
        $transaction = $this->pakasirService->getTransactionDetail(
            $order->order_number,
            (int)$order->total_amount
        );

        if ($transaction && $transaction['status'] === 'completed') {
            // Update order if not already updated by webhook
            if ($order->status === Order::STATUS_PENDING_PAYMENT) {
                $order->update([
                    'status' => Order::STATUS_PROCESSING,
                    'payment_status' => Order::PAYMENT_PAID,
                    'payment_method' => $transaction['payment_method'] ?? 'pakasir',
                    'paid_at' => now(),
                ]);
            }

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pembayaran berhasil! Terima kasih atas pesanan Anda.');
        }

        // Payment not yet completed, show payment page again
        return redirect()->route('customer.payment.show', $order)
            ->with('info', 'Pembayaran belum selesai. Silakan selesaikan pembayaran Anda.');
    }
}
