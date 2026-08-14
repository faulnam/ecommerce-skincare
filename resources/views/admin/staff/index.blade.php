@extends('layouts.admin')

@section('page-title', 'Manajemen Staff')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-user-shield me-2"></i>Daftar Staff (Admin)</span>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah Staff
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.staff.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
                <input type="text" class="form-control" name="search" placeholder="Cari nama, email, atau telepon..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="role">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="developer" {{ request('role') == 'developer' ? 'selected' : '' }}>Developer</option>
                    <option value="blogger" {{ request('role') == 'blogger' ? 'selected' : '' }}>Blogger</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search me-1"></i>Cari
                </button>
            </div>
        </form>

        <!-- Staff Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Staff</th>
                        <th>Kontak</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                         class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        <br><small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $user->phone ?? '-' }}
                                <br><small class="text-muted">{{ Str::limit($user->address, 30) }}</small>
                            </td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($user->role === 'developer')
                                    <span class="badge" style="background-color: #6366f1; color: white;">Developer</span>
                                @elseif($user->role === 'blogger')
                                    <span class="badge" style="background-color: #f59e0b; color: white;">Blogger</span>
                                @else
                                    <span class="badge bg-info">Kurir</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.staff.edit', $user) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.staff.toggle-active', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" 
                                                title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.staff.destroy', $user) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus staff ini? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada staff</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links('pagination.admin') }}
    </div>
</div>
@endsection
