@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-1">Laporan Penjualan</h4>
        <p class="text-muted mb-0" style="font-size: 0.875rem;">Download laporan penjualan dan produk habis</p>
    </div>
</div>

    <div class="row g-4">
        <!-- Laporan Penjualan -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-chart-line text-success"></i>
                    <div>
                        <div class="fw-bold">Laporan Penjualan</div>
                        <small class="text-muted" style="font-size: 0.75rem;">Download data penjualan berdasarkan periode</small>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.download-sales') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Pilih Periode</label>
                            <div class="btn-group w-100 mb-3" role="group">
                                <input type="radio" class="btn-check" name="period_type" id="period_custom" value="custom" checked>
                                <label class="btn btn-outline-primary btn-sm" for="period_custom">Custom</label>
                                
                                <input type="radio" class="btn-check" name="period_type" id="period_week" value="week">
                                <label class="btn btn-outline-primary btn-sm" for="period_week">Minggu Ini</label>
                                
                                <input type="radio" class="btn-check" name="period_type" id="period_month" value="month">
                                <label class="btn btn-outline-primary btn-sm" for="period_month">Bulan Ini</label>
                                
                                <input type="radio" class="btn-check" name="period_type" id="period_year" value="year">
                                <label class="btn btn-outline-primary btn-sm" for="period_year">Tahun Ini</label>
                            </div>
                        </div>

                        <div id="custom-date-range">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="{{ now()->startOfMonth()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-download me-2"></i>Download Laporan (CSV)
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Laporan Produk Habis -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-box-open text-danger"></i>
                    <div>
                        <div class="fw-bold">Produk Habis</div>
                        <small class="text-muted" style="font-size: 0.75rem;">Daftar produk yang stoknya habis</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3" style="font-size: 0.8125rem;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>{{ $soldOutProducts->count() }} Produk</strong> sedang habis stok
                    </div>

                    @if($soldOutProducts->count() > 0)
                        <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($soldOutProducts->take(5) as $product)
                                <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                             <img src="{{ $product->image_url ?: 'https://via.placeholder.com/50' }}" 
                                         alt="{{ $product->name }}" 
                                         class="rounded" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold" style="font-size: 0.875rem;">{{ $product->name }}</div>
                                        <small class="text-muted">{{ $product->category_label }}</small>
                                    </div>
                                    <span class="badge bg-danger">Habis</span>
                                </div>
                            @endforeach
                            @if($soldOutProducts->count() > 5)
                                <div class="text-center mt-2">
                                    <small class="text-muted">Dan {{ $soldOutProducts->count() - 5 }} produk lainnya...</small>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('admin.reports.download-soldout') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-download me-2"></i>Download Laporan (CSV)
                        </a>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3" style="opacity: 0.3;"></i>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">Semua produk masih tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\Order::where('payment_status', 'paid')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</h3>
                    <p>Penjualan Minggu Ini</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon accent">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\Order::where('payment_status', 'paid')->whereMonth('created_at', now()->month)->count() }}</h3>
                    <p>Penjualan Bulan Ini</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\Order::where('payment_status', 'paid')->whereYear('created_at', now()->year)->count() }}</h3>
                    <p>Penjualan Tahun Ini</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodRadios = document.querySelectorAll('input[name="period_type"]');
        const customDateRange = document.getElementById('custom-date-range');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const form = document.querySelector('form');

        periodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateRange.style.display = 'block';
                    startDateInput.required = true;
                    endDateInput.required = true;
                } else {
                    customDateRange.style.display = 'none';
                    startDateInput.required = false;
                    endDateInput.required = false;
                    
                    const today = new Date();
                    let startDate, endDate;

                    if (this.value === 'week') {
                        const firstDay = today.getDate() - today.getDay();
                        startDate = new Date(today.setDate(firstDay));
                        endDate = new Date();
                    } else if (this.value === 'month') {
                        startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                        endDate = new Date();
                    } else if (this.value === 'year') {
                        startDate = new Date(today.getFullYear(), 0, 1);
                        endDate = new Date();
                    }

                    startDateInput.value = startDate.toISOString().split('T')[0];
                    endDateInput.value = endDate.toISOString().split('T')[0];
                }
            });
        });

        // Trigger change on page load
        document.querySelector('input[name="period_type"]:checked').dispatchEvent(new Event('change'));
    });
</script>
@endpush
