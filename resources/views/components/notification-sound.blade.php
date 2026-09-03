{{-- 
    Notification Sound Component
    Include this in layouts to enable sound notifications
    
    Usage: <x-notification-sound :role="'admin'" />
--}}

@props(['role' => 'customer'])

{{-- Load Push Notification Manager --}}
<script src="{{ asset('js/push-notifications.js') }}" defer></script>

{{-- Push Notification Permission Banner --}}
<div id="pushNotificationBanner" class="push-notification-banner" style="display: none;">
    <div class="push-banner-content">
        <div class="push-banner-icon">
            <i class="fas fa-bell"></i>
        </div>
        <div class="push-banner-text">
            <strong>Aktifkan Notifikasi</strong>
            <p>Dapatkan pemberitahuan pesanan langsung di HP/Laptop Anda</p>
        </div>
        <div class="push-banner-actions">
            <button type="button" class="btn-push-enable" onclick="window.enablePushNotifications()">
                <i class="fas fa-check me-1"></i>Aktifkan
            </button>
            <button type="button" class="btn-push-later" onclick="window.dismissPushBanner()">
                Nanti
            </button>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div id="notificationToastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 380px;"></div>

{{-- Floating Modal Overlay --}}
<div id="notificationModalOverlay" class="notification-modal-overlay" style="display: none;">
    <div class="notification-modal">
        <div class="notification-modal-icon" id="modalNotifIcon">
            <i class="fas fa-bell"></i>
        </div>
        <div class="notification-modal-content">
            <h4 id="modalNotifTitle">Notifikasi Baru</h4>
            <p id="modalNotifMessage">Anda memiliki notifikasi baru</p>
            <span class="notification-modal-time" id="modalNotifTime">Baru saja</span>
        </div>
        <div class="notification-modal-actions">
            <button type="button" class="btn-modal-secondary" onclick="window.notificationManager.closeModal()">
                Tutup
            </button>
            <button type="button" class="btn-modal-primary" id="modalNotifAction">
                Lihat Detail
            </button>
        </div>
    </div>
</div>

<style>
    /* Push Notification Banner */
    .push-notification-banner {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #1e293b, #334155);
        color: white;
        padding: 16px 24px;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        z-index: 99998;
        max-width: 500px;
        width: calc(100% - 40px);
        animation: slideUp 0.4s ease;
    }
    
    .push-banner-content {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    
    .push-banner-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    
    .push-banner-text {
        flex: 1;
        min-width: 200px;
    }
    
    .push-banner-text strong {
        font-size: 15px;
        display: block;
        margin-bottom: 4px;
    }
    
    .push-banner-text p {
        font-size: 13px;
        opacity: 0.8;
        margin: 0;
    }
    
    .push-banner-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-push-enable {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-push-enable:hover {
        transform: scale(1.05);
    }
    
    .btn-push-later {
        background: transparent;
        color: rgba(255,255,255,0.7);
        border: 1px solid rgba(255,255,255,0.3);
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-push-later:hover {
        background: rgba(255,255,255,0.1);
    }
    
    @keyframes slideUp {
        from { transform: translateX(-50%) translateY(100px); opacity: 0; }
        to { transform: translateX(-50%) translateY(0); opacity: 1; }
    }
    
    @media (max-width: 500px) {
        .push-banner-content {
            flex-direction: column;
            text-align: center;
        }
        .push-banner-actions {
            width: 100%;
            justify-content: center;
        }
    }

    /* Floating Modal Overlay */
    .notification-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
        padding: 20px;
    }
    
    .notification-modal {
        background: white;
        border-radius: 20px;
        padding: 30px;
        max-width: 420px;
        width: 100%;
        text-align: center;
        box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        animation: bounceIn 0.4s ease;
        position: relative;
    }
    
    .notification-modal-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 20px;
        animation: pulse 1.5s infinite;
    }
    
    .notification-modal-icon.order {
        background: linear-gradient(135deg, #fef3c7, #fcd34d);
        color: #b45309;
    }
    
    .notification-modal-icon.payment {
        background: linear-gradient(135deg, #dcfce7, #86efac);
        color: #15803d;
    }
    
    .notification-modal-icon.courier, .notification-modal-icon.delivery {
        background: linear-gradient(135deg, #dbeafe, #93c5fd);
        color: #1d4ed8;
    }
    
    .notification-modal-icon.default {
        background: linear-gradient(135deg, #e0e7ff, #a5b4fc);
        color: #4338ca;
    }
    
    .notification-modal-content h4 {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 12px;
    }
    
    .notification-modal-content p {
        font-size: 15px;
        color: #6b7280;
        margin-bottom: 8px;
        line-height: 1.5;
    }
    
    .notification-modal-time {
        font-size: 12px;
        color: #9ca3af;
    }
    
    .notification-modal-actions {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .btn-modal-primary {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    
    .btn-modal-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
    }
    
    .btn-modal-secondary {
        background: #f3f4f6;
        color: #4b5563;
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-modal-secondary:hover {
        background: #e5e7eb;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes bounceIn {
        0% { transform: scale(0.5); opacity: 0; }
        60% { transform: scale(1.05); }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Toast styles */
    .notification-toast {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        padding: 16px;
        margin-bottom: 10px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        animation: slideIn 0.3s ease;
        border-left: 4px solid #22c55e;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .notification-toast:hover {
        transform: translateX(-5px);
    }
    
    .notification-toast.order {
        border-left-color: #f59e0b;
    }
    
    .notification-toast.delivery {
        border-left-color: #3b82f6;
    }
    
    .notification-toast.payment {
        border-left-color: #22c55e;
    }
    
    .notification-toast .toast-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .notification-toast.order .toast-icon {
        background: #fef3c7;
        color: #f59e0b;
    }
    
    .notification-toast.delivery .toast-icon {
        background: #dbeafe;
        color: #3b82f6;
    }
    
    .notification-toast.payment .toast-icon {
        background: #dcfce7;
        color: #22c55e;
    }
    
    .notification-toast .toast-content {
        flex: 1;
    }
    
    .notification-toast .toast-title {
        font-weight: 600;
        font-size: 14px;
        color: #1f2937;
        margin-bottom: 4px;
    }
    
    .notification-toast .toast-message {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.4;
    }
    
    .notification-toast .toast-time {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }
    
    .notification-toast .toast-close {
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
        font-size: 14px;
    }
    
    .notification-toast .toast-close:hover {
        color: #6b7280;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    /* Notification badge pulse */
    .notification-badge-pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
</style>

<script>
class NotificationManager {
    constructor(role) {
        this.role = role;
        this.lastCheckTime = new Date().toISOString();
        this.isEnabled = true;
        this.checkInterval = null;
        this.audioContext = null;
        this.toastContainer = document.getElementById('notificationToastContainer');
        
        // Initialize audio context on first user interaction
        this.initAudioOnInteraction();
        
        // Request notification permission
        this.requestPermission();
        
        // Start polling
        this.startPolling();
        
        // Check for sound preference
        this.loadSoundPreference();
    }
    
    initAudioOnInteraction() {
        const initAudio = () => {
            if (!this.audioContext) {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }
            document.removeEventListener('click', initAudio);
            document.removeEventListener('keydown', initAudio);
        };
        
        document.addEventListener('click', initAudio);
        document.addEventListener('keydown', initAudio);
    }
    
    loadSoundPreference() {
        const saved = localStorage.getItem('notificationSoundEnabled');
        this.isEnabled = saved === null ? true : saved === 'true';
    }
    
    toggleSound(enabled) {
        this.isEnabled = enabled;
        localStorage.setItem('notificationSoundEnabled', enabled);
    }
    
    requestPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }
    
    startPolling() {
        // Check every 10 seconds
        this.checkInterval = setInterval(() => {
            this.checkNewNotifications();
        }, 10000);
        
        // Initial check after 2 seconds
        setTimeout(() => this.checkNewNotifications(), 2000);
    }
    
    stopPolling() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
    }
    
    async checkNewNotifications() {
        try {
            const response = await fetch(`/api/notifications/check?role=${this.role}&since=${encodeURIComponent(this.lastCheckTime)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) return;
            
            const data = await response.json();
            
            if (data.notifications && data.notifications.length > 0) {
                this.lastCheckTime = new Date().toISOString();
                
                data.notifications.forEach(notif => {
                    this.showNotification(notif);
                });
                
                // Update badge count if exists
                this.updateBadgeCount(data.unread_count || 0);
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }
    
    showNotification(notif) {
        // Play sound
        this.playSound(notif.type);
        
        // Show floating modal popup (more prominent)
        this.showFloatingModal(notif);
        
        // Also show browser notification if permitted
        this.showBrowserNotification(notif);
    }
    
    showFloatingModal(notif) {
        const overlay = document.getElementById('notificationModalOverlay');
        const iconContainer = document.getElementById('modalNotifIcon');
        const title = document.getElementById('modalNotifTitle');
        const message = document.getElementById('modalNotifMessage');
        const time = document.getElementById('modalNotifTime');
        const actionBtn = document.getElementById('modalNotifAction');
        
        // Get icon and type class
        const icon = this.getIcon(notif.type);
        const typeClass = this.getTypeClass(notif.type);
        
        // Update modal content
        iconContainer.className = `notification-modal-icon ${typeClass}`;
        iconContainer.innerHTML = `<i class="${icon}"></i>`;
        title.textContent = notif.title || 'Notifikasi Baru';
        message.textContent = notif.message || '';
        time.textContent = 'Baru saja';
        
        // Setup action button
        if (notif.url) {
            actionBtn.style.display = 'inline-block';
            actionBtn.onclick = () => {
                window.location.href = notif.url;
            };
        } else {
            actionBtn.style.display = 'none';
        }
        
        // Store current notification for marking as read
        this.currentNotification = notif;
        
        // Show modal
        overlay.style.display = 'flex';
        
        // Auto close after 15 seconds
        if (this.modalTimeout) clearTimeout(this.modalTimeout);
        this.modalTimeout = setTimeout(() => {
            this.closeModal();
        }, 15000);
    }
    
    closeModal() {
        const overlay = document.getElementById('notificationModalOverlay');
        overlay.style.display = 'none';
        
        // Mark notification as read if exists
        if (this.currentNotification && this.currentNotification.id) {
            fetch(`/api/notifications/${this.currentNotification.id}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            }).catch(e => console.log('Failed to mark as read:', e));
        }
        
        if (this.modalTimeout) {
            clearTimeout(this.modalTimeout);
        }
    }
    
    getTypeClass(type) {
        const typeMap = {
            'order': 'order',
            'new_order': 'order',
            'payment': 'payment',
            'payment_uploaded': 'payment',
            'delivery': 'delivery',
            'courier': 'courier',
            'courier_assigned': 'courier',
            'status_changed': 'default'
        };
        return typeMap[type] || 'default';
    }

    playSound(type) {
        if (!this.isEnabled) return;
        
        try {
            // Initialize audio context if not exists
            if (!this.audioContext) {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }
            
            // Resume audio context if suspended
            if (this.audioContext.state === 'suspended') {
                this.audioContext.resume();
            }
            
            // Create notification sound using Web Audio API
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);
            
            // Different sounds for different notification types
            if (type === 'new_order' || type === 'order') {
                // Order notification: Two-tone ascending chime
                this.playOrderSound();
            } else if (type === 'payment_uploaded' || type === 'payment') {
                // Payment: Pleasant ding
                this.playPaymentSound();
            } else if (type === 'courier_assigned' || type === 'delivery') {
                // Courier/Delivery: Quick beeps
                this.playCourierSound();
            } else {
                // Default notification sound
                this.playDefaultSound();
            }
        } catch (e) {
            console.error('Error playing sound:', e);
        }
    }
    
    playOrderSound() {
        // Exciting two-tone for new orders
        const now = this.audioContext.currentTime;
        
        [0, 0.15, 0.3].forEach((delay, i) => {
            const osc = this.audioContext.createOscillator();
            const gain = this.audioContext.createGain();
            
            osc.connect(gain);
            gain.connect(this.audioContext.destination);
            
            osc.frequency.value = [523.25, 659.25, 783.99][i]; // C5, E5, G5
            osc.type = 'sine';
            
            gain.gain.setValueAtTime(0, now + delay);
            gain.gain.linearRampToValueAtTime(0.3, now + delay + 0.05);
            gain.gain.linearRampToValueAtTime(0, now + delay + 0.2);
            
            osc.start(now + delay);
            osc.stop(now + delay + 0.25);
        });
    }
    
    playPaymentSound() {
        // Pleasant ding for payments
        const now = this.audioContext.currentTime;
        const osc = this.audioContext.createOscillator();
        const gain = this.audioContext.createGain();
        
        osc.connect(gain);
        gain.connect(this.audioContext.destination);
        
        osc.frequency.value = 880; // A5
        osc.type = 'sine';
        
        gain.gain.setValueAtTime(0, now);
        gain.gain.linearRampToValueAtTime(0.3, now + 0.05);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
        
        osc.start(now);
        osc.stop(now + 0.5);
    }
    
    playCourierSound() {
        // Double beep for courier
        const now = this.audioContext.currentTime;
        
        [0, 0.2].forEach((delay) => {
            const osc = this.audioContext.createOscillator();
            const gain = this.audioContext.createGain();
            
            osc.connect(gain);
            gain.connect(this.audioContext.destination);
            
            osc.frequency.value = 698.46; // F5
            osc.type = 'sine';
            
            gain.gain.setValueAtTime(0, now + delay);
            gain.gain.linearRampToValueAtTime(0.25, now + delay + 0.03);
            gain.gain.linearRampToValueAtTime(0, now + delay + 0.15);
            
            osc.start(now + delay);
            osc.stop(now + delay + 0.2);
        });
    }
    
    playDefaultSound() {
        // Simple ding
        const now = this.audioContext.currentTime;
        const osc = this.audioContext.createOscillator();
        const gain = this.audioContext.createGain();
        
        osc.connect(gain);
        gain.connect(this.audioContext.destination);
        
        osc.frequency.value = 587.33; // D5
        osc.type = 'sine';
        
        gain.gain.setValueAtTime(0, now);
        gain.gain.linearRampToValueAtTime(0.25, now + 0.03);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.4);
        
        osc.start(now);
        osc.stop(now + 0.4);
    }
    
    showToast(notif) {
        const toast = document.createElement('div');
        toast.className = `notification-toast ${notif.type || 'default'}`;
        
        const icon = this.getIcon(notif.type);
        
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="${icon}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${notif.title || 'Notifikasi'}</div>
                <div class="toast-message">${notif.message || ''}</div>
                <div class="toast-time">Baru saja</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Add click handler to go to URL
        if (notif.url) {
            toast.style.cursor = 'pointer';
            toast.addEventListener('click', (e) => {
                if (!e.target.closest('.toast-close')) {
                    window.location.href = notif.url;
                }
            });
        }
        
        this.toastContainer.appendChild(toast);
        
        // Auto remove after 8 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 8000);
    }
    
    showBrowserNotification(notif) {
        // Use Service Worker for persistent notifications
        if (this.pushManager && this.pushManager.swRegistration) {
            this.pushManager.showLocalNotification(notif.title || 'LUMINA', {
                body: notif.message,
                message: notif.message,
                icon: '/images/logo.png',
                url: notif.url,
                id: notif.id,
                requireInteraction: true
            });
        } else if ('Notification' in window && Notification.permission === 'granted') {
            // Fallback to regular browser notification
            const browserNotif = new Notification(notif.title || 'LUMINA', {
                body: notif.message,
                icon: '/images/logo.png',
                badge: '/images/logo.png',
                tag: notif.id || Date.now(),
                requireInteraction: true
            });
            
            browserNotif.onclick = function() {
                window.focus();
                if (notif.url) {
                    window.location.href = notif.url;
                }
                browserNotif.close();
            };
        }
    }
    
    getIcon(type) {
        const icons = {
            'order': 'fas fa-shopping-cart',
            'new_order': 'fas fa-shopping-cart',
            'payment': 'fas fa-credit-card',
            'payment_uploaded': 'fas fa-credit-card',
            'delivery': 'fas fa-truck',
            'courier': 'fas fa-motorcycle',
            'courier_assigned': 'fas fa-motorcycle',
            'status': 'fas fa-info-circle',
            'status_changed': 'fas fa-exchange-alt',
            'default': 'fas fa-bell'
        };
        return icons[type] || icons['default'];
    }
    
    updateBadgeCount(count) {
        const badges = document.querySelectorAll('.notification-badge, .notif-count');
        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
                badge.classList.add('notification-badge-pulse');
            } else {
                badge.style.display = 'none';
                badge.classList.remove('notification-badge-pulse');
            }
        });
    }
}

// Push Notification Functions
window.pushManager = null;

window.enablePushNotifications = async function() {
    if (!window.pushManager) {
        window.pushManager = new PushNotificationManager();
    }
    
    const initialized = await window.pushManager.init();
    if (initialized) {
        const subscription = await window.pushManager.subscribe();
        if (subscription) {
            // Successfully subscribed
            localStorage.setItem('pushNotificationsEnabled', 'true');
            localStorage.setItem('pushBannerDismissed', 'true');
            document.getElementById('pushNotificationBanner').style.display = 'none';
            
            // Show success message
            alert('✅ Notifikasi berhasil diaktifkan! Anda akan menerima pemberitahuan meskipun browser ditutup.');
        } else {
            // Permission granted but no server-side push, use local notifications
            localStorage.setItem('pushNotificationsEnabled', 'local');
            localStorage.setItem('pushBannerDismissed', 'true');
            document.getElementById('pushNotificationBanner').style.display = 'none';
            
            alert('✅ Notifikasi browser diaktifkan! Anda akan menerima pemberitahuan saat website terbuka.');
        }
    }
};

window.dismissPushBanner = function() {
    document.getElementById('pushNotificationBanner').style.display = 'none';
    localStorage.setItem('pushBannerDismissed', Date.now().toString());
};

// Check if should show push notification banner
function checkPushBanner() {
    const dismissed = localStorage.getItem('pushBannerDismissed');
    const enabled = localStorage.getItem('pushNotificationsEnabled');
    
    // Don't show if already enabled
    if (enabled) return;
    
    // Don't show if dismissed within last 7 days
    if (dismissed) {
        const dismissedTime = parseInt(dismissed);
        if (!isNaN(dismissedTime) && Date.now() - dismissedTime < 7 * 24 * 60 * 60 * 1000) {
            return;
        }
    }
    
    // Check if notifications are supported
    if ('Notification' in window && Notification.permission === 'default') {
        // Show banner after 5 seconds
        setTimeout(() => {
            document.getElementById('pushNotificationBanner').style.display = 'block';
        }, 5000);
    }
}

// Initialize when DOM ready
document.addEventListener('DOMContentLoaded', async function() {
    // Initialize notification manager
    window.notificationManager = new NotificationManager('{{ $role }}');
    
    // Initialize push manager
    window.pushManager = new PushNotificationManager();
    await window.pushManager.init();
    
    // Link push manager to notification manager
    if (window.notificationManager) {
        window.notificationManager.pushManager = window.pushManager;
    }
    
    // Check if should show push banner
    checkPushBanner();
});
</script>
