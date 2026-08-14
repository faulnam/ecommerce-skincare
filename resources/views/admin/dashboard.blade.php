@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Overview
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f8fafc; color: #0f172a;">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalOrders) }}</h3>
                <p>Total Pesanan</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f8fafc; color: #0f172a;">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-info">
                <h3>Rp {{ number_format($totalRevenue / 1000, 0, ',', '.') }}K</h3>
                <p>Total Pendapatan</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f8fafc; color: #0f172a;">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalProducts) }}</h3>
                <p>Total Produk</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f8fafc; color: #0f172a;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalCustomers) }}</h3>
                <p>Total Pelanggan</p>
            </div>
        </div>
    </div>
</div>


<!-- Order Status & Chart -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Pendapatan 6 Bulan Terakhir</span>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">Status Pesanan</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="d-flex align-items-center gap-2">
                            <span class="badge" style="width: 8px; height: 8px; padding: 0; border-radius: 50%; background: #94a3b8;"></span>
                            Menunggu Pembayaran
                        </span>
                        <strong>{{ $orderStats['pending'] }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="d-flex align-items-center gap-2">
                            <span class="badge" style="width: 8px; height: 8px; padding: 0; border-radius: 50%; background: #64748b;"></span>
                            Diproses
                        </span>
                        <strong>{{ $orderStats['processing'] }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="d-flex align-items-center gap-2">
                            <span class="badge" style="width: 8px; height: 8px; padding: 0; border-radius: 50%; background: #334155;"></span>
                            Dikirim
                        </span>
                        <strong>{{ $orderStats['shipped'] }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="d-flex align-items-center gap-2">
                            <span class="badge" style="width: 8px; height: 8px; padding: 0; border-radius: 50%; background: #0f172a;"></span>
                            Selesai
                        </span>
                        <strong>{{ $orderStats['completed'] }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="d-flex align-items-center gap-2">
                            <span class="badge" style="width: 8px; height: 8px; padding: 0; border-radius: 50%; background: #cbd5e1;"></span>
                            Dibatalkan
                        </span>
                        <strong>{{ $orderStats['cancelled'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alerts & Recent Orders -->
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Perlu Tindakan</div>
            <div class="card-body">
                @if($pendingPayments > 0)
                    <a href="{{ route('admin.orders.index', ['payment_status' => 'pending_verification']) }}" class="d-flex justify-content-between align-items-center p-3 rounded mb-2 text-decoration-none" style="background: var(--accent-light);">
                        <span style="color: #c2410c;">
                            <i class="fas fa-credit-card me-2"></i>Pembayaran Pending
                        </span>
                        <span class="badge bg-warning">{{ $pendingPayments }}</span>
                    </a>
                @endif
                
                @if($pendingTestimonials > 0)
                    <a href="{{ route('admin.testimonials.index', ['status' => 'pending']) }}" class="d-flex justify-content-between align-items-center p-3 rounded mb-2 text-decoration-none" style="background: #e2e8f0;">
                        <span style="color: #0f172a;">
                            <i class="fas fa-star me-2"></i>Testimoni Pending
                        </span>
                        <span class="badge bg-info">{{ $pendingTestimonials }}</span>
                    </a>
                @endif
                
                @if($pendingPayments == 0 && $pendingTestimonials == 0)
                    <div class="empty-state">
                        <i class="fas fa-check-circle" style="color: var(--primary);"></i>
                        <p class="mb-0 mt-2">Semua sudah selesai! 🎉</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Pesanan Terbaru</span>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none fw-500" style="color: var(--primary);">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->user->name }}</td>
                                    <td class="fw-600">{{ $order->formatted_total }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $order->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyRevenue, 'month')) !!},
            datasets: [{
                label: 'Pendapatan',
                data: {!! json_encode(array_column($monthlyRevenue, 'revenue')) !!},
                borderColor: '#334155',
                backgroundColor: 'rgba(51, 65, 85, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointBackgroundColor: '#334155',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000) + 'K';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });


</script>
@endpush
