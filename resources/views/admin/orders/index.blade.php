@extends('layouts.admin')

@section('page-title', 'Manajemen Pesanan')

@section('content')
<style>
    .order-badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color: var(--primary);">Dashboard</a></li>
        <li class="breadcrumb-item active">Pesanan</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <i class="fas fa-shopping-cart me-2" style="color: var(--primary);"></i>Daftar Pesanan
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Cari no. pesanan/customer..." value="{{ request('search') }}" style="border: 2px solid var(--border-color);">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status" style="border: 2px solid var(--border-color);">
                    <option value="">Semua Status</option>
                    <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Pesanan Diproses</option>
                    <option value="ready_to_ship" {{ request('status') == 'ready_to_ship' ? 'selected' : '' }}>Siap Pickup</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Sudah Sampai</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="payment_status" style="border: 2px solid var(--border-color);">
                    <option value="">Semua Pembayaran</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="pending_verification" {{ request('payment_status') == 'pending_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="Dari tanggal" style="border: 2px solid var(--border-color);">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai tanggal" style="border: 2px solid var(--border-color);">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <!-- Orders Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="font-weight: 600; color: var(--gray);">No. Pesanan</th>
                        <th style="font-weight: 600; color: var(--gray);">Customer</th>
                        <th style="font-weight: 600; color: var(--gray);">Total</th>
                        <th style="font-weight: 600; color: var(--gray);">Status</th>
                        <th style="font-weight: 600; color: var(--gray);">Pembayaran</th>
                        <th style="font-weight: 600; color: var(--gray);">Tanggal</th>
                        <th style="font-weight: 600; color: var(--gray);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $statusColors = [
                                'pending_payment' => 'background: rgba(245, 158, 11, 0.1); color: #f59e0b;',
                                'processing' => 'background: rgba(59, 130, 246, 0.1); color: #2563eb;',
                                'ready_to_ship' => 'background: rgba(139, 92, 246, 0.1); color: #8b5cf6;',
                                'shipped' => 'background: rgba(249, 115, 22, 0.1); color: #f97316;',
                                'delivered' => 'background: rgba(16, 185, 129, 0.1); color: #10b981;',
                                'completed' => 'background: rgba(16, 185, 129, 0.1); color: #10b981;',
                                'cancelled' => 'background: rgba(239, 68, 68, 0.1); color: #ef4444;',
                            ];
                            $paymentColors = [
                                'pending' => 'background: rgba(245, 158, 11, 0.1); color: #f59e0b;',
                                'unpaid' => 'background: rgba(245, 158, 11, 0.1); color: #f59e0b;',
                                'pending_verification' => 'background: rgba(139, 92, 246, 0.1); color: #8b5cf6;',
                                'paid' => 'background: rgba(16, 185, 129, 0.1); color: #10b981;',
                                'failed' => 'background: rgba(239, 68, 68, 0.1); color: #ef4444;',
                            ];
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none" style="font-weight: 600; color: var(--primary);">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--dark);">{{ $order->user->name }}</span>
                                <br><small class="text-muted">{{ $order->user->phone }}</small>
                            </td>
                            <td style="font-weight: 600; color: var(--dark);">{{ $order->formatted_total }}</td>
                            <td>
                                <span class="order-badge" style="{{ $statusColors[$order->status] ?? 'background: #f3f4f6; color: #6b7280;' }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td>
                                <span class="order-badge" style="{{ $paymentColors[$order->payment_status] ?? 'background: #f3f4f6; color: #6b7280;' }}">
                                    {{ $order->payment_status_label }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x mb-3" style="color: var(--gray); opacity: 0.5;"></i>
                                <p class="text-muted mb-0">Belum ada pesanan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links('pagination.admin') }}
    </div>
</div>
@endsection
