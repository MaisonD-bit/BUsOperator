@extends('layouts.app')

@section('content')
<div class="container-fluid p-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-building me-2 text-primary fs-4"></i>
            <h2 class="mb-0 fw-bold">South Bus Terminal Layout</h2>
        </div>
    </div>

    <!-- Date Selection -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                        <label for="terminal-date" class="form-label mb-0 fw-bold">Select Date:</label>
                        <input type="date" id="terminal-date" class="form-control form-control-sm" style="width: auto;" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                        <button id="refresh-terminal" class="btn btn-primary btn-sm">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-center flex-wrap gap-3">
                        <span class="badge bg-success">&nbsp;</span><small class="ms-2 me-3">Available</small>
                        <span class="badge bg-danger">&nbsp;</span><small class="ms-2 me-3">Occupied</small>
                        <span class="badge bg-primary">&nbsp;</span><small class="ms-2 me-3">Selected</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SVG Terminal Layout -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-secondary">
                <div class="card-body p-3">
                    <!-- Zoom Controls -->
                    <div class="mb-2 d-flex gap-2 justify-content-center">
                        <button id="zoomIn" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-search-plus"></i> Zoom In
                        </button>
                        <button id="zoomOut" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-search-minus"></i> Zoom Out
                        </button>
                        <button id="zoomReset" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                        <span id="zoomLevel" class="badge bg-info">100%</span>
                    </div>

                    <!-- SVG Container with Pan & Zoom -->
                    <div id="svg-container" style="
                        overflow: auto; 
                        max-height: 600px; 
                        border: 1px solid #ddd; 
                        border-radius: 5px;
                        background: white;
                        position: relative;
                    ">
                        <svg id="terminal-svg" 
                            width="100%" 
                            height="auto" 
                            viewBox="0 0 2506 2160"
                            xmlns="http://www.w3.org/2000/svg"
                            style="display: block; min-width: 100%; transform-origin: top left; transition: transform 0.2s;">
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parking Space Info & Assignment -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Selected Space</h5>
                </div>
                <div class="card-body py-3">
                    <div id="space-info">
                        <p class="text-muted text-center mb-0">Click on a parking space to select</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Assign Route & Bus</h5>
                </div>
                <div class="card-body py-3">
                    <form id="assignSpaceForm">
                        <input type="hidden" id="selectedSpaceId" name="space_id">
                        
                        <div class="mb-2">
                            <label for="assign_route_id" class="form-label small fw-bold">Route <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="assign_route_id" name="route_id" required disabled>
                                <option value="">Select Route</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->code }} - {{ $route->start_location }} → {{ $route->end_location }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label for="assign_driver_id" class="form-label small fw-bold">Driver <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="assign_driver_id" name="driver_id" required disabled>
                                <option value="">Select Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->license_number ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label for="assign_bus_id" class="form-label small fw-bold">Bus <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="assign_bus_id" name="bus_id" required disabled>
                                <option value="">Select Bus</option>
                                @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}">{{ $bus->plate_number }} ({{ $bus->capacity }} seats)</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100" disabled id="assignBtn">
                            <i class="fas fa-check me-1"></i> Assign Space
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2" id="clearBtn" style="display: none;">
                            <i class="fas fa-times me-1"></i> Clear Selection
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Assignments -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Today's Assignments</h5>
                </div>
                <div class="card-body py-3">
                    <div id="assignments-summary">
                        <p class="text-muted text-center mb-0">Loading assignments...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectedSpaceId = document.getElementById('selectedSpaceId');
    const spaceInfo = document.getElementById('space-info');
    const assignForm = document.getElementById('assignSpaceForm');
    const assignBtn = document.getElementById('assignBtn');
    const clearBtn = document.getElementById('clearBtn');
    const selects = document.querySelectorAll('#assign_route_id, #assign_driver_id, #assign_bus_id');

    // Zoom variables
    let currentZoom = 1;
    const zoomStep = 0.1;
    const minZoom = 0.5;
    const maxZoom = 3;

    // Load SVG and setup click handlers
    fetch('{{ asset("svg/CSBT_layout.svg") }}')
        .then(response => response.text())
        .then(svgContent => {
            const svgContainer = document.getElementById('terminal-svg');
            svgContainer.innerHTML = svgContent;
            setupParkingSpaceClickHandlers();
            setupZoomControls();
        })
        .catch(error => {
            console.error('Error loading SVG:', error);
            document.getElementById('terminal-svg').innerHTML = '<text x="50%" y="50%" text-anchor="middle">Error loading terminal layout. Make sure CSBT_layout.svg exists in public/svg folder</text>';
        });

    function setupZoomControls() {
        const svg = document.getElementById('terminal-svg');
        const container = document.getElementById('svg-container');
        const zoomLevel = document.getElementById('zoomLevel');

        function updateZoom() {
            svg.style.transform = `scale(${currentZoom})`;
            zoomLevel.textContent = Math.round(currentZoom * 100) + '%';
        }

        document.getElementById('zoomIn').addEventListener('click', function() {
            if (currentZoom < maxZoom) {
                currentZoom += zoomStep;
                updateZoom();
            }
        });

        document.getElementById('zoomOut').addEventListener('click', function() {
            if (currentZoom > minZoom) {
                currentZoom -= zoomStep;
                updateZoom();
            }
        });

        document.getElementById('zoomReset').addEventListener('click', function() {
            currentZoom = 1;
            updateZoom();
            container.scrollLeft = 0;
            container.scrollTop = 0;
        });

        // Mouse wheel zoom
        container.addEventListener('wheel', function(e) {
            if (e.ctrlKey) {
                e.preventDefault();
                if (e.deltaY < 0) {
                    // Scroll up = zoom in
                    if (currentZoom < maxZoom) {
                        currentZoom += zoomStep;
                        updateZoom();
                    }
                } else {
                    // Scroll down = zoom out
                    if (currentZoom > minZoom) {
                        currentZoom -= zoomStep;
                        updateZoom();
                    }
                }
            }
        });
    }

    function setupParkingSpaceClickHandlers() {
        // Query all parking space rectangles (green colored elements)
        const parkingSpaces = document.querySelectorAll('#terminal-svg rect[fill="#ABE6AF"]');
        
        console.log(`Found ${parkingSpaces.length} parking spaces`);
        
        parkingSpaces.forEach((space, index) => {
            const spaceId = `P${index + 1}`;
            space.setAttribute('data-space-id', spaceId);
            space.style.cursor = 'pointer';
            space.style.transition = 'opacity 0.2s, filter 0.2s';
            
            space.addEventListener('click', function(e) {
                selectSpace(spaceId, space);
                e.stopPropagation();
            });

            space.addEventListener('mouseover', function() {
                this.style.filter = 'brightness(0.8)';
            });

            space.addEventListener('mouseout', function() {
                this.style.filter = 'brightness(1)';
            });
        });
    }

    function selectSpace(spaceId, element) {
        // Remove previous selection
        document.querySelectorAll('#terminal-svg rect[data-space-id]').forEach(el => {
            el.style.stroke = 'none';
            el.style.strokeWidth = '0';
        });

        // Highlight selected space
        element.style.stroke = '#007bff';
        element.style.strokeWidth = '3';

        selectedSpaceId.value = spaceId;
        
        // Update space info
        spaceInfo.innerHTML = `
            <div class="text-start">
                <p class="mb-1"><strong>Space ID:</strong> ${spaceId}</p>
                <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Available</span></p>
            </div>
        `;

        // Enable form fields
        selects.forEach(select => select.disabled = false);
        assignBtn.disabled = false;
        clearBtn.style.display = 'block';
    }

    clearBtn.addEventListener('click', function() {
        selectedSpaceId.value = '';
        document.querySelectorAll('#terminal-svg rect[data-space-id]').forEach(el => {
            el.style.stroke = 'none';
        });
        spaceInfo.innerHTML = '<p class="text-muted text-center mb-0">Click on a parking space to select</p>';
        selects.forEach(select => {
            select.disabled = true;
            select.value = '';
        });
        assignBtn.disabled = true;
        clearBtn.style.display = 'none';
    });

    assignForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!selectedSpaceId.value) {
            alert('Please select a parking space');
            return;
        }

        const formData = new FormData(this);
        
        fetch('{{ route("terminal.assign-space") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Space assigned successfully!');
                clearBtn.click();
                loadAssignments();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    function loadAssignments() {
        const selectedDate = document.getElementById('terminal-date').value;
        
        fetch(`{{ route('terminal.get-assignments') }}?date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                const summary = document.getElementById('assignments-summary');
                
                if (data.assignments.length === 0) {
                    summary.innerHTML = '<p class="text-muted text-center mb-0">No assignments for this date</p>';
                    return;
                }

                let html = '<div class="table-responsive"><table class="table table-sm mb-0">' +
                    '<thead><tr><th>Space</th><th>Route</th><th>Driver</th><th>Bus</th><th>Status</th><th>Action</th></tr></thead><tbody>';
                
                data.assignments.forEach(assignment => {
                    html += `
                        <tr>
                            <td><strong>${assignment.space_id}</strong></td>
                            <td>${assignment.route.code}</td>
                            <td>${assignment.driver.name}</td>
                            <td>${assignment.bus.plate_number}</td>
                            <td><span class="badge bg-warning">Assigned</span></td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="removeAssignment(${assignment.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
                summary.innerHTML = html;
            });
    }

    window.removeAssignment = function(assignmentId) {
        if (confirm('Remove this assignment?')) {
            fetch(`{{ route('terminal.remove-assignment') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ assignment_id: assignmentId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadAssignments();
                }
            });
        }
    };

    // Load assignments on page load and when date changes
    loadAssignments();
    document.getElementById('terminal-date').addEventListener('change', loadAssignments);
    document.getElementById('refresh-terminal').addEventListener('click', loadAssignments);
});
</script>
@endpush