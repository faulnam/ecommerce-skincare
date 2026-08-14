/**
 * Push Notification Manager
 * Handles service worker registration and push subscription
 */

class PushNotificationManager {
    constructor() {
        this.swRegistration = null;
        this.isSubscribed = false;
        this.vapidPublicKey = null;
    }

    async init() {
        // Check if service workers and push are supported
        if (!('serviceWorker' in navigator)) {
            console.log('Service workers not supported');
            return false;
        }

        if (!('PushManager' in window)) {
            console.log('Push notifications not supported');
            return false;
        }

        try {
            // Register service worker
            this.swRegistration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });
            console.log('Service Worker registered:', this.swRegistration);

            // Check current subscription status
            const subscription = await this.swRegistration.pushManager.getSubscription();
            this.isSubscribed = subscription !== null;

            if (this.isSubscribed) {
                console.log('Already subscribed to push notifications');
            }

            return true;
        } catch (error) {
            console.error('Service Worker registration failed:', error);
            return false;
        }
    }

    async requestPermission() {
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            console.log('Notification permission granted');
            return true;
        } else {
            console.log('Notification permission denied');
            return false;
        }
    }

    async subscribe() {
        try {
            // First request permission
            const permissionGranted = await this.requestPermission();
            if (!permissionGranted) {
                return null;
            }

            // Get VAPID public key from server
            const keyResponse = await fetch('/api/push/vapid-key');
            if (!keyResponse.ok) {
                console.log('VAPID key not available, using local notifications only');
                return null;
            }
            
            const keyData = await keyResponse.json();
            this.vapidPublicKey = keyData.publicKey;

            if (!this.vapidPublicKey) {
                console.log('No VAPID key configured');
                return null;
            }

            // Subscribe to push
            const subscription = await this.swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
            });

            console.log('Push subscription:', subscription);

            // Send subscription to server
            await this.sendSubscriptionToServer(subscription);

            this.isSubscribed = true;
            return subscription;
        } catch (error) {
            console.error('Failed to subscribe:', error);
            return null;
        }
    }

    async sendSubscriptionToServer(subscription) {
        try {
            const response = await fetch('/api/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    subscription: subscription.toJSON()
                })
            });

            if (!response.ok) {
                throw new Error('Failed to save subscription on server');
            }

            console.log('Subscription saved on server');
            return true;
        } catch (error) {
            console.error('Error saving subscription:', error);
            return false;
        }
    }

    async unsubscribe() {
        try {
            const subscription = await this.swRegistration.pushManager.getSubscription();
            
            if (subscription) {
                await subscription.unsubscribe();
                
                // Notify server
                await fetch('/api/push/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });
            }

            this.isSubscribed = false;
            console.log('Unsubscribed from push notifications');
            return true;
        } catch (error) {
            console.error('Error unsubscribing:', error);
            return false;
        }
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // Show local notification (fallback when push not available)
    async showLocalNotification(title, options = {}) {
        if (!this.swRegistration) {
            console.log('Service worker not registered');
            return;
        }

        if (Notification.permission !== 'granted') {
            console.log('Notification permission not granted');
            return;
        }

        return this.swRegistration.showNotification(title, {
            body: options.body || options.message,
            icon: options.icon || '/storage/logo.png',
            badge: '/images/hijab-hijab-favicon.svg',
            vibrate: [200, 100, 200],
            tag: options.tag || 'local-' + Date.now(),
            renotify: true,
            requireInteraction: options.requireInteraction || false,
            data: {
                url: options.url || '/',
                notificationId: options.id
            }
        });
    }
}

// Export for use
window.PushNotificationManager = PushNotificationManager;
