@extends('layouts.app')

@section('title', 'Notifications & Alerts')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell me-3 text-primary fs-4"></i>
            <h2 class="mb-0 fw-bold">Notifications & Alerts</h2>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-check-double me-1"></i> Mark All Read
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-trash me-1"></i> Clear All
            </button>
        </div>
    </div>

    <!-- Notifications Container -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-inbox me-2"></i>Recent Notifications
                    </h5>
                    <span class="badge bg-primary">4 New</span>
                </div>
                <div class="card-body p-0">
                    <!-- Notification Item 1 -->
                    <div class="notification-item border-bottom p-3 hover-bg-light">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3">
                                <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-info-circle text-info"></i>
                                </div>
                            </div>
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-1 fw-semibold">Route Assignment</h6>
                                    <small class="text-muted">2 hours ago</small>
                                </div>
                                <p class="mb-1 text-secondary">You have been assigned to Route 05B - Coastal Line starting tomorrow.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-info bg-opacity-10 text-info">New Assignment</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Item 2 -->
                    <div class="notification-item border-bottom p-3 hover-bg-light">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                </div>
                            </div>
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-1 fw-semibold">Traffic Alert</h6>
                                    <small class="text-muted">5 hours ago</small>
                                </div>
                                <p class="mb-1 text-secondary">Heavy traffic reported on Osmeña Blvd. Consider alternate route.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-warning bg-opacity-10 text-warning">Traffic Alert</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Item 3 -->
                    <div class="notification-item border-bottom p-3 hover-bg-light opacity-75">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-tools text-danger"></i>
                                </div>
                            </div>
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-1 fw-semibold text-muted">Bus Maintenance</h6>
                                    <small class="text-muted">Yesterday</small>
                                </div>
                                <p class="mb-1 text-secondary">Scheduled maintenance for Bus CEB-789 on Friday at 10 AM.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Read</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Item 4 -->
                    <div class="notification-item p-3 opacity-75">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3">
                                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar-alt text-success"></i>
                                </div>
                            </div>
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-1 fw-semibold text-muted">Schedule Update</h6>
                                    <small class="text-muted">2 days ago</small>
                                </div>
                                <p class="mb-1 text-secondary">Route 01A will have extended hours starting next week.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Read</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Load More Section -->
                <div class="card-footer bg-light text-center">
                    <button type="button" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-chevron-down me-1"></i> Load More Notifications
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State (if no notifications) -->
    <div class="row mt-4" style="display: none;" id="emptyState">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No notifications</h4>
                <p class="text-muted">You're all caught up! Check back later for new updates.</p>
            </div>
        </div>
    </div>

    <!-- Notification Settings Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Notification Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="routeAssignments" checked>
                                <label class="form-check-label" for="routeAssignments">
                                    <strong>Route Assignments</strong>
                                    <br><small class="text-muted">Get notified about new route assignments</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="trafficAlerts" checked>
                                <label class="form-check-label" for="trafficAlerts">
                                    <strong>Traffic Alerts</strong>
                                    <br><small class="text-muted">Receive real-time traffic updates</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="maintenanceAlerts">
                                <label class="form-check-label" for="maintenanceAlerts">
                                    <strong>Maintenance Alerts</strong>
                                    <br><small class="text-muted">Bus maintenance notifications</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="scheduleUpdates" checked>
                                <label class="form-check-label" for="scheduleUpdates">
                                    <strong>Schedule Updates</strong>
                                    <br><small class="text-muted">Changes to schedules and routes</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-bg-light:hover {
    background-color: #f8f9fa !important;
    transition: background-color 0.2s ease;
}

.notification-item {
    transition: all 0.2s ease;
}

.notification-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
@vite('resources/js/panels/notifications.js')
@endpush