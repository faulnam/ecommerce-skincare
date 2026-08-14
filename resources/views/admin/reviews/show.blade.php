@extends('layouts.admin')

@section('page-title', 'Detail Review')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-star me-2"></i>Detail Review
        </div>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Product Info -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Produk</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <img src="{{ $review->product->image_url }}" alt="{{ $review->product->name }}" 
                                 class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            <div>
                                <a href="{{ $review->detail_url }}" class="text-decoration-none text-dark">
                                    <h5 class="mb-1">{{ $review->product->name }}</h5>
                                </a>
                                <p class="mb-1 text-muted">{{ $review->product->category_label }}</p>
                                <p class="mb-0 text-dark fw-bold">{{ $review->product->formatted_price }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Info -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">User</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 60px; height: 60px; font-size: 24px;">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $review->user->name }}</h5>
                                <p class="mb-1 text-muted">{{ $review->user->email }}</p>
                                @if($review->is_verified)
                                    <span class="badge bg-success">Verified Buyer</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Details -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Detail Review</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="fw-bold">Rating:</label>
                            <div class="mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }} text-lg"></i>
                                @endfor
                                <span class="ms-2 fw-bold">{{ $review->rating }}/5</span>
                            </div>
                        </div>

                        @if($review->comment)
                            <div class="mb-4">
                                <label class="fw-bold">Comment:</label>
                                <p class="mt-1 bg-light p-3 rounded">{{ $review->comment }}</p>
                            </div>
                        @endif

                        <div class="row">
                            @if($review->quality_rating)
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Quality Rating:</label>
                                    <div class="mt-1">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="width: {{ $review->quality_rating }}%">
                                                {{ $review->quality_rating }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($review->sizing_rating)
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Sizing Rating:</label>
                                    <div class="mt-1">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" style="width: {{ $review->sizing_rating }}%">
                                                {{ $review->sizing_rating }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($review->usual_size)
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Usual Size:</label>
                                    <p class="mt-1 bg-light p-2 rounded text-center">{{ $review->usual_size }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Status:</label>
                                <div class="mt-1">
                                    @if($review->is_approved)
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Tanggal Dibuat:</label>
                                <p class="mt-1">{{ $review->created_at->format('d M Y H:i') }}</p>
                            </div>
                            @if($review->order_id)
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Pesanan:</label>
                                    <p class="mt-1">
                                        <a href="{{ route('admin.orders.show', $review->order) }}" class="text-decoration-none">
                                            {{ $review->order->order_number }}
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-4">
            @if(!$review->is_approved)
                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Setujui Review
                    </button>
                </form>
            @else
                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-eye-slash me-1"></i>Tolak Review
                    </button>
                </form>
            @endif

            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Yakin ingin menghapus review ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Hapus Review
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
