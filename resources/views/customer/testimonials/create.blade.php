@extends('layouts.app')

@section('title', 'Write Testimonial - Hijab')

@section('content')
@php
    $jsonPath = public_path('translation/testicreate.json');
    $testTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-3 mb-lg-4">
                <ol class="breadcrumb breadcrumb-mobile">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ $testTrans['breadcrumb_home'][$lang] ?? 'Home' }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.orders.index') }}">{{ $testTrans['breadcrumb_orders'][$lang] ?? 'Orders' }}</a></li>
                    <li class="breadcrumb-item active">{{ $testTrans['breadcrumb_testimonial'][$lang] ?? 'Testimonial' }}</li>
                </ol>
            </nav>
            
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white card-header-mobile">
                    <h5 class="mb-0 card-title-mobile">
                        <i class="fas fa-star me-2"></i>{{ $testTrans['card_title'][$lang] ?? 'Write Testimonial' }}
                    </h5>
                </div>
                <div class="card-body p-3 p-lg-4">
                    <div class="alert alert-info py-2 alert-mobile">
                        <i class="fas fa-info-circle me-2"></i>
                        <span class="d-none d-sm-inline">{{ $testTrans['alert_thanks'][$lang] ?? 'Thank you for shopping at Hijab!' }} </span>{{ $testTrans['alert_share'][$lang] ?? 'Share your experience.' }}
                    </div>

                    <!-- Order Info -->
                    <div class="card bg-light mb-4">
                        <div class="card-body p-3">
                            <h6 class="card-title order-info-title">{{ $testTrans['order_details_title'][$lang] ?? 'Order Details' }}</h6>
                            <table class="table table-sm table-borderless mb-0 order-info-table">
                                <tr>
                                    <td class="order-info-label">{{ $testTrans['label_order_no'][$lang] ?? 'Order No.' }}</td>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="order-info-label">{{ $testTrans['label_date'][$lang] ?? 'Tanggal' }}</td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="order-info-label">{{ $testTrans['label_total'][$lang] ?? 'Total' }}</td>
                                    <td><strong class="text-success">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                            
                            <hr>
                            
                            <h6 class="order-info-title">{{ $testTrans['label_products'][$lang] ?? 'Produk:' }}</h6>
                            <ul class="list-unstyled mb-0 product-list">
                                @foreach($order->orderItems as $item)
                                <li>
                                    <i class="fas fa-check text-success me-2"></i>
                                    {{ $item->product->name ?? ($testTrans['product_unavailable'][$lang] ?? 'Produk tidak tersedia') }} (x{{ $item->quantity }})
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Testimonial Form -->
                    <form action="{{ route('customer.testimonials.store', $order) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="rating" class="form-label fw-bold">{{ $testTrans['label_rating'][$lang] ?? 'Rating' }} <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                <div class="btn-group btn-group-mobile" role="group">
                                    @for($i = 1; $i <= 5; $i++)
                                    <input type="radio" class="btn-check" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-warning btn-rating" for="rating{{ $i }}">
                                        <i class="fas fa-star"></i><span class="d-none d-sm-inline"> {{ $i }}</span>
                                    </label>
                                    @endfor
                                </div>
                            </div>
                            @error('rating')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2 rating-hint">
                                {{ $testTrans['rating_hint'][$lang] ?? '1 = Very Poor, 5 = Very Good' }}
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ $testTrans['label_content'][$lang] ?? 'Your Testimonial' }} <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" rows="4" 
                                class="form-control @error('content') is-invalid @enderror" 
                                placeholder="{{ $testTrans['placeholder_content'][$lang] ?? 'Tell your experience...' }}"
                                required>{{ old('content') }}</textarea>
                            @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ $testTrans['content_hint'][$lang] ?? 'Minimum 20 characters' }}</small>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label fw-bold">{{ $testTrans['label_photo'][$lang] ?? 'Testimonial Photo' }} <span class="text-muted fw-normal">{{ $testTrans['label_optional'][$lang] ?? '(optional)' }}</span></label>
                            <input type="file" name="image" id="image" 
                                class="form-control @error('image') is-invalid @enderror" 
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                onchange="previewImage(this)">
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ $testTrans['photo_hint'][$lang] ?? 'Format: JPEG, PNG, WEBP. Maximum 2MB. Image will be displayed in Hijab gallery.' }}</small>
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="rounded shadow-sm" style="max-height: 200px; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImage()">
                                    <i class="fas fa-times me-1"></i>{{ $testTrans['btn_remove_photo'][$lang] ?? 'Remove Photo' }}
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-warning py-2 alert-mobile">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ $testTrans['alert_review_notice'][$lang] ?? 'Testimonial will be reviewed by admin before being displayed.' }}
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                            <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-secondary btn-action-mobile order-2 order-sm-1">
                                <i class="fas fa-arrow-left me-2"></i>{{ $testTrans['btn_back'][$lang] ?? 'Back' }}
                            </a>
                            <button type="submit" class="btn btn-success btn-action-mobile order-1 order-sm-2">
                                <i class="fas fa-paper-plane me-2"></i>{{ $testTrans['btn_submit'][$lang] ?? 'Submit Testimonial' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input .btn-outline-warning:hover,
.rating-input .btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #fff;
}

/* Mobile Responsive */
@media (max-width: 767.98px) {
    .breadcrumb-mobile {
        font-size: 0.8rem;
    }
    .card-header-mobile {
        padding: 0.75rem 1rem;
    }
    .card-title-mobile {
        font-size: 1rem;
    }
    .alert-mobile {
        font-size: 0.85rem;
    }
    .order-info-title {
        font-size: 0.9rem;
    }
    .order-info-table {
        font-size: 0.85rem;
    }
    .order-info-label {
        width: 100px;
    }
    .product-list {
        font-size: 0.85rem;
    }
    .btn-rating {
        padding: 0.4rem 0.6rem;
    }
    .rating-hint {
        font-size: 0.75rem;
    }
    .form-label {
        font-size: 0.9rem;
    }
    .form-control {
        font-size: 0.9rem;
    }
    .btn-action-mobile {
        font-size: 0.9rem;
    }
}

@media (max-width: 575.98px) {
    .btn-rating {
        padding: 0.35rem 0.5rem;
        font-size: 0.85rem;
    }
    .order-info-label {
        width: 80px;
    }
}
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    input.value = '';
    previewImg.src = '';
    preview.style.display = 'none';
}
</script>
@endsection