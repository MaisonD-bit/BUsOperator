<?php $__env->startSection('title', 'Operator Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-tachometer-alt me-3 text-primary fs-4"></i>
            <h2 class="mb-0 fw-bold">Operator Dashboard</h2>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="quickSchedule()">
                <i class="fas fa-plus me-1"></i> Quick Schedule
            </button>
        </div>
    </div>
    
    <!-- Dashboard Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-primary border-2 bg-white shadow-sm h-100 dashboard-card" onclick="redirectTo('<?php echo e(route('routes.index')); ?>')">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-route fs-1 text-primary"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1"><?php echo e($activeRoutes); ?></h2>
                    <p class="text-muted mb-0">Active Routes</p>
                    <small class="text-primary">Click to manage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-success border-2 bg-white shadow-sm h-100 dashboard-card" onclick="redirectTo('<?php echo e(route('buses.panel')); ?>')">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-bus fs-1 text-success"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1"><?php echo e($activeBuses); ?></h2>
                    <p class="text-muted mb-0">Buses in Operation</p>
                    <small class="text-success">Click to manage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-info border-2 bg-white shadow-sm h-100 dashboard-card" onclick="redirectTo('<?php echo e(route('drivers.panel')); ?>')">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fs-1 text-info"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1"><?php echo e($activeDrivers); ?></h2>
                    <p class="text-muted mb-0">Active Drivers</p>
                    <small class="text-info">Click to manage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-warning border-2 bg-white shadow-sm h-100 dashboard-card" onclick="showIssuesModal()">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-circle fs-1 text-warning"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1"><?php echo e($issues); ?></h2>
                    <p class="text-muted mb-0">Issues Today</p>
                    <small class="text-warning">Click to view</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="mb-1" id="totalSchedulesToday"><?php echo e($todaySchedules); ?></h4>
                    <small>Today's Schedules</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="mb-1" id="activeSchedules"><?php echo e($activeSchedules); ?></h4>
                    <small>Currently Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 class="mb-1" id="completedSchedules"><?php echo e($completedSchedules); ?></h4>
                    <small>Completed Today</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 class="mb-1" id="pendingSchedules"><?php echo e($pendingSchedules); ?></h4>
                    <small>Pending Schedules</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Schedules -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Recent Schedules</h5>
            <div class="d-flex gap-2 align-items-center">
                <select id="statusFilter" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <a href="<?php echo e(route('schedule.panel')); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-calendar-plus me-1"></i> New Schedule
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if($recentSchedules->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="schedulesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Route</th>
                            <th>Driver</th>
                            <th>Bus</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $recentSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr data-status="<?php echo e($schedule->status); ?>">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-route text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo e($schedule->route->name ?? 'N/A'); ?></div>
                                        <small class="text-muted"><?php echo e($schedule->route->code ?? 'N/A'); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-user text-info"></i>
                                    </div>
                                    <?php echo e($schedule->driver->name ?? 'N/A'); ?>

                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-bus text-success"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold"><?php echo e($schedule->bus->bus_number ?? 'N/A'); ?></span>
                                        <br><small class="text-muted"><?php echo e($schedule->bus->model ?? 'N/A'); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold"><?php echo e(\Carbon\Carbon::parse($schedule->date)->format('M d, Y')); ?></span>
                                <br><small class="text-muted"><?php echo e(\Carbon\Carbon::parse($schedule->date)->format('l')); ?></small>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-semibold"><?php echo e(\Carbon\Carbon::parse($schedule->start_time)->format('h:i A')); ?></div>
                                    <div class="text-muted small">to</div>
                                    <div class="fw-semibold"><?php echo e(\Carbon\Carbon::parse($schedule->end_time)->format('h:i A')); ?></div>
                                </div>
                            </td>
                            <td>
                                <?php switch($schedule->status):
                                    case ('active'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-play me-1"></i>Active
                                        </span>
                                        <?php break; ?>
                                    <?php case ('scheduled'): ?>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-clock me-1"></i>Scheduled
                                        </span>
                                        <?php break; ?>
                                    <?php case ('completed'): ?>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-check me-1"></i>Completed
                                        </span>
                                        <?php break; ?>
                                    <?php case ('cancelled'): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Cancelled
                                        </span>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <span class="badge bg-secondary"><?php echo e(ucfirst($schedule->status)); ?></span>
                                <?php endswitch; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewScheduleDetails(<?php echo e($schedule->id); ?>)" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($recentSchedules->hasPages()): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Showing <?php echo e($recentSchedules->firstItem()); ?> to <?php echo e($recentSchedules->lastItem()); ?> 
                        of <?php echo e($recentSchedules->total()); ?> schedules
                    </small>
                </div>
                <div>
                    <?php echo e($recentSchedules->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Schedules Yet</h4>
                <p class="text-muted">You haven't created any schedules. Start by creating your first schedule.</p>
                <a href="<?php echo e(route('schedule.panel')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Create First Schedule
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Today's Performance</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-success mb-1"><?php echo e($performanceStats['onTime']); ?>%</h4>
                                <small class="text-muted">On Time</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-warning mb-1"><?php echo e($performanceStats['delayed']); ?>%</h4>
                                <small class="text-muted">Delayed</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-danger mb-1"><?php echo e($performanceStats['cancelled']); ?>%</h4>
                            <small class="text-muted">Cancelled</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Quick Info</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info alert-sm py-2 mb-2">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>You have <strong><?php echo e($todaySchedules); ?></strong> schedules for today</small>
                    </div>
                    <div class="alert alert-success alert-sm py-2 mb-2">
                        <i class="fas fa-check-circle me-2"></i>
                        <small><strong><?php echo e($activeSchedules); ?></strong> schedules currently active</small>
                    </div>
                    <div class="alert alert-warning alert-sm py-2 mb-0">
                        <i class="fas fa-clock me-2"></i>
                        <small><strong><?php echo e($pendingSchedules); ?></strong> schedules pending</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Details Modal -->
<div class="modal fade" id="scheduleDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Schedule Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="scheduleDetailsContent">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/panels/operator.js'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kylecb\Desktop\BUsOperator\resources\views/panels/operator.blade.php ENDPATH**/ ?>