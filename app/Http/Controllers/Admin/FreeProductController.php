<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FreeProductController extends Controller
{
    /**
     * Display a listing of the free products.
     */
    public function index()
    {
        $products = Product::where('is_free_event', true)->latest()->get();
        $eventActive = Setting::where('key', 'free_event_active')->value('value') ?? '1';
        $eventTitle = Setting::where('key', 'free_event_title')->value('value') ?? 'Pilihan Produk Gratis 🎁';
        $eventDescription = Setting::where('key', 'free_event_description')->value('value') ?? 'Untuk pembelian pertama satu akun';
        $eventImage = Setting::where('key', 'free_event_image')->value('value') ?? null;
        $eventErrorMessage = Setting::where('key', 'free_event_error_message')->value('value') ?? 'Produk gratis hanya bisa di checkout oleh user yang sudah login. Silakan login terlebih dahulu.';
        return view('admin.free-products.index', compact('products', 'eventActive', 'eventTitle', 'eventDescription', 'eventImage', 'eventErrorMessage'));
    }

    /**
     * Update the free event settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'is_active' => 'required|in:1,0',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'error_message' => 'nullable|string|max:1000',
        ]);

        Setting::updateOrCreate(['key' => 'free_event_active'], ['value' => $request->is_active]);
        Setting::updateOrCreate(['key' => 'free_event_title'], ['value' => $request->title]);
        Setting::updateOrCreate(['key' => 'free_event_description'], ['value' => $request->description]);
        Setting::updateOrCreate(['key' => 'free_event_error_message'], ['value' => $request->error_message ?? 'Produk gratis hanya bisa di checkout oleh user yang sudah login. Silakan login terlebih dahulu.']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('free-events', 'public');
            $imageUrl = Storage::disk('public')->url($imagePath);
            Setting::updateOrCreate(['key' => 'free_event_image'], ['value' => $imageUrl]);
        }

        return redirect()->route('admin.free-products.index')->with('success', 'Pengaturan event berhasil diperbarui.');
    }

    /**
     * Show the form for creating a new free product event.
     */
    public function create(Request $request)
    {
        $query = Product::active()->where('is_free_event', false);
        
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        
        $availableProducts = $query->latest()->paginate(20);
        return view('admin.free-products.create', compact('availableProducts'));
    }

    /**
     * Store a newly created free product event in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        Product::whereIn('id', $request->product_ids)->update(['is_free_event' => true]);

        return redirect()->route('admin.free-products.index')->with('success', 'Produk berhasil ditambahkan ke Event Free Produk.');
    }

    /**
     * Remove the specified free product event from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_free_event' => false]);

        return redirect()->route('admin.free-products.index')->with('success', 'Produk berhasil dihapus dari Event Free Produk.');
    }
}
