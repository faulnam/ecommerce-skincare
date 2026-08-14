@extends('layouts.admin')

@section('page-title', 'Statistik Pengunjung')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Statistik Pengunjung
@endsection

@section('content')

<!-- Visitor Stats & Chart -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header border-bottom-0 pb-0">Ringkasan Pengunjung</div>
            <div class="card-body">
                <div class="mb-4">
                    <h2 class="mb-0 fw-bold" style="color: #0f172a;">{{ number_format($todayVisitors) }}</h2>
                    <p class="text-muted mb-0">Pengunjung Hari Ini</p>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Kemarin</span>
                    <span class="fw-bold" style="color: #0f172a;">{{ number_format($yesterdayVisitors) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Bulan Ini</span>
                    <span class="fw-bold" style="color: #0f172a;">{{ number_format($thisMonthVisitors) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Total Pengunjung Unik</span>
                    <span class="fw-bold" style="color: #0f172a;">{{ number_format($totalVisitors) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Total Page Views</span>
                    <span class="fw-bold" style="color: #0f172a;">{{ number_format($totalPageViews) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Tren Pengunjung (7 Hari Terakhir)</span>
            </div>
            <div class="card-body">
                <canvas id="visitorChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Visitors Log -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Log Pengunjung Terbaru</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Tanggal</th>
                        <th>Page Views</th>
                        <th>Perangkat (User Agent)</th>
                        <th>Update Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentVisitors as $visitor)
                        <tr>
                            <td class="fw-500" style="color: #334155;">{{ $visitor->ip_address }}</td>
                            <td>{{ $visitor->date->format('d M Y') }}</td>
                            <td><span class="badge" style="background: #e2e8f0; color: #0f172a;">{{ $visitor->views }}x</span></td>
                            <td class="text-muted small text-wrap" style="max-width: 300px;">{{ Str::limit($visitor->user_agent, 80) }}</td>
                            <td class="text-muted">{{ $visitor->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data pengunjung.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($recentVisitors->hasPages())
        <div class="card-footer bg-white d-flex justify-content-end pt-3 pb-1">
            {{ $recentVisitors->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    const vCtx = document.getElementById('visitorChart').getContext('2d');
    new Chart(vCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($visitorChart, 'date')) !!}.reverse(),
            datasets: [{
                label: 'Pengunjung',
                data: {!! json_encode(array_column($visitorChart, 'visitors')) !!}.reverse(),
                borderColor: '#0f172a',
                backgroundColor: 'rgba(15, 23, 42, 0.05)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointBackgroundColor: '#0f172a',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { stepSize: 1 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
