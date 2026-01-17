

<?php $__env->startSection('title', 'Notifications & Alerts'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell me-3 text-primary fs-4"></i>
            <h2 class="mb-0 fw-bold">Notifications & Alerts</h2>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
                <i class="fas fa-paper-plane me-1"></i> Send to Driver(s)
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="markAllReadBtn">
                <i class="fas fa-check-double me-1"></i> Mark All Read
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" id="clearAllBtn">
                <i class="fas fa-trash me-1"></i> Clear All
            </button>
        </div>
    </div>

    <!-- Tabs for Received vs Sent -->
    <ul class="nav nav-tabs mb-4" id="notificationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="received-tab" data-bs-toggle="tab" data-bs-target="#received" type="button" role="tab">
                <i class="fas fa-inbox me-2"></i>Received (<?php echo e($receivedNotifications->count()); ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab">
                <i class="fas fa-paper-plane me-2"></i>Sent (<?php echo e($sentNotifications->count()); ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="notificationTabsContent">
        <!-- Received Notifications Tab -->
        <div class="tab-pane fade show active" id="received" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-inbox me-2"></i>Received Notifications</h5>
                </div>
                <div class="card-body">
                    <?php if($receivedNotifications->isEmpty()): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No received notifications</h4>
                            <p class="text-muted">You're all caught up!</p>
                        </div>
                    <?php else: ?>
                        <?php $__currentLoopData = $receivedNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="notification-item d-flex align-items-start p-3 mb-3 border rounded <?php echo e($notification->is_read ? 'bg-light' : 'bg-white'); ?> hover-bg-light">
                                <div class="me-3">
                                    <?php switch($notification->type):
                                        case ('emergency'): ?>
                                            <i class="fas fa-exclamation-circle text-danger fa-2x"></i>
                                            <?php break; ?>
                                        <?php case ('issue_report'): ?>
                                            <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                                            <?php break; ?>
                                        <?php case ('schedule_update'): ?>
                                            <i class="fas fa-calendar-alt text-info fa-2x"></i>
                                            <?php break; ?>
                                        <?php case ('inspection_required'): ?>
                                            <i class="fas fa-wrench text-primary fa-2x"></i>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <i class="fas fa-info-circle text-secondary fa-2x"></i>
                                    <?php endswitch; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1 fw-bold">
                                                    <?php switch($notification->type):
                                                        case ('emergency'): ?>
                                                            🚨 Emergency Alert
                                                            <?php break; ?>
                                                        <?php case ('issue_report'): ?>
                                                            ⚠️ Issue Report
                                                            <?php break; ?>
                                                        <?php case ('schedule_update'): ?>
                                                            📅 Schedule Update
                                                            <?php break; ?>
                                                        <?php case ('inspection_required'): ?>
                                                            🔧 Inspection Required
                                                            <?php break; ?>
                                                        <?php default: ?>
                                                            📢 Notification
                                                    <?php endswitch; ?>
                                                </h6>
                                                <small class="text-muted ms-3 text-nowrap"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                                            </div>
                                            <p class="text-muted small mb-0">
                                                <strong>From:</strong> 
                                                <?php if($notification->driver): ?>
                                                    <?php echo e($notification->driver->name); ?> (Driver)
                                                <?php elseif($notification->sender): ?>
                                                    <?php echo e($notification->sender->name); ?>

                                                <?php else: ?>
                                                    System
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <p class="mb-2"><?php echo e($notification->message); ?></p>
                                    
                                    <?php if($notification->driver): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-secondary">Driver: <?php echo e($notification->driver->name); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($notification->schedule): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-info">Schedule ID: <?php echo e($notification->schedule->id); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($notification->bus): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-primary">Bus: <?php echo e($notification->bus->bus_number); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark"><?php echo e(ucfirst(str_replace('_', ' ', $notification->type))); ?></span>
                                        <?php if(!$notification->is_read): ?>
                                            <button class="btn btn-sm btn-outline-primary mark-read-btn" data-id="<?php echo e($notification->id); ?>">Mark Read</button>
                                        <?php else: ?>
                                            <span class="text-success small"><i class="fas fa-check"></i> Read</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sent Notifications Tab -->
        <div class="tab-pane fade" id="sent" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Sent Notifications</h5>
                </div>
                <div class="card-body">
                    <?php if($sentNotifications->isEmpty()): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No sent notifications</h4>
                            <p class="text-muted">You haven't sent any notifications yet.</p>
                        </div>
                    <?php else: ?>
                        <?php $__currentLoopData = $sentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="notification-item d-flex align-items-start p-3 mb-3 border rounded bg-light">
                                <div class="me-3">
                                    <?php switch($notification->type):
                                        case ('schedule_update'): ?>
                                            <i class="fas fa-calendar-alt text-info fa-2x"></i>
                                            <?php break; ?>
                                        <?php case ('inspection_required'): ?>
                                            <i class="fas fa-wrench text-primary fa-2x"></i>
                                            <?php break; ?>
                                        <?php case ('general'): ?>
                                            <i class="fas fa-info-circle text-secondary fa-2x"></i>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <i class="fas fa-paper-plane text-primary fa-2x"></i>
                                    <?php endswitch; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1 fw-bold">
                                                    <i class="fas fa-arrow-right text-success me-1"></i>
                                                    <?php switch($notification->type):
                                                        case ('schedule_update'): ?>
                                                            📅 Schedule Update
                                                            <?php break; ?>
                                                        <?php case ('inspection_required'): ?>
                                                            🔧 Inspection Required
                                                            <?php break; ?>
                                                        <?php case ('general'): ?>
                                                            📢 General Message
                                                            <?php break; ?>
                                                        <?php default: ?>
                                                            📢 Notification
                                                    <?php endswitch; ?>
                                                </h6>
                                                <small class="text-muted ms-3 text-nowrap"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                                            </div>
                                            <p class="text-muted small mb-0">
                                                <strong>To:</strong> 
                                                <?php if($notification->driver): ?>
                                                    <?php echo e($notification->driver->name); ?> (Driver)
                                                <?php else: ?>
                                                    Unknown Recipient
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <p class="mb-2"><?php echo e($notification->message); ?></p>
                                    
                                    <?php if($notification->driver): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-secondary">Driver: <?php echo e($notification->driver->name); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($notification->schedule): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-info">Schedule ID: <?php echo e($notification->schedule->id); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($notification->bus): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-primary">Bus: <?php echo e($notification->bus->bus_number); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark"><?php echo e(ucfirst(str_replace('_', ' ', $notification->type))); ?></span>
                                        <span class="text-success small"><i class="fas fa-check-double"></i> Sent</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Notification Modal -->
<div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendNotificationModalLabel">
                    <i class="fas fa-paper-plane me-2"></i>Send Notification to Driver(s)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sendNotificationForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Driver(s)</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllDrivers">
                                <i class="fas fa-check-square me-1"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllDrivers">
                                <i class="fas fa-square me-1"></i> Deselect All
                            </button>
                        </div>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <?php $__currentLoopData = \App\Models\Driver::where('user_id', Auth::id())->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check">
                                    <input class="form-check-input driver-checkbox" type="checkbox" name="driver_ids[]" value="<?php echo e($driver->id); ?>" id="driver_<?php echo e($driver->id); ?>">
                                    <label class="form-check-label" for="driver_<?php echo e($driver->id); ?>">
                                        <?php echo e($driver->name); ?> - <?php echo e($driver->license_number); ?>

                                    </label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <small class="text-muted">Selected: <span id="selectedCount">0</span> driver(s)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notification_type" class="form-label">Notification Type</label>
                        <select class="form-select" id="notification_type" name="type" required>
                            <option value="schedule_update">📅 Schedule Update</option>
                            <option value="inspection_required">🔧 Inspection Required</option>
                            <option value="general">📢 General Message</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notification_message" class="form-label">Message</label>
                        <textarea class="form-control" id="notification_message" name="message" rows="4" placeholder="Enter your message here..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="schedule_id" class="form-label">Related Schedule (Optional)</label>
                        <select class="form-select" id="schedule_id" name="schedule_id">
                            <option value="">None</option>
                            <?php $__currentLoopData = \App\Models\Schedule::with('driver', 'route')->whereHas('driver', function($q) {
                                $q->where('user_id', Auth::id());
                            })->latest()->take(20)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($schedule->id); ?>">
                                    <?php echo e($schedule->driver->name); ?> - <?php echo e($schedule->route->name ?? 'N/A'); ?> - <?php echo e($schedule->date); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bus_id" class="form-label">Related Bus (Optional)</label>
                        <select class="form-select" id="bus_id" name="bus_id">
                            <option value="">None</option>
                            <?php $__currentLoopData = \App\Models\Bus::where('user_id', Auth::id())->orderBy('bus_number')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($bus->id); ?>"><?php echo e($bus->bus_number); ?> - <?php echo e($bus->model); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Count selected drivers
    function updateSelectedCount() {
        const count = document.querySelectorAll('.driver-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;
    }

    // Select/Deselect all drivers
    document.getElementById('selectAllDrivers').addEventListener('click', function() {
        document.querySelectorAll('.driver-checkbox').forEach(cb => cb.checked = true);
        updateSelectedCount();
    });

    document.getElementById('deselectAllDrivers').addEventListener('click', function() {
        document.querySelectorAll('.driver-checkbox').forEach(cb => cb.checked = false);
        updateSelectedCount();
    });

    // Update count when checkboxes change
    document.querySelectorAll('.driver-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Send notification form handler
    document.getElementById('sendNotificationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selectedDrivers = Array.from(document.querySelectorAll('.driver-checkbox:checked')).map(cb => cb.value);
        
        if (selectedDrivers.length === 0) {
            alert('Please select at least one driver');
            return;
        }

        const formData = {
            driver_ids: selectedDrivers,
            type: document.getElementById('notification_type').value,
            message: document.getElementById('notification_message').value,
            schedule_id: document.getElementById('schedule_id').value || null,
            bus_id: document.getElementById('bus_id').value || null,
        };
        
        fetch('/api/v1/notifications/operator-send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Notification sent successfully to ${data.sent_count} driver(s)!`);
                document.getElementById('sendNotificationForm').reset();
                updateSelectedCount();
                bootstrap.Modal.getInstance(document.getElementById('sendNotificationModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to send notification'));
            }
        })
        .catch(error => {
            console.error('Error sending notification:', error);
            alert('Error sending notification. Please try again.');
        });
    });

    // Mark as read handler (only in received tab)
    document.querySelectorAll('.mark-read-btn').forEach(button => {
        button.addEventListener('click', function () {
            const notificationId = this.getAttribute('data-id');
            fetch(`/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('.notification-item').classList.remove('bg-white');
                    this.closest('.notification-item').classList.add('bg-light');
                    this.remove();
                    updateNotificationBadge();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        });
    });

    document.getElementById('markAllReadBtn').addEventListener('click', function () {
        fetch('/notifications/mark-all-read', {
            method: 'PATCH',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    });

    document.getElementById('clearAllBtn').addEventListener('click', function () {
        if (confirm('Are you sure you want to clear all notifications?')) {
            fetch('/notifications/clear-all', {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
            })
            .catch(error => console.error('Error clearing all notifications:', error));
        }
    });
});

function updateNotificationBadge() {
    fetch('/notifications/unread-count', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.getElementById('notificationBadge');
        if (badge && data.count > 0) {
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.style.display = 'block';
        } else if (badge) {
            badge.style.display = 'none';
        }
    })
    .catch(error => console.error('Error fetching notification count:', error));
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Desktop\TransiTrack System\BusOperator\resources\views/panels/notifications.blade.php ENDPATH**/ ?>