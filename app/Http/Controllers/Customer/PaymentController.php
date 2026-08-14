<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PakasirService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PakasirService $pakasirService;

    protected function canAccessOrder(Order $order): bool
    {
        if (auth()->check()) {
            return $order->user_id === auth()->id();
        }

        $guestOrders = session()->get('guest_orders', []);
        $guestUserId = session()->get('guest_user_id');

        \Log::info('PaymentController canAccessOrder check', [
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'guest_orders' => $guestOrders,
            'guest_user_id' => $guestUserId,
            'in_guest_orders' => in_array($order->id, $guestOrders, true),
            'user_id_match' => $guestUserId && (int) $guestUserId === (int) $order->user_id
        ]);

        return in_array($order->id, $guestOrders, true) || ($guestUserId && (int) $guestUserId === (int) $order->user_id);
    }

    public function __construct(PakasirService $pakasirService)
    {
        $this->pakasirService = $pakasirService;
    }

    /**
     * Show payment gateway selection (guest + customer)
     */
    public function selectGateway(Order $order)
    {
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        return view('customer.payment.select-gateway', compact('order'));
    }

    /**
     * Show payment page with payment method options
     */
    public function show(Order $order)
    {
        // Ensure user owns this order
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        // If order already paid, redirect to order detail
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        // If order cancelled, redirect to order detail
        if ($order->status === Order::STATUS_CANCELLED) {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'Pesanan ini sudah dibatalkan.');
        }

        $paymentMethods = $this->pakasirService->getPaymentMethods();

        return view('customer.orders.payment', compact('order', 'paymentMethods'));
    }

    /**
     * Process payment - create transaction with selected method
     */
    public function process(Request $request, Order $order)
    {
        // Ensure user owns this order
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        // If order already paid
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|in:qris,bni_va,bri_va,cimb_niaga_va,permata_va,maybank_va,redirect',
        ]);

        $paymentMethod = $validated['payment_method'];
        $amount = (int) $order->total_amount;

        // If using redirect method (let user choose on Pakasir page)
        if ($paymentMethod === 'redirect') {
            $redirectUrl = route('customer.payment.callback', $order);
            $paymentUrl = $this->pakasirService->getPaymentUrl(
                $order->order_number,
                $amount,
                $redirectUrl
            );

            return redirect()->away($paymentUrl);
        }

        // Create transaction via API
        $transaction = $this->pakasirService->createTransaction(
            $order->order_number,
            $amount,
            $paymentMethod
        );

        if (!$transaction) {
            return back()->with('error', 'Gagal membuat transaksi pembayaran. Silakan coba lagi.');
        }

        // Store payment info in session for display
        session([
            'payment_transaction' => [
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'payment_number' => $transaction['payment_number'] ?? '',
                'total_payment' => $transaction['total_payment'] ?? $amount,
                'fee' => $transaction['fee'] ?? 0,
                'expired_at' => $transaction['expired_at'] ?? null,
            ]
        ]);

        return redirect()->route('customer.payment.waiting', $order);
    }

    /**
     * Show payment waiting page (with QR code or VA number)
     */
    public function waiting(Order $order)
    {
        // Ensure user owns this order
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        // Get transaction info from session
        $paymentTransaction = session('payment_transaction');

        if (!$paymentTransaction || $paymentTransaction['order_id'] !== $order->id) {
            // Try to get transaction status from API
            $transaction = $this->pakasirService->getTransactionDetail(
                $order->order_number,
                (int) $order->total_amount
            );

            if ($transaction && $transaction['status'] === 'completed') {
                return redirect()->route('customer.orders.show', $order)
                    ->with('success', 'Pembayaran berhasil!');
            }

            // No transaction found, redirect to payment page
            return redirect()->route('customer.payment.show', $order)
                ->with('info', 'Silakan pilih metode pembayaran.');
        }

        $paymentMethods = $this->pakasirService->getPaymentMethods();

        return view('customer.orders.payment-waiting', compact('order', 'paymentTransaction', 'paymentMethods'));
    }

    /**
     * Check payment status via AJAX
     */
    public function checkStatus(Order $order)
    {
        // Ensure user owns this order
        if (!$this->canAccessOrder($order)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Refresh order from database
        $order->refresh();

        // Check if already paid
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return response()->json([
                'status' => 'paid',
                'message' => 'Pembayaran berhasil!',
                'redirect' => route('customer.orders.show', $order)
            ]);
        }

        // Check via API
        $transaction = $this->pakasirService->getTransactionDetail(
            $order->order_number,
            (int) $order->total_amount
        );

        if ($transaction && $transaction['status'] === 'completed') {
            // Update order if webhook hasn't done it yet
            if ($order->status === Order::STATUS_PENDING_PAYMENT) {
                $order->update([
                    'status' => Order::STATUS_PROCESSING,
                    'payment_status' => Order::PAYMENT_PAID,
                    'payment_method' => $transaction['payment_method'] ?? 'pakasir',
                    'paid_at' => now(),
                ]);
            }

            return response()->json([
                'status' => 'paid',
                'message' => 'Pembayaran berhasil!',
                'redirect' => route('customer.orders.show', $order)
            ]);
        }

        return response()->json([
            'status' => 'pending',
            'message' => 'Menunggu pembayaran...'
        ]);
    }

    /**
     * Redirect payment (simple URL redirect)
     */
    public function redirect(Order $order)
    {
        // Ensure user owns this order
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        // If order already paid
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        $amount = (int) $order->total_amount;
        $redirectUrl = route('customer.payment.callback', $order);
        
        $paymentUrl = $this->pakasirService->getPaymentUrl(
            $order->order_number,
            $amount,
            $redirectUrl
        );

        return redirect()->away($paymentUrl);
    }

    /**
     * Simulate payment (sandbox mode only)
     */
    public function simulatePayment(Order $order)
    {
        // Ensure user owns this order
        if (!$this->canAccessOrder($order)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only allow in sandbox mode
        if (!config('services.pakasir.sandbox')) {
            return response()->json(['error' => 'Simulation only available in sandbox mode'], 400);
        }

        // If order already paid
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return response()->json([
                'status' => 'already_paid',
                'message' => 'Pesanan sudah dibayar.',
                'redirect' => route('customer.orders.show', $order)
            ]);
        }

        // Get payment transaction from session
        $paymentTransaction = session('payment_transaction');
        $amount = $paymentTransaction['total_payment'] ?? (int) $order->total_amount;

        // Simulate payment via Pakasir API
        $result = $this->pakasirService->simulatePayment(
            $order->order_number,
            $amount
        );

        if ($result) {
            // Update order status
            $order->update([
                'status' => Order::STATUS_PROCESSING,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => $paymentTransaction['method'] ?? 'qris',
                'paid_at' => now(),
            ]);

            // Clear session
            session()->forget('payment_transaction');

            return response()->json([
                'status' => 'success',
                'message' => 'Simulasi pembayaran berhasil!',
                'redirect' => route('customer.orders.show', $order)
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal simulasi pembayaran. Coba lagi.'
        ], 500);
    }
}
