@extends('layouts.admin')

@section('title', 'Tambah Brand Catalog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-plus me-2"></i>Tambah Brand Catalog
    </h4>
    <a href="{{ route('admin.brand-catalogs.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.brand-catalogs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Nama Brand <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('brand_name') is-invalid @enderror"
                               name="brand_name" value="{{ old('brand_name') }}" required>
                        @error('brand_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                               name="slug" value="{{ old('slug') }}" placeholder="auto-generate jika kosong">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File Katalog PDF (Legacy)</label>
                        <input type="file" class="form-control @error('pdf_file') is-invalid @enderror"
                               name="pdf_file" accept=".pdf">
                        <div class="form-text">Format: PDF. Maks 10MB. (Opsional, gunakan PDF per kategori di bawah)</div>
                        @error('pdf_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3"><i class="fas fa-file-pdf text-danger me-2"></i>PDF per Kategori</h6>

                    @foreach(\App\Models\BrandCatalog::$categories as $key => $label)
                    <div class="mb-3">
                        <label class="form-label">{{ $label }}</label>
                        <input type="file" class="form-control @error("pdf_{$key}") is-invalid @enderror"
                               name="pdf_{{ $key }}" accept=".pdf">
                        <div class="form-text">Format: PDF. Maks 10MB.</div>
                        @error("pdf_{$key}")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endforeach

                    <div class="mb-3">
                        <label class="form-label">Gambar Cover</label>
                        <input type="file" class="form-control @error('cover_image') is-invalid @enderror"
                               name="cover_image" accept="image/*" id="coverInput">
                        <div class="form-text">Format: JPG, PNG, WEBP. Maks 5MB.</div>
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div id="coverPreview" class="mt-2 d-none">
                            <img id="previewImg" src="" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                       name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                <div class="form-text">Angka kecil tampil lebih dulu</div>
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active"
                                           id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Simpan
                </button>
                <a href="{{ route('admin.brand-catalogs.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const coverInput = document.getElementById('coverInput');
    const coverPreview = document.getElementById('coverPreview');
    const previewImg = document.getElementById('previewImg');

    coverInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                coverPreview.classList.remove('d-none');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
@endpush
@endsection
