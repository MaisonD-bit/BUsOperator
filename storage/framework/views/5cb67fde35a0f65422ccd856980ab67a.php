

<?php $__env->startSection('title', 'Bus Management'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-0">
                <i class="fas fa-bus text-primary me-2"></i>Bus Management
            </h2>
        </div>
        <button class="btn btn-sm btn-outline-primary ms-2 active" id="addBusBtn">
            <i class="fas fa-plus me-1"></i> Add New Bus
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-3">
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card border shadow-sm h-100 bg-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 text-muted fw-normal">Total Buses</h6>
                            <h2 class="mb-0 fw-bold text-dark"><?php echo e($stats['total_buses'] ?? 0); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-bus fa-2x text-primary" style="opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card border shadow-sm h-100 bg-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 text-muted fw-normal">Available</h6>
                            <h2 class="mb-0 fw-bold text-dark"><?php echo e($stats['available_buses'] ?? 0); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x text-success" style="opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card border shadow-sm h-100 bg-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 text-muted fw-normal">In Service</h6>
                            <h2 class="mb-0 fw-bold text-dark"><?php echo e($stats['in_service_buses'] ?? 0); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-route fa-2x text-info" style="opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card border shadow-sm h-100 bg-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 text-muted fw-normal">Maintenance</h6>
                            <h2 class="mb-0 fw-bold text-dark"><?php echo e($stats['maintenance_buses'] ?? 0); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-tools fa-2x text-warning" style="opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card border-0 bg-white shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="<?php echo e(route('buses.panel')); ?>" class="row g-3">
                <div class="col-md-4">
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="Search by bus number, plate, model, or company..." 
                           value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-2">
                    <select id="filter_status" name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="available" <?php echo e(request('status') === 'available' ? 'selected' : ''); ?>>Available</option>
                        <option value="in_service" <?php echo e(request('status') === 'in_service' ? 'selected' : ''); ?>>In Service</option>
                        <option value="maintenance" <?php echo e(request('status') === 'maintenance' ? 'selected' : ''); ?>>Maintenance</option>
                        <option value="out_of_service" <?php echo e(request('status') === 'out_of_service' ? 'selected' : ''); ?>>Out of Service</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filter_accommodation" name="accommodation_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="regular" <?php echo e(request('accommodation_type') === 'regular' ? 'selected' : ''); ?>>Regular</option>
                        <option value="air-conditioned" <?php echo e(request('accommodation_type') === 'air-conditioned' ? 'selected' : ''); ?>>Air-Conditioned</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i> Clear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Buses Table -->
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body p-0">
            <?php if($buses->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Bus Number</th>
                            <th class="py-3">Plate Number</th>
                            <th class="py-3">Model</th>
                            <th class="py-3">Capacity</th>
                            <th class="py-3">Company</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Terminal</th>
                            <th class="py-3" width="140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $buses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="fw-bold px-4"><?php echo e($bus->bus_number); ?></td>
                            <td><?php echo e($bus->plate_number); ?></td>
                            <td><?php echo e($bus->model); ?></td>
                            <td><?php echo e($bus->capacity); ?> seats</td>
                            <td><?php echo e($bus->bus_company ?? '-'); ?></td>
                            <td>
                                <?php if($bus->accommodation_type == 'regular'): ?>
                                    <span class="badge bg-secondary">Regular</span>
                                <?php else: ?>
                                    <span class="badge bg-info">Air-Conditioned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($bus->status == 'available'): ?>
                                    <span class="badge bg-success">Available</span>
                                <?php elseif($bus->status == 'in_service'): ?>
                                    <span class="badge bg-info">In Service</span>
                                <?php elseif($bus->status == 'maintenance'): ?>
                                    <span class="badge bg-warning">Maintenance</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Out of Service</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?php echo e(ucfirst($bus->terminal)); ?></span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editBus(<?php echo e($bus->id); ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBus(<?php echo e($bus->id); ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($buses->hasPages()): ?>
            <div class="p-4 d-flex justify-content-between align-items-center border-top">
                <div>
                    <p class="text-muted mb-0">
                        Showing <?php echo e($buses->firstItem()); ?> to <?php echo e($buses->lastItem()); ?> of <?php echo e($buses->total()); ?> results
                    </p>
                </div>
                <nav aria-label="Buses pagination">
                    <?php echo e($buses->appends(request()->query())->links()); ?>

                </nav>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-bus fa-3x text-muted mb-3"></i>
                <h4>No buses found for <?php echo e(ucfirst(Auth::user()->terminal)); ?> Terminal</h4>
                <?php if(request()->hasAny(['search', 'status', 'accommodation_type'])): ?>
                    <p class="text-muted">No buses match your search criteria.</p>
                    <a href="<?php echo e(route('buses.panel')); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> View All Buses
                    </a>
                <?php else: ?>
                    <p class="text-muted">Add your first bus for <?php echo e(ucfirst(Auth::user()->terminal)); ?> Terminal using the button above.</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Bus Modal -->
<div class="modal fade" id="busModal" tabindex="-1" aria-labelledby="busModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="busModalLabel">
                    <i class="fas fa-bus me-2"></i><span id="modalTitleText">Add New Bus</span>
                    <small class="text-muted ms-2">- <?php echo e(ucfirst(Auth::user()->terminal)); ?> Terminal</small>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="busForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="bus_id" name="bus_id">
                    <input type="hidden" id="method_field" name="_method">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bus_number" class="form-label">Bus Number <span class="text-danger">*</span></label>
                            <input type="text" id="bus_number" name="bus_number" class="form-control" required placeholder="e.g., JT-N001">
                            <div class="invalid-feedback" id="bus_number_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="plate_number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" id="plate_number" name="plate_number" class="form-control" required placeholder="e.g., JLT-N001">
                            <div class="invalid-feedback" id="plate_number_error"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="model" class="form-label">Bus Model <span class="text-danger">*</span></label>
                            <input type="text" id="model" name="model" class="form-control" required placeholder="e.g., Hyundai County">
                            <div class="invalid-feedback" id="model_error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                            <input type="number" id="capacity" name="capacity" class="form-control" required min="1" max="100" placeholder="45">
                            <div class="invalid-feedback" id="capacity_error"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bus_company" class="form-label">Bus Company</label>
                            <input type="text" id="bus_company" name="bus_company" class="form-control" placeholder="e.g., JULILA TRANSIT">
                            <div class="invalid-feedback" id="bus_company_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="accommodation_type" class="form-label">Accommodation Type <span class="text-danger">*</span></label>
                            <select id="accommodation_type" name="accommodation_type" class="form-select" required>
                                <option value="regular">Regular</option>
                                <option value="air-conditioned">Air-Conditioned</option>
                            </select>
                            <small class="text-muted">This affects fare calculation and passenger comfort</small>
                            <div class="invalid-feedback" id="accommodation_type_error"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="bus_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select id="bus_status" name="status" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="in_service">In Service</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="out_of_service">Out of Service</option>
                            </select>
                            <div class="invalid-feedback" id="status_error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" placeholder="Additional notes about the bus (e.g., specific routes it serves)"></textarea>
                        <div class="invalid-feedback" id="description_error"></div>
                    </div>

                    <!-- Terminal Display (Read-only) -->
                    <div class="alert alert-light border">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            This bus will be assigned to: <strong><?php echo e(ucfirst(Auth::user()->terminal)); ?> Terminal</strong>
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="submitBtn" onclick="saveBus()">
                    <i class="fas fa-save me-1"></i><span id="submitText">Save Bus</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/panels/buses.js'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Desktop\TransiTrack System\BusOperator\resources\views/panels/buses.blade.php ENDPATH**/ ?>