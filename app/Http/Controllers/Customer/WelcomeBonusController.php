<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WelcomeBonusController extends Controller
{
    public function claimBonus(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'customer') {
            return redirect()->back()->with('error', 'Hanya customer yang dapat mengklaim bonus.');
        }

        if ($user->welcome_bonus_claimed) {
            return redirect()->back()->with('error', 'Anda sudah mengklaim bonus welcome.');
        }

        // Check if user has any orders
        $hasOrders = $user->orders()->exists();
        if ($hasOrders) {
            return redirect()->back()->with('error', 'Bonus hanya tersedia untuk customer baru yang belum pernah berbelanja.');
        }
        
        // Mark as claimed (user already has 100 points from registration)
        $user->update([
            'welcome_bonus_claimed' => true,
        ]);

        return redirect()->back()->with('success', 'Selamat! Bonus welcome Anda telah diklaim. Gunakan 100 poin untuk diskon dan dapatkan free grip pada pembelian pertama!');
    }
}
