@extends('layouts.admin')

@section('title', 'Manajemen Filter')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-list me-2"></i>Manajemen Filter
    </h4>
    <a href="{{ route('admin.filters.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-1"></i>Tambah Filter
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.filters.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="category" class="col-form-label">Filter Kategori:</label>
            </div>
            <div class="col-md-3">
                <select name="category" id="category" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            @if(request('category'))
                <div class="col-auto">
                    <a href="{{ route('admin.filters.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            @endif
        </form>
    </div>
</div>

@if($filters->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-list fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Data Filter</h5>
            <p class="text-muted">Mulai tambahkan opsi filter untuk digunakan pada produk.</p>
            <a href="{{ route('admin.filters.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Tambah Filter
            </a>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Kategori</th>
                            <th>Nama / Opsi</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filters as $spec)
                            <tr>
                                <td class="ps-4">{{ $spec->id }}</td>
                                <td><span class="badge bg-secondary">{{ $spec->category }}</span></td>
                                <td class="fw-medium">{{ $spec->name }}</td>
                                <td>
                                    @if($spec->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.filters.edit', $spec) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.filters.destroy', $spec) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus filter ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $filters->links('pagination.admin') }}
    </div>
@endif
@endsection
