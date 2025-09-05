// Simple toast notification function
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

// Show bus modal for adding new bus
function showBusModal() {
    document.getElementById('busForm').reset();
    document.getElementById('bus_id').value = '';
    document.getElementById('method_field').value = '';
    document.getElementById('modalTitleText').textContent = 'Add New Bus';
    document.getElementById('submitText').textContent = 'Save Bus';
    
    // Clear validation errors
    clearValidationErrors();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('busModal'));
    modal.show();
}

// Edit bus function
function editBus(busId) {
    fetch(`/api/buses/${busId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Populate form fields
            document.getElementById('bus_id').value = data.id;
            document.getElementById('bus_number').value = data.bus_number || '';
            document.getElementById('plate_number').value = data.plate_number || '';
            document.getElementById('model').value = data.model || '';
            document.getElementById('capacity').value = data.capacity || '';
            document.getElementById('bus_company').value = data.bus_company || '';
            document.getElementById('accommodation_type').value = data.accommodation_type || 'regular';
            document.getElementById('bus_status').value = data.status || 'active';
            document.getElementById('description').value = data.description || '';
            
            document.getElementById('method_field').value = 'PUT';
            document.getElementById('modalTitleText').textContent = 'Edit Bus';
            document.getElementById('submitText').textContent = 'Update Bus';
            
            clearValidationErrors();
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('busModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to load bus details', 'error');
        });
}

// Save bus function (handles both create and update)
function saveBus() {
    const form = document.getElementById('busForm');
    const formData = new FormData(form);
    const busId = document.getElementById('bus_id').value;
    const isEdit = busId !== '';
    
    // Determine URL and method
    const url = isEdit ? `/buses/${busId}` : '/buses';
    
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const originalText = submitText.textContent;
    
    submitBtn.disabled = true;
    submitText.textContent = 'Saving...';
    
    // Add method for Laravel
    if (isEdit) {
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
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
            showToast(data.message || 'Bus saved successfully', 'success');
            
            // Close modal
            const modalElement = document.getElementById('busModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            
            // Reload page after a short delay
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        if (error.errors) {
            showValidationErrors(error.errors);
        } else {
            let errorMessage = 'Error saving bus';
            
            if (error.errors) {
                const errors = Object.values(error.errors).flat();
                errorMessage = errors.join('\n');
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            showToast(errorMessage, 'error');
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitText.textContent = originalText;
    });
}

// Delete bus function
function deleteBus(busId) {
    if (confirm('Are you sure you want to delete this bus? This action cannot be undone.')) {
        fetch(`/buses/${busId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Bus deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting bus: ' + (error.message || 'Unknown error'), 'error');
        });
    }
}

// Clear filters function
function clearFilters() {
    document.getElementById('search').value = '';
    document.getElementById('filter_status').value = '';
    document.getElementById('filter_accommodation').value = '';
    window.location.href = window.location.pathname;
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
        const input = document.getElementById(field);
        const errorDiv = document.getElementById(`${field}_error`);
        
        if (input && errorDiv) {
            input.classList.add('is-invalid');
            errorDiv.textContent = messages[0];
        }
    }
}

// Make functions globally available
window.showBusModal = showBusModal;
window.editBus = editBus;
window.deleteBus = deleteBus;
window.clearFilters = clearFilters;
window.saveBus = saveBus;

// DOM Content Loaded event listener
document.addEventListener('DOMContentLoaded', function() {
    // Ensure CSRF token is available
    if (!document.querySelector('meta[name="csrf-token"]')) {
        console.error('CSRF token meta tag not found!');
        return;
    }

    // Add Bus button event listener
    const addBusBtn = document.getElementById('addBusBtn');
    if (addBusBtn) {
        addBusBtn.addEventListener('click', showBusModal);
    }
    
    // Handle form submission with Enter key
    const busForm = document.getElementById('busForm');
    if (busForm) {
        busForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                saveBus();
            }
        });
    }
});