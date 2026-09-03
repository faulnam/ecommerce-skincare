<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\CourierLocation;
use App\Notifications\OrderStatusChanged;
use App\Notifications\CustomerOrderCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    /**
     * Display list of assigned deliveries
     */
    public function index(Request $request)
    {
        $courier = auth()->user();
        $query = Order::where('courier_id', $courier->id)->with(['user', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show active deliveries
            $query->whereIn('status', [
                Order::STATUS_ASSIGNED,
                Order::STATUS_PICKED_UP,
                Order::STATUS_ON_DELIVERY,
                Order::STATUS_DELIVERED
            ]);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('delivery_date', $request->date);
        }

        $deliveries = $query->orderBy('delivery_date')
            ->orderBy('assigned_at')
            ->paginate(10);

        return view('courier.deliveries.index', compact('deliveries'));
    }

    /**
     * Show delivery detail
     */
    public function show(Order $order)
    {
        // Make sure this delivery belongs to the courier
        if ($order->courier_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pengiriman ini.');
        }

        $order->load(['user', 'items.product']);

        return view('courier.deliveries.show', compact('order'));
    }

    /**
     * Update delivery status - Pick up items (with photo)
     */
    public function pickUp(Request $request, Order $order)
    {
        if ($order->courier_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_ASSIGNED) {
            return back()->with('error', 'Status pesanan tidak valid untuk diambil.');
        }

        // Validate base64 photo is required
        $request->validate([
            'pickup_photo_base64' => 'required|string',
        ], [
            'pickup_photo_base64.required' => 'Foto pengambilan barang wajib diambil dari kamera.',
        ]);

        // Process base64 image
        $base64Image = $request->input('pickup_photo_base64');
        $photoPath = $this->saveBase64Image($base64Image, 'delivery-photos/pickup');
        
        if (!$photoPath) {
            return back()->with('error', 'Gagal menyimpan foto. Silakan coba lagi.');
        }
        
        $order->pickup_photo = $photoPath;
        $order->save();

        $order->updateDeliveryStatus(Order::STATUS_PICKED_UP);

        // Notify customer
        $order->user->notify(new OrderStatusChanged($order, 'Barang pesanan Anda sudah diambil kurir dan sedang dipersiapkan.'));

        return back()->with('success', 'Status berhasil diupdate. Barang sudah diambil.');
    }

    /**
     * Update delivery status - Start delivery
     */
    public function startDelivery(Order $order)
    {
        if ($order->courier_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_PICKED_UP) {
            return back()->with('error', 'Status pesanan tidak valid.');
        }

        $order->updateDeliveryStatus(Order::STATUS_ON_DELIVERY);

        // Notify customer
        $order->user->notify(new OrderStatusChanged($order, 'Pesanan Anda sedang dalam perjalanan menuju alamat tujuan.'));

        return back()->with('success', 'Status berhasil diupdate. Pengiriman dimulai.');
    }

    /**
     * Update delivery status - Mark as delivered (with photo)
     */
    public function markDelivered(Request $request, Order $order)
    {
        if ($order->courier_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_ON_DELIVERY) {
            return back()->with('error', 'Status pesanan tidak valid.');
        }

        // Validate base64 photo is required
        $request->validate([
            'delivery_photo_base64' => 'required|string',
            'delivery_notes' => 'nullable|string|max:500',
        ], [
            'delivery_photo_base64.required' => 'Foto bukti pengiriman wajib diambil dari kamera.',
        ]);

        // Process base64 image
        $base64Image = $request->input('delivery_photo_base64');
        $photoPath = $this->saveBase64Image($base64Image, 'delivery-photos/delivered');
        
        if (!$photoPath) {
            return back()->with('error', 'Gagal menyimpan foto. Silakan coba lagi.');
        }
        
        $order->delivery_photo = $photoPath;
        $order->save();

        $order->updateDeliveryStatus(Order::STATUS_DELIVERED, $request->delivery_notes);

        // For COD orders, don't auto-complete - wait for COD verification
        if ($order->payment_method !== 'cod') {
            // Auto complete after delivery for non-COD orders
            $order->updateDeliveryStatus(Order::STATUS_COMPLETED);

            // Notify customer
            $order->user->notify(new OrderStatusChanged($order, 'Pesanan Anda sudah sampai dan selesai! Terima kasih telah berbelanja di LUMINA.'));

            // Send email notification to customer
            if ($order->user) {
                $order->user->notify(new CustomerOrderCompletedNotification($order));
            }
        } else {
            // For COD, notify to pay
            $order->user->notify(new OrderStatusChanged($order, 'Pesanan Anda sudah sampai! Silakan bayar kepada kurir.'));
        }

        return back()->with('success', 'Pesanan berhasil diantar' . ($order->payment_method === 'cod' ? '. Silakan konfirmasi pembayaran COD dari customer.' : ' dan selesai!'));
    }

    /**
     * Verify COD payment received
     */
    public function verifyCod(Order $order)
    {
        if ($order->courier_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canVerifyCod()) {
            return back()->with('error', 'Verifikasi COD tidak valid untuk pesanan ini.');
        }

        $order->update([
            'cod_verified' => true,
            'cod_verified_at' => now(),
            'payment_status' => Order::PAYMENT_PAID,
            'paid_at' => now(),
        ]);

        // Mark as completed after COD payment verified
        $order->updateDeliveryStatus(Order::STATUS_COMPLETED);

        // Notify customer
        $order->user->notify(new OrderStatusChanged($order, 'Pembayaran COD sudah diterima. Pesanan selesai! Terima kasih telah berbelanja di LUMINA.'));

        // Send email notification to customer
        if ($order->user) {
            $order->user->notify(new CustomerOrderCompletedNotification($order));
        }

        // Notify admin
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new OrderStatusChanged($order, 'Pembayaran COD pesanan ' . $order->order_number . ' sudah diverifikasi oleh kurir.'));
        }

        return back()->with('success', 'Pembayaran COD berhasil diverifikasi. Pesanan selesai!');
    }

    /**
     * Display delivery history
     */
    public function history(Request $request)
    {
        $courier = auth()->user();
        $query = Order::where('courier_id', $courier->id)
            ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_COMPLETED])
            ->with('user');

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('delivered_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('delivered_at', '<=', $request->to_date);
        }

        $deliveries = $query->latest('delivered_at')->paginate(15);

        // Stats for this period
        $stats = Order::where('courier_id', $courier->id)
            ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_COMPLETED]);
        
        if ($request->filled('from_date')) {
            $stats->whereDate('delivered_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $stats->whereDate('delivered_at', '<=', $request->to_date);
        }

        $totalDelivered = $stats->count();
        $totalEarnings = $stats->sum('shipping_cost');

        return view('courier.deliveries.history', compact('deliveries', 'totalDelivered', 'totalEarnings'));
    }

    /**
     * Update courier location
     */
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $user = auth()->user();

        // If order_id provided, verify courier is assigned to that order
        if (isset($validated['order_id'])) {
            $order = Order::find($validated['order_id']);
            if (!$order || $order->courier_id !== $user->id) {
                return response()->json(['error' => 'Not assigned to this order'], 403);
            }
        }

        // Deactivate old locations for this courier
        CourierLocation::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Create new location record
        $location = CourierLocation::create([
            'user_id' => $user->id,
            'order_id' => $validated['order_id'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'accuracy' => $validated['accuracy'] ?? null,
            'speed' => $validated['speed'] ?? null,
            'heading' => $validated['heading'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'location' => $location,
        ]);
    }
    
    /**
     * Save base64 image to storage
     */
    private function saveBase64Image($base64Image, $folder)
    {
        try {
            // Extract the image data from base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
                $extension = $matches[1];
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            } else {
                $extension = 'jpg';
            }
            
            $imageData = base64_decode($base64Image);
            
            if ($imageData === false) {
                return null;
            }
            
            // Generate unique filename
            $filename = $folder . '/' . uniqid() . '_' . time() . '.' . $extension;
            
            // Save to storage
            Storage::disk('public')->put($filename, $imageData);
            
            return $filename;
        } catch (\Exception $e) {
            \Log::error('Failed to save base64 image: ' . $e->getMessage());
            return null;
        }
    }
}
