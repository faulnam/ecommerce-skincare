<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminInsightController extends Controller
{
    public function index()
    {
        if (auth()->user()->isBlogger()) {
            $insights = Insight::where('user_id', auth()->id())->latest()->paginate(10)->withQueryString();
        } else {
            $insights = Insight::latest()->paginate(10)->withQueryString();
        }
        return view('admin.insights.index', compact('insights'));
    }

    public function create()
    {
        return view('admin.insights.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:insights,slug',
            'author' => 'nullable|string|max:100',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,scheduled',
            'image' => 'nullable|image|max:2048',
            'alt_image' => 'required_with:image|nullable|string|max:255',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'published_at' => 'nullable|date|required_if:status,scheduled',
        ]);

        $validated['slug'] = Str::slug($validated['title'] ?? $validated['slug']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($validated['alt_image']) . '-' . time() . '.' . $extension;
            $path = $file->storeAs('insights', $filename, 'public');
            $validated['image'] = $path;
        } else {
            $validated['image'] = null; 
        }

        // Handle Publishing Schedule
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] === 'scheduled') {
            $validated['published_at'] = $request->published_at;
        } else {
            $validated['published_at'] = null; // Draft
        }

        $validated['user_id'] = auth()->id();
        if (empty($validated['author'])) {
            $validated['author'] = auth()->user()->name;
        }

        Insight::create($validated);

        return redirect()->route('admin.insights.index')->with('success', 'Insight created successfully.');
    }

    public function edit(Insight $insight)
    {
        if (auth()->user()->isBlogger() && $insight->user_id !== auth()->id()) {
            abort(403, 'Anda hanya dapat mengedit insight milik Anda sendiri.');
        }

        return view('admin.insights.edit', compact('insight'));
    }

    public function update(Request $request, Insight $insight)
    {
        if (auth()->user()->isBlogger() && $insight->user_id !== auth()->id()) {
            abort(403, 'Anda hanya dapat mengedit insight milik Anda sendiri.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:100',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'status' => 'required|in:draft,published,scheduled',
            'image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date|required_if:status,scheduled',
        ]);

        if ($request->title !== $insight->title) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('image')) {
            if ($insight->image && !str_starts_with($insight->image, 'http') && Storage::disk('public')->exists($insight->image)) {
                Storage::disk('public')->delete($insight->image);
            }
            $validated['image'] = $request->file('image')->store('insights', 'public');
        }

        // Handle Publishing Schedule on Edit
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] === 'scheduled') {
            $validated['published_at'] = $request->published_at;
        } else {
            $validated['published_at'] = null;
        }

        $insight->update($validated);

        return redirect()->route('admin.insights.index')->with('success', 'Insight updated successfully.');
    }

    public function destroy(Insight $insight)
    {
        if (auth()->user()->isBlogger() && $insight->user_id !== auth()->id()) {
            abort(403, 'Anda hanya dapat menghapus insight milik Anda sendiri.');
        }

        if ($insight->image && !str_starts_with($insight->image, 'http') && Storage::disk('public')->exists($insight->image)) {
            Storage::disk('public')->delete($insight->image);
        }

        $insight->delete();

        return redirect()->route('admin.insights.index')->with('success', 'Insight deleted successfully.');
    }
    /**
     * Search products for the insight text editor modal
     */
    public function searchProducts(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Fetch top 5 active products matching the search
        $products = \App\Models\Product::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'name', 'slug']);

        // Append the actual frontend URL so JavaScript doesn't have to guess
        $products->transform(function ($product) {
            $product->url = $product->detail_url;
            return $product;
        });

        return response()->json($products);
    }

    /**
     * Search insights for the insight text editor modal
     */
    public function searchInsights(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $insights = Insight::where('title', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'title', 'slug', 'status', 'published_at']);

        // Append the actual frontend URL
        $insights->transform(function ($insight) {
            $insight->url = route('insight.show', $insight->slug);
            return $insight;
        });

        return response()->json($insights);
    }
}

