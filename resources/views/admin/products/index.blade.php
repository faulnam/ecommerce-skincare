@extends('layouts.admin')

@section('page-title', 'Manajemen Produk')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-box me-2"></i>Daftar Produk</span>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Tambah Produk
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="category">
                    <option value="">Semua Kategori</option>
                    @foreach(\App\Models\Product::categories() as $value => $label)
                        <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
            </div>
        </form>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Berat</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Highlight</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                          <img src="{{ $product->image_url ?: 'https://via.placeholder.com/50' }}" 
                                     alt="{{ $product->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                            </td>
                            <td><span class="badge bg-{{ $product->category == 'skincare' ? 'primary' : ($product->category == 'new-arrivals' ? 'info' : 'secondary') }}">{{ $product->category_label }}</span></td>
                            <td><span class="badge bg-secondary">{{ $product->formatted_weight }}</span></td>
                            <td>
                                @if($product->hasActiveDiscount())
                                    <div>
                                        <del class="text-muted small">{{ $product->formatted_price }}</del>
                                        <br>
                                        <span class="text-success fw-bold">{{ $product->formatted_discounted_price }}</span>
                                    </div>
                                @else
                                    {{ $product->formatted_price }}
                                @endif
                            </td>
                            <td>
                                @if($product->stock > 10)
                                    <span class="text-success">{{ $product->stock }}</span>
                                @elseif($product->stock > 0)
                                    <span class="text-warning">{{ $product->stock }}</span>
                                @else
                                    <span class="text-danger">Habis</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.products.toggle-featured', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-{{ $product->is_featured ? 'warning' : 'outline-secondary' }}" 
                                            title="{{ $product->is_featured ? 'Hapus dari Highlight' : 'Jadikan Highlight' }}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                                @if($product->is_featured)
                                    <br><small class="text-warning fw-bold">Highlight</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $product->is_active ? 'warning' : 'success' }}" 
                                                title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas fa-{{ $product->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
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
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada produk</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
<br>
        {{ $products->links('pagination.admin') }}
    </div>
</div>
@endsection
