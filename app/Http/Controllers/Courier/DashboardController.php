<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display courier dashboard
     */
    public function index()
    {
        $courier = auth()->user();

        // Statistics
        $stats = [
            'pending' => Order::where('courier_id', $courier->id)
                ->where('status', Order::STATUS_ASSIGNED)
                ->count(),
            'on_progress' => Order::where('courier_id', $courier->id)
                ->whereIn('status', [Order::STATUS_PICKED_UP, Order::STATUS_ON_DELIVERY])
                ->count(),
            'delivered_today' => Order::where('courier_id', $courier->id)
                ->where('status', Order::STATUS_DELIVERED)
                ->whereDate('delivered_at', today())
                ->count(),
            'completed_today' => Order::where('courier_id', $courier->id)
                ->where('status', Order::STATUS_COMPLETED)
                ->whereDate('completed_at', today())
                ->count(),
            'total_completed' => Order::where('courier_id', $courier->id)
                ->where('status', Order::STATUS_COMPLETED)
                ->count(),
        ];

        // Active deliveries
        $activeDeliveries = Order::where('courier_id', $courier->id)
            ->whereIn('status', [Order::STATUS_ASSIGNED, Order::STATUS_PICKED_UP, Order::STATUS_ON_DELIVERY])
            ->with(['user', 'items'])
            ->orderBy('delivery_date')
            ->orderBy('assigned_at')
            ->get();

        // Recent completed
        $recentCompleted = Order::where('courier_id', $courier->id)
            ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_COMPLETED])
            ->with('user')
            ->latest('delivered_at')
            ->take(5)
            ->get();

        return view('courier.dashboard', compact('stats', 'activeDeliveries', 'recentCompleted'));
    }
}
