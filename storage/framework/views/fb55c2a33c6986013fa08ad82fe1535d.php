

<?php $__env->startSection('title', 'Drivers Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-tie me-3 text-primary fs-4"></i>
            <h2 class="mb-0 fw-bold">Drivers Management</h2>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-warning" id="pendingRegistrationsBtn">
                <i class="fas fa-clock me-1"></i> Pending Approvals
                <span class="badge bg-warning text-dark ms-1" id="pendingCount">0</span>
            </button>
            <button class="btn btn-sm btn-outline-primary ms-2 active" id="addDriverBtn">
                <i class="fas fa-plus me-1"></i> Add Driver
            </button>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #edf2f7; flex-shrink: 0;">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <h2 class="mb-0 fs-4 fw-semibold"><?php echo e($stats['total'] ?? 0); ?></h2>
                        <p class="mb-0 text-muted">Total Drivers</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #ebf8ee; flex-shrink: 0;">
                        <i class="fas fa-user-check text-success"></i>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <h2 class="mb-0 fs-4 fw-semibold"><?php echo e($stats['active'] ?? 0); ?></h2>
                        <p class="mb-0 text-muted">Active Drivers</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #fef0ee; flex-shrink: 0;">
                        <i class="fas fa-user-slash text-danger"></i>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <h2 class="mb-0 fs-4 fw-semibold"><?php echo e($stats['inactive'] ?? 0); ?></h2>
                        <p class="mb-0 text-muted">Inactive Drivers</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #fef6e9; flex-shrink: 0;">
                        <i class="fas fa-user-clock text-warning"></i>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <h2 class="mb-0 fs-4 fw-semibold"><?php echo e($stats['onLeave'] ?? 0); ?></h2>
                        <p class="mb-0 text-muted">Drivers on Leave</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group" role="group">
                    <button class="btn btn-outline-primary btn-sm" id="tableViewBtn">
                        <i class="fas fa-list"></i> List
                    </button>
                    <button class="btn btn-outline-primary btn-sm active" id="gridViewBtn">
                        <i class="fas fa-th-large"></i> Grid
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Grid View -->
            <div id="gridView">
                <div class="row g-3">
                    <?php $__empty_1 = true; $__currentLoopData = $drivers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm position-relative driver-card" 
                             style="cursor: pointer; min-height: 300px;" 
                             onclick="viewDriver(<?php echo e($driver->id ?? 0); ?>)"
                             data-driver-id="<?php echo e($driver->id); ?>"
                             data-status="<?php echo e($driver->status); ?>"
                             data-source="<?php echo e($driver->app_registered ? 'app' : 'web'); ?>">
                            
                            <!-- Status and Source Badges -->
                            <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge <?php echo e($driver->status == 'active' ? 'bg-success' : ($driver->status == 'inactive' ? 'bg-danger' : ($driver->status == 'pending' ? 'bg-warning text-dark' : ($driver->status == 'rejected' ? 'bg-dark' : 'bg-secondary')))); ?>" 
                                        style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <?php echo e(ucfirst($driver->status ?? 'Active')); ?>

                                    </span>
                                    <?php if($driver->app_registered ?? false): ?>
                                    <span class="badge bg-info" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <i class="fas fa-mobile-alt"></i> App
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <i class="fas fa-desktop"></i> Web
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Image Container -->
                            <div class="position-relative" style="padding-bottom: 75%; overflow: hidden; z-index: 1;">
                                <img src="<?php echo e($driver->photo_url ? asset('storage/'.$driver->photo_url) : 'https://randomuser.me/api/portraits/men/'.(($driver->id ?? 1) % 70).'.jpg'); ?>" 
                                    alt="<?php echo e($driver->name ?? 'Driver'); ?>" 
                                    class="position-absolute top-0 start-0 w-100 h-100" 
                                    style="object-fit: cover; z-index: 1;">
                            </div>
                            
                            <div class="card-body flex-grow-1 p-3">
                                <h6 class="card-title fw-semibold mb-1 text-truncate text-center"><?php echo e($driver->name ?? 'Unnamed'); ?></h6>
                                <p class="text-muted small mb-2 text-center">DRV-<?php echo e(str_pad($driver->id ?? 0, 3, '0', STR_PAD_LEFT)); ?></p>
                                <div class="d-flex align-items-center justify-content-center small mb-1">
                                    <i class="fas fa-phone-alt text-primary me-2"></i>
                                    <span class="text-truncate"><?php echo e($driver->contact_number ?? 'N/A'); ?></span>
                                </div>
                                <?php if($driver->email): ?>
                                <div class="d-flex align-items-center justify-content-center small mb-1">
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    <span class="text-truncate"><?php echo e($driver->email); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($driver->app_registered && $driver->last_app_login): ?>
                                <div class="text-center">
                                    <small class="text-muted">Last login: <?php echo e(\Carbon\Carbon::parse($driver->last_app_login)->diffForHumans()); ?></small>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer bg-light p-2">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-primary btn-sm" style="width: 32px; height: 32px;" onclick="event.stopPropagation(); viewDriver(<?php echo e($driver->id ?? 0); ?>)" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if($driver->status == 'pending' && $driver->app_registered): ?>
                                    <button class="btn btn-outline-success btn-sm" style="width: 32px; height: 32px;" onclick="event.stopPropagation(); approveDriver(<?php echo e($driver->id ?? 0); ?>)" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" style="width: 32px; height: 32px;" onclick="event.stopPropagation(); rejectDriver(<?php echo e($driver->id ?? 0); ?>)" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-outline-success btn-sm" style="width: 32px; height: 32px;" onclick="event.stopPropagation(); editDriver(<?php echo e($driver->id ?? 0); ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" style="width: 32px; height: 32px;" onclick="event.stopPropagation(); deleteDriver(<?php echo e($driver->id ?? 0); ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <h4>No drivers found</h4>
                            <p class="text-muted">There are no drivers matching your criteria</p>
                            <button class="btn btn-primary mt-2" id="addFirstDriverBtn">
                                <i class="fas fa-plus me-2"></i> Add Your First Driver
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Table View -->
            <div id="tableView" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Driver</th>
                                <th>ID</th>
                                <th>Contact</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $drivers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="driver-row" 
                                data-driver-id="<?php echo e($driver->id); ?>"
                                data-status="<?php echo e($driver->status); ?>"
                                data-source="<?php echo e($driver->app_registered ? 'app' : 'web'); ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo e($driver->photo_url ? asset('storage/'.$driver->photo_url) : 'https://randomuser.me/api/portraits/men/'.(($driver->id ?? 1) % 70).'.jpg'); ?>" 
                                             alt="<?php echo e($driver->name ?? 'Driver'); ?>" 
                                             class="rounded-circle me-3" 
                                             width="40" height="40" 
                                             style="object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fw-semibold"><?php echo e($driver->name ?? 'Unnamed Driver'); ?></h6>
                                            <span class="text-muted small"><?php echo e($driver->email ?? ''); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>DRV-<?php echo e(str_pad($driver->id ?? 0, 3, '0', STR_PAD_LEFT)); ?></td>
                                <td><?php echo e($driver->contact_number ?? 'N/A'); ?></td>
                                <td>
                                    <?php if($driver->app_registered ?? false): ?>
                                    <span class="badge bg-info">
                                        <i class="fas fa-mobile-alt"></i> App
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-desktop"></i> Web
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo e($driver->status == 'active' ? 'bg-success' : ($driver->status == 'inactive' ? 'bg-danger' : ($driver->status == 'pending' ? 'bg-warning text-dark' : ($driver->status == 'rejected' ? 'bg-dark' : 'bg-secondary')))); ?>">
                                        <?php echo e(ucfirst($driver->status ?? 'Active')); ?>

                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewDriver(<?php echo e($driver->id ?? 0); ?>)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if($driver->status == 'pending' && $driver->app_registered): ?>
                                        <button class="btn btn-outline-success" onclick="approveDriver(<?php echo e($driver->id ?? 0); ?>)" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="rejectDriver(<?php echo e($driver->id ?? 0); ?>)" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-outline-success" onclick="editDriver(<?php echo e($driver->id ?? 0); ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteDriver(<?php echo e($driver->id ?? 0); ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                        <h4>No drivers found</h4>
                                        <p class="text-muted">There are no drivers matching your criteria</p>
                                        <button class="btn btn-primary mt-2" id="addFirstDriverBtnTable">
                                            <i class="fas fa-plus me-2"></i> Add Your First Driver
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Showing <?php echo e(count($drivers ?? [])); ?> drivers
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#"><i class="fas fa-angle-left"></i></a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#"><i class="fas fa-angle-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Pending Registrations Modal -->
<div class="modal fade" id="pendingRegistrationsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clock me-2"></i>Pending Driver Registrations
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="pendingRegistrationsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Driver Details Modal -->
<div class="modal fade" id="driverDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driverDetailsTitle">Driver Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="driverDetailsContent">
                <!-- Driver details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Add Driver Form Section -->
<div id="addDriverFormSection" class="mt-4" style="display: none;">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" id="formTitle">Add New Driver</h5>
                <button type="button" class="btn-close" onclick="toggleAddDriverForm()"></button>
            </div>
        </div>
        <div class="card-body p-4">
            <form id="driverForm" action="/drivers" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="driver_id" name="driver_id" value="">
                <input type="hidden" id="method_field" name="_method">
                
                <div class="row">
                    <!-- Photo Upload -->
                    <div class="col-md-3">
                        <div class="text-center mb-4">
                            <label class="form-label fw-bold">Driver Photo</label>
                            <div class="mx-auto position-relative" style="width: 120px; height: 120px; cursor: pointer;">
                                <img id="photo-preview" 
                                     src="https://randomuser.me/api/portraits/men/1.jpg" 
                                     alt="Driver Photo" 
                                     class="rounded-circle w-100 h-100" 
                                     style="object-fit: cover;">
                                <div class="position-absolute top-0 start-0 w-100 h-100 rounded-circle d-flex flex-column align-items-center justify-content-center text-white photo-overlay" 
                                     style="background: rgba(0,0,0,0.7); opacity: 0; transition: opacity 0.3s;">
                                    <i class="fas fa-camera fs-5 mb-1"></i>
                                    <small style="font-size: 0.7rem;">Upload Photo</small>
                                </div>
                                <input type="file" id="photo" name="photo" accept="image/*" style="display: none;">
                            </div>
                            <small class="text-muted d-block mt-2">Click to upload photo</small>
                        </div>
                    </div>
                    
                    <!-- Form Fields -->
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" required placeholder="Enter full name">
                                <div class="invalid-feedback" id="name_error"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" required placeholder="Enter email address">
                                <div class="invalid-feedback" id="email_error"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="tel" id="contact_number" name="contact_number" class="form-control" required placeholder="Enter contact number">
                                <div class="invalid-feedback" id="contact_number_error"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" required>
                                <div class="invalid-feedback" id="date_of_birth_error"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select id="gender" name="gender" class="form-select" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="invalid-feedback" id="gender_error"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                                <div class="invalid-feedback" id="status_error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                    <textarea id="address" name="address" class="form-control" rows="2" required placeholder="Enter complete address"></textarea>
                    <div class="invalid-feedback" id="address_error"></div>
                </div>
                
                <!-- License Information -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-3"><i class="fas fa-id-card me-2"></i>License Information</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="license_number" class="form-label">License Number <span class="text-danger">*</span></label>
                        <input type="text" id="license_number" name="license_number" class="form-control" required placeholder="Enter license number">
                        <div class="invalid-feedback" id="license_number_error"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="license_expiry" class="form-label">License Expiry Date <span class="text-danger">*</span></label>
                        <input type="date" id="license_expiry" name="license_expiry" class="form-control" required>
                        <div class="invalid-feedback" id="license_expiry_error"></div>
                    </div>
                </div>
                
                <!-- Emergency Contact -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-3"><i class="fas fa-exclamation-circle me-2"></i>Emergency Contact</h6>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="emergency_name" class="form-label">Contact Person <span class="text-danger">*</span></label>
                        <input type="text" id="emergency_name" name="emergency_name" class="form-control" required placeholder="Enter contact person name">
                        <div class="invalid-feedback" id="emergency_name_error"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="emergency_relation" class="form-label">Relationship <span class="text-danger">*</span></label>
                        <input type="text" id="emergency_relation" name="emergency_relation" class="form-control" required placeholder="e.g., Spouse, Parent">
                        <div class="invalid-feedback" id="emergency_relation_error"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="emergency_contact" class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input type="tel" id="emergency_contact" name="emergency_contact" class="form-control" required placeholder="Enter emergency contact">
                        <div class="invalid-feedback" id="emergency_contact_error"></div>
                    </div>
                </div>
                
                <!-- Notes -->
                <div class="mb-4">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Enter any additional notes about the driver"></textarea>
                </div>
                
                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" onclick="toggleAddDriverForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> <span id="submitText">Save Driver</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteDriverModal" tabindex="-1" aria-labelledby="deleteDriverModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteDriverModalLabel">Delete Driver</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this driver?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelDeleteDriverBtn" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteDriverBtn">Delete</button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/panels/drivers.js'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Desktop\TransiTrack System\BusOperator\resources\views/panels/drivers.blade.php ENDPATH**/ ?>