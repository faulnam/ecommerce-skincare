<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Display order history
     */
    public function index(Request $request)
    {
        $query = Order::with('user', 'items')
            ->whereIn('status', ['completed', 'cancelled']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total_completed' => Order::where('status', 'completed')->count(),
            'total_cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'completed')
                ->where('payment_status', 'paid')
                ->sum('total'),
        ];

        return view('admin.history.index', compact('orders', 'stats'));
    }

    /**
     * Show order detail
     */
    public function show(Order $order)
    {
        $order->load('user', 'items.product', 'testimonial');
        return view('admin.history.show', compact('order'));
    }

    /**
     * Export to Excel/CSV
     */
    public function export(Request $request)
    {
        // This would use Laravel Excel package
        // For now, return CSV
        $orders = Order::with('user', 'items')
            ->whereIn('status', ['completed', 'cancelled']);

        if ($request->filled('date_from')) {
            $orders->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $orders->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $orders->get();

        $filename = 'history-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No. Pesanan', 'Tanggal', 'Customer', 'Total', 'Status', 'Pembayaran']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user->name,
                    $order->total,
                    $order->status_label,
                    $order->payment_status_label,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
