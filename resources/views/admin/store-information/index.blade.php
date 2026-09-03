@extends('layouts.admin')

@section('page-title', 'Informasi Toko')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Informasi Toko
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- 1. Hero Banners -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-image text-primary me-2"></i> Banner Utama (Hero Section)</h5>
                <p class="text-muted small mt-1">Mengelola banner besar yang tampil di bagian atas halaman utama.</p>
            </div>
            <div class="card-body">
                @if($heroBanners->count() > 0)
                    <div class="row g-4">
                        @foreach($heroBanners as $banner)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100 position-relative">
                                    <form action="{{ route('admin.store-information.banner.update', $banner) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-3 text-center">
                                            <img src="{{ $banner->image_url }}" alt="Hero Banner" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Ganti Gambar</label>
                                            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                            <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Judul (Title)</label>
                                            <input type="text" name="title" class="form-control form-control-sm" value="{{ $banner->title }}" placeholder="Teks besar di tengah banner">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Teks Tombol</label>
                                            <input type="text" name="button_text" class="form-control form-control-sm" value="{{ $banner->button_text }}" placeholder="Contoh: SHOP NOW">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Link Tombol</label>
                                            <input type="text" name="link" class="form-control form-control-sm" value="{{ $banner->link }}" placeholder="Contoh: /produk">
                                        </div>
                                        
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="heroActive{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="heroActive{{ $banner->id }}">Aktifkan Banner</label>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">Belum ada banner tipe 'hero' di database.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. Split Banners (Model Images) -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-columns text-primary me-2"></i> Gambar Model (Split Banners)</h5>
                <p class="text-muted small mt-1">Mengelola gambar-gambar model yang sejajar di bawah kategori.</p>
            </div>
            <div class="card-body">
                @if($splitBanners->count() > 0)
                    <div class="row g-4">
                        @foreach($splitBanners as $banner)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100 position-relative">
                                    <form action="{{ route('admin.store-information.banner.update', $banner) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-3 text-center">
                                            <img src="{{ $banner->image_url }}" alt="Split Banner" class="img-fluid rounded" style="max-height: 300px; object-fit: cover;">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Ganti Gambar</label>
                                            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                            <small class="text-muted">Rasio ideal 3:4 (Portrait).</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Judul (Title)</label>
                                            <input type="text" name="title" class="form-control form-control-sm" value="{{ $banner->title }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Teks Tombol</label>
                                            <input type="text" name="button_text" class="form-control form-control-sm" value="{{ $banner->button_text }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Link Tombol</label>
                                            <input type="text" name="link" class="form-control form-control-sm" value="{{ $banner->link }}" placeholder="Contoh: /kategori/skincare-skincare">
                                        </div>
                                        
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="splitActive{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="splitActive{{ $banner->id }}">Aktifkan Banner</label>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">Belum ada banner tipe 'split' di database.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
