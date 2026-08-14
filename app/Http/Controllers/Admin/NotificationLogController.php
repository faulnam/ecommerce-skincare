<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationLog;

class NotificationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = NotificationLog::with('user')->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        
        $categories = [
            'Status Pesanan' => [
                'Pesanan Selesai',
                'Belum Melakukan Pembayaran',
                'Pembayaran Selesai'
            ],
            'Lainnya' => [
                'Registrasi',
                'Contact'
            ]
        ];

        return view('admin.notification-logs.index', compact('logs', 'categories'));
    }

    public function show(NotificationLog $log)
    {
        return view('admin.notification-logs.show', compact('log'));
    }
}
