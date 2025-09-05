// Toast notification function (same as other panels)
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

// Edit driver function
function editDriver(id) {
    const modal = new bootstrap.Modal(document.getElementById('editDriverModal'));
    modal.show();
}

// Delete driver function
function deleteDriver(id) {
    if (confirm('Are you sure you want to delete this driver? This action cannot be undone.')) {
        fetch(`/drivers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Driver deleted successfully', 'success');
                setTimeout(() => {
                    window.location.href = window.driversRoute || '/drivers';
                }, 1000);
            } else {
                showToast('Error deleting driver: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting driver', 'error');
        });
    }
}

// Toggle driver status function
function toggleDriverStatus(id, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    
    if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this driver?`)) {
        fetch(`/drivers/${id}/status`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

// Save driver changes function
function saveDriverChanges() {
    const form = document.getElementById('editDriverForm');
    const formData = new FormData(form);
    const driverId = document.getElementById('edit_driver_id').value;
    
    const saveBtn = document.getElementById('saveDriverBtn');
    const saveText = document.getElementById('saveDriverText');
    const originalText = saveText.textContent;
    
    // Clear previous validation errors
    clearValidationErrors();
    
    saveBtn.disabled = true;
    saveText.textContent = 'Saving...';
    
    fetch(`/drivers/${driverId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Driver updated successfully', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('editDriverModal'));
            modal.hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        if (error.errors) {
            showValidationErrors(error.errors);
            showToast('Please check the form for errors', 'error');
        } else {
            showToast('Error updating driver: ' + (error.message || 'Unknown error'), 'error');
        }
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveText.textContent = originalText;
    });
}

// Clear validation errors
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

// Show validation errors
function showValidationErrors(errors) {
    clearValidationErrors();
    
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.getElementById(`edit_${field}`);
        const errorDiv = document.getElementById(`edit_${field}_error`);
        
        if (input && errorDiv) {
            input.classList.add('is-invalid');
            errorDiv.textContent = messages[0];
        }
    }
}

// Make functions globally available
window.editDriver = editDriver;
window.deleteDriver = deleteDriver;
window.toggleDriverStatus = toggleDriverStatus;
window.saveDriverChanges = saveDriverChanges;

// DOMContentLoaded event
document.addEventListener('DOMContentLoaded', function() {
    // Photo upload functionality
    const photoContainer = document.querySelector('#edit-photo-preview').parentElement;
    const photoInput = document.getElementById('edit_photo');
    const photoPreview = document.getElementById('edit-photo-preview');

    photoContainer?.addEventListener('click', function() {
        photoInput.click();
    });

    photoInput?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Save button click handler
    document.getElementById('saveDriverBtn')?.addEventListener('click', saveDriverChanges);
    
    // Set the drivers route for navigation
    window.driversRoute = document.querySelector('a[href*="drivers.panel"]')?.getAttribute('href') || '/drivers';
});