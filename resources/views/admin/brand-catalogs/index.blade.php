@extends('layouts.admin')

@section('title', 'Manajemen Brand Catalog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-book me-2"></i>Manajemen Brand Catalog
    </h4>
    <a href="{{ route('admin.brand-catalogs.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-1"></i>Tambah Brand Catalog
    </a>
</div>

@if($catalogs->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-book fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Brand Catalog</h5>
            <p class="text-muted">Mulai tambahkan katalog brand untuk ditampilkan di website.</p>
            <a href="{{ route('admin.brand-catalogs.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Tambah Brand Catalog
            </a>
        </div>
    </div>
@else
    <div class="row">
        @foreach($catalogs as $catalog)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 {{ !$catalog->is_active ? 'opacity-50' : '' }}">
                    <div class="position-relative">
                        @if($catalog->cover_image)
                            <img src="{{ $catalog->cover_image_url }}" class="card-img-top" alt="{{ $catalog->brand_name }}" style="height: 180px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif

                        @if(!$catalog->is_active)
                            <span class="position-absolute top-0 start-0 m-2 badge bg-secondary">Nonaktif</span>
                        @endif

                        @php $catCount = collect($catalog->pdf_files ?? [])->filter()->count(); @endphp
                        @if($catCount > 0)
                            <span class="position-absolute top-0 end-0 m-2 badge bg-danger">
                                <i class="fas fa-file-pdf me-1"></i>{{ $catCount }} PDF
                            </span>
                        @endif
                    </div>

                    <div class="card-body">
                        <h6 class="card-title mb-1">{{ $catalog->brand_name }}</h6>
                        @if($catalog->description)
                            <p class="card-text small text-muted mb-2">{{ Str::limit($catalog->description, 50) }}</p>
                        @endif
                        <small class="text-muted">Urutan: {{ $catalog->sort_order }}</small>
                    </div>

                    <div class="card-footer bg-white">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('admin.brand-catalogs.edit', $catalog) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.brand-catalogs.toggle', $catalog) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-{{ $catalog->is_active ? 'warning' : 'success' }}" title="{{ $catalog->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $catalog->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.brand-catalogs.destroy', $catalog) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus brand catalog ini?')">
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

    {{ $catalogs->links('pagination.admin') }}
@endif
@endsection
