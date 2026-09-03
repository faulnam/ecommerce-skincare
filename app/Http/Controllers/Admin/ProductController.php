<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Generate unique slug for product
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Check if slug exists (excluding current product if editing)
        $existsQuery = function($checkSlug) use ($excludeId) {
            $query = Product::where('slug', $checkSlug);
            if ($excludeId !== null) {
                $query->where('id', '!=', $excludeId);
            }
            return $query->exists();
        };

        while ($existsQuery($slug)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Display products list
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $query->orderBy('created_at', 'desc');

        $products = $query->paginate(10)->withQueryString();

        // Simpan URL index beserta query params ke session untuk redirect kembali setelah edit/create
        session(['admin_products_url' => $request->fullUrl()]);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $specifications = \App\Models\Specification::active()
            ->orderBy('name')
            ->get()
            ->groupBy('category');
            
        return view('admin.products.create', compact('specifications'));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $isFeatured = $request->boolean('is_featured');

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => $request->boolean('has_variants') ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'stock' => $request->boolean('has_variants') ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'category' => 'required|in:skincare,new-arrivals,accessories',
            'package_type' => 'nullable|in:single,bundle',
            'bundle_type' => 'nullable|string',
            'weight' => 'nullable|integer|min:0|max:50000',
            'package_weight' => 'nullable|integer|min:0|max:50000',
            'brand' => 'nullable|string',
            'series' => 'nullable|string',
            'shape' => 'nullable|string',
            'balance' => 'nullable|string',
            'skincare_weight' => 'nullable|string',
            'play_style' => 'nullable|string',
            'core' => 'nullable|string',
            'carbon_type' => 'nullable|string',
            'surface' => 'nullable|string',
            'feel' => 'nullable|string',
            'power' => 'nullable|string',
            'control' => 'nullable|string',
            'maneuverability' => 'nullable|string',
            'comfort' => 'nullable|string',
            'image' => $request->boolean('has_variants') ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->filled('discount_start') && $request->filled('discount_end')) {
                $start = \Carbon\Carbon::parse($request->discount_start);
                $end = \Carbon\Carbon::parse($request->discount_end);
                if ($start->diffInYears($end) >= 10 && $start->copy()->addYears(10)->lt($end)) {
                    $validator->errors()->add('discount_end', 'Masa diskon tidak boleh lebih dari 10 tahun.');
                }
            }
        });

        $validated = $validator->validate();

        // Upload images
        $imagePath = $request->hasFile('image') ? ($request->file('image')->store('products', 'public') ?: null) : null;
        $image2Path = $request->hasFile('image_2') ? ($request->file('image_2')->store('products', 'public') ?: null) : null;
        $image3Path = $request->hasFile('image_3') ? ($request->file('image_3')->store('products', 'public') ?: null) : null;
        $image4Path = $request->hasFile('image_4') ? ($request->file('image_4')->store('products', 'public') ?: null) : null;

        if ($isFeatured) {
            Product::where('category', $validated['category'])
                ->where('is_featured', true)
                ->update(['is_featured' => false]);
        }

        $hasVariants = $request->boolean('has_variants');

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name']),
            'description' => $validated['description'],
            'price' => $hasVariants ? 0 : ($validated['price'] ?? 0),
            'discount_percent' => $hasVariants ? 0 : ($validated['discount_percent'] ?? 0),
            'discount_start' => $validated['discount_start'] ?? null,
            'discount_end' => $validated['discount_end'] ?? null,
            'stock' => $hasVariants ? 0 : ($validated['stock'] ?? 0),
            'category' => $validated['category'],
            'package_type' => $validated['package_type'] ?? 'single',
            'bundle_type' => $validated['bundle_type'] ?? null,
            'weight' => $validated['weight'] ?? 500,
            'package_weight' => $validated['package_weight'] ?? null,
            'brand' => $validated['category'] === 'skincare' ? ($validated['brand'] ?? null) : null,
            'series' => $validated['category'] === 'skincare' ? ($validated['series'] ?? null) : null,
            'shape' => $validated['category'] === 'skincare' ? ($validated['shape'] ?? null) : null,
            'balance' => $validated['category'] === 'skincare' ? ($validated['balance'] ?? null) : null,
            'skincare_weight' => $validated['category'] === 'skincare' ? ($validated['skincare_weight'] ?? null) : null,
            'play_style' => $validated['category'] === 'skincare' ? ($validated['play_style'] ?? null) : null,
            'core' => $validated['category'] === 'skincare' ? ($validated['core'] ?? null) : null,
            'carbon_type' => $validated['category'] === 'skincare' ? ($validated['carbon_type'] ?? null) : null,
            'surface' => $validated['category'] === 'skincare' ? ($validated['surface'] ?? null) : null,
            'feel' => $validated['category'] === 'skincare' ? ($validated['feel'] ?? null) : null,
            'power' => $validated['category'] === 'skincare' ? ($validated['power'] ?? null) : null,
            'control' => $validated['category'] === 'skincare' ? ($validated['control'] ?? null) : null,
            'maneuverability' => $validated['category'] === 'skincare' ? ($validated['maneuverability'] ?? null) : null,
            'comfort' => $validated['category'] === 'skincare' ? ($validated['comfort'] ?? null) : null,
            'image' => $hasVariants ? null : ($imagePath ?? null),
            'image_2' => $hasVariants ? null : $image2Path,
            'image_3' => $hasVariants ? null : $image3Path,
            'image_4' => $hasVariants ? null : $image4Path,
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $isFeatured,
            'has_variants' => $hasVariants,
        ]);

        $this->saveVariants($request, $product);

        $redirectUrl = session('admin_products_url', route('admin.products.index'));

        return redirect($redirectUrl)->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Show product detail
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show edit form
     */
    public function edit(Product $product)
    {
        $specifications = \App\Models\Specification::active()
            ->orderBy('name')
            ->get()
            ->groupBy('category');
            
        return view('admin.products.edit', compact('product', 'specifications'));
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        $isFeatured = $request->boolean('is_featured');

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => $request->boolean('has_variants') ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'stock' => $request->boolean('has_variants') ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'category' => 'required|in:skincare,new-arrivals,accessories',
            'package_type' => 'nullable|in:single,bundle',
            'bundle_type' => 'nullable|string',
            'weight' => 'nullable|integer|min:0|max:50000',
            'package_weight' => 'nullable|integer|min:0|max:50000',
            'brand' => 'nullable|string',
            'series' => 'nullable|string',
            'shape' => 'nullable|string',
            'balance' => 'nullable|string',
            'skincare_weight' => 'nullable|string',
            'play_style' => 'nullable|string',
            'core' => 'nullable|string',
            'carbon_type' => 'nullable|string',
            'surface' => 'nullable|string',
            'feel' => 'nullable|string',
            'power' => 'nullable|string',
            'control' => 'nullable|string',
            'maneuverability' => 'nullable|string',
            'comfort' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->filled('discount_start') && $request->filled('discount_end')) {
                $start = \Carbon\Carbon::parse($request->discount_start);
                $end = \Carbon\Carbon::parse($request->discount_end);
                if ($start->diffInYears($end) >= 10 && $start->copy()->addYears(10)->lt($end)) {
                    $validator->errors()->add('discount_end', 'Masa diskon tidak boleh lebih dari 10 tahun.');
                }
            }
        });

        $validated = $validator->validate();

        $product->name = $validated['name'];
        $product->description = $validated['description'];
        $hasVariants = $request->boolean('has_variants');

        if ($hasVariants) {
            $product->price = 0;
            $product->discount_percent = 0;
            $product->discount_start = $request->discount_start ?? null;
            $product->discount_end = $request->discount_end ?? null;
            $product->stock = 0;
        } else {
            $product->price = $validated['price'] ?? 0;
            $product->discount_percent = $request->discount_percent ?? 0;
            $product->discount_start = $request->discount_start ?? null;
            $product->discount_end = $request->discount_end ?? null;
            $product->stock = $validated['stock'] ?? 0;
        }
        $product->category = $validated['category'];
        $product->package_type = $validated['package_type'] ?? 'single';
        $product->bundle_type = $validated['bundle_type'] ?? null;
        $product->weight = $validated['weight'] ?? 500;
        $product->package_weight = $validated['package_weight'] ?? null;

        if ($product->category === 'skincare') {
            $product->brand = $validated['brand'] ?? null;
            $product->series = $validated['series'] ?? null;
            $product->shape = $validated['shape'] ?? null;
            $product->balance = $validated['balance'] ?? null;
            $product->skincare_weight = $validated['skincare_weight'] ?? null;
            $product->play_style = $validated['play_style'] ?? null;
            $product->core = $validated['core'] ?? null;
            $product->carbon_type = $validated['carbon_type'] ?? null;
            $product->surface = $validated['surface'] ?? null;
            $product->feel = $validated['feel'] ?? null;
            $product->power = $validated['power'] ?? null;
            $product->control = $validated['control'] ?? null;
            $product->maneuverability = $validated['maneuverability'] ?? null;
            $product->comfort = $validated['comfort'] ?? null;
        } else {
            $product->brand = null;
            $product->series = null;
            $product->shape = null;
            $product->balance = null;
            $product->skincare_weight = null;
            $product->play_style = null;
            $product->core = null;
            $product->carbon_type = null;
            $product->surface = null;
            $product->feel = null;
            $product->power = null;
            $product->control = null;
            $product->maneuverability = null;
            $product->comfort = null;
        }
        $product->is_active = $request->boolean('is_active', true);

        $newFeatured = $request->boolean('is_featured');
        if ($newFeatured && !$product->is_featured) {
            Product::where('category', $product->category)
                ->where('id', '!=', $product->id)
                ->where('is_featured', true)
                ->update(['is_featured' => false]);
        }
        $product->is_featured = $newFeatured;

        $oldName = trim($product->getOriginal('name'));
        if ($oldName !== trim($validated['name'])) {
            $product->slug = $this->generateUniqueSlug($validated['name'], $product->id);
        }

        // Handle image updates
        if ($hasVariants) {
            // Delete old main images if they exist
            if ($product->image) Storage::disk('public')->delete($product->image);
            if ($product->image_2) Storage::disk('public')->delete($product->image_2);
            if ($product->image_3) Storage::disk('public')->delete($product->image_3);
            if ($product->image_4) Storage::disk('public')->delete($product->image_4);

            $product->image = null;
            $product->image_2 = null;
            $product->image_3 = null;
            $product->image_4 = null;
        } else {
            if ($request->hasFile('image')) {
                if ($product->image) Storage::disk('public')->delete($product->image);
                $product->image = $request->file('image')->store('products', 'public') ?: null;
            }

            if ($request->hasFile('image_2')) {
                if ($product->image_2) Storage::disk('public')->delete($product->image_2);
                $product->image_2 = $request->file('image_2')->store('products', 'public') ?: null;
            }

            if ($request->hasFile('image_3')) {
                if ($product->image_3) Storage::disk('public')->delete($product->image_3);
                $product->image_3 = $request->file('image_3')->store('products', 'public') ?: null;
            }

            if ($request->hasFile('image_4')) {
                if ($product->image_4) Storage::disk('public')->delete($product->image_4);
                $product->image_4 = $request->file('image_4')->store('products', 'public') ?: null;
            }
        }

        $product->has_variants = $request->boolean('has_variants');
        $product->save();

        $this->saveVariants($request, $product);

        $redirectUrl = session('admin_products_url', route('admin.products.index'));

        return redirect($redirectUrl)->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Handle product variants saving
     */
    private function saveVariants(Request $request, Product $product)
    {
        if (!$request->boolean('has_variants')) {
            // Delete all variants if turned off
            foreach ($product->variants as $variant) {
                if ($variant->image) Storage::disk('public')->delete($variant->image);
                if ($variant->image_2) Storage::disk('public')->delete($variant->image_2);
                if ($variant->image_3) Storage::disk('public')->delete($variant->image_3);
                if ($variant->image_4) Storage::disk('public')->delete($variant->image_4);
                $variant->delete();
            }
            return;
        }

        $submittedVariants = $request->input('variants', []);
        $existingVariantIds = $product->variants->pluck('id')->toArray();
        $submittedVariantIds = collect($submittedVariants)->pluck('id')->filter()->toArray();

        // Delete variants that were removed
        $variantsToDelete = array_diff($existingVariantIds, $submittedVariantIds);
        if (!empty($variantsToDelete)) {
            $variantsToDeleteModels = ProductVariant::whereIn('id', $variantsToDelete)->get();
            foreach ($variantsToDeleteModels as $variant) {
                if ($variant->image) Storage::disk('public')->delete($variant->image);
                if ($variant->image_2) Storage::disk('public')->delete($variant->image_2);
                if ($variant->image_3) Storage::disk('public')->delete($variant->image_3);
                if ($variant->image_4) Storage::disk('public')->delete($variant->image_4);
                $variant->delete();
            }
        }

        // Process submitted variants
        $totalStock = 0;
        foreach ($submittedVariants as $index => $vData) {
            $variant = null;
            if (!empty($vData['id'])) {
                $variant = ProductVariant::find($vData['id']);
            }

            if (!$variant) {
                $variant = new ProductVariant();
                $variant->product_id = $product->id;
            }

            $variant->name = $vData['name'];
            $variant->stock = (int) ($vData['stock'] ?? 0);
            $variant->price = isset($vData['price']) && $vData['price'] !== '' ? (float) $vData['price'] : null;
            $variant->discount_percent = (float) ($vData['discount_percent'] ?? 0);
            $variant->discount_start = $request->filled('discount_start') ? $request->input('discount_start') : null;
            $variant->discount_end = $request->filled('discount_end') ? $request->input('discount_end') : null;
            $variant->sort_order = $index;
            $variant->is_active = true;
            
            $totalStock += $variant->stock;

            // Handle image uploads if they exist in request files
            if ($request->hasFile("variants.{$index}.image")) {
                if ($variant->image) Storage::disk('public')->delete($variant->image);
                $variant->image = $request->file("variants.{$index}.image")->store('products/variants', 'public');
            }
            if ($request->hasFile("variants.{$index}.image_2")) {
                if ($variant->image_2) Storage::disk('public')->delete($variant->image_2);
                $variant->image_2 = $request->file("variants.{$index}.image_2")->store('products/variants', 'public');
            }
            if ($request->hasFile("variants.{$index}.image_3")) {
                if ($variant->image_3) Storage::disk('public')->delete($variant->image_3);
                $variant->image_3 = $request->file("variants.{$index}.image_3")->store('products/variants', 'public');
            }
            if ($request->hasFile("variants.{$index}.image_4")) {
                if ($variant->image_4) Storage::disk('public')->delete($variant->image_4);
                $variant->image_4 = $request->file("variants.{$index}.image_4")->store('products/variants', 'public');
            }

            $variant->save();
        }

        // Update product stock based on variants
        $product->update(['stock' => $totalStock]);
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        // Delete variant images
        foreach ($product->variants as $variant) {
            if ($variant->image) Storage::disk('public')->delete($variant->image);
            if ($variant->image_2) Storage::disk('public')->delete($variant->image_2);
            if ($variant->image_3) Storage::disk('public')->delete($variant->image_3);
            if ($variant->image_4) Storage::disk('public')->delete($variant->image_4);
        }

        // Delete all images
        if ($product->image) Storage::disk('public')->delete($product->image);
        if ($product->image_2) Storage::disk('public')->delete($product->image_2);
        if ($product->image_3) Storage::disk('public')->delete($product->image_3);
        if ($product->image_4) Storage::disk('public')->delete($product->image_4);

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Produk berhasil {$status}.");
    }

    /**
     * Toggle product featured status
     */
    public function toggleFeatured(Product $product)
    {
        if (!$product->is_featured) {
            // Unfeature other products in the same category
            Product::where('category', $product->category)
                ->where('id', '!=', $product->id)
                ->where('is_featured', true)
                ->update(['is_featured' => false]);
        }

        $product->update(['is_featured' => !$product->is_featured]);

        $status = $product->is_featured ? 'dijadikan highlight' : 'dihapus dari highlight';

        return back()->with('success', "Produk berhasil {$status}.");
    }
}
