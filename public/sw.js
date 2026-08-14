/**
 * Service Worker for Push Notifications
 * Hijab - Push Notification System
 */

const CACHE_NAME = 'hijab-hijab-v1';

// Install event
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activated');
    event.waitUntil(clients.claim());
});

// Push notification received
self.addEventListener('push', (event) => {
    console.log('Service Worker: Push received');
    
    let data = {
        title: 'Hijab',
        body: 'Anda memiliki notifikasi baru',
    icon: '/storage/logo.png',
        badge: '/images/hijab-hijab-favicon.svg',
        url: '/',
        type: 'default'
    };
    
    if (event.data) {
        try {
            data = { ...data, ...event.data.json() };
        } catch (e) {
            data.body = event.data.text();
        }
    }
    
    const options = {
        body: data.body || data.message,
    icon: data.icon || '/storage/logo.png',
    badge: data.badge || '/images/hijab-hijab-favicon.svg',
        vibrate: [200, 100, 200, 100, 200],
        tag: data.tag || 'notification-' + Date.now(),
        renotify: true,
        requireInteraction: true,
        actions: [
            { action: 'open', title: 'Lihat Detail', icon: '/images/icon-open.png' },
            { action: 'close', title: 'Tutup', icon: '/images/icon-close.png' }
        ],
        data: {
            url: data.url || '/',
            type: data.type,
            notificationId: data.id
        }
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    console.log('Service Worker: Notification clicked');
    
    event.notification.close();
    
    const urlToOpen = event.notification.data?.url || '/';
    
    if (event.action === 'close') {
        return;
    }
    
    // Open or focus the window
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window open
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.focus();
                        client.navigate(urlToOpen);
                        return;
                    }
                }
                // Open new window if none exists
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Background sync for offline notifications
self.addEventListener('sync', (event) => {
    if (event.tag === 'check-notifications') {
        event.waitUntil(checkForNotifications());
    }
});

// Periodic background sync (if supported)
self.addEventListener('periodicsync', (event) => {
    if (event.tag === 'check-notifications') {
        event.waitUntil(checkForNotifications());
    }
});

async function checkForNotifications() {
    try {
        const response = await fetch('/api/notifications/check-push', {
            credentials: 'include'
        });
        
        if (response.ok) {
            const data = await response.json();
            
            if (data.notifications && data.notifications.length > 0) {
                for (const notif of data.notifications) {
                    await self.registration.showNotification(notif.title, {
                        body: notif.message,
                        icon: '/storage/logo.png',
                        badge: '/images/hijab-hijab-favicon.svg',
                        vibrate: [200, 100, 200],
                        tag: 'notif-' + notif.id,
                        renotify: true,
                        data: {
                            url: notif.url || '/',
                            notificationId: notif.id
                        }
                    });
                }
            }
        }
    } catch (error) {
        console.error('Background notification check failed:', error);
    }
}
