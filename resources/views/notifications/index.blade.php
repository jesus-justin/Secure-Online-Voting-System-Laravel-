@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-bell-fill me-2"></i>All Notifications
                        </h4>
                        <button class="btn btn-sm btn-light" onclick="markAllAsRead()">
                            Mark all as read
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="allNotificationsList">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load all notifications from localStorage
const allNotifications = JSON.parse(localStorage.getItem('notifications') || '[]');

function loadAllNotifications() {
    const container = document.getElementById('allNotificationsList');
    
    if (allNotifications.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash" style="font-size: 4rem; opacity: 0.3;"></i>
                <p class="mt-3 mb-0">No notifications yet</p>
            </div>
        `;
        return;
    }
    
    const html = allNotifications.map(notif => `
        <div class="notification-item ${notif.read ? '' : 'unread'} border-bottom p-4" 
             style="${notif.read ? '' : 'background-color: #f8f9fa;'}">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-${getNotificationColor(notif.type)} bg-opacity-10 p-3">
                        <i class="bi ${notif.icon} text-${getNotificationColor(notif.type)}" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-2 fw-bold">${notif.title}</h5>
                            <p class="mb-2 text-muted">${notif.message}</p>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> ${notif.time}
                            </small>
                        </div>
                        ${!notif.read ? '<span class="badge bg-primary">New</span>' : ''}
                    </div>
                </div>
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

document.addEventListener('DOMContentLoaded', loadAllNotifications);
</script>
@endsection
