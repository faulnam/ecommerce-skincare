@extends('layouts.admin')

@section('title', 'Detail Log Notifikasi')
@section('page-title', 'Detail Log Notifikasi Email')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
    <a href="{{ route('admin.notification-logs.index') }}">Log Email Notifikasi</a> /
    <span class="text-dark">Detail</span>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card bg-white shadow-sm rounded-3 border-0">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center p-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-envelope-open-text text-primary me-2"></i>Detail Email</h5>
                @if($log->status == 'sent')
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i>Terkirim</span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">{{ $log->status }}</span>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-sm-3 text-muted fw-medium">Waktu Terkirim</div>
                    <div class="col-sm-9">{{ $log->created_at->locale('id')->translatedFormat('d F Y, H:i:s') }} WIB</div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3 text-muted fw-medium">Penerima</div>
                    <div class="col-sm-9">
                        <strong>{{ $log->email }}</strong>
                        @if($log->user)
                            <br><small class="text-muted">User: {{ $log->user->name }}</small>
                        @endif
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3 text-muted fw-medium">Kategori</div>
                    <div class="col-sm-9">
                        <span class="badge bg-secondary rounded-pill">{{ $log->category }}</span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3 text-muted fw-medium">Subjek</div>
                    <div class="col-sm-9 fw-bold">{{ $log->subject }}</div>
                </div>
                
                <hr>
                
                <div class="mt-4">
                    <h6 class="text-muted fw-medium mb-3">Isi Pesan:</h6>
                    @php
                        // Decode quoted-printable and HTML entities
                        $cleanMessage = html_entity_decode(quoted_printable_decode($log->message));
                        // Remove excessive newlines (more than 2) and trailing spaces
                        $cleanMessage = preg_replace('/[ \t]+/', ' ', $cleanMessage); // Collapse horizontal spaces
                        $cleanMessage = preg_replace("/(\r?\n){3,}/", "\n\n", $cleanMessage); // Collapse vertical spaces
                        $cleanMessage = trim($cleanMessage);
                    @endphp
                    <div class="p-4 bg-light rounded-3" style="white-space: pre-wrap; font-size: 0.95rem;">{{ $cleanMessage }}</div>
                </div>

                @if($log->error_message)
                <div class="mt-4">
                    <h6 class="text-danger fw-medium mb-3">Pesan Error:</h6>
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-3" style="font-family: monospace;">{{ $log->error_message }}</div>
                </div>
                @endif
                
                <div class="mt-5 text-end">
                    <a href="{{ route('admin.notification-logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Log
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
