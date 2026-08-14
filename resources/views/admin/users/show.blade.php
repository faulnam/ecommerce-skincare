@extends('layouts.admin')

@section('page-title', 'Detail Customer')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <!-- User Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                     class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #16a34a;">
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-3">Customer</p>
                
                @if($user->trashed())
                    <span class="badge bg-secondary">Dihapus</span>
                @elseif($user->is_active)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-danger">Nonaktif</span>
                @endif
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="fas fa-envelope me-2"></i>Email</span>
                    <span>{{ $user->email }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="fas fa-phone me-2"></i>Telepon</span>
                    <span>{{ $user->phone ?? '-' }}</span>
                </li>
                <li class="list-group-item">
                    <i class="fas fa-map-marker-alt me-2"></i>Alamat<br>
                    <small class="text-muted">{{ $user->address ?? '-' }}</small>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="fas fa-calendar me-2"></i>Bergabung</span>
                    <span>{{ $user->created_at->format('d M Y') }}</span>
                </li>
            </ul>
            <div class="card-body">
                @if(!$user->trashed())
                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }} w-100 mb-2">
                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} me-1"></i>
                        {{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                    </button>
                </form>
                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                      onsubmit="return confirm('Reset password customer ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-key me-1"></i>Reset Password
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <!-- Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3 class="mb-1">{{ $stats['total_orders'] }}</h3>
                        <small>Total Pesanan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3 class="mb-1">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</h3>
                        <small>Total Belanja</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h3 class="mb-1">{{ $stats['completed_orders'] }}</h3>
                        <small>Pesanan Selesai</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-shopping-cart me-2"></i>Pesanan Terbaru
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->formatted_total }}</td>
                                <td><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada pesanan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>
@endsection
