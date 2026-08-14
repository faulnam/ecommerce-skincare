@extends('layouts.admin')

@section('title', 'Log Notifikasi Email')
@section('header', 'Log Notifikasi Email')

@section('content')
<div class="card bg-white shadow-sm rounded-3 border-0">
    <div class="card-body p-4">
        
        <!-- Filter & Search -->
        <form method="GET" action="{{ route('admin.notification-logs.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari email, subjek, pesan..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $group => $items)
                        <optgroup label="{{ $group }}">
                            @foreach($items as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100">Cari</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.notification-logs.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">Waktu</th>
                        <th>Email Tujuan</th>
                        <th>Kategori</th>
                        <th>Subjek / Pesan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-nowrap text-muted small">
                                {{ $log->created_at->locale('id')->translatedFormat('d M Y H:i') }} WIB
                            </td>
                            <td>
                                <div class="fw-medium">{{ $log->email }}</div>
                                @if($log->user)
                                    <small class="text-muted"><i class="bi bi-person"></i> {{ $log->user->name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary rounded-pill">
                                    {{ $log->category }}
                                </span>
                            </td>
                            <td style="max-width: 300px;">
                                <div class="fw-semibold text-truncate" title="{{ $log->subject }}">{{ $log->subject }}</div>
                                <div class="small text-muted text-truncate" title="{{ $log->message }}">{{ Str::limit($log->message, 80) }}</div>
                            </td>
                            <td>
                                @if($log->status == 'sent')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3"><i class="bi bi-check-circle me-1"></i>Terkirim</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.notification-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Belum ada log notifikasi email yang tercatat.
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
