<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandCatalogController extends Controller
{
    public function index()
    {
        $catalogs = BrandCatalog::ordered()->paginate(12)->withQueryString();
        return view('admin.brand-catalogs.index', compact('catalogs'));
    }

    public function create()
    {
        return view('admin.brand-catalogs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brand_catalogs,slug',
            'description' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_skincares' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_shoes' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_accessories' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_bags' => 'nullable|file|mimes:pdf|max:10240',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'brand_name.required' => 'Nama brand wajib diisi.',
            'pdf_file.mimes' => 'File harus berupa PDF.',
            'pdf_file.max' => 'Ukuran PDF maksimal 10MB.',
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $catalog = new BrandCatalog();
        $catalog->brand_name = $validated['brand_name'];
        $catalog->slug = $validated['slug'] ?? Str::slug($validated['brand_name']);
        $catalog->description = $validated['description'] ?? null;
        $catalog->sort_order = $validated['sort_order'] ?? 0;
        $catalog->is_active = $request->boolean('is_active', true);

        if ($request->hasFile('pdf_file')) {
            $catalog->pdf_path = $request->file('pdf_file')->store('catalogs/pdf', 'public');
        }

        $pdfFiles = [];
        foreach (array_keys(BrandCatalog::$categories) as $cat) {
            $inputName = 'pdf_' . $cat;
            if ($request->hasFile($inputName)) {
                $pdfFiles[$cat] = $request->file($inputName)->store('catalogs/pdf', 'public');
            }
        }
        $catalog->pdf_files = $pdfFiles;

        if ($request->hasFile('cover_image')) {
            $catalog->cover_image = $request->file('cover_image')->store('catalogs/covers', 'public');
        }

        $catalog->save();

        return redirect()->route('admin.brand-catalogs.index')
            ->with('success', 'Brand catalog berhasil ditambahkan.');
    }

    public function edit(BrandCatalog $brandCatalog)
    {
        return view('admin.brand-catalogs.edit', compact('brandCatalog'));
    }

    public function update(Request $request, BrandCatalog $brandCatalog)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brand_catalogs,slug,' . $brandCatalog->id,
            'description' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_skincares' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_shoes' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_accessories' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_bags' => 'nullable|file|mimes:pdf|max:10240',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'brand_name.required' => 'Nama brand wajib diisi.',
            'pdf_file.mimes' => 'File harus berupa PDF.',
            'pdf_file.max' => 'Ukuran PDF maksimal 10MB.',
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $brandCatalog->brand_name = $validated['brand_name'];
        $brandCatalog->slug = $validated['slug'] ?? Str::slug($validated['brand_name']);
        $brandCatalog->description = $validated['description'] ?? null;
        $brandCatalog->sort_order = $validated['sort_order'] ?? 0;
        $brandCatalog->is_active = $request->boolean('is_active', true);

        if ($request->hasFile('pdf_file')) {
            if ($brandCatalog->pdf_path) {
                Storage::disk('public')->delete($brandCatalog->pdf_path);
            }
            $brandCatalog->pdf_path = $request->file('pdf_file')->store('catalogs/pdf', 'public');
        }

        $pdfFiles = $brandCatalog->pdf_files ?? [];
        foreach (array_keys(BrandCatalog::$categories) as $cat) {
            $inputName = 'pdf_' . $cat;
            if ($request->hasFile($inputName)) {
                if (!empty($pdfFiles[$cat])) {
                    Storage::disk('public')->delete($pdfFiles[$cat]);
                }
                $pdfFiles[$cat] = $request->file($inputName)->store('catalogs/pdf', 'public');
            }
        }
        $brandCatalog->pdf_files = $pdfFiles;

        if ($request->hasFile('cover_image')) {
            if ($brandCatalog->cover_image) {
                Storage::disk('public')->delete($brandCatalog->cover_image);
            }
            $brandCatalog->cover_image = $request->file('cover_image')->store('catalogs/covers', 'public');
        }

        $brandCatalog->save();

        return redirect()->route('admin.brand-catalogs.index')
            ->with('success', 'Brand catalog berhasil diupdate.');
    }

    public function destroy(BrandCatalog $brandCatalog)
    {
        if ($brandCatalog->pdf_path) {
            Storage::disk('public')->delete($brandCatalog->pdf_path);
        }
        foreach ($brandCatalog->pdf_files ?? [] as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
        if ($brandCatalog->cover_image) {
            Storage::disk('public')->delete($brandCatalog->cover_image);
        }

        $brandCatalog->delete();

        return redirect()->route('admin.brand-catalogs.index')
            ->with('success', 'Brand catalog berhasil dihapus.');
    }

    public function toggle(BrandCatalog $brandCatalog)
    {
        $brandCatalog->is_active = !$brandCatalog->is_active;
        $brandCatalog->save();

        return back()->with('success', 'Status brand catalog berhasil diubah.');
    }
}
