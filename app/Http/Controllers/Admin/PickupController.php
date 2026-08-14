<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\BiteshipService;
use Illuminate\Http\Request;

class PickupController extends Controller
{
    protected $biteship;

    public function __construct(BiteshipService $biteship)
    {
        $this->biteship = $biteship;
    }

    /**
     * Request pickup dari Biteship
     */
    public function requestPickup(Request $request, Order $order)
    {
        // Validasi order sudah bayar dan ada ekspedisi
        if ($order->payment_status !== 'paid') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Order belum dibayar. Tidak bisa request pickup.']);
            }
            return back()->with('error', 'Order belum dibayar. Tidak bisa request pickup.');
        }

        if (!$order->courier_code) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Customer belum memilih ekspedisi saat checkout.']);
            }
            return back()->with('error', 'Customer belum memilih ekspedisi saat checkout.');
        }

        if ($order->biteship_order_id) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Pickup sudah pernah direquest untuk order ini.']);
            }
            return back()->with('error', 'Pickup sudah pernah direquest untuk order ini.');
        }

        // Prepare items
        $items = $order->items->map(function ($item) {
            return [
                'name' => $item->product_name,
                'description' => $item->product_name,
                'value' => (int) $item->product_price,
                'quantity' => $item->quantity,
                'weight' => ($item->product->weight ?? 500) * $item->quantity,
            ];
        })->toArray();

        // Request pickup ke Biteship (tanpa courier_type karena optional)
        $result = $this->biteship->createOrder([
            'destination_contact_name' => $order->shipping_name,
            'destination_contact_phone' => $order->shipping_phone,
            'destination_address' => $order->shipping_address,
            'destination_latitude' => $order->shipping_latitude,
            'destination_longitude' => $order->shipping_longitude,
            'destination_postal_code' => $order->shipping_postal_code ?? '61219',
            'courier_code' => $order->courier_code,
            'order_note' => 'Order #' . $order->order_number . ($order->notes ? ' - ' . $order->notes : '') . ' | PAYMENT: ' . ($order->isCod() ? 'COD' : 'NON-COD'),
            'is_cod' => $order->isCod(),
            'payment_method' => $order->isCod() ? 'cash_on_delivery' : 'online_payment',
            'total_amount' => (int) round((float) $order->total),
            'cash_on_delivery_amount' => $order->isCod() ? (int) round((float) $order->total) : 0,
            'items' => $items,
        ]);

        if (!$result['success']) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal request pickup: ' . $result['message']]);
            }
            return back()->with('error', 'Gagal request pickup: ' . $result['message']);
        }

        // Update order dengan data dari Biteship
        $biteshipData = $result['data'];
        $courierInfo = $biteshipData['courier'] ?? [];
        
        $order->update([
            'biteship_order_id' => $biteshipData['id'] ?? null,
            'waybill_id' => $courierInfo['waybill_id'] ?? null,
            'label_url' => $biteshipData['label_url'] ?? null,
            'status' => Order::STATUS_READY_TO_SHIP, // Siap pickup, menunggu kurir ambil
            'courier_driver_name' => $courierInfo['name'] ?? null,
            'courier_driver_phone' => $courierInfo['phone'] ?? null,
            'courier_driver_photo' => $courierInfo['photo'] ?? null,
            'courier_driver_rating' => $courierInfo['rating'] ?? null,
            'courier_driver_vehicle' => $courierInfo['vehicle_type'] ?? null,
            'courier_driver_vehicle_number' => $courierInfo['vehicle_number'] ?? null,
            'pickup_time' => $biteshipData['pickup_time'] ?? null,
        ]);

        // Notify customer
        $order->user->notify(new \App\Notifications\OrderStatusChanged(
            $order,
            'Pesanan Anda siap untuk pickup. Kurir ' . $order->courier_name . ' akan segera mengambil paket.'
        ));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pickup berhasil direquest! Kurir ' . ($order->courier_driver_name ?? $order->courier_name) . ' akan datang dalam 30 menit.',
                'data' => [
                    'courier_name' => $order->courier_driver_name,
                    'courier_phone' => $order->courier_driver_phone,
                    'pickup_time' => $order->pickup_time?->format('H:i'),
                ]
            ]);
        }

        return back()->with('success', 'Pickup berhasil direquest! Kurir ' . ($order->courier_driver_name ?? $order->courier_name) . ' akan datang dalam 30 menit.');
    }

    /**
     * Get tracking info
     */
    public function getTracking(Order $order)
    {
        if (!$order->waybill_id) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor resi belum tersedia',
            ]);
        }

        $result = $this->biteship->trackOrder($order->waybill_id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tracking',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }

    /**
     * Cetak label/resi dari Biteship API
     */
    public function printLabel(Order $order)
    {
        if (!$order->biteship_order_id) {
            return back()->with('error', 'Order belum diproses oleh Biteship. Lakukan request pickup terlebih dahulu.');
        }

        // Check if sandbox mode
        if (config('biteship.sandbox', true)) {
            return back()->with('error', 'Label resi Biteship hanya tersedia di mode production. Ubah BITESHIP_SANDBOX=false di file .env untuk menggunakan API Biteship sungguhan dan mendapatkan label resi resmi dari ekspedisi.');
        }

        $result = $this->biteship->printLabel($order->biteship_order_id);

        if (!$result['success']) {
            return back()->with('error', 'Gagal cetak label: ' . $result['message']);
        }

        // Jika response berupa file PDF/image langsung
        if (!empty($result['content'])) {
            return response($result['content'], 200)
                ->header('Content-Type', $result['content_type'])
                ->header('Content-Disposition', 'inline; filename="label-' . $order->order_number . '.pdf"');
        }

        // Jika response berupa URL, redirect ke URL label
        if (!empty($result['url'])) {
            return redirect($result['url']);
        }

        return back()->with('error', 'Format response label tidak dikenali.');
    }

    /**
     * Manual input resi (jika pickup dilakukan di luar sistem)
     */
    public function updateWaybill(Request $request, Order $order)
    {
        $request->validate([
            'waybill_id' => 'required|string|max:255',
        ]);

        $order->update([
            'waybill_id' => $request->waybill_id,
            'status' => Order::STATUS_SHIPPED,
        ]);

        // Notify customer
        $order->user->notify(new \App\Notifications\OrderStatusChanged(
            $order,
            'Pesanan Anda sedang dikirim dengan nomor resi: ' . $request->waybill_id
        ));

        return back()->with('success', 'Nomor resi berhasil disimpan!');
    }
}
