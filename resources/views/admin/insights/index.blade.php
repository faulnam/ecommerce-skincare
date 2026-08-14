@extends('layouts.admin')

@section('title', 'Kelola Insights')
@section('page-title', 'Kelola Insights')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-newspaper me-2"></i>Daftar Insights</span>
        <a href="{{ route('admin.insights.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Tambah Insight
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="50">ID</th>
                        <th width="100">Gambar</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th width="80" class="text-center">Views</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($insights as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                            </td>
                            <td>
                                <strong>{{ $item->title }}</strong><br>
                                <small class="text-muted">{{ $item->author }} &bull; {{ $item->created_at->format('d M Y') }}</small>
                            </td>

                            <td>
                                @if($item->status === 'published')
                                    <span class="badge bg-success">Published</span>
                                @elseif($item->status === 'scheduled')
                                    <span class="badge bg-info text-dark">Scheduled<br><small class="fw-normal">{{ $item->published_at ? $item->published_at->format('d M, H:i') : '' }}</small></span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="text-center"><i class="fas fa-eye text-muted"></i> {{ number_format($item->views) }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.insights.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.insights.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus insight ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada data insight.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($insights->hasPages())
        <div class="card-footer bg-white">
            {{ $insights->links('pagination.admin') }}
        </div>
    @endif
</div>
@endsection
