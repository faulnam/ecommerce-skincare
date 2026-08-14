@extends('layouts.admin')

@section('title', 'Tambah Filter')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-plus me-2"></i>Tambah Filter
    </h4>
    <a href="{{ route('admin.filters.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.filters.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required onchange="toggleNewCategory(this)">
                    <option value="" disabled selected>Pilih Kategori...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                    <option value="new" {{ old('category') == 'new' ? 'selected' : '' }}>+ Buat Kategori Baru</option>
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3" id="new_category_div" style="display: {{ old('category') == 'new' ? 'block' : 'none' }};">
                <label for="new_category" class="form-label">Nama Kategori Baru <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('new_category') is-invalid @enderror" id="new_category" name="new_category" value="{{ old('new_category') }}" placeholder="Contoh: Warna, Ukuran, dll">
                @error('new_category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nama Opsi / Value <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Merah, XL, Nox, dll">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
                <div class="form-text">Jika nonaktif, opsi ini tidak akan muncul saat membuat/mengedit produk.</div>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-secondary me-2">Reset</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Simpan Filter
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleNewCategory(select) {
        const newCategoryDiv = document.getElementById('new_category_div');
        const newCategoryInput = document.getElementById('new_category');
        
        if (select.value === 'new') {
            newCategoryDiv.style.display = 'block';
            newCategoryInput.setAttribute('required', 'required');
        } else {
            newCategoryDiv.style.display = 'none';
            newCategoryInput.removeAttribute('required');
        }
    }
    
    // Run on load in case of validation errors
    document.addEventListener('DOMContentLoaded', function() {
        toggleNewCategory(document.getElementById('category'));
    });
</script>
@endpush
