@extends('layouts.admin')

@section('title', 'Manajemen Galeri')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-images me-2"></i>Manajemen Galeri
    </h4>
    <a href="{{ route('admin.galleries.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-1"></i>Tambah Galeri
    </a>
</div>

@if($galleries->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-images fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Galeri</h5>
            <p class="text-muted">Mulai tambahkan gambar atau video untuk galeri Anda.</p>
            <a href="{{ route('admin.galleries.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Tambah Galeri
            </a>
        </div>
    </div>
@else
    <div class="row">
        @foreach($galleries as $gallery)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 {{ !$gallery->is_active ? 'opacity-50' : '' }}">
                    <div class="position-relative">
                        @if($gallery->isImage())
                            @if($gallery->image)
                                <img src="{{ $gallery->image_url }}" class="card-img-top" alt="{{ $gallery->title }}" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        @else
                            <div class="bg-dark d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="fab fa-instagram fa-3x text-white"></i>
                            </div>
                        @endif
                        
                        <span class="position-absolute top-0 end-0 m-2 badge {{ $gallery->isImage() ? 'bg-primary' : 'bg-danger' }}">
                            <i class="fas {{ $gallery->isImage() ? 'fa-image' : 'fa-video' }} me-1"></i>
                            {{ $gallery->type_label }}
                        </span>
                        
                        @if(!$gallery->is_active)
                            <span class="position-absolute top-0 start-0 m-2 badge bg-secondary">Nonaktif</span>
                        @endif
                    </div>
                    
                    <div class="card-body">
                        <h6 class="card-title mb-1">{{ Str::limit($gallery->title, 30) }}</h6>
                        @if($gallery->description)
                            <p class="card-text small text-muted mb-2">{{ Str::limit($gallery->description, 50) }}</p>
                        @endif
                        <small class="text-muted">Urutan: {{ $gallery->sort_order }}</small>
                    </div>
                    
                    <div class="card-footer bg-white">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.galleries.toggle', $gallery) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-{{ $gallery->is_active ? 'warning' : 'success' }}" title="{{ $gallery->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $gallery->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus galeri ini?')">
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
        @endforeach
    </div>
    
    {{ $galleries->links('pagination.admin') }}
@endif
@endsection
