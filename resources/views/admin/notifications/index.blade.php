@extends('layouts.admin')

@section('page-title', 'Notifikasi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-bell me-2"></i>Notifikasi</span>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('admin.notifications.mark-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notification)
                <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                @if(!$notification->read_at)
                                    <span class="badge bg-primary me-2">Baru</span>
                                @endif
                                {{ $notification->data['title'] ?? 'Notifikasi' }}
                            </h6>
                            <p class="mb-1 text-muted">{{ $notification->data['message'] ?? '' }}</p>
                            @if(isset($notification->data['order_number']))
                                <span class="text-muted small">
                                    Pesanan #{{ $notification->data['order_number'] }}
                                </span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $notifications->links('pagination.admin') }}
</div>
@endsection
