/**
 * Notification System for Bus Operator
 * Handles real-time notification updates, badge counts, and alerts
 */

let lastNotificationCount = 0;
let notificationCheckInterval = null;

// Initialize notification system when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeNotifications();
});

/**
 * Initialize the notification system
 */
function initializeNotifications() {
    console.log('Initializing notification system...');
    
    // Initial check
    updateNotificationBadge();
    
    // Start polling for new notifications every 5 seconds
    notificationCheckInterval = setInterval(updateNotificationBadge, 5000);
    
    // Also listen for visibility changes (update when tab becomes visible)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            console.log('Tab became visible, checking notifications...');
            updateNotificationBadge();
        }
    });
    
    console.log('Notification system initialized');
}

/**
 * Update the notification badge with unread count
 */
async function updateNotificationBadge() {
    try {
        const response = await fetch('/notifications/unread-count', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            console.warn('Failed to fetch notification count:', response.status);
            return;
        }

        const data = await response.json();
        const unreadCount = data.count || data.unread_count || 0;

        console.log('Current unread notifications:', unreadCount);

        // Update badge display
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.style.display = 'inline-block';
                
                // Show toast notification if count increased
                if (unreadCount > lastNotificationCount) {
                    const newCount = unreadCount - lastNotificationCount;
                    showNotificationToast(`You have ${newCount} new notification${newCount > 1 ? 's' : ''}`);
                }
            } else {
                badge.style.display = 'none';
            }
        }

        lastNotificationCount = unreadCount;

    } catch (error) {
        console.error('Error updating notification badge:', error);
    }
}

/**
 * Show a toast notification
 */
function showNotificationToast(message, type = 'info', duration = 5000) {
    const toastHTML = `
        <div class="toast-notification toast-${type}">
            <div class="toast-content">
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `;

    const container = document.getElementById('toastContainer') || createToastContainer();
    container.insertAdjacentHTML('beforeend', toastHTML);

    // Auto remove after duration
    setTimeout(() => {
        const toast = container.lastElementChild;
        if (toast) {
            toast.remove();
        }
    }, duration);
}

/**
 * Create toast notification container
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 400px;
    `;
    document.body.appendChild(container);
    return container;
}

/**
 * Mark notification as read
 */
async function markNotificationAsRead(notificationId) {
    try {
        const response = await fetch(`/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            console.log('Notification marked as read:', notificationId);
            updateNotificationBadge();
            return true;
        } else {
            console.error('Failed to mark notification as read');
            return false;
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
        return false;
    }
}

/**
 * Mark all notifications as read
 */
async function markAllNotificationsAsRead() {
    try {
        const response = await fetch('/notifications/mark-all-read', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            console.log('All notifications marked as read');
            updateNotificationBadge();
            showNotificationToast('All notifications marked as read', 'success');
            return true;
        } else {
            console.error('Failed to mark all notifications as read');
            return false;
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
        return false;
    }
}

/**
 * Delete all notifications
 */
async function deleteAllNotifications() {
    if (!confirm('Are you sure you want to delete all notifications?')) {
        return false;
    }

    try {
        const response = await fetch('/notifications/clear-all', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            console.log('All notifications deleted');
            updateNotificationBadge();
            showNotificationToast('All notifications cleared', 'success');
            location.reload();
            return true;
        } else {
            console.error('Failed to delete all notifications');
            return false;
        }
    } catch (error) {
        console.error('Error deleting all notifications:', error);
        return false;
    }
}

/**
 * Handle incoming chat message notification
 */
function handleChatMessageNotification(message) {
    const toastMessage = `New message from ${message.sender_name}: ${message.text.substring(0, 50)}${message.text.length > 50 ? '...' : ''}`;
    showNotificationToast(toastMessage, 'info', 7000);
    updateNotificationBadge();
}

/**
 * Handle incoming driver report notification
 */
function handleDriverReportNotification(report) {
    const toastMessage = `New report from ${report.driver_name}: ${report.message.substring(0, 50)}${report.message.length > 50 ? '...' : ''}`;
    showNotificationToast(toastMessage, 'warning', 7000);
    updateNotificationBadge();
}

/**
 * Handle incoming announcement notification
 */
function handleAnnouncementNotification(announcement) {
    showNotificationToast(`New announcement: ${announcement.title}`, 'info', 10000);
    updateNotificationBadge();
}

/**
 * Cleanup notification system
 */
function cleanupNotifications() {
    if (notificationCheckInterval) {
        clearInterval(notificationCheckInterval);
        console.log('Notification polling stopped');
    }
}

// Cleanup when page unloads
window.addEventListener('beforeunload', cleanupNotifications);

// Add CSS styles for toast notifications
const style = document.createElement('style');
style.textContent = `
    .toast-notification {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-radius: 0.375rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        animation: slideInRight 0.3s ease-in-out;
        gap: 12px;
        min-width: 250px;
        max-width: 100%;
    }

    .toast-notification.toast-info {
        background-color: #e3f2fd;
        color: #1565c0;
        border-left: 4px solid #1976d2;
    }

    .toast-notification.toast-success {
        background-color: #e8f5e9;
        color: #2e7d32;
        border-left: 4px solid #43a047;
    }

    .toast-notification.toast-warning {
        background-color: #fff3e0;
        color: #e65100;
        border-left: 4px solid #fb8c00;
    }

    .toast-notification.toast-error {
        background-color: #ffebee;
        color: #c62828;
        border-left: 4px solid #e53935;
    }

    .toast-content {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        min-width: 0;
    }

    .toast-content span {
        word-break: break-word;
    }

    .toast-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: inherit;
        opacity: 0.7;
        transition: opacity 0.2s;
        flex-shrink: 0;
    }

    .toast-close:hover {
        opacity: 1;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    #notificationBadge {
        background-color: #dc3545 !important;
        color: white !important;
        font-weight: bold;
        min-width: 18px;
        text-align: center;
    }
`;
document.head.appendChild(style);
