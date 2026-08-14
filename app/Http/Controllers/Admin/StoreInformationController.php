<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreInformationController extends Controller
{
    public function index()
    {
        $heroBanners = Banner::where('type', 'hero')->orderBy('sort_order')->get();
        $splitBanners = Banner::where('type', 'split')->orderBy('sort_order')->get();

        return view('admin.store-information.index', compact('heroBanners', 'splitBanners'));
    }

    public function updateBanner(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'button_text', 'link']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada dan bukan dari URL external
            if ($banner->image && !str_starts_with($banner->image, 'http')) {
                Storage::disk('public')->delete($banner->image);
            }

            // Upload gambar baru
            $path = $request->file('image')->store('banners', 'public');
            $data['image'] = $path;
        }

        $banner->update($data);

        return redirect()->back()->with('success', 'Banner berhasil diperbarui!');
    }
}
