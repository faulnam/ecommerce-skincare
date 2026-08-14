<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaylabsService;
use Illuminate\Http\Request;

class PaylabsPaymentController extends Controller
{
    protected $paylabs;

    protected function canAccessOrder(Order $order): bool
    {
        if (auth()->check()) {
            return $order->user_id === auth()->id();
        }

        $guestOrders = session()->get('guest_orders', []);
        $guestUserId = session()->get('guest_user_id');

        return in_array($order->id, $guestOrders, true) || ($guestUserId && (int) $guestUserId === (int) $order->user_id);
    }

    public function __construct(PaylabsService $paylabs)
    {
        $this->paylabs = $paylabs;
    }

    /**
     * Show payment page with Paylabs options
     */
    public function show(Order $order)
    {
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        if ($order->payment_status === Order::PAYMENT_PAID) {
            $routeName = auth()->check() ? 'customer.orders.show' : 'customer.orders.guest-show';
            return redirect()->route($routeName, $order)
                ->with('info', 'Pesanan sudah dibayar.');
        }

        $paymentMethods = config('paylabs.payment_methods');
        
        // Cek apakah sudah ada transaksi Paylabs yang belum expired
        $existingPaymentHtml = '';
        if ($order->paylabs_transaction_id && $order->payment_data) {
            $paymentData = json_decode($order->payment_data, true);
            $expiredAt = $paymentData['expired_at'] ?? null;
            
            if ($expiredAt && now()->lt(\Carbon\Carbon::parse($expiredAt))) {
                $paymentChannel = $order->payment_channel;
                $expiryTime = \Carbon\Carbon::parse($expiredAt)->format('d M Y, H:i');
                $lang = 'id';
                $canSimulate = true;
                
                $existingPaymentHtml = view('customer.payment.partials.paylabs-waiting-content', compact(
                    'order', 'paymentData', 'paymentChannel', 'expiryTime', 'lang', 'canSimulate'
                ))->render();
            }
        }

        return view('customer.payment.paylabs', compact('order', 'paymentMethods', 'existingPaymentHtml'));
    }

    /**
     * Process payment with selected method
     */
    public function process(Request $request, Order $order)
    {
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        $request->validate([
            'payment_channel' => 'required|string',
        ]);

        $paymentChannel = $request->payment_channel;
        
        // Determine payment method from channel
        if (str_starts_with($paymentChannel, 'VA_')) {
            $paymentMethod = 'va';
        } elseif ($paymentChannel === 'QRIS') {
            $paymentMethod = 'qris';
        } elseif (str_starts_with($paymentChannel, 'EWALLET_')) {
            $paymentMethod = 'ewallet';
        } elseif (str_starts_with($paymentChannel, 'RETAIL_')) {
            $paymentMethod = 'retail';
        } else {
            return back()->with('error', 'Metode pembayaran tidak valid.');
        }

        // Get total amount - ensure it's at least 1000 (Paylabs minimum)
        $totalAmount = (float) $order->total_amount;
        
        // Validate minimum amount
        if ($totalAmount < 1000) {
            \Log::error('Paylabs payment amount too low', [
                'order_number' => $order->order_number,
                'total_amount' => $totalAmount,
                'order_total' => $order->total,
                'order_total_pembayaran' => $order->total_pembayaran,
            ]);
            
            return back()->with('error', 'Total pembayaran minimal Rp 1.000. Total saat ini: Rp ' . number_format($totalAmount, 0, ',', '.'));
        }

        // Validate QRIS maximum amount (Rp 10.000.000 per transaction)
        if ($paymentMethod === 'qris' && $totalAmount > 10000000) {
            return back()->with('error', 'Pembayaran QRIS maksimal Rp 10.000.000 per transaksi. Total saat ini: Rp ' . number_format($totalAmount, 0, ',', '.') . '. Silakan gunakan metode pembayaran lain seperti Virtual Account atau E-Wallet.');
        }

        // Check if payment already exists and still valid
        if ($order->paylabs_transaction_id && $order->payment_data) {
            $paymentData = json_decode($order->payment_data, true);
            $expiredAt = $paymentData['expired_at'] ?? null;
            
            // If payment exists and not expired
            if ($expiredAt && now()->lt(\Carbon\Carbon::parse($expiredAt))) {
                \Log::info('Reusing existing Paylabs transaction', [
                    'order_number' => $order->order_number,
                    'transaction_id' => $order->paylabs_transaction_id,
                ]);
                
                if ($request->wantsJson()) {
                    $paymentChannel = $order->payment_channel;
                    $expiryTime = \Carbon\Carbon::parse($expiredAt)->format('d M Y, H:i');
                    $lang = 'id';
                    $canSimulate = true;
                    
                    return response()->json([
                        'success' => true,
                        'html' => view('customer.payment.partials.paylabs-waiting-content', compact(
                            'order', 'paymentData', 'paymentChannel', 'expiryTime', 'lang', 'canSimulate'
                        ))->render()
                    ]);
                }
                
                return redirect()->route('customer.payment.paylabs.waiting', $order)
                    ->with('info', 'Menggunakan pembayaran yang sudah dibuat sebelumnya.');
            }
        }

        $result = $this->paylabs->createTransaction([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $totalAmount,
            'customer_name' => $order->shipping_name ?: ($order->user->name ?: 'Customer'),
            'customer_email' => $order->user->email ?: '',
            'customer_phone' => $order->shipping_phone ?: ($order->user->phone ?: '08000000000'),
            'payment_method' => $paymentMethod,
            'payment_channel' => $paymentChannel,
        ]);

        if (!$result['success']) {
            // If duplicate error and payment data exists, redirect to waiting page
            if (str_contains($result['message'], 'Duplicate') && $order->paylabs_transaction_id) {
                \Log::warning('Duplicate Paylabs transaction, redirecting to waiting page', [
                    'order_number' => $order->order_number,
                    'transaction_id' => $order->paylabs_transaction_id,
                ]);
                
                return redirect()->route('customer.payment.paylabs.waiting', $order)
                    ->with('info', 'Pembayaran sudah dibuat sebelumnya. Silakan selesaikan pembayaran.');
            }
            
            return back()->with('error', 'Gagal membuat pembayaran: ' . $result['message']);
        }

        // Update order with payment data
        $order->update([
            'paylabs_transaction_id' => $result['data']['transaction_id'],
            'payment_gateway' => 'paylabs',
            'payment_channel' => $paymentChannel,
            'payment_data' => json_encode($result['data']),
        ]);

        if ($request->wantsJson()) {
            $paymentData = $result['data'];
            $expiryTime = \Carbon\Carbon::parse($paymentData['expired_at'])->format('d M Y, H:i');
            $lang = 'id';
            $canSimulate = true;
            
            return response()->json([
                'success' => true,
                'html' => view('customer.payment.partials.paylabs-waiting-content', compact(
                    'order', 'paymentData', 'paymentChannel', 'expiryTime', 'lang', 'canSimulate'
                ))->render()
            ]);
        }

        // Redirect to waiting page
        return redirect()->route('customer.payment.paylabs.waiting', $order);
    }

    /**
     * Waiting page for payment confirmation
     */
    public function waiting(Order $order)
    {
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        if ($order->payment_status === Order::PAYMENT_PAID) {
            $routeName = auth()->check() ? 'customer.orders.show' : 'customer.orders.guest-show';
            return redirect()->route($routeName, $order)
                ->with('success', 'Pembayaran berhasil!');
        }

        $paymentData = json_decode($order->payment_data, true) ?? [];
        
        // Debug log
        \Log::info('Payment Data for waiting page', [
            'order_id' => $order->id,
            'payment_data' => $paymentData,
        ]);

        $vaNumber = (string) (
            $paymentData['va_number']
            ?? $paymentData['vaCode']
            ?? $paymentData['virtual_account_number']
            ?? $paymentData['virtual_account']
            ?? $paymentData['account_number']
            ?? $paymentData['payment_number']
            ?? '-'
        );

        $qrString = (string) (
            $paymentData['qrCode']
            ?? $paymentData['qr_code']
            ?? $paymentData['qr_string']
            ?? $paymentData['qr_content']
            ?? ''
        );
        $qrUrl = (string) (
            $paymentData['qrisUrl']
            ?? $paymentData['qr_url']
            ?? $paymentData['qr_image_url']
            ?? $paymentData['qr_code_url']
            ?? ''
        );

        if ($qrUrl === '' && $qrString !== '') {
            if (filter_var($qrString, FILTER_VALIDATE_URL)) {
                $qrUrl = $qrString;
            } else {
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrString);
            }
        }
        
        \Log::info('QR Data extracted', [
            'qrString' => substr($qrString, 0, 50),
            'qrUrl' => $qrUrl,
        ]);

        $deeplinkUrl = (string) (
            $paymentData['deeplink_url']
            ?? $paymentData['redirect_url']
            ?? $paymentData['payment_url']
            ?? '#'
        );

        $paymentCode = (string) (
            $paymentData['payment_code']
            ?? $paymentData['bill_code']
            ?? $paymentData['pay_code']
            ?? '-'
        );

        $paymentData['va_number_display'] = $vaNumber;
        $paymentData['qr_url_display'] = $qrUrl;
        $paymentData['deeplink_url_display'] = $deeplinkUrl;
        $paymentData['payment_code_display'] = $paymentCode;

        $paymentChannel = $order->payment_channel;
        $expiryTime = $paymentData['expired_at'] ?? now()->addHours(24)->toIso8601String();
        $canSimulate = true;

        return view('customer.payment.paylabs-waiting', compact('order', 'paymentData', 'paymentChannel', 'expiryTime', 'canSimulate'));
    }

    /**
     * Simulate successful payment (for testing)
     */
    public function simulate(Request $request, Order $order)
    {
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'paid_at' => now(),
            'status' => Order::STATUS_PROCESSING,
        ]);

        return back()->with('success', 'Simulasi pembayaran berhasil.');
    }

    /**
     * Check payment status (AJAX)
     */
   public function checkStatus(Order $order)
{
    // GANTI: if ($order->user_id !== auth()->id())
    // DENGAN:
    if (!$this->canAccessOrder($order)) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    if (!$order->paylabs_transaction_id) {
        return response()->json([
            'success' => false,
            'message' => 'Transaction ID not found',
        ]);
    }

    // GANTI: $this->paylabs->checkStatus($order->paylabs_transaction_id)
    // DENGAN: kirim order_number dan channel
    $result = $this->paylabs->checkStatus($order->order_number, $order->payment_channel ?? 'QRIS');

    \Log::info('Paylabs checkStatus result', [
        'order_id' => $order->id,
        'order_number' => $order->order_number,
        'result' => $result,
    ]);

    if (!$result['success']) {
        return response()->json($result);
    }

    $status = $result['data']['status'] ?? 'pending';

    if (in_array($status, ['paid', 'success', '02'])) {
        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'paid_at' => now(),
            'status' => Order::STATUS_PROCESSING,
        ]);
    }

    return response()->json([
        'success' => true,
        'status' => $status,
        'paid' => in_array($status, ['paid', 'success', '02']),
    ]);
}


}
