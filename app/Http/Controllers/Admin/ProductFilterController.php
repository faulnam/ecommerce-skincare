<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductFilter;
use Illuminate\Http\Request;

class ProductFilterController extends Controller
{
    /**
     * Display a listing of the filters.
     */
    public function index(Request $request)
    {
        $categories = ProductFilter::select('category')->distinct()->pluck('category');
        
        $query = ProductFilter::latest();
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $filters = $query->paginate(20)->withQueryString();
        
        return view('admin.filters.index', compact('filters', 'categories'));
    }

    /**
     * Show the form for creating a new filter.
     */
    public function create()
    {
        $categories = ProductFilter::select('category')->distinct()->pluck('category');
        return view('admin.filters.create', compact('categories'));
    }

    /**
     * Store a newly created filter in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'new_category' => 'nullable|string|max:100|required_if:category,new',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $category = $validated['category'] === 'new' ? $validated['new_category'] : $validated['category'];

        ProductFilter::create([
            'category' => $category,
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.filters.index')
            ->with('success', 'Filter berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified filter.
     */
    public function edit(ProductFilter $filter)
    {
        $categories = ProductFilter::select('category')->distinct()->pluck('category');
        return view('admin.filters.edit', compact('filter', 'categories'));
    }

    /**
     * Update the specified filter in storage.
     */
    public function update(Request $request, ProductFilter $filter)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'new_category' => 'nullable|string|max:100|required_if:category,new',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);
        
        $category = $validated['category'] === 'new' ? $validated['new_category'] : $validated['category'];

        $filter->update([
            'category' => $category,
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.filters.index')
            ->with('success', 'Filter berhasil diperbarui.');
    }

    /**
     * Remove the specified filter from storage.
     */
    public function destroy(ProductFilter $filter)
    {
        $filter->delete();
        
        return redirect()->route('admin.filters.index')
            ->with('success', 'Filter berhasil dihapus.');
    }
}
