<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specification;
use Illuminate\Http\Request;

class SpecificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Specification::query();
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $specifications = $query->orderBy('category')->orderBy('name')->paginate(20);
        $categories = Specification::distinct()->pluck('category');
        
        return view('admin.specifications.index', compact('specifications', 'categories'));
    }

    public function create()
    {
        $categories = Specification::distinct()->pluck('category');
        return view('admin.specifications.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'new_category' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $category = $request->category === 'new' ? $request->new_category : $request->category;

        Specification::create([
            'category' => $category,
            'name' => $request->name,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.specifications.index')->with('success', 'Spesifikasi berhasil ditambahkan');
    }

    public function edit(Specification $specification)
    {
        $categories = Specification::distinct()->pluck('category');
        return view('admin.specifications.edit', compact('specification', 'categories'));
    }

    public function update(Request $request, Specification $specification)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'new_category' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $category = $request->category === 'new' ? $request->new_category : $request->category;

        $specification->update([
            'category' => $category,
            'name' => $request->name,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.specifications.index')->with('success', 'Spesifikasi berhasil diperbarui');
    }

    public function destroy(Specification $specification)
    {
        $specification->delete();
        return redirect()->route('admin.specifications.index')->with('success', 'Spesifikasi berhasil dihapus');
    }
}
