@extends('layouts.admin')

@section('title', 'Tambah Galeri')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-plus me-2"></i>Tambah Galeri
    </h4>
    <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               name="title" value="{{ old('title') }}" required>
                        @error('title')
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
                        <label class="form-label">Tipe <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type" id="typeImage" value="image" {{ old('type', 'image') == 'image' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="typeImage">
                                <i class="fas fa-image me-1"></i>Gambar
                            </label>
                            
                            <input type="radio" class="btn-check" name="type" id="typeVideo" value="video" {{ old('type') == 'video' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger" for="typeVideo">
                                <i class="fab fa-instagram me-1"></i>Video Instagram
                            </label>
                        </div>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Image Upload (shown when type is image) -->
                    <div class="mb-3" id="imageField">
                        <label class="form-label">Upload Gambar <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               name="image" accept="image/*" id="imageInput">
                        <div class="form-text">Format: JPG, PNG, GIF. Maks 5MB.</div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-2 d-none">
                            <img id="previewImg" src="" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                    
                    <!-- Embed URL (shown when type is video) -->
                    <div class="mb-3 d-none" id="embedField">
                        <label class="form-label">URL Embed Instagram <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('embed_url') is-invalid @enderror" 
                                  name="embed_url" rows="4" placeholder="Paste kode embed dari Instagram di sini...">{{ old('embed_url') }}</textarea>
                        <div class="form-text">
                            Cara mendapatkan embed code:
                            <ol class="small mb-0 mt-1">
                                <li>Buka video di Instagram</li>
                                <li>Klik ikon titik tiga (...)</li>
                                <li>Pilih "Embed"</li>
                                <li>Copy seluruh kode embed dan paste di sini</li>
                            </ol>
                        </div>
                        @error('embed_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const imageField = document.getElementById('imageField');
    const embedField = document.getElementById('embedField');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    function toggleFields() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;
        
        if (selectedType === 'image') {
            imageField.classList.remove('d-none');
            embedField.classList.add('d-none');
        } else {
            imageField.classList.add('d-none');
            embedField.classList.remove('d-none');
        }
    }
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', toggleFields);
    });
    
    // Initial toggle
    toggleFields();
    
    // Image preview
    imageInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
@endpush
@endsection
