@extends('layouts.admin')

@section('page-title', 'Manajemen Testimoni')

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

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-comment me-2"></i>Daftar Testimoni Gambar
        </div>
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Tambah Testimoni
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.testimonials.index') }}" method="GET" class="row g-3 mb-4">
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

        <!-- Testimonials Grid -->
        <div class="row g-4">
            @forelse($testimonials as $testimonial)
                <div class="col-md-6">
                    <div class="card h-100 {{ !$testimonial->is_approved ? 'border-warning' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $testimonial->user->avatar_url }}" alt="{{ $testimonial->user->name }}" 
                                         class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">{{ $testimonial->user->name }}</h6>
                                        <small class="text-muted">{{ $testimonial->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                                @if($testimonial->is_approved)
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </div>
                            
                            <div class="mb-2">
                                {!! $testimonial->stars !!}
                            </div>
                            
                            <p class="card-text">{{ $testimonial->content }}</p>
                            
                            @if($testimonial->image)
                                <div class="mb-3">
                                    <img src="{{ $testimonial->image_url }}" alt="Foto Testimoni" 
                                         class="img-fluid rounded" style="max-height: 200px; object-fit: cover; cursor: pointer;"
                                         data-bs-toggle="modal" data-bs-target="#testimonialImageModal{{ $testimonial->id }}">
                                </div>
                            @endif

                            @if($testimonial->order_id)
                                <small class="text-muted d-block mb-3">
                                    Pesanan: <a href="{{ route('admin.orders.show', $testimonial->order) }}">
                                        {{ $testimonial->order->order_number }}
                                    </a>
                                </small>
                            @else
                                <small class="text-muted d-block mb-3">
                                    <i class="fas fa-user-shield me-1"></i>Ditambahkan oleh Admin
                                </small>
                            @endif
                            
                            <div class="d-flex gap-2">
                                @if(!$testimonial->is_approved)
                                    <form action="{{ route('admin.testimonials.approve', $testimonial) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check me-1"></i>Setujui
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.testimonials.reject', $testimonial) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            <i class="fas fa-eye-slash me-1"></i>Sembunyikan
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus testimoni ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Belum ada testimoni</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $testimonials->links('pagination.admin') }}
        </div>
    </div>
</div>

{{-- Image Modals --}}
@foreach($testimonials as $testimonial)
    @if($testimonial->image)
        <div class="modal fade" id="testimonialImageModal{{ $testimonial->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-transparent border-0">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-white">Foto Testimoni - {{ $testimonial->user->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 text-center">
                        <img src="{{ $testimonial->image_url }}" alt="Foto Testimoni" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection
