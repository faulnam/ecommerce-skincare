@extends('layouts.admin')

@section('page-title', 'Pilih Free Produk')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-plus me-2"></i>Pilih Produk untuk Event Gratis</span>
        <a href="{{ route('admin.free-products.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.free-products.create') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-8">
                <input type="text" class="form-control" name="q" placeholder="Cari produk..." value="{{ request('q') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search me-1"></i>Cari
                </button>
            </div>
        </form>

        <form action="{{ route('admin.free-products.store') }}" method="POST">
            @csrf
            <div class="table-responsive mb-4">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Pilih</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($availableProducts as $product)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="product_ids[]" value="{{ $product->id }}" id="product_{{ $product->id }}">
                                    </div>
                                </td>
                                <td>
                                    <label for="product_{{ $product->id }}">
                                        <img src="{{ $product->image_url ?: 'https://via.placeholder.com/50' }}" alt="{{ $product->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    </label>
                                </td>
                                <td><label for="product_{{ $product->id }}"><strong>{{ $product->name }}</strong></label></td>
                                <td><span class="badge bg-secondary">{{ $product->category_label }}</span></td>
                                <td>{{ $product->stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Semua produk sudah masuk dalam event atau tidak ada produk yang sesuai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    {{ $availableProducts->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
                <button type="submit" class="btn btn-primary" {{ $availableProducts->isEmpty() ? 'disabled' : '' }}>
                    <i class="fas fa-save me-1"></i>Simpan Pilihan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
