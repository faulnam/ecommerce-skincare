@extends('layouts.app')

@section('title', 'Notifications - Hijab')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
        <h3 class="mb-0 notif-title">
            <i class="fas fa-bell me-2 text-success"></i>Notifications
        </h3>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('customer.notifications.mark-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-check-double me-1"></i><span class="d-none d-sm-inline">Mark All as </span>Read
                </button>
            </form>
        @endif
    </div>
    
    <div class="card">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <div class="list-group-item notif-item {{ $notification->read_at ? '' : 'bg-light' }}">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-1">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 notif-item-title">
                                    @if(!$notification->read_at)
                                        <span class="badge bg-success me-2">New</span>
                                    @endif
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h6>
                                <p class="mb-1 text-muted notif-item-message">{{ $notification->data['message'] ?? '' }}</p>
                                @if(isset($notification->data['order_number']))
                                    <a href="{{ route('customer.orders.show', $notification->data['order_id']) }}" class="text-success small">
                                        View Order #{{ $notification->data['order_number'] }}
                                    </a>
                                @endif
                            </div>
                            <small class="text-muted notif-time">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center py-4 py-lg-5">
                        <i class="fas fa-bell-slash fa-2x fa-lg-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No notifications</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>

@push('styles')
<style>
    /* Mobile Responsive */
    @media (max-width: 767.98px) {
        .notif-title {
            font-size: 1.3rem;
        }
        .notif-item {
            padding: 0.75rem 1rem;
        }
        .notif-item-title {
            font-size: 0.9rem;
        }
        .notif-item-title .badge {
            font-size: 0.65rem;
        }
        .notif-item-message {
            font-size: 0.8rem;
        }
        .notif-time {
            font-size: 0.7rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .notif-item-title {
            font-size: 0.85rem;
        }
        .notif-item-message {
            font-size: 0.75rem;
        }
    }
</style>
@endpush
@endsection
