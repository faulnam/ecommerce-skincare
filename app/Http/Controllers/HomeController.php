<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Testimonial;
use App\Models\Gallery;
use App\Models\Order;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show landing page
     */
    public function index()
    {
        // Get featured products
        $products = Product::active()
            ->inStock()
            ->latest()
            ->take(6)
            ->get();

        // Get approved testimonials
        $testimonials = Testimonial::approved()
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        // Get active galleries
        $galleries = Gallery::active()
            ->ordered()
            ->get();

        // Statistik realtime
        $stats = $this->getStats();

        return view('home', compact('products', 'testimonials', 'galleries', 'stats'));
    }

    /**
     * Get realtime statistics
     */
    private function getStats()
    {
        // Total pelanggan yang sudah selesai order (completed)
        $totalCustomers = Order::where('status', Order::STATUS_COMPLETED)
            ->distinct('user_id')
            ->count('user_id');

        // Total review/testimoni yang diapprove
        $totalReviews = Testimonial::approved()->count();

        // Rata-rata rating - default 5.0 jika belum ada review
        if ($totalReviews > 0) {
            $avgRating = Testimonial::approved()->avg('rating');
            $avgRating = round($avgRating, 1);
        } else {
            $avgRating = 5.0; // Default rating untuk toko baru
        }

        // Persentase kepuasan (order completed vs total order non-cancelled)
        // Default 100% jika belum ada order
        $totalOrders = Order::whereNotIn('status', [Order::STATUS_CANCELLED])->count();
        $completedOrders = Order::where('status', Order::STATUS_COMPLETED)->count();
        $satisfactionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 100;

        return [
            'total_customers' => $totalCustomers,
            'total_reviews' => $totalReviews,
            'avg_rating' => $avgRating,
            'satisfaction_rate' => $satisfactionRate,
        ];
    }
}
