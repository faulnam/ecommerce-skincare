<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $soldOutProducts = Product::where('stock', 0)->get();
        
        return view('admin.reports.index', compact('soldOutProducts'));
    }

    public function downloadSalesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $orders = Order::with(['user', 'items.product'])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'laporan-penjualan-' . $startDate->format('Y-m-d') . '-sampai-' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'No Order',
                'Tanggal',
                'Nama Customer',
                'Produk',
                'Kategori',
                'Jumlah',
                'Harga Satuan',
                'Subtotal',
                'Total Order',
                'Status Pembayaran',
                'Status Pengiriman'
            ]);

            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    fputcsv($file, [
                        $order->order_number,
                        $order->created_at->format('d/m/Y H:i'),
                        $order->user->name,
                        $item->product->name ?? 'Produk Dihapus',
                        $item->product->category_label ?? '-',
                        $item->quantity,
                        'Rp ' . number_format($item->price, 0, ',', '.'),
                        'Rp ' . number_format($item->quantity * $item->price, 0, ',', '.'),
                        'Rp ' . number_format($order->total, 0, ',', '.'),
                        $order->payment_status_label,
                        $order->status_label
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadSoldOutReport()
    {
        $products = Product::where('stock', 0)->get();

        $filename = 'laporan-produk-habis-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Nama Produk',
                'Kategori',
                'Harga',
                'Berat',
                'Stok',
                'Status'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->category_label,
                    'Rp ' . number_format($product->price, 0, ',', '.'),
                    $product->formatted_weight,
                    $product->stock,
                    'Habis'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
