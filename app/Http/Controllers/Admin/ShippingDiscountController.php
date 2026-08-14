<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingDiscount;
use Illuminate\Http\Request;

class ShippingDiscountController extends Controller
{
    /**
     * Display a listing of shipping discounts
     */
    public function index()
    {
        $discounts = ShippingDiscount::latest()->paginate(10)->withQueryString();
        return view('admin.shipping-discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new shipping discount
     */
    public function create()
    {
        return view('admin.shipping-discounts.create');
    }

    /**
     * Store a newly created shipping discount
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'max_discount' => 'nullable|numeric|min:0',
            'min_subtotal' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        ShippingDiscount::create($validated);

        return redirect()->route('admin.shipping-discounts.index')
            ->with('success', 'Diskon ongkir berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified shipping discount
     */
    public function edit(ShippingDiscount $shippingDiscount)
    {
        return view('admin.shipping-discounts.edit', compact('shippingDiscount'));
    }

    /**
     * Update the specified shipping discount
     */
    public function update(Request $request, ShippingDiscount $shippingDiscount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'max_discount' => 'nullable|numeric|min:0',
            'min_subtotal' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $shippingDiscount->update($validated);

        return redirect()->route('admin.shipping-discounts.index')
            ->with('success', 'Diskon ongkir berhasil diperbarui.');
    }

    /**
     * Remove the specified shipping discount
     */
    public function destroy(ShippingDiscount $shippingDiscount)
    {
        $shippingDiscount->delete();

        return redirect()->route('admin.shipping-discounts.index')
            ->with('success', 'Diskon ongkir berhasil dihapus.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(ShippingDiscount $shippingDiscount)
    {
        $shippingDiscount->update(['is_active' => !$shippingDiscount->is_active]);

        return back()->with('success', 'Status diskon berhasil diubah.');
    }
}
