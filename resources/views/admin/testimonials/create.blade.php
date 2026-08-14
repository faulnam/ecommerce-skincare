@extends('layouts.admin')

@section('page-title', 'Tambah Testimoni / Review')

@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.testimonials.index') }}">
            <i class="fas fa-images me-1"></i>Testimoni Gambar <small class="text-muted">(home)</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.reviews.index') }}">
            <i class="fas fa-comments me-1"></i>Review Produk <small class="text-muted">(detail)</small>
        </a>
    </li>
</ul>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-plus me-2"></i>Tambah Baru
            </div>
            <div class="card-body">
                <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data" id="createForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Tipe <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeImage" value="image" checked onchange="toggleType()">
                                <label class="form-check-label" for="typeImage">
                                    <i class="fas fa-images me-1"></i>Testimoni Gambar (tampil di home)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeReview" value="review" onchange="toggleType()">
                                <label class="form-check-label" for="typeReview">
                                    <i class="fas fa-comment me-1"></i>Review Produk (tampil di detail)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Image Fields -->
                    <div id="imageFields">
                        <div class="mb-3">
                            <label for="images" class="form-label">Gambar Testimoni <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" accept="image/*" multiple>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: JPG, PNG, WEBP. Maksimal 2MB per gambar (maks 3).</small>
                            <div class="mt-3 row g-2" id="imagePreview" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Review Fields -->
                    <div id="reviewFields" style="display: none;">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Produk <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                                <option value="">Pilih Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Pilih User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                            <select name="rating" id="rating" class="form-select @error('rating') is-invalid @enderror">
                                <option value="">Pilih Rating</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} Bintang</option>
                                @endfor
                            </select>
                            @error('rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">Komentar <span class="text-danger">*</span></label>
                            <textarea name="comment" id="comment" rows="4" class="form-control @error('comment') is-invalid @enderror" placeholder="Tulis review minimal 10 karakter..."></textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="quality_rating" class="form-label">Quality Rating (0-100)</label>
                                <input type="number" name="quality_rating" id="quality_rating" class="form-control @error('quality_rating') is-invalid @enderror" min="0" max="100" placeholder="0-100">
                                @error('quality_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sizing_rating" class="form-label">Sizing Rating (0-100)</label>
                                <input type="number" name="sizing_rating" id="sizing_rating" class="form-control @error('sizing_rating') is-invalid @enderror" min="0" max="100" placeholder="0-100">
                                @error('sizing_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="usual_size" class="form-label">Usual Size</label>
                                <input type="text" name="usual_size" id="usual_size" class="form-control @error('usual_size') is-invalid @enderror" maxlength="10" placeholder="e.g. 40, M, L">
                                @error('usual_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_verified" id="is_verified" value="1">
                            <label class="form-check-label" for="is_verified">Verified Buyer</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleType() {
    const isImage = document.getElementById('typeImage').checked;
    document.getElementById('imageFields').style.display = isImage ? 'block' : 'none';
    document.getElementById('reviewFields').style.display = isImage ? 'none' : 'block';

    // Toggle required attributes
    document.getElementById('images').required = isImage;
    document.getElementById('product_id').required = !isImage;
    document.getElementById('user_id').required = !isImage;
    document.getElementById('rating').required = !isImage;
    document.getElementById('comment').required = !isImage;
}

document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    const files = Array.from(e.target.files || []).slice(0, 3);

    if (!files.length) {
        preview.style.display = 'none';
        return;
    }

    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4';
            col.innerHTML = `<img src="${event.target.result}" alt="Preview" class="img-fluid rounded" style="max-height: 220px;">`;
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
    preview.style.display = 'flex';
});
</script>
@endpush
