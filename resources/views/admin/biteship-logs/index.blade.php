@extends('layouts.admin')

@section('title', 'Log Biteship')
@section('page-title', 'Log Biteship')

@section('content')
<div class="card bg-white shadow-sm rounded-3 border-0">
    <div class="card-body p-4">
        
        <!-- Filter & Search -->
        <form method="GET" action="{{ route('admin.biteship-logs.index') }}" class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari Order ID atau Endpoint..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100">Cari</button>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.biteship-logs.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">Waktu</th>
                        <th>Order ID</th>
                        <th>Endpoint</th>
                        <th>Method</th>
                        <th>Status Code</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-nowrap text-muted small">
                                {{ $log->created_at->locale('id')->translatedFormat('d M Y H:i:s') }} WIB
                            </td>
                            <td>
                                <span class="fw-medium">{{ $log->order_id ?? '-' }}</span>
                            </td>
                            <td>
                                <code>{{ $log->endpoint }}</code>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $log->method }}</span>
                            </td>
                            <td>
                                @if($log->status_code >= 200 && $log->status_code < 300)
                                    <span class="badge bg-success">{{ $log->status_code }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $log->status_code }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.biteship-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-truck fs-1 d-block mb-3"></i>
                                Belum ada log Biteship yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links('pagination.admin') }}
        </div>
        
    </div>
</div>
@endsection
