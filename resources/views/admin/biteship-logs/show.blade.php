@extends('layouts.admin')

@section('title', 'Detail Log Biteship')
@section('page-title', 'Detail Log Biteship')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
    <a href="{{ route('admin.biteship-logs.index') }}">Log Biteship</a> /
    <span class="text-dark">Detail</span>
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card bg-white shadow-sm rounded-3 border-0">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center p-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-truck text-primary me-2"></i>Detail Biteship Log</h5>
                @if($log->status_code >= 200 && $log->status_code < 300)
                    <span class="badge bg-success">{{ $log->status_code }} OK</span>
                @else
                    <span class="badge bg-danger">{{ $log->status_code }} Error</span>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-medium">Waktu Request</div>
                    <div class="col-sm-9">{{ $log->created_at->locale('id')->translatedFormat('d F Y, H:i:s') }} WIB</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-medium">Order ID</div>
                    <div class="col-sm-9 fw-bold">{{ $log->order_id ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-medium">Endpoint</div>
                    <div class="col-sm-9"><code>{{ $log->endpoint }}</code></div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3 text-muted fw-medium">Method</div>
                    <div class="col-sm-9"><span class="badge bg-secondary">{{ $log->method }}</span></div>
                </div>
                
                @if($log->error_message)
                <div class="mt-4 mb-4">
                    <h6 class="text-danger fw-medium mb-2">Pesan Error:</h6>
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-3" style="font-family: monospace;">{{ $log->error_message }}</div>
                </div>
                @endif
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6 class="text-muted fw-medium mb-3">Request Payload:</h6>
                        <pre class="bg-dark text-white p-3 rounded-3" style="font-size: 0.85rem; max-height: 400px; overflow-y: auto;">{{ json_encode($log->request_payload, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted fw-medium mb-3">Response Payload:</h6>
                        <pre class="bg-dark text-white p-3 rounded-3" style="font-size: 0.85rem; max-height: 400px; overflow-y: auto;">{{ json_encode($log->response_payload, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.biteship-logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
