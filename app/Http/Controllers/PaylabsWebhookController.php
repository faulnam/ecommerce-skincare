<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\CustomerPaymentReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaylabsWebhookController extends Controller
{
    /**
     * Handle webhook from Paylabs
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Paylabs Webhook Received', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        if (config('paylabs.webhook.verify_signature', false)) {
            $headerName = (string) config('paylabs.webhook.signature_header', 'X-Paylabs-Signature');
            $signature = (string) $request->header($headerName, '');

            if (!$this->verifySignature($request->all(), $signature)) {
                Log::warning('Paylabs Webhook: Invalid signature', [
                    'signature_header' => $headerName,
                ]);

                return response()->json(['message' => 'Invalid signature'], 401);
            }
        }

        // Support multiple field names from Paylabs
        $transactionId = $request->input('transaction_id') 
            ?? $request->input('platformTradeNo')
            ?? $request->input('platform_trade_no');
        
        $status = $request->input('status') 
            ?? $request->input('tradeStatus')
            ?? $request->input('trade_status');
        
        $merchantRefNo = $request->input('merchant_ref_no')
            ?? $request->input('merchantTradeNo')
            ?? $request->input('merchant_trade_no');

        Log::info('Paylabs Webhook Parsed', [
            'transaction_id' => $transactionId,
            'status' => $status,
            'merchant_ref_no' => $merchantRefNo,
        ]);

        // Find order by transaction ID or order number
        $order = Order::where('paylabs_transaction_id', $transactionId)
            ->orWhere('order_number', $merchantRefNo)
            ->first();

        if (!$order) {
            Log::error('Paylabs Webhook: Order not found', [
                'transaction_id' => $transactionId,
                'merchant_ref_no' => $merchantRefNo,
            ]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update order based on status (support multiple status formats)
        // Status bisa: 'paid', 'success', '02' (paid code), 'SUCCESS', 'PAID'
        $statusLower = strtolower((string)$status);
        
        if (in_array($statusLower, ['paid', 'success', '02']) || $status === '02') {
            $wasPending = $order->status === Order::STATUS_PENDING_PAYMENT;

            $order->update([
                'payment_status' => Order::PAYMENT_PAID,
                'paid_at' => now(),
                'status' => Order::STATUS_PROCESSING,
            ]);

            // Notify customer via email
            if ($wasPending && $order->user) {
                $order->user->notify(new CustomerPaymentReceivedNotification($order));
            }

            Log::info('Paylabs Webhook: Payment success', [
                'order_number' => $order->order_number,
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'status' => $status,
            ]);
        } elseif (in_array($statusLower, ['failed', 'expired', '09']) || $status === '09') {
            Log::info('Paylabs Webhook: Payment failed/expired', [
                'order_number' => $order->order_number,
                'transaction_id' => $transactionId,
                'status' => $status,
            ]);
        } else {
            Log::warning('Paylabs Webhook: Unknown status', [
                'order_number' => $order->order_number,
                'status' => $status,
                'status_lower' => $statusLower,
            ]);
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    /**
     * Handle callback from Paylabs (redirect after payment)
     */
    public function handleCallback(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $status = $request->input('status');

        if (in_array($status, ['paid', 'success'])) {
            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pembayaran berhasil!');
        }

        return redirect()->route('customer.payment.paylabs.waiting', $order)
            ->with('info', 'Menunggu konfirmasi pembayaran...');
    }

    /**
     * Verify webhook signature
     */
    protected function verifySignature(array $data, string $signature): bool
    {
        if ($signature === '') {
            return false;
        }

        $secret = (string) (config('paylabs.webhook.secret') ?: config('paylabs.api_key'));
        if ($secret === '') {
            return false;
        }

        $payload = json_encode($data);
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
