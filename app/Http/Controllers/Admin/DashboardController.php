<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Testimonial;
use App\Models\Visitor;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        $orderQuery = Order::query()->withoutBiteshipOrder();

        // Statistics
        $totalOrders = (clone $orderQuery)->count();
        $totalRevenue = (clone $orderQuery)->where('payment_status', 'paid')->sum('total');
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Quick visitor statistic
        $todayVisitors = Visitor::where('date', Carbon::today()->toDateString())->count();
        $totalPageViews = Visitor::sum('views') ?? 0;

        // Order status statistics
        $orderStats = [
            'pending' => (clone $orderQuery)->where('status', 'pending_payment')->count(),
            'processing' => (clone $orderQuery)->where('status', 'processing')->count(),
            'shipped' => (clone $orderQuery)->where('status', 'shipped')->count(),
            'completed' => (clone $orderQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $orderQuery)->where('status', 'cancelled')->count(),
        ];

        // Recent orders
        $recentOrders = (clone $orderQuery)->with('user')
            ->latest()
            ->take(5)
            ->get();

        // Monthly revenue chart (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Order::where('payment_status', 'paid')
                ->withoutBiteshipOrder()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total');
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Pending payment verifications
    $pendingPayments = (clone $orderQuery)->where('payment_status', 'pending_verification')->count();

        // Pending testimonials
        $pendingTestimonials = Testimonial::where('is_approved', false)->count();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalProducts',
            'totalCustomers',
            'orderStats',
            'recentOrders',
            'monthlyRevenue',
            'pendingPayments',
            'pendingTestimonials',
            'todayVisitors',
            'totalPageViews'
        ));
    }
}
