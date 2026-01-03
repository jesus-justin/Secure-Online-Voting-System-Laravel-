<!-- Notification Bell Component -->
<div class="dropdown">
    <button class="nav-link btn btn-link position-relative px-3" 
            type="button" 
            id="notificationDropdown" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            aria-label="Notifications">
        <i class="bi bi-bell-fill" style="font-size: 1.25rem;"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-count" 
              id="notificationBadge" 
              style="display: none; font-size: 0.65rem;">
            0
            <span class="visually-hidden">unread notifications</span>
        </span>
    </button>
    
    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" 
         style="width: 350px; max-height: 500px; border-radius: 12px; overflow: hidden;"
         aria-labelledby="notificationDropdown">
        
        <!-- Notification Header -->
        <div class="p-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-bell-fill me-2"></i>Notifications
            </h6>
            <button class="btn btn-sm btn-light" onclick="markAllAsRead()" aria-label="Mark all as read">
                <small>Mark all read</small>
            </button>
        </div>
        
        <!-- Notifications List -->
        <div id="notificationsList" style="max-height: 400px; overflow-y: auto;">
            <!-- Notifications will be dynamically loaded here -->
            <div class="text-center py-5 text-muted" id="emptyNotifications">
                <i class="bi bi-bell-slash" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mt-3 mb-0">No new notifications</p>
            </div>
        </div>
        
        <!-- View All Footer -->
        <div class="p-2 border-top bg-light text-center">
            <a href="{{ route('notifications.index') }}" class="text-decoration-none small fw-bold">
                View All Notifications
            </a>
        </div>
    </div>
</div>

<script>
// Notification data (would come from backend in production)
let notifications = [
    @auth
    @if(!auth()->user()->verified_at)
    {
        id: 1,
        type: 'warning',
        icon: 'bi-exclamation-triangle-fill',
        title: 'Account Verification Pending',
        message: 'Your account is awaiting administrator verification.',
        time: 'Just now',
        read: false,
        link: null
    },
    @endif
    @endauth
];

function loadNotifications() {
    const container = document.getElementById('notificationsList');
    const emptyState = document.getElementById('emptyNotifications');
    const badge = document.getElementById('notificationBadge');
    
    if (notifications.length === 0) {
        emptyState.style.display = 'block';
        badge.style.display = 'none';
        return;
    }
    
    emptyState.style.display = 'none';
    
    // Count unread
    const unreadCount = notifications.filter(n => !n.read).length;
    if (unreadCount > 0) {
        badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
    
    // Build notifications HTML
    const html = notifications.map(notif => `
        <div class="notification-item ${notif.read ? '' : 'unread'} border-bottom p-3" 
             style="cursor: pointer; transition: background 0.2s; ${notif.read ? '' : 'background-color: #f8f9fa;'}"
             onclick="markAsRead(${notif.id})"
             role="button"
             tabindex="0">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-${getNotificationColor(notif.type)} bg-opacity-10 p-2">
                        <i class="bi ${notif.icon} text-${getNotificationColor(notif.type)}" style="font-size: 1.2rem;"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1 fw-bold" style="font-size: 0.9rem;">${notif.title}</h6>
                    <p class="mb-1 text-muted small">${notif.message}</p>
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> ${notif.time}
                    </small>
                </div>
                ${!notif.read ? '<div class="flex-shrink-0"><span class="badge bg-primary rounded-pill">New</span></div>' : ''}
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

function getNotificationColor(type) {
    const colors = {
        success: 'success',
        error: 'danger',
        warning: 'warning',
        info: 'info'
    };
    return colors[type] || 'primary';
}

function markAsRead(notificationId) {
    const notif = notifications.find(n => n.id === notificationId);
    if (notif) {
        notif.read = true;
        if (notif.link) {
            window.location.href = notif.link;
        }
        loadNotifications();
        saveNotificationState();
    }
}

function markAllAsRead() {
    notifications.forEach(n => n.read = true);
    loadNotifications();
    saveNotificationState();
    showToast('All notifications marked as read', 'success');
}

function addNotification(notification) {
    notification.id = Date.now();
    notification.read = false;
    notifications.unshift(notification);
    loadNotifications();
    saveNotificationState();
    
    // Show toast for new notification
    showToast(notification.title, notification.type || 'info');
}

function saveNotificationState() {
    localStorage.setItem('notifications', JSON.stringify(notifications));
}

function loadNotificationState() {
    const saved = localStorage.getItem('notifications');
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            notifications = [...parsed, ...notifications];
        } catch (e) {
            console.error('Failed to load notifications', e);
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadNotificationState();
    loadNotifications();
    
    // Simulate checking for new notifications every 30 seconds
    setInterval(() => {
        // In production, this would be an AJAX call to fetch new notifications
        // For now, we'll just reload the existing ones
        loadNotifications();
    }, 30000);
});

// Export function globally
window.addNotification = addNotification;
</script>

<style>
.notification-item:hover {
    background-color: #f8f9fa !important;
}

.notification-item:focus {
    outline: 2px solid #667eea;
    outline-offset: -2px;
}

.notification-count {
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
