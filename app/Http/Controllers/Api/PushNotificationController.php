<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller
{
    /**
     * Get VAPID public key for push subscription
     */
    public function getVapidKey()
    {
        $publicKey = config('services.webpush.public_key');
        
        if (!$publicKey) {
            return response()->json([
                'publicKey' => null,
                'message' => 'Push notifications not configured'
            ], 200);
        }
        
        return response()->json([
            'publicKey' => $publicKey
        ]);
    }

    /**
     * Subscribe user to push notifications
     */
    public function subscribe(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = $request->input('subscription');
        
        if (!$subscription || !isset($subscription['endpoint'])) {
            return response()->json(['error' => 'Invalid subscription'], 400);
        }

        // Save or update subscription
        PushSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'endpoint' => $subscription['endpoint']
            ],
            [
                'public_key' => $subscription['keys']['p256dh'] ?? null,
                'auth_token' => $subscription['keys']['auth'] ?? null,
                'content_encoding' => $subscription['contentEncoding'] ?? 'aesgcm',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription saved'
        ]);
    }

    /**
     * Unsubscribe user from push notifications
     */
    public function unsubscribe(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Delete all subscriptions for this user
        PushSubscription::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unsubscribed'
        ]);
    }
}
