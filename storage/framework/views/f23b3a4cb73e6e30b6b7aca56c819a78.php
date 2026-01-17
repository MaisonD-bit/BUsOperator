<?php $__env->startSection('title', 'Routes'); ?>

<meta name="user-terminal" content="<?php echo e(auth()->user()->terminal); ?>">

<?php $__env->startPush('styles'); ?>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fas fa-route text-primary me-2"></i>Route Management</h2>
        <button class="btn btn-sm btn-outline-primary ms-2 active" onclick="showAddRouteForm()">
            <i class="fas fa-plus me-1"></i> Add Route
        </button>
    </div>

    <!-- Search and Filter Section -->
    <div class="card border-0 bg-white shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('routes.panel')); ?>" class="row g-3">
        <!-- Search -->
        <div class="col-md-3">
            <label for="search" class="form-label">Search Routes</label>
            <input type="text" id="search" name="search" class="form-control"
                placeholder="Name, code, or location..."
                value="<?php echo e(request('search')); ?>">
        </div>

        <!-- Status Filter -->
        <div class="col-md-2">
            <label for="filter_status" class="form-label">Status</label>
            <select id="filter_status" name="status" class="form-select">
            <option value="">All</option>
            <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
            <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
            </select>
        </div>

        <!-- Bus Type Filter -->
        <div class="col-md-2">
            <label for="filter_bus_type" class="form-label">Bus Type</label>
            <select id="filter_bus_type" name="bus_type" class="form-select">
            <option value="">All Types</option>
            <option value="regular" <?php echo e(request('bus_type') === 'regular' ? 'selected' : ''); ?>>Regular</option>
            <option value="aircon" <?php echo e(request('bus_type') === 'aircon' ? 'selected' : ''); ?>>Air-Con</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-outline-primary">
            <i class="fas fa-search me-1"></i> Apply Filters
            </button>
            <a href="<?php echo e(route('routes.panel')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-times me-1"></i> Clear
            </a>
        </div>
        </form>
    </div>
    </div>

    <!-- Add/Edit Form (Initially Hidden) -->
    <div id="routeFormSection" class="card border-0 bg-white shadow-sm mb-4" style="display: none;">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" id="formTitle">
                    <i class="fas fa-route me-2"></i>Add New Route
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="hideRouteForm()"></button>
            </div>
        </div>
        <div class="card-body">
            <form id="routeForm" method="POST" data-routes-store-url="<?php echo e(route('routes.store')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="route_id" name="route_id">
                <input type="hidden" id="method_field" name="_method">
                <input type="hidden" id="distance_km" name="distance_km">
                <input type="hidden" id="estimated_duration" name="estimated_duration">
                <input type="hidden" id="start_coordinates" name="start_coordinates" value="123.920994,10.311008">
                <input type="hidden" id="end_coordinates" name="end_coordinates">
                <input type="hidden" id="stops_data" name="stops_data">
                <input type="hidden" id="geometry" name="geometry">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="route_code" class="form-label">Route Code <span class="text-danger">*</span></label>
                        <input type="text" id="route_code" name="code" class="form-control" required placeholder="e.g., RT01">
                        <div class="invalid-feedback" id="code_error"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="route_name" class="form-label">Route Name <span class="text-danger">*</span></label>
                        <input type="text" id="route_name" name="name" class="form-control" required placeholder="e.g., Downtown Express">
                        <div class="invalid-feedback" id="name_error"></div>
                    </div>
                </div>

                <!-- Start Location (Fixed) -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            All routes start from: <strong><?php echo e(auth()->user()->terminal); ?> Bus Terminal</strong>
                            <br>
                            This is the main departure point for your routes.
                        </div>
                    </div>
                </div>

                <!-- Destination Search -->
                <div class="mb-3">
                <label for="destinationSearch" class="form-label">Search Destination in Cebu</label>
                <input type="text" id="destinationSearch" class="form-control" placeholder="Type a destination (e.g., Tabogon, Daanbantayan)">
                <div id="geocodingResults" class="list-group mt-1" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                </div>

                <!-- Map for End Location and Stops Selection -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Select Destination & Stops in Cebu</h6>
                                <small class="text-muted">Click on the map to set the destination (red marker). Add a pathway by clicking "Add Pathway" and then clicking on the map for each stop.</small>
                            </div>
                            <div class="card-body p-0">
                                <div id="routeMap" style="height: 400px; width: 100%;"></div>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-success">
                                            <i class="fas fa-circle text-success me-1"></i>
                                            Start: Cebu North Bus Terminal (Fixed)
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-danger">
                                            <i class="fas fa-circle text-danger me-1"></i>
                                            Click to Set Destination
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-primary">
                                        <i class="fas fa-route text-primary me-1"></i>
                                        Add Pathway (Optional)
                                        </small>
                                    </div>
                                </div>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearEndPoint()">
                                        <i class="fas fa-trash me-1"></i>Clear Destination
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="centerMapToCebu()">
                                        <i class="fas fa-crosshairs me-1"></i>Center to Cebu
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="addStopBtn">
                                    <i class="fas fa-route me-1"></i>Add Pathway
                                    </button>
                                </div>
                                <div class="mt-2" id="stopsList"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_location" class="form-label">Start Location</label>
                        <input type="text" id="start_location" name="start_location" class="form-control" readonly value="Cebu North Bus Terminal (SM City)" style="background-color: #e9ecef;">
                        <small class="text-muted">Fixed starting point for all routes</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_location" class="form-label">Destination <span class="text-danger">*</span></label>
                        <input type="text" id="end_location" name="end_location" class="form-control" required readonly placeholder="Click on map to select destination">
                        <small class="text-muted">Auto-filled from map selection</small>
                        <div class="invalid-feedback" id="end_location_error"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                    <label for="route_fare" class="form-label">Route Fare <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" step="0.01" id="route_fare" name="route_fare" class="form-control" required readonly>
                    </div>
                    <small class="text-muted">Auto-calculated based on distance and bus type</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="route_status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select id="route_status" name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div class="invalid-feedback" id="status_error"></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="bus_type" class="form-label">Bus Type <span class="text-danger">*</span></label>
                        <select id="bus_type" name="bus_type" class="form-select" required>
                            <option value="regular">Regular (Non Air-Con)</option>
                            <option value="aircon">Air-Conditioned</option>
                        </select>
                        <div class="invalid-feedback" id="bus_type_error"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Additional route information..."></textarea>
                    <div class="invalid-feedback" id="description_error"></div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" onclick="hideRouteForm()">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveRouteBtn">
                        <i class="fas fa-save me-2"></i>Save Route
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Routes Table -->
    <div class="card border-0 bg-white shadow-sm mt-3">
        <div class="card-body p-0"> 
            <?php if(isset($routes) && $routes->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Route Fare</th>
                            <th>Duration</th>
                            <th>Distance</th>
                            <th>Bus Type</th>
                            <th>Status</th>
                            <th width="140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="fw-bold"><?php echo e($route->code); ?></td>
                            <td><?php echo e($route->name); ?></td>
                            <td><?php echo e($route->start_location); ?></td>
                            <td><?php echo e($route->end_location); ?></td>
                            <td> ₱<?php echo e(number_format($route->route_fare ?? ($route->regular_price ?? 0), 2)); ?></td>
                            <td><?php echo e($route->estimated_duration ?? '-'); ?> mins</td>
                            <td><?php echo e($route->distance_km ?? '-'); ?> km</td>
                            <td>
                            <?php if($route->bus_type == 'aircon'): ?>
                                <span class="badge bg-info">Air-Con</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Regular</span>
                            <?php endif; ?>
                            </td>
                            <td>
                            <?php if($route->status == 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-info" onclick="viewRoute(<?php echo e($route->id); ?>)" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editRoute(<?php echo e($route->id); ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRoute(<?php echo e($route->id); ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Bootstrap Pagination -->
            <?php if($routes->hasPages()): ?>
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">
                        Showing <?php echo e($routes->firstItem()); ?> to <?php echo e($routes->lastItem()); ?> of <?php echo e($routes->total()); ?> results
                    </p>
                </div>
                <nav aria-label="Routes pagination">
                    <ul class="pagination pagination-sm mb-0">
                        
                        <?php if($routes->onFirstPage()): ?>
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($routes->previousPageUrl()); ?>&<?php echo e(http_build_query(request()->except('page'))); ?>">Previous</a></li>
                        <?php endif; ?>

                        
                        <?php $__currentLoopData = $routes->getUrlRange(1, $routes->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($page == $routes->currentPage()): ?>
                                <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo e($url); ?>&<?php echo e(http_build_query(request()->except('page'))); ?>"><?php echo e($page); ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        
                        <?php if($routes->hasMorePages()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($routes->nextPageUrl()); ?>&<?php echo e(http_build_query(request()->except('page'))); ?>">Next</a></li>
                        <?php else: ?>
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-route fa-3x text-muted mb-3"></i>
                <h4>No routes found</h4>
                <?php if(request()->hasAny(['search', 'status'])): ?>
                    <p class="text-muted">No routes match your search criteria.</p>
                    <a href="<?php echo e(route('routes.panel')); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> View All Routes
                    </a>
                <?php else: ?>
                    <p class="text-muted">Add your first route using the button above.</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- View Route Modal -->
<div id="viewRouteModal" class="position-fixed top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5); z-index: 1050; display: none;">
    <div class="d-flex align-items-center justify-content-center h-100 p-3">
        <div class="bg-white rounded shadow-lg" style="max-width: 800px; width: 100%;">
            <div class="bg-info text-white p-3 rounded-top d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2"></i>Route Details
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="hideViewModal()"></button>
            </div>
            <div class="p-4" id="viewRouteContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="p-3 border-top d-flex justify-content-end">
                <button type="button" class="btn btn-secondary" onclick="hideViewModal()">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
<script>
    mapboxgl.accessToken = 'pk.eyJ1Ijoic2Vlam83IiwiYSI6ImNtY3ZqcWJ1czBic3QycHEycnM0d2xtaXEifQ.DdQ8QFpf5LlgTDtejDgJSA';
</script>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/panels/routes.js'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kylecb\Desktop\BUsOperator\resources\views/panels/routes.blade.php ENDPATH**/ ?>