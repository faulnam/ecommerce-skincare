@extends('layouts.admin')

@section('page-title', 'Google Analytics')

@push('styles')
<style>
    @media print {
        /* Hide sidebar, topbar, and actionable buttons */
        .sidebar, .topbar, .navbar, .btn, form, header, footer {
            display: none !important;
        }
        /* Make content take full width */
        #content, .container-fluid, .main-panel {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        /* Ensure charts print properly */
        canvas {
            max-width: 100% !important;
            page-break-inside: avoid;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
        body {
            background-color: white !important;
        }
    }
</style>
@endpush

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Analytics
@endsection

@section('content')

<!-- HEADER SECTION -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-chart-pie text-primary me-2"></i> Dashboard Analytics</h4>
        <p class="text-muted mb-0 small">Analisis performa website dan pengunjung secara keseluruhan.</p>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-md-end">
        <!-- Date Filter -->
        <form action="{{ route('admin.analytics.index') }}" method="GET" class="d-flex align-items-center flex-wrap gap-2" id="filterForm">
            <div class="d-flex align-items-center gap-2">
                <label for="period" class="fw-bold text-muted small mb-0 d-none d-sm-block">Periode:</label>
                <select name="period" id="period" class="form-select form-select-sm shadow-sm" onchange="toggleCustomDate(this.value)" style="width: auto; min-width: 150px;">
                    <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Hari Ini (Per Jam)</option>
                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Pilih Manual...</option>
                </select>
            </div>
            
            <div id="customDateBox" class="align-items-center gap-2 flex-wrap {{ $period === 'custom' ? 'd-flex' : 'd-none' }}">
                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm shadow-sm" style="max-width: 130px;" value="{{ request('start_date') }}">
                <span class="text-muted small d-none d-sm-inline">-</span>
                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm shadow-sm" style="max-width: 130px;" value="{{ request('end_date') }}">
                <button type="submit" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-check"></i></button>
            </div>

            <!-- Export Button (uses the same form) -->
            <button type="submit" name="export" value="excel" class="btn btn-sm btn-success shadow-sm ms-md-2">
                <i class="fas fa-file-excel me-1"></i> Ekspor Excel
            </button>
        </form>

        <!-- Actions -->
        <a href="{{ route('admin.analytics.guide') }}" class="btn btn-sm btn-info text-white shadow-sm">
            <i class="fas fa-book me-1"></i> Juknis
        </a>
    </div>
</div>

<div class="mb-4">


    <!-- GA4 TRAFFIC STATS SECTION -->
    <h6 class="fw-bold text-primary text-uppercase mb-3 mt-5"><i class="fas fa-users me-2"></i> Lalu Lintas Pengunjung</h6>
    <div class="row g-4 mb-4">
        <!-- GA4 Stats Cards -->
        <div class="col-md-2 col-sm-6">
            <div class="stat-card p-3 h-100 bg-white rounded shadow-sm" style="border-top: 3px solid #ef4444;">
                <div class="stat-info">
                    <p class="mb-1 text-muted small text-truncate">Active Users</p>
                    <h3 class="text-danger fw-bold fs-4 mb-0 text-truncate" title="{{ $ga4MockStats['active_users'] }}">{{ $ga4MockStats['active_users'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-card p-3 h-100 bg-white rounded shadow-sm" style="border-top: 3px solid #3b82f6;">
                <div class="stat-info">
                    <p class="mb-1 text-muted small text-truncate">Total Users</p>
                    <h3 class="fw-bold fs-4 mb-0 text-truncate" title="{{ number_format($ga4MockStats['total_users']) }}">{{ number_format($ga4MockStats['total_users']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-card p-3 h-100 bg-white rounded shadow-sm" style="border-top: 3px solid #10b981;">
                <div class="stat-info">
                    <p class="mb-1 text-muted small text-truncate">New Users</p>
                    <h3 class="fw-bold fs-4 mb-0 text-truncate" title="{{ number_format($ga4MockStats['new_users']) }}">{{ number_format($ga4MockStats['new_users']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-card p-3 h-100 bg-white rounded shadow-sm" style="border-top: 3px solid #8b5cf6;">
                <div class="stat-info">
                    <p class="mb-1 text-muted small text-truncate">Pageviews</p>
                    <h3 class="fw-bold fs-4 mb-0 text-truncate" title="{{ number_format($ga4MockStats['pageviews']) }}">{{ number_format($ga4MockStats['pageviews']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-card p-3 h-100 bg-white rounded shadow-sm" style="border-top: 3px solid #f59e0b;">
                <div class="stat-info">
                    <p class="mb-1 text-muted small text-truncate">Bounce Rate</p>
                    <h3 class="fw-bold fs-4 mb-0 text-truncate" title="{{ $ga4MockStats['bounce_rate'] }}">{{ $ga4MockStats['bounce_rate'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-card p-3 h-100 bg-white rounded shadow-sm" style="border-top: 3px solid #64748b;">
                <div class="stat-info">
                    <p class="mb-1 text-muted small text-truncate">Avg Session</p>
                    <h3 class="fw-bold fs-4 mb-0 text-truncate" title="{{ $ga4MockStats['avg_session'] }}">{{ $ga4MockStats['avg_session'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- GA4 Charts -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <span class="fw-bold">Traffic Trend ({{ $trendLabel }})</span>
                </div>
                <div class="card-body">
                    <canvas id="ga4LineChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <span class="fw-bold">Traffic Acquisition</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <canvas id="trafficAcquisitionChart" height="200" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row g-4 mb-5">
        <!-- Top Products -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0" style="border-top: 4px solid #10b981 !important;">
                <div class="card-header bg-white">
                    <span class="fw-bold text-success"><i class="fas fa-box-open me-2"></i> Top Products (By Revenue)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-center">Sales</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                <tr>
                                    <td class="fw-bold text-dark align-middle">{{ $product['name'] }}</td>
                                    <td class="text-center align-middle">{{ number_format($product['sales']) }}</td>
                                    <td class="text-end align-middle fw-500 text-success">Rp {{ number_format($product['revenue'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Pages -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0" style="border-top: 4px solid #3b82f6 !important;">
                <div class="card-header bg-white">
                    <span class="fw-bold text-primary"><i class="fas fa-file-alt me-2"></i> Top Pages</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Page Title / Path</th>
                                    <th class="text-end">Pageviews</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPages as $page)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $page['title'] }}</div>
                                        <div class="text-muted small">{{ $page['path'] }}</div>
                                    </td>
                                    <td class="text-end align-middle fw-500">{{ number_format($page['views']) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Demographics -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold">Demographics (City)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>City</th>
                                    <th class="text-end">Users</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCities as $city)
                                <tr>
                                    <td class="align-middle">{{ $city['city'] }}</td>
                                    <td class="text-end align-middle fw-500">{{ number_format($city['users']) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Categories -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold">Device Category</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($deviceCategories as $device)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div><i class="{{ $device['icon'] }} me-3 fa-lg" style="color: {{ $device['color'] }}; width: 20px; text-align: center;"></i> <span class="fw-bold">{{ $device['device'] }}</span></div>
                            <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-bold fs-6">{{ number_format($device['users']) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Browser & OS -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold">Browser & OS</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($browsers as $browser)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-1">
                            <div class="small"><i class="{{ $browser['icon'] }} me-2" style="color: {{ $browser['color'] }}; width: 16px; text-align: center;"></i> {{ $browser['browser'] }}</div>
                            <span class="fw-500 small">{{ number_format($browser['users']) }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="border-top p-1 bg-light fw-bold text-uppercase text-muted" style="font-size: 0.7rem; padding-left: 1rem !important;">Operating Systems</div>
                    <div class="list-group list-group-flush">
                        @foreach($os as $system)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-1">
                            <div class="small"><i class="{{ $system['icon'] }} me-2" style="color: {{ $system['color'] }}; width: 16px; text-align: center;"></i> {{ $system['os'] }}</div>
                            <span class="fw-500 small">{{ number_format($system['users']) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Handle period selection
    function toggleCustomDate(val) {
        const customBox = document.getElementById('customDateBox');
        if (val === 'custom') {
            customBox.classList.remove('d-none');
            customBox.classList.add('d-flex');
        } else {
            customBox.classList.add('d-none');
            customBox.classList.remove('d-flex');
            document.getElementById('filterForm').submit();
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Line Chart for GA4 (Mock)
        const lineCtx = document.getElementById('ga4LineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($ga4Chart, 'date')) !!}.reverse(),
                datasets: [
                    {
                        label: 'Users',
                        data: {!! json_encode(array_column($ga4Chart, 'visitors')) !!}.reverse(),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    },
                    {
                        label: 'Pageviews',
                        data: {!! json_encode(array_column($ga4Chart, 'pageviews')) !!}.reverse(),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        tension: 0.4,
                        fill: false,
                        borderWidth: 2,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Doughnut Chart for Traffic Acquisition
        const doughnutCtx = document.getElementById('trafficAcquisitionChart').getContext('2d');
        const trafficData = {!! json_encode($trafficSources) !!};
        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: trafficData.map(d => d.source),
                datasets: [{
                    data: trafficData.map(d => d.percentage),
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#8b5cf6'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush
