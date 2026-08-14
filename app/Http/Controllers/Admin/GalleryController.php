<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of galleries.
     */
    public function index()
    {
        $galleries = Gallery::ordered()->paginate(12)->withQueryString();
        return view('admin.galleries.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new gallery.
     */
    public function create()
    {
        return view('admin.galleries.create');
    }

    /**
     * Store a newly created gallery.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:image,video',
            'image' => 'required_if:type,image|nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'embed_url' => 'required_if:type,video|nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'Judul wajib diisi.',
            'type.required' => 'Tipe wajib dipilih.',
            'image.required_if' => 'Gambar wajib diupload untuk tipe gambar.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
            'embed_url.required_if' => 'URL embed wajib diisi untuk tipe video.',
        ]);

        $gallery = new Gallery();
        $gallery->title = $validated['title'];
        $gallery->description = $validated['description'] ?? null;
        $gallery->type = $validated['type'];
        $gallery->sort_order = $validated['sort_order'] ?? 0;
        $gallery->is_active = $request->boolean('is_active', true);

        if ($request->type === 'image' && $request->hasFile('image')) {
            $gallery->image = $request->file('image')->store('galleries', 'public');
        }

        if ($request->type === 'video') {
            $gallery->embed_url = $validated['embed_url'];
        }

        $gallery->save();

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galeri berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified gallery.
     */
    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    /**
     * Update the specified gallery.
     */
    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:image,video',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'embed_url' => 'required_if:type,video|nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $gallery->title = $validated['title'];
        $gallery->description = $validated['description'] ?? null;
        $gallery->type = $validated['type'];
        $gallery->sort_order = $validated['sort_order'] ?? 0;
        $gallery->is_active = $request->boolean('is_active', true);

        if ($request->type === 'image' && $request->hasFile('image')) {
            // Delete old image
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
            }
            $gallery->image = $request->file('image')->store('galleries', 'public');
        }

        if ($request->type === 'video') {
            $gallery->embed_url = $validated['embed_url'];
            // Remove image if switching to video
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
                $gallery->image = null;
            }
        } else {
            $gallery->embed_url = null;
        }

        $gallery->save();

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galeri berhasil diupdate.');
    }

    /**
     * Remove the specified gallery.
     */
    public function destroy(Gallery $gallery)
    {
        // Delete image file if exists
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }

        $gallery->delete();

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galeri berhasil dihapus.');
    }

    /**
     * Toggle gallery status.
     */
    public function toggle(Gallery $gallery)
    {
        $gallery->is_active = !$gallery->is_active;
        $gallery->save();

        return back()->with('success', 'Status galeri berhasil diubah.');
    }
}
