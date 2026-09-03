@extends('layouts.admin')

@section('title', 'Test Push Notification')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Test Push Notification</h5>
                </div>
                <div class="card-body">
                    <!-- Push Status -->
                    <div class="alert alert-info mb-4" id="pushStatus">
                        <i class="fas fa-spinner fa-spin me-2"></i>Mengecek status push notification...
                    </div>

                    <!-- Enable Push Button -->
                    <div class="mb-4" id="enablePushSection" style="display: none;">
                        <button type="button" class="btn btn-success btn-lg w-100" onclick="enablePushNotifications()">
                            <i class="fas fa-bell me-2"></i>Aktifkan Push Notification
                        </button>
                        <p class="text-muted small mt-2 mb-0">Klik untuk mengizinkan notifikasi muncul di HP/Laptop Anda</p>
                    </div>

                    <!-- Test Form -->
                    <div id="testSection" style="display: none;">
                        <h6 class="mb-3">Kirim Test Notification</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Tipe Notifikasi</label>
                            <select class="form-select" id="notifType">
                                <option value="new_order">🛒 Pesanan Baru</option>
                                <option value="payment_uploaded">💳 Pembayaran Diupload</option>
                                <option value="courier_assigned">🛵 Kurir Ditugaskan</option>
                                <option value="status_changed">📦 Status Berubah</option>
                                <option value="default">🔔 Default</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control" id="notifTitle" value="Test Notifikasi">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pesan</label>
                            <textarea class="form-control" id="notifMessage" rows="2">Ini adalah test push notification dari sistem</textarea>
                        </div>

                        <button type="button" class="btn btn-primary w-100" onclick="sendTestNotification()">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Test Notification
                        </button>

                        <hr class="my-4">

                        <h6 class="mb-3">Test dari Server</h6>
                        <button type="button" class="btn btn-warning w-100" onclick="sendServerPush()">
                            <i class="fas fa-server me-2"></i>Kirim Push dari Server
                        </button>
                        <p class="text-muted small mt-2">Ini akan mengirim push notification melalui server (seperti notifikasi asli)</p>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Cara Kerja Push Notification</h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">User mengaktifkan notifikasi (klik "Aktifkan")</li>
                        <li class="mb-2">Browser meminta izin → User klik "Allow"</li>
                        <li class="mb-2">Subscription tersimpan di database</li>
                        <li class="mb-2">Saat ada event (order baru, dll), server kirim push</li>
                        <li class="mb-0">Notifikasi muncul di HP/Laptop meski browser tertutup! 🎉</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    await checkPushStatus();
});

async function checkPushStatus() {
    const statusEl = document.getElementById('pushStatus');
    const enableSection = document.getElementById('enablePushSection');
    const testSection = document.getElementById('testSection');

    // Check if push is supported
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        statusEl.className = 'alert alert-danger';
        statusEl.innerHTML = '<i class="fas fa-times-circle me-2"></i>Browser Anda tidak mendukung push notification';
        return;
    }

    // Check permission status
    const permission = Notification.permission;
    
    if (permission === 'granted') {
        // Check if service worker is registered
        const registration = await navigator.serviceWorker.getRegistration('/sw.js');
        if (registration) {
            const subscription = await registration.pushManager.getSubscription();
            if (subscription) {
                statusEl.className = 'alert alert-success';
                statusEl.innerHTML = '<i class="fas fa-check-circle me-2"></i>Push notification sudah aktif! ✅';
                testSection.style.display = 'block';
            } else {
                statusEl.className = 'alert alert-warning';
                statusEl.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Izin diberikan, tapi belum subscribe';
                enableSection.style.display = 'block';
            }
        } else {
            statusEl.className = 'alert alert-warning';
            statusEl.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Service Worker belum terdaftar';
            enableSection.style.display = 'block';
        }
    } else if (permission === 'denied') {
        statusEl.className = 'alert alert-danger';
        statusEl.innerHTML = '<i class="fas fa-ban me-2"></i>Notifikasi diblokir. Silakan aktifkan di pengaturan browser.';
    } else {
        statusEl.className = 'alert alert-info';
        statusEl.innerHTML = '<i class="fas fa-info-circle me-2"></i>Push notification belum diaktifkan';
        enableSection.style.display = 'block';
    }
}

async function enablePushNotifications() {
    if (typeof window.enablePushNotifications === 'function') {
        await window.enablePushNotifications();
        location.reload();
    } else {
        // Manual enable
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            // Register service worker
            const registration = await navigator.serviceWorker.register('/sw.js');
            console.log('SW registered:', registration);
            
            // Get VAPID key
            const keyResponse = await fetch('/api/push/vapid-key');
            const keyData = await keyResponse.json();
            
            if (keyData.publicKey) {
                // Subscribe
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(keyData.publicKey)
                });
                
                // Send to server
                await fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ subscription: subscription.toJSON() })
                });
                
                alert('✅ Push notification berhasil diaktifkan!');
                location.reload();
            }
        }
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

async function sendTestNotification() {
    const type = document.getElementById('notifType').value;
    const title = document.getElementById('notifTitle').value;
    const message = document.getElementById('notifMessage').value;
    
    // Show local notification via service worker
    const registration = await navigator.serviceWorker.getRegistration('/sw.js');
    if (registration) {
        await registration.showNotification(title, {
            body: message,
            icon: '/storage/logo.png',
            badge: '/images/skincare-skincare-favicon.svg',
            vibrate: [200, 100, 200],
            tag: 'test-' + Date.now(),
            data: { url: '/admin/dashboard', type: type }
        });
        alert('✅ Test notification terkirim! Cek notifikasi Anda.');
    } else {
        alert('❌ Service Worker belum terdaftar');
    }
}

async function sendServerPush() {
    try {
        const response = await fetch('/admin/test-push', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✅ Push notification dikirim dari server!');
        } else {
            alert('❌ Gagal: ' + (data.message || 'Unknown error'));
        }
    } catch (e) {
        alert('❌ Error: ' + e.message);
    }
}
</script>
@endsection
