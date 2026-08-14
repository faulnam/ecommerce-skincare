<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourierLocation;
use App\Models\Order;
use Illuminate\Http\Request;

class CourierLocationController extends Controller
{
    /**
     * Update courier location
     */
    public function update(Request $request)
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

        if (!$user->isCourier()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // If order_id provided, verify courier is assigned to that order
        if (isset($validated['order_id'])) {
            $order = Order::find($validated['order_id']);
            if (!$order || $order->courier_id !== $user->id) {
                return response()->json(['error' => 'Not assigned to this order'], 403);
            }
        }

        // Deactivate old locations for this courier
        CourierLocation::where('user_id', $user->id)
            ->active()
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
     * Get courier location for an order (for customer tracking)
     */
    public function getForOrder(Order $order)
    {
        // Check if user is the order owner
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only allow tracking when order is being delivered
        if (!in_array($order->status, [Order::STATUS_ON_DELIVERY])) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking not available for this order status',
            ]);
        }

        if (!$order->courier_id) {
            return response()->json([
                'success' => false,
                'message' => 'No courier assigned',
            ]);
        }

        $location = CourierLocation::getLatestForOrder($order->id);

        if (!$location) {
            // Try to get courier's last known location
            $location = CourierLocation::getLatestForCourier($order->courier_id);
        }

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Courier location not available',
            ]);
        }

        return response()->json([
            'success' => true,
            'location' => [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'accuracy' => $location->accuracy,
                'speed' => $location->speed,
                'heading' => $location->heading,
                'updated_at' => $location->updated_at->toISOString(),
                'updated_ago' => $location->updated_at->diffForHumans(),
            ],
            'destination' => [
                'latitude' => $order->shipping_latitude,
                'longitude' => $order->shipping_longitude,
                'address' => $order->shipping_address,
            ],
            'courier' => [
                'name' => $order->courier->name,
                'phone' => $order->courier->phone,
                'avatar' => $order->courier->avatar_url,
            ],
        ]);
    }

    /**
     * Stop tracking (when delivery is completed)
     */
    public function stopTracking(Request $request)
    {
        $user = auth()->user();

        if (!$user->isCourier()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        CourierLocation::where('user_id', $user->id)
            ->active()
            ->update(['is_active' => false]);

        return response()->json(['success' => true]);
    }
}
