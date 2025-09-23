// Driver management functions for the web panel
// Store current form data globally to preserve it
let currentFormData = {};

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `position-fixed top-0 end-0 m-3 alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

function saveCurrentFormData() {
    const form = document.getElementById('driverForm');
    if (!form) return;

    currentFormData = {
        driver_id: document.getElementById('driver_id')?.value || '',
        name: document.getElementById('name')?.value || '',
        email: document.getElementById('email')?.value || '',
        contact_number: document.getElementById('contact_number')?.value || '',
        date_of_birth: document.getElementById('date_of_birth')?.value || '',
        gender: document.getElementById('gender')?.value || '',
        address: document.getElementById('address')?.value || '',
        license_number: document.getElementById('license_number')?.value || '',
        license_expiry: document.getElementById('license_expiry')?.value || '',
        emergency_name: document.getElementById('emergency_name')?.value || '',
        emergency_relation: document.getElementById('emergency_relation')?.value || '',
        emergency_contact: document.getElementById('emergency_contact')?.value || '',
        status: document.getElementById('status')?.value || 'active',
        notes: document.getElementById('notes')?.value || '',
        photo_preview: document.getElementById('photo-preview')?.src || ''
    };
}

function restoreFormData() {
    if (Object.keys(currentFormData).length === 0) return;

    const setFieldValue = (id, value) => {
        const element = document.getElementById(id);
        if (element && value !== undefined) {
            element.value = value;
        }
    };

    setFieldValue('driver_id', currentFormData.driver_id);
    setFieldValue('name', currentFormData.name);
    setFieldValue('email', currentFormData.email);
    setFieldValue('contact_number', currentFormData.contact_number);
    setFieldValue('date_of_birth', currentFormData.date_of_birth);
    setFieldValue('gender', currentFormData.gender);
    setFieldValue('address', currentFormData.address);
    setFieldValue('license_number', currentFormData.license_number);
    setFieldValue('license_expiry', currentFormData.license_expiry);
    setFieldValue('emergency_name', currentFormData.emergency_name);
    setFieldValue('emergency_relation', currentFormData.emergency_relation);
    setFieldValue('emergency_contact', currentFormData.emergency_contact);
    setFieldValue('status', currentFormData.status);
    setFieldValue('notes', currentFormData.notes);
    
    if (currentFormData.photo_preview) {
        const preview = document.getElementById('photo-preview');
        if (preview) {
            preview.src = currentFormData.photo_preview;
        }
    }
}

function showDriverForm() {
    const form = document.getElementById('driverForm');
    const formSection = document.getElementById('addDriverFormSection');
    
    if (form) form.reset();
    
    const setFieldValue = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.value = value;
    };

    setFieldValue('driver_id', '');
    setFieldValue('method_field', '');
    
    const formTitle = document.getElementById('formTitle');
    const submitText = document.getElementById('submitText');
    
    if (formTitle) formTitle.innerHTML = '<i class="fas fa-user-plus me-2"></i>Add New Driver';
    if (submitText) submitText.textContent = 'Save Driver';
    
    // Reset photo preview
    const preview = document.getElementById('photo-preview');
    if (preview) preview.src = 'https://randomuser.me/api/portraits/men/1.jpg';
    
    // Clear validation errors
    clearValidationErrors();
    
    // Clear stored form data for new driver
    currentFormData = {};
    
    if (formSection) {
        formSection.style.display = 'block';
        formSection.scrollIntoView({ behavior: 'smooth' });
    }
}

function hideDriverForm() {
    const formSection = document.getElementById('addDriverFormSection');
    if (formSection) {
        formSection.style.display = 'none';
    }
    // Don't reset form - preserve data
}

function showModal(modalId) {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function hideModal(modalId) {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
}

function toggleAddDriverForm() {
    const formSection = document.getElementById('addDriverFormSection');
    if (!formSection) return;
    
    if (formSection.style.display === 'none' || formSection.style.display === '') {
        showDriverForm();
    } else {
        hideDriverForm();
    }
}

function viewDriver(driverId) {
    window.location.href = `/panel/profile/${driverId}`;
}

function editDriver(driverId) {
    // Save current form data before loading new data
    saveCurrentFormData();
    
    fetch(`/api/drivers/${driverId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Show form
            const formSection = document.getElementById('addDriverFormSection');
            if (formSection) {
                formSection.style.display = 'block';
                formSection.scrollIntoView({ behavior: 'smooth' });
            }
            
            // Populate form fields
            const setFieldValue = (id, value) => {
                const element = document.getElementById(id);
                if (element) element.value = value || '';
            };

            setFieldValue('driver_id', data.id);
            setFieldValue('name', data.name);
            setFieldValue('email', data.email);
            setFieldValue('contact_number', data.contact_number);
            setFieldValue('date_of_birth', data.date_of_birth);
            setFieldValue('gender', data.gender);
            setFieldValue('address', data.address);
            setFieldValue('license_number', data.license_number);
            setFieldValue('license_expiry', data.license_expiry);
            setFieldValue('emergency_name', data.emergency_name);
            setFieldValue('emergency_relation', data.emergency_relation);
            setFieldValue('emergency_contact', data.emergency_contact);
            setFieldValue('status', data.status);
            setFieldValue('notes', data.notes);
            
            // Update photo preview
            const preview = document.getElementById('photo-preview');
            if (preview) {
                if (data.photo_url) {
                    preview.src = `/storage/${data.photo_url}`;
                } else {
                    preview.src = `https://randomuser.me/api/portraits/men/${data.id % 70}.jpg`;
                }
            }
            
            const methodField = document.getElementById('method_field');
            const formTitle = document.getElementById('formTitle');
            const submitText = document.getElementById('submitText');
            
            if (methodField) methodField.value = 'PUT';
            if (formTitle) formTitle.innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit Driver';
            if (submitText) submitText.textContent = 'Update Driver';
            
            // Save the loaded data
            saveCurrentFormData();
            
            clearValidationErrors();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to load driver details', 'error');
            // Restore previous form data on error
            restoreFormData();
        });
}

function updateDriverStatus(driverId, newStatus) {
    const actionText = newStatus === 'active' ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${actionText} this driver?`)) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showToast('CSRF token not found', 'error');
            return;
        }

        fetch(`/drivers/${driverId}/status`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Driver status updated successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error updating status: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error updating driver status', 'error');
        });
    }
}

function toggleView(viewType) {
    const gridView = document.getElementById('gridView');
    const tableView = document.getElementById('tableView');
    const gridBtn = document.getElementById('gridViewBtn');
    const tableBtn = document.getElementById('tableViewBtn');
    
    if (viewType === 'grid') {
        if (gridView) gridView.style.display = 'block';
        if (tableView) tableView.style.display = 'none';
        if (gridBtn) gridBtn.classList.add('active');
        if (tableBtn) tableBtn.classList.remove('active');
    } else {
        if (gridView) gridView.style.display = 'none';
        if (tableView) tableView.style.display = 'block';
        if (tableBtn) tableBtn.classList.add('active');
        if (gridBtn) gridBtn.classList.remove('active');
    }
}

function clearFilters() {
    const searchInput = document.getElementById('driverSearch');
    const statusFilter = document.getElementById('statusFilter');
    
    if (searchInput) searchInput.value = '';
    if (statusFilter) statusFilter.value = '';
    
    window.location.href = window.location.pathname;
}

function clearValidationErrors() {
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.classList.remove('is-invalid');
    });
    
    const errorDivs = document.querySelectorAll('.invalid-feedback');
    errorDivs.forEach(div => {
        div.textContent = '';
    });
}

function showValidationErrors(errors) {
    clearValidationErrors();
    
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.getElementById(field);
        const errorDiv = document.getElementById(`${field}_error`);
        
        if (input && errorDiv) {
            input.classList.add('is-invalid');
            errorDiv.textContent = messages[0];
        }
    }
}

function approveDriver(driverId) {
    updateDriverStatus(driverId, 'active');
}

function rejectDriver(driverId) {
    updateDriverStatus(driverId, 'rejected');
}

// Make functions globally available
window.showDriverForm = showDriverForm;
window.hideDriverForm = hideDriverForm;
window.toggleAddDriverForm = toggleAddDriverForm;
window.viewDriver = viewDriver;
window.editDriver = editDriver;
// window.deleteDriver = deleteDriver;
window.updateDriverStatus = updateDriverStatus;
window.toggleView = toggleView;
window.clearFilters = clearFilters;
window.approveDriver = approveDriver;
window.rejectDriver = rejectDriver;

// DOMContentLoaded event

let driverToDelete = null;

function deleteDriver(driverId) {
    driverToDelete = driverId;
    showModal('deleteDriverModal');
}

window.deleteDriver = deleteDriver;

document.addEventListener('DOMContentLoaded', function() {
    // Ensure CSRF token is available
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token meta tag not found!');
        return;
    }

    // Add Driver button event listeners
    const addDriverBtn = document.getElementById('addDriverBtn');
    if (addDriverBtn) {
        addDriverBtn.addEventListener('click', function() {
            // Reset form for new driver
            const form = document.getElementById('driverForm');
            if (form) form.reset();
            
            const setFieldValue = (id, value) => {
                const element = document.getElementById(id);
                if (element) element.value = value;
            };

            setFieldValue('driver_id', '');
            setFieldValue('method_field', '');
            
            const formTitle = document.getElementById('formTitle');
            const submitText = document.getElementById('submitText');
            const preview = document.getElementById('photo-preview');
            
            if (formTitle) formTitle.innerHTML = '<i class="fas fa-user-plus me-2"></i>Add New Driver';
            if (submitText) submitText.textContent = 'Save Driver';
            if (preview) preview.src = 'https://randomuser.me/api/portraits/men/1.jpg';
            
            // Clear stored data for new driver
            currentFormData = {};
            
            showDriverForm();
        });
    }

    document.getElementById('confirmDeleteDriverBtn').addEventListener('click', function() {
        if (!driverToDelete) return;
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        fetch(`/drivers/${driverToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message || 'Driver deleted successfully', data.success ? 'success' : 'error');
            setTimeout(() => window.location.reload(), 1000);
        })
        .catch(error => {
            showToast('Error deleting driver', 'error');
        })
        .finally(() => {
            driverToDelete = null;
            hideModal('deleteDriverModal');
        });
    });

    document.getElementById('cancelDeleteDriverBtn').addEventListener('click', function() {
        driverToDelete = null;
        hideModal('deleteDriverModal');
    });
    
    // Add First Driver buttons
    const addFirstDriverBtn = document.getElementById('addFirstDriverBtn');
    const addFirstDriverBtnTable = document.getElementById('addFirstDriverBtnTable');
    
    if (addFirstDriverBtn && addDriverBtn) {
        addFirstDriverBtn.addEventListener('click', function() {
            addDriverBtn.click();
        });
    }
    
    if (addFirstDriverBtnTable && addDriverBtn) {
        addFirstDriverBtnTable.addEventListener('click', function() {
            addDriverBtn.click();
        });
    }
    
    // Photo upload functionality
    const photoContainer = document.querySelector('#photo-preview')?.parentElement;
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photo-preview');
    const photoOverlay = document.querySelector('.photo-overlay');

    // Show overlay on hover
    if (photoContainer && photoOverlay) {
        photoContainer.addEventListener('mouseenter', function() {
            photoOverlay.style.opacity = '1';
        });
        
        photoContainer.addEventListener('mouseleave', function() {
            photoOverlay.style.opacity = '0';
        });
    }

    if (photoContainer && photoInput) {
        photoContainer.addEventListener('click', function() {
            photoInput.click();
        });
    }

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    // Update stored data
                    if (currentFormData) {
                        currentFormData.photo_preview = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Auto-save form data on input changes
    const formInputs = document.querySelectorAll('#driverForm input, #driverForm select, #driverForm textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', saveCurrentFormData);
        input.addEventListener('change', saveCurrentFormData);
    });
    
    // Form submission with better error handling and data preservation
    const driverForm = document.getElementById('driverForm');
    if (driverForm) {
        driverForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Save current form data before submission
            saveCurrentFormData();
            
            const formData = new FormData(this);
            const driverId = document.getElementById('driver_id')?.value || '';
            const isEdit = driverId !== '';
            
            // Determine the correct URL and method
            let url = '/drivers';
            if (isEdit) {
                url = `/drivers/${driverId}`;
                formData.append('_method', 'PUT');
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const originalText = submitText?.textContent || 'Save';
            
            if (submitBtn) submitBtn.disabled = true;
            if (submitText) submitText.textContent = 'Saving...';
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Error response:', text);
                        let errorData;
                        try {
                            errorData = JSON.parse(text);
                        } catch (e) {
                            throw new Error(`HTTP ${response.status}: ${text.substring(0, 100)}...`);
                        }
                        
                        // Handle validation errors
                        if (errorData.errors) {
                            showValidationErrors(errorData.errors);
                            // Restore form data after validation error
                            setTimeout(() => restoreFormData(), 100);
                            throw new Error('Validation failed. Please check the form.');
                        }
                        
                        throw new Error(errorData.message || `HTTP ${response.status}`);
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Success response:', data);
                if (data.success) {
                    showToast(data.message || 'Driver saved successfully', 'success');
                    // Clear stored data on successful save
                    currentFormData = {};
                    hideDriverForm();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    console.error('Server returned error:', data);
                    showToast('Error: ' + (data.message || 'Unknown error'), 'error');
                    // Restore form data on error
                    restoreFormData();
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showToast('Error saving driver: ' + error.message, 'error');
                // Restore form data on error
                restoreFormData();
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
                if (submitText) submitText.textContent = originalText;
            });
        });
    }
    
    // View toggle functionality
    const tableViewBtn = document.getElementById('tableViewBtn');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');

    if (tableViewBtn && gridViewBtn && tableView && gridView) {
        tableViewBtn.addEventListener('click', function() {
            tableView.style.display = 'block';
            gridView.style.display = 'none';
            tableViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
        });

        gridViewBtn.addEventListener('click', function() {
            gridView.style.display = 'block';
            tableView.style.display = 'none';
            gridViewBtn.classList.add('active');
            tableViewBtn.classList.remove('active');
        });
    }
});