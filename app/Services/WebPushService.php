<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushService
{
    protected ?WebPush $webPush = null;

    public function __construct()
    {
        $this->initWebPush();
    }

    /**
     * Initialize WebPush with VAPID keys
     */
    protected function initWebPush(): void
    {
        $publicKey = config('services.webpush.public_key');
        $privateKey = config('services.webpush.private_key');
    $subject = config('services.webpush.subject', 'mailto:admin@luminaskincare.id');

        if (!$publicKey || !$privateKey) {
            Log::warning('WebPush: VAPID keys not configured');
            return;
        }

        try {
            $auth = [
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ];

            $this->webPush = new WebPush($auth);
            $this->webPush->setReuseVAPIDHeaders(true);
        } catch (\Exception $e) {
            Log::error('WebPush initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Send push notification to a user
     */
    public function sendToUser(User $user, array $payload): bool
    {
        if (!$this->webPush) {
            Log::warning('WebPush not initialized');
            return false;
        }

        $subscriptions = PushSubscription::where('user_id', $user->id)->get();
        
        if ($subscriptions->isEmpty()) {
            Log::info("No push subscriptions for user {$user->id}");
            return false;
        }

        $success = false;
        foreach ($subscriptions as $sub) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?? 'aesgcm',
                ]);

                $result = $this->webPush->sendOneNotification(
                    $subscription,
                    json_encode($payload)
                );

                if ($result->isSuccess()) {
                    $success = true;
                    Log::info("Push sent to user {$user->id}");
                } else {
                    Log::warning("Push failed for user {$user->id}: " . $result->getReason());
                    
                    // Remove expired/invalid subscriptions
                    if ($result->isSubscriptionExpired()) {
                        $sub->delete();
                        Log::info("Removed expired subscription for user {$user->id}");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Push error for user {$user->id}: " . $e->getMessage());
            }
        }

        return $success;
    }

    /**
     * Send push notification with standard format
     */
    public function send(User $user, string $title, string $message, ?string $url = null, string $type = 'default'): bool
    {
        $payload = [
            'title' => $title,
            'body' => $message,
            'message' => $message,
            'icon' => '/storage/logo.png',
            'badge' => '/images/skincare-skincare-favicon.svg',
            'url' => $url ?? '/',
            'type' => $type,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToUser($user, $payload);
    }

    /**
     * Send to all admins
     */
    public function sendToAdmins(string $title, string $message, ?string $url = null, string $type = 'default'): int
    {
        $admins = User::where('role', 'admin')->get();
        $sent = 0;
        
        foreach ($admins as $admin) {
            if ($this->send($admin, $title, $message, $url, $type)) {
                $sent++;
            }
        }
        
        Log::info("Push sent to {$sent} admins");
        return $sent;
    }

    /**
     * Send to all couriers
     */
    public function sendToCouriers(string $title, string $message, ?string $url = null, string $type = 'default'): int
    {
        $couriers = User::where('role', 'courier')->get();
        $sent = 0;
        
        foreach ($couriers as $courier) {
            if ($this->send($courier, $title, $message, $url, $type)) {
                $sent++;
            }
        }
        
        Log::info("Push sent to {$sent} couriers");
        return $sent;
    }

    /**
     * Send to specific courier
     */
    public function sendToCourier(User $courier, string $title, string $message, ?string $url = null): bool
    {
        return $this->send($courier, $title, $message, $url, 'courier_assigned');
    }

    /**
     * Send to customer
     */
    public function sendToCustomer(User $customer, string $title, string $message, ?string $url = null, string $type = 'status_changed'): bool
    {
        return $this->send($customer, $title, $message, $url, $type);
    }
}
