<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display users list
     */
    public function index(Request $request)
    {
        $query = User::withTrashed()->where('role', 'customer');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->withCount('orders')->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user detail
     */
    public function show(User $user)
    {
        $user->load(['orders' => function ($q) {
            $q->latest()->take(10);
        }]);

        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()->where('payment_status', 'paid')->sum('total'),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun customer berhasil {$status}.");
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        $newPassword = 'password123';
        
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return back()->with('success', "Password berhasil direset menjadi: {$newPassword}");
    }
}
