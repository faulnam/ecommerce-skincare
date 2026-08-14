<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display testimonials list
     */
    public function index(Request $request)
    {
        $query = Testimonial::with('user', 'order');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $testimonials = $query->latest()->paginate(15)->withQueryString();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $products = Product::active()->select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        return view('admin.testimonials.create', compact('products', 'users'));
    }

    /**
     * Store new testimonial
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'image');

        if ($type === 'review') {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'user_id' => 'required|exists:users,id',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|min:10|max:1000',
                'quality_rating' => 'nullable|integer|min:0|max:100',
                'sizing_rating' => 'nullable|integer|min:0|max:100',
                'usual_size' => 'nullable|string|max:10',
                'is_verified' => 'nullable|boolean',
            ]);

            $user = User::find($validated['user_id']);
            Review::create([
                'product_id' => $validated['product_id'],
                'user_id' => $validated['user_id'],
                'reviewer_name' => $user?->name ?? 'Anonymous',
                'order_id' => null,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
                'quality_rating' => $validated['quality_rating'],
                'sizing_rating' => $validated['sizing_rating'],
                'usual_size' => $validated['usual_size'],
                'is_verified' => $validated['is_verified'] ?? false,
                'is_approved' => true,
            ]);

            return redirect()->route('admin.reviews.index')
                ->with('success', 'Review berhasil ditambahkan.');
        }

        // Default: image testimonial
        $validated = $request->validate([
            'images' => 'required|array|max:3',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $images = $request->file('images', []);

        foreach ($images as $image) {
            $imagePath = $image->store('testimonials', 'public');

            Testimonial::create([
                'user_id' => auth()->id(),
                'image' => $imagePath,
                'content' => '',
                'rating' => 5,
                'is_approved' => true,
            ]);
        }

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimoni berhasil ditambahkan.');
    }

    /**
     * Approve testimonial
     */
    public function approve(Testimonial $testimonial)
    {
        $testimonial->update(['is_approved' => true]);

        return back()->with('success', 'Testimoni berhasil disetujui dan akan ditampilkan di landing page.');
    }

    /**
     * Reject/hide testimonial
     */
    public function reject(Testimonial $testimonial)
    {
        $testimonial->update(['is_approved' => false]);

        return back()->with('success', 'Testimoni berhasil disembunyikan.');
    }

    /**
     * Delete testimonial
     */
    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return back()->with('success', 'Testimoni berhasil dihapus.');
    }
}
