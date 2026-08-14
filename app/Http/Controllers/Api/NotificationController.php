<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Check for new notifications
     */
    public function check(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'notifications' => [],
                'unread_count' => 0
            ]);
        }

        $since = $request->input('since');
        $role = $request->input('role', $user->role);

        // Get unread notifications
        $query = $user->unreadNotifications();
        
        if ($since) {
            try {
                $sinceDate = \Carbon\Carbon::parse($since);
                $query->where('created_at', '>', $sinceDate);
            } catch (\Exception $e) {
                // Invalid date, ignore filter
            }
        }

        $notifications = $query->take(5)->get()->map(function ($notification) {
            $data = $notification->data;
            
            return [
                'id' => $notification->id,
                'type' => $data['type'] ?? $this->getTypeFromNotification($notification),
                'title' => $data['title'] ?? 'Notifikasi',
                'message' => $data['message'] ?? $data['body'] ?? '',
                'url' => $data['url'] ?? $data['action_url'] ?? null,
                'created_at' => $notification->created_at->toISOString(),
            ];
        });

        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Get notification type from class name
     */
    protected function getTypeFromNotification($notification): string
    {
        $class = class_basename($notification->type);
        
        $typeMap = [
            'NewOrderNotification' => 'new_order',
            'PaymentUploadedNotification' => 'payment_uploaded',
            'OrderStatusChanged' => 'status_changed',
            'CourierAssigned' => 'courier_assigned',
            'OrderDelivered' => 'delivery',
        ];

        return $typeMap[$class] ?? 'default';
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
