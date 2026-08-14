@extends('layouts.admin')

@section('page-title', 'Event Free Produk')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-gift me-2"></i>Produk Gratis (Event)</span>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Pengaturan Teks Promo</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.free-products.settings') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Status Event</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ old('is_active', $eventActive ?? '1') == '1' ? 'selected' : '' }}>Aktif (Munculkan Popup & Produk Free)</option>
                                <option value="0" {{ old('is_active', $eventActive ?? '1') == '0' ? 'selected' : '' }}>Nonaktif (Sembunyikan Popup & Produk Free)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Judul Promo</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $eventTitle) }}" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Deskripsi Promo</label>
                            <input type="text" name="description" class="form-control" value="{{ old('description', $eventDescription) }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Pesan Error Guest Add to Cart</label>
                            <input type="text" name="error_message" class="form-control" value="{{ old('error_message', $eventErrorMessage ?? 'Produk gratis hanya bisa di checkout oleh user yang sudah login. Silakan login terlebih dahulu.') }}">
                            <small class="text-muted">Pesan yang akan muncul di pojok kanan atas (toast) ketika user yang belum login (guest) mencoba menambahkan produk gratis ke keranjang.</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Gambar Popup Bonus (Opsional)</label>
                            @if(isset($eventImage) && $eventImage)
                                <div class="mb-2">
                                    <img src="{{ $eventImage }}" alt="Promo Image" style="height: 100px; object-fit: contain;" class="border p-1 rounded bg-white">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control mb-2" accept="image/*">
                            <small class="text-muted d-block mb-3">Rekomendasi ukuran: 500x500px, maksimal 2MB. Akan muncul di Welcome Bonus Popup.</small>
                            <a href="{{ route('admin.free-products.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Pilih Produk
                            </a>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <img src="{{ $product->image_url ?: 'https://via.placeholder.com/50' }}" alt="{{ $product->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td><span class="badge bg-secondary">{{ $product->category_label }}</span></td>
                            <td>
                                <form action="{{ route('admin.free-products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini dari daftar Free Product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus dari Event">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Belum ada produk untuk Event Free Produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
