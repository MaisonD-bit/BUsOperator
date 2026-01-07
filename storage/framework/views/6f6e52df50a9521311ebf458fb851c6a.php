

<?php $__env->startSection('title', 'Bus Schedule'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-calendar-alt me-3 text-primary fs-4"></i>
            <h2 class="mb-0 fw-bold">Schedule Management</h2>
        </div>
        <!-- Toggle Button for Route Assignment -->
         <div class="modal fade" id="deleteScheduleModal" tabindex="-1" aria-labelledby="deleteScheduleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteScheduleModalLabel">Delete Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this schedule?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteBtn">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
                </div>
            </div>
            </div>
        <button type="button" class="btn btn-sm btn-outline-primary ms-2 active" id="toggleScheduleFormBtn">
            <i class="fas fa-plus me-2"></i>Create New Schedule
        </button>
    </div>

    <!-- Route Assignment Section - Initially Hidden -->
    <div class="card border-0 mb-4 shadow-sm" id="scheduleFormCard" style="display: none;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-route me-2"></i>Assign Routes to Driver</h5>
            <button type="button" class="btn btn-sm btn-outline-light" id="hideScheduleFormBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="card-body">
            <!-- Step 1: Select Driver -->
            <form id="driverSelectionForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="driver_select" class="form-label fw-bold">Select Driver <span class="text-danger">*</span></label>
                        <select id="driver_select" name="driver_id" class="form-select" required>
                            <option value="">-- Choose Driver --</option>
                            <?php $__currentLoopData = $drivers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($driver->id); ?>"><?php echo e($driver->name); ?> (<?php echo e($driver->status); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="invalid-feedback" id="driver_select_error"></div>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary" id="selectDriverBtn">
                            <i class="fas fa-arrow-right me-2"></i>Select Driver
                        </button>
                    </div>
                </div>
            </form>

            <!-- Step 2: Create Schedules for Selected Driver -->
            <div id="scheduleCreationSection" style="display: none;">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-user me-2"></i>
                    <strong id="selectedDriverName">Driver Name</strong> selected.
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="changeDriverBtn">
                        <i class="fas fa-edit me-1"></i>Change Driver
                    </button>
                </div>

                <div id="schedulesContainer">
                    <!-- Dynamic schedule rows will be added here -->
                </div>

                <button type="button" class="btn btn-outline-success mb-3" id="addScheduleRowBtn">
                    <i class="fas fa-plus me-2"></i>Add Another Schedule
                </button>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="button" class="btn btn-outline-secondary me-md-2" id="resetSchedulesFormBtn">
                        <i class="fas fa-undo me-2"></i>Reset Form
                    </button>
                    <button type="button" class="btn btn-primary" id="saveAllSchedulesBtn">
                        <i class="fas fa-check me-2"></i>Save All Schedules
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden template for a single schedule row -->
    <div id="scheduleRowTemplate" style="display: none;">
        <div class="schedule-row card mb-3">
            <div class="card-body">
                <input type="hidden" name="schedules[][driver_id]" class="driver_id_input">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Route <span class="text-danger">*</span></label>
                        <select name="schedules[][route_id]" class="form-select route-select" required>
                            <option value="">-- Choose Route --</option>
                            <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($route->id); ?>"
                                        data-duration="<?php echo e($route->estimated_duration); ?>"
                                        data-regular-fare="<?php echo e($route->regular_price ?? $route->route_fare ?? 0); ?>"
                                        data-aircon-fare="<?php echo e($route->aircon_price ?? $route->route_fare ?? 0); ?>"
                                        data-route-fare="<?php echo e($route->route_fare ?? $route->regular_price ?? 0); ?>"
                                        data-bus-type="<?php echo e($route->bus_type); ?>">
                                    <?php echo e($route->name); ?> (<?php echo e($route->end_location); ?>) - ₱<?php echo e(number_format($route->route_fare ?? $route->regular_price ?? 0, 2)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="invalid-feedback route-error"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Bus <span class="text-danger">*</span></label>
                        <select name="schedules[][bus_id]" class="form-select bus-select" required>
                            <option value="">-- Choose Bus --</option>
                            <?php $__currentLoopData = $buses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($bus->id); ?>" data-type="<?php echo e($bus->accommodation_type); ?>">
                                    <?php echo e($bus->bus_number); ?> - <?php echo e($bus->model); ?>

                                    <?php if($bus->accommodation_type === 'air-conditioned'): ?>
                                        <span class="badge bg-info ms-1">A/C</span>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="invalid-feedback bus-error"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="schedules[][date]" class="form-control date-input" required value="<?php echo e(date('Y-m-d')); ?>">
                        <div class="invalid-feedback date-error"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="schedules[][start_time]" class="form-control start-time-input" required>
                        <div class="invalid-feedback start-time-error"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">End Time</label>
                        <input type="time" name="schedules[][end_time]" class="form-control end-time-input" readonly>
                        <div class="invalid-feedback end-time-error"></div>
                    </div>
                </div>
                
                <!--   Display calculated fare -->
                <div class="mt-2">
                    <small class="text-muted">Calculated Fare: <strong class="fare-display text-success">₱0.00</strong></small>
                </div>
                
                <!-- Hidden fare inputs -->
                <input type="hidden" name="schedules[][fare_regular]" class="fare-regular-input">
                <input type="hidden" name="schedules[][fare_aircon]" class="fare-aircon-input">
                
                <button type="button" class="btn btn-sm btn-outline-danger mt-3 remove-schedule-row">
                    <i class="fas fa-trash me-1"></i>Remove
                </button>
            </div>
        </div>
    </div>

    <!-- Schedule Management -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Current Schedules</h5>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="<?php echo e(route('schedule.panel')); ?>" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="filterDriver" class="form-label">Filter by Driver</label>
                    <select id="filterDriver" name="driver" class="form-select">
                        <option value="">All Drivers</option>
                        <?php $__currentLoopData = $drivers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($driver->id); ?>" <?php echo e(request('driver') == $driver->id ? 'selected' : ''); ?>><?php echo e($driver->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterRoute" class="form-label">Filter by Route</label>
                    <select id="filterRoute" name="route" class="form-select">
                        <option value="">All Routes</option>
                        <?php $__currentLoopData = $routes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($route->id); ?>" <?php echo e(request('route') == $route->id ? 'selected' : ''); ?>><?php echo e($route->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterDate" class="form-label">Filter by Date</label>
                    <input type="date" id="filterDate" name="date" class="form-control" 
                           value="<?php echo e(request('date') ? \Carbon\Carbon::parse(request('date'))->format('Y-m-d') : ''); ?>"
                           data-placeholder="dd/mm/yyyy">
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Filter by Status</label>
                    <select id="filterStatus" name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="scheduled" <?php echo e(request('status') === 'scheduled' ? 'selected' : ''); ?>>Scheduled</option>
                        <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                        <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i> Clear Filters
                    </button>
                </div>
            </form>

            <!-- Schedule Table -->
            <?php if($schedules->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Driver</th>
                            <th>Bus</th>
                            <th>Route</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Fare</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr data-schedule-id="<?php echo e($schedule->id); ?>">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <?php echo e($schedule->driver->name ?? 'N/A'); ?>

                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-bus text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?php echo e($schedule->bus->bus_number ?? 'N/A'); ?></div>
                                        <small class="text-muted">
                                            <?php echo e($schedule->bus->model ?? ''); ?>

                                            <?php if($schedule->bus && $schedule->bus->accommodation_type === 'air-conditioned'): ?>
                                                <span class="badge badge-sm bg-info ms-1">A/C</span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-route text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?php echo e($schedule->route->name ?? 'N/A'); ?></div>
                                        <small class="text-muted"><?php echo e($schedule->route->start_location ?? ''); ?> → <?php echo e($schedule->route->end_location ?? ''); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold"><?php echo e(\Carbon\Carbon::parse($schedule->date)->format('d/m/Y')); ?></span>
                                <br>
                                <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($schedule->date)->format('l')); ?></small>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-semibold"><?php echo e(\Carbon\Carbon::parse($schedule->start_time)->format('h:i A')); ?></div>
                                    <div class="text-muted">to</div>
                                    <div class="fw-semibold"><?php echo e(\Carbon\Carbon::parse($schedule->end_time)->format('h:i A')); ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-semibold text-success">₱<?php echo e(number_format($schedule->fare_regular, 2)); ?></div>
                                    <?php if($schedule->fare_aircon != $schedule->fare_regular): ?>
                                        <small class="text-muted">A/C: ₱<?php echo e(number_format($schedule->fare_aircon, 2)); ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php switch($schedule->status):
                                    case ('active'): ?>
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-play me-1"></i>Active
                                        </span>
                                        <?php break; ?>
                                    <?php case ('scheduled'): ?>
                                        <span class="badge bg-primary fs-6">
                                            <i class="fas fa-clock me-1"></i>Scheduled
                                        </span>
                                        <?php break; ?>
                                    <?php case ('completed'): ?>
                                        <span class="badge bg-secondary fs-6">
                                            <i class="fas fa-check me-1"></i>Completed
                                        </span>
                                        <?php break; ?>
                                    <?php case ('cancelled'): ?>
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-times me-1"></i>Cancelled
                                        </span>
                                        <?php break; ?>
                                    <?php case ('accepted'): ?>
                                        <span class="badge bg-info fs-6">
                                            <i class="fas fa-thumbs-up me-1"></i>Accepted
                                        </span>
                                        <?php break; ?>
                                    <?php case ('declined'): ?>
                                        <span class="badge bg-warning fs-6">
                                            <i class="fas fa-thumbs-down me-1"></i>Declined
                                        </span>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <span class="badge bg-secondary fs-6"><?php echo e(ucfirst($schedule->status)); ?></span>
                                <?php endswitch; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-info" onclick="viewSchedule(<?php echo e($schedule->id); ?>)" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editSchedule(<?php echo e($schedule->id); ?>)" title="Edit Schedule">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule(<?php echo e($schedule->id); ?>)" title="Delete Schedule">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            <?php if($schedules->hasPages()): ?>
            <div class="row mt-4">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Showing <?php echo e($schedules->firstItem()); ?> to <?php echo e($schedules->lastItem()); ?> of <?php echo e($schedules->total()); ?> schedules
                    </p>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Schedules pagination" class="d-flex justify-content-end">
                        <ul class="pagination pagination-sm mb-0">
                            
                            <?php if($schedules->onFirstPage()): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </span>
                                </li>
                            <?php else: ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo e($schedules->appends(request()->query())->previousPageUrl()); ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            
                            <?php if($schedules->currentPage() > 3): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo e($schedules->appends(request()->query())->url(1)); ?>">1</a>
                                </li>
                                <?php if($schedules->currentPage() > 4): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            
                            <?php $__currentLoopData = range(max(1, $schedules->currentPage() - 2), min($schedules->lastPage(), $schedules->currentPage() + 2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($page == $schedules->currentPage()): ?>
                                    <li class="page-item active">
                                        <span class="page-link"><?php echo e($page); ?></span>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo e($schedules->appends(request()->query())->url($page)); ?>"><?php echo e($page); ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            
                            <?php if($schedules->currentPage() < $schedules->lastPage() - 2): ?>
                                <?php if($schedules->currentPage() < $schedules->lastPage() - 3): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo e($schedules->appends(request()->query())->url($schedules->lastPage())); ?>"><?php echo e($schedules->lastPage()); ?></a>
                                </li>
                            <?php endif; ?>

                            
                            <?php if($schedules->hasMorePages()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo e($schedules->appends(request()->query())->nextPageUrl()); ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4>No schedules found</h4>
                <?php if(request()->hasAny(['driver', 'route', 'date', 'status'])): ?>
                    <p class="text-muted">No schedules match your filter criteria.</p>
                    <button type="button" class="btn btn-outline-primary" onclick="clearFilters()">
                        <i class="fas fa-arrow-left me-1"></i> View All Schedules
                    </button>
                <?php else: ?>
                    <p class="text-muted">Create your first schedule using the button above</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- View Schedule Modal - CENTERED -->
<div class="modal fade" id="viewScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Schedule Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewScheduleContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal - CENTERED -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Schedule
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editScheduleForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" id="edit_schedule_id" name="schedule_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_route_id" class="form-label">Route <span class="text-danger">*</span></label>
                            <select id="edit_route_id" name="route_id" class="form-select" required>
                                <option value="">-- Select Route --</option>
                                <?php $__currentLoopData = $routes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($route->id); ?>"><?php echo e($route->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback" id="edit_route_id_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_bus_id" class="form-label">Bus <span class="text-danger">*</span></label>
                            <select id="edit_bus_id" name="bus_id" class="form-select" required>
                                <option value="">-- Select Bus --</option>
                                <?php $__currentLoopData = $buses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($bus->id); ?>"><?php echo e($bus->bus_number); ?> - <?php echo e($bus->model); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback" id="edit_bus_id_error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_driver_id" class="form-label">Driver <span class="text-danger">*</span></label>
                            <select id="edit_driver_id" name="driver_id" class="form-select" required>
                                <option value="">-- Select Driver --</option>
                                <?php $__currentLoopData = $drivers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($driver->id); ?>"><?php echo e($driver->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback" id="edit_driver_id_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="scheduled">Scheduled</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="accepted">Accepted</option>
                                <option value="declined">Declined</option>
                            </select>
                            <div class="invalid-feedback" id="edit_status_error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="edit_date" name="date" class="form-control" required>
                            <div class="invalid-feedback" id="edit_date_error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" id="edit_start_time" name="start_time" class="form-control" required>
                            <div class="invalid-feedback" id="edit_start_time_error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" id="edit_end_time" name="end_time" class="form-control" required>
                            <div class="invalid-feedback" id="edit_end_time_error"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea id="edit_notes" name="notes" class="form-control" rows="3" placeholder="Additional notes for this schedule..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveScheduleBtn">
                    <i class="fas fa-save me-2"></i><span id="saveScheduleText">Save Changes</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/panels/schedule.js'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Desktop\TransiTrack System\BusOperator\resources\views/panels/schedule.blade.php ENDPATH**/ ?>