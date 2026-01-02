

<?php $__env->startSection('title', 'Driver Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fas fa-id-card text-primary me-2"></i>Driver Profile</h2>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('drivers.panel')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Drivers
            </a>
            <button class="btn btn-outline-primary" onclick="editDriver(<?php echo e($driver->id); ?>)">
                <i class="fas fa-edit me-1"></i> Edit Driver
            </button>
            <button class="btn btn-outline-danger" onclick="deleteDriver(<?php echo e($driver->id); ?>)">
                <i class="fas fa-trash-alt me-1"></i> Delete
            </button>
            <button class="btn btn-outline-<?php echo e($driver->status === 'active' ? 'warning' : 'success'); ?>" onclick="toggleDriverStatus(<?php echo e($driver->id); ?>, '<?php echo e($driver->status); ?>')">
                <i class="fas fa-power-off me-1"></i> <?php echo e($driver->status === 'active' ? 'Deactivate' : 'Activate'); ?>

            </button>
        </div>
    </div>

    <!-- Driver Profile Card -->
    <div class="card border-0 bg-white shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="<?php echo e($driver->photo_url ? asset('storage/'.$driver->photo_url) : 'https://randomuser.me/api/portraits/men/'.(($driver->id ?? 1) % 70).'.jpg'); ?>" 
                         alt="Driver Photo" 
                         class="rounded-circle border border-3 border-light shadow" 
                         style="width: 180px; height: 180px; object-fit: cover;">
                    <div class="mt-3">
                        <?php if($driver->status == 'active'): ?>
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>Active
                            </span>
                        <?php elseif($driver->status == 'inactive'): ?>
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>Inactive
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>On Leave
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-9">
                    <h2 class="fw-bold text-dark mb-1"><?php echo e($driver->name ?? 'Unnamed Driver'); ?></h2>
                    <p class="text-muted mb-3">Driver ID: DRV-<?php echo e(str_pad($driver->id ?? 1, 3, '0', STR_PAD_LEFT)); ?></p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-birthday-cake text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Date of Birth</small>
                                    <span class="fw-semibold"><?php echo e($driver->date_of_birth ? $driver->date_of_birth->format('F j, Y') : 'Not provided'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-venus-mars text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Gender</small>
                                    <span class="fw-semibold"><?php echo e(ucfirst($driver->gender ?? 'Not specified')); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-phone-alt text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Contact Number</small>
                                    <span class="fw-semibold"><?php echo e($driver->contact_number ?? 'Not provided'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Email Address</small>
                                    <span class="fw-semibold"><?php echo e($driver->email ?? 'Not provided'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Address</small>
                                    <span class="fw-semibold"><?php echo e($driver->address ?? 'Not provided'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Cards -->
    <div class="row mb-4">
        <!-- License Information -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 bg-white shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-id-card text-primary me-2"></i>License Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <small class="text-muted d-block">License Number</small>
                            <span class="fw-semibold"><?php echo e($driver->license_number ?? 'Not provided'); ?></span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Expiry Date</small>
                            <span class="fw-semibold"><?php echo e($driver->license_expiry ? $driver->license_expiry->format('F j, Y') : 'Not provided'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Emergency Contact -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 bg-white shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-exclamation-circle text-warning me-2"></i>Emergency Contact</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <small class="text-muted d-block">Contact Person</small>
                            <span class="fw-semibold"><?php echo e($driver->emergency_name ?? 'Not provided'); ?></span>
                        </div>
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Relationship</small>
                            <span class="fw-semibold"><?php echo e($driver->emergency_relation ?? 'Not provided'); ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Contact Number</small>
                            <span class="fw-semibold"><?php echo e($driver->emergency_contact ?? 'Not provided'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Routes -->
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fas fa-route text-success me-2"></i>Assigned Routes & Schedules</h5>
        </div>
        <div class="card-body">
            <?php if(isset($driver->schedules) && $driver->schedules->count() > 0): ?>
                <?php $__currentLoopData = $driver->schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($schedule->route && $schedule->bus): ?>
                        <div class="border-start border-primary border-4 bg-light p-3 mb-3 rounded-end">
                            <div class="mb-2">
                                <i class="fas fa-route text-primary me-2"></i>
                                <strong>Route:</strong> <?php echo e($schedule->route->name); ?>

                                <span class="badge bg-primary ms-2"><?php echo e($schedule->route->route_code ?? 'N/A'); ?></span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-bus text-primary me-2"></i>
                                <strong>Bus:</strong> <?php echo e($schedule->bus->bus_number); ?> (<?php echo e($schedule->bus->model); ?>)
                                <?php if($schedule->bus->accommodation_type): ?>
                                    <span class="badge bg-info ms-2"><?php echo e(ucfirst(str_replace('-', ' ', $schedule->bus->accommodation_type))); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Schedule:</strong> 
                                <?php if($schedule->days && is_array($schedule->days)): ?>
                                    <?php echo e(implode(', ', $schedule->days)); ?>

                                <?php elseif($schedule->days && is_string($schedule->days)): ?>
                                    <?php echo e($schedule->days); ?>

                                <?php else: ?>
                                    Daily
                                <?php endif; ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Time:</strong> 
                                <?php echo e($schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : 'Not set'); ?> - 
                                <?php echo e($schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : 'Not set'); ?>

                            </div>
                            <?php if($schedule->status): ?>
                                <div>
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Status:</strong> 
                                    <?php if($schedule->status == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php elseif($schedule->status == 'completed'): ?>
                                        <span class="badge bg-secondary">Completed</span>
                                    <?php elseif($schedule->status == 'cancelled'): ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning"><?php echo e(ucfirst($schedule->status)); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-route fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Routes Assigned</h5>
                    <p class="text-muted">This driver has not been assigned to any routes or schedules yet.</p>
                    <a href="<?php echo e(route('schedule.panel')); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Assign Schedule
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Additional Notes (if any) -->
    <?php if($driver->notes): ?>
    <div class="card border-0 bg-white shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fas fa-sticky-note text-info me-2"></i>Additional Notes</h5>
        </div>
        <div class="card-body">
            <p class="mb-0"><?php echo e($driver->notes); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Edit Driver Modal -->
    <div class="modal fade" id="editDriverModal" tabindex="-1" aria-labelledby="editDriverModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editDriverModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Driver Information
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editDriverForm" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="edit_driver_id" name="driver_id" value="<?php echo e($driver->id); ?>">
                        
                        <div class="row">
                            <!-- Photo Upload -->
                            <div class="col-md-4 text-center mb-4">
                                <label class="form-label fw-bold">Driver Photo</label>
                                <div class="mx-auto" style="cursor: pointer; width: 150px; height: 150px;">
                                    <img id="edit-photo-preview" 
                                         src="<?php echo e($driver->photo_url ? asset('storage/'.$driver->photo_url) : 'https://randomuser.me/api/portraits/men/'.(($driver->id ?? 1) % 70).'.jpg'); ?>" 
                                         alt="Driver Photo" 
                                         class="rounded-circle border border-3 border-light shadow w-100 h-100" 
                                         style="object-fit: cover;">
                                    <input type="file" id="edit_photo" name="photo" accept="image/*" style="display: none;">
                                </div>
                                <small class="text-muted d-block mt-2">Click to upload photo</small>
                            </div>
                            
                            <!-- Form Fields -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_name" name="name" class="form-control" value="<?php echo e($driver->name); ?>" required>
                                        <div class="invalid-feedback" id="edit_name_error"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" id="edit_email" name="email" class="form-control" value="<?php echo e($driver->email); ?>" required>
                                        <div class="invalid-feedback" id="edit_email_error"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                        <input type="tel" id="edit_contact_number" name="contact_number" class="form-control" value="<?php echo e($driver->contact_number); ?>" required>
                                        <div class="invalid-feedback" id="edit_contact_number_error"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" id="edit_date_of_birth" name="date_of_birth" class="form-control" value="<?php echo e($driver->date_of_birth ? $driver->date_of_birth->format('Y-m-d') : ''); ?>" required>
                                        <div class="invalid-feedback" id="edit_date_of_birth_error"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select id="edit_gender" name="gender" class="form-select" required>
                                            <option value="male" <?php echo e($driver->gender === 'male' ? 'selected' : ''); ?>>Male</option>
                                            <option value="female" <?php echo e($driver->gender === 'female' ? 'selected' : ''); ?>>Female</option>
                                            <option value="other" <?php echo e($driver->gender === 'other' ? 'selected' : ''); ?>>Other</option>
                                        </select>
                                        <div class="invalid-feedback" id="edit_gender_error"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select id="edit_status" name="status" class="form-select" required>
                                            <option value="active" <?php echo e($driver->status === 'active' ? 'selected' : ''); ?>>Active</option>
                                            <option value="inactive" <?php echo e($driver->status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                            <option value="on-leave" <?php echo e($driver->status === 'on-leave' ? 'selected' : ''); ?>>On Leave</option>
                                        </select>
                                        <div class="invalid-feedback" id="edit_status_error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea id="edit_address" name="address" class="form-control" rows="2" required><?php echo e($driver->address); ?></textarea>
                            <div class="invalid-feedback" id="edit_address_error"></div>
                        </div>
                        
                        <!-- License Information -->
                        <div class="border border-primary border-opacity-25 rounded p-3 mb-3">
                            <h6 class="text-primary mb-3"><i class="fas fa-id-card me-2"></i>License Information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_license_number" class="form-label">License Number <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_license_number" name="license_number" class="form-control" value="<?php echo e($driver->license_number); ?>" required>
                                    <div class="invalid-feedback" id="edit_license_number_error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_license_expiry" class="form-label">License Expiry <span class="text-danger">*</span></label>
                                    <input type="date" id="edit_license_expiry" name="license_expiry" class="form-control" value="<?php echo e($driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : ''); ?>" required>
                                    <div class="invalid-feedback" id="edit_license_expiry_error"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Emergency Contact -->
                        <div class="border border-warning border-opacity-25 rounded p-3 mb-3">
                            <h6 class="text-warning mb-3"><i class="fas fa-exclamation-circle me-2"></i>Emergency Contact</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="edit_emergency_name" class="form-label">Contact Person <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_emergency_name" name="emergency_name" class="form-control" value="<?php echo e($driver->emergency_name); ?>" required>
                                    <div class="invalid-feedback" id="edit_emergency_name_error"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_emergency_relation" class="form-label">Relationship <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_emergency_relation" name="emergency_relation" class="form-control" value="<?php echo e($driver->emergency_relation); ?>" required>
                                    <div class="invalid-feedback" id="edit_emergency_relation_error"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_emergency_contact" class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                                    <input type="tel" id="edit_emergency_contact" name="emergency_contact" class="form-control" value="<?php echo e($driver->emergency_contact); ?>" required>
                                    <div class="invalid-feedback" id="edit_emergency_contact_error"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_notes" class="form-label">Additional Notes</label>
                            <textarea id="edit_notes" name="notes" class="form-control" rows="2"><?php echo e($driver->notes); ?></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveDriverBtn">
                        <i class="fas fa-save me-1"></i> <span id="saveDriverText">Save Changes</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/panels/profile.js'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Desktop\TransiTrack System\BusOperator\resources\views/panels/profile.blade.php ENDPATH**/ ?>