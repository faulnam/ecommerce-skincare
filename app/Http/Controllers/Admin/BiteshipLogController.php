<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BiteshipLogController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\BiteshipLog::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('endpoint', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        return view('admin.biteship-logs.index', compact('logs'));
    }

    public function show(\App\Models\BiteshipLog $log)
    {
        return view('admin.biteship-logs.show', compact('log'));
    }
}
