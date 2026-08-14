<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    /**
     * Store testimonial
     */
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canGiveTestimonial()) {
            return back()->with('error', 'Anda tidak dapat memberikan testimoni untuk pesanan ini.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'rating.required' => 'Rating wajib dipilih.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'content.required' => 'Testimoni wajib diisi.',
            'content.min' => 'Testimoni minimal 10 karakter.',
            'content.max' => 'Testimoni maksimal 500 karakter.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar: jpeg, png, jpg, webp.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'rating' => $validated['rating'],
            'content' => $validated['content'],
            'is_approved' => false,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('testimonials', 'public');
        }

        Testimonial::create($data);

        return back()->with('success', 'Terima kasih atas testimoni Anda. Testimoni akan ditampilkan setelah disetujui admin.');
    }

    /**
     * Update testimonial
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        // Check ownership
        if ($testimonial->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'rating.required' => 'Rating wajib dipilih.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'content.required' => 'Testimoni wajib diisi.',
            'content.min' => 'Testimoni minimal 10 karakter.',
            'content.max' => 'Testimoni maksimal 500 karakter.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar: jpeg, png, jpg, webp.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data = [
            'rating' => $validated['rating'],
            'content' => $validated['content'],
            'is_approved' => false, // Reset approval when edited
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }
            $data['image'] = $request->file('image')->store('testimonials', 'public');
        }

        $testimonial->update($data);

        return back()->with('success', 'Testimoni berhasil diperbarui. Testimoni akan ditampilkan setelah disetujui admin.');
    }
}
