@extends('layouts.admin')

@section('page-title', 'Manajemen Reviews')

@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.testimonials.index') }}">
            <i class="fas fa-images me-1"></i>Testimoni Gambar <small class="text-muted">(home)</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.reviews.index') }}">
            <i class="fas fa-comments me-1"></i>Review Produk <small class="text-muted">(detail)</small>
        </a>
    </li>
</ul>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-star me-2"></i>Daftar Reviews
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="rating">
                    <option value="">Semua Rating</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
            </div>
        </form>

        <!-- Reviews Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr class="{{ !$review->is_approved ? 'table-warning' : '' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $review->product->image_url }}" alt="{{ $review->product->name }}" 
                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <a href="{{ $review->detail_url }}" class="text-decoration-none text-dark">
                                            <strong>{{ Str::limit($review->product->name, 30) }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $review->product->category_label }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $review->reviewer_name ?? $review->user->name }}</strong>
                                    @if($review->is_verified)
                                        <span class="badge bg-success ms-1">Verified</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $review->user->email }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }} text-sm"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <small>{{ Str::limit($review->comment, 80) }}</small>
                            </td>
                            <td>
                                @if($review->is_approved)
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $review->created_at->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!$review->is_approved)
                                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning" title="Tolak">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus review ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada review</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reviews->links('pagination.admin') }}
        </div>
    </div>
</div>
@endsection
