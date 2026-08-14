<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['product', 'user', 'order']);
        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        
        return back()->with('success', 'Review berhasil disetujui.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);
        
        return back()->with('success', 'Review berhasil ditolak.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        
        return back()->with('success', 'Review berhasil dihapus.');
    }
}
