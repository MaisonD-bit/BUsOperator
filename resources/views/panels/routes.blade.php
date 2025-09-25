@extends('layouts.app')

@section('title', 'Routes')

@push('styles')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
@endpush

@section('content')
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
            <form method="GET" action="{{ route('routes.panel') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Routes</label>
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="Search by name, code, or location..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="filter_status" class="form-label">Status</label>
                    <select id="filter_status" name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                    <a href="{{ route('routes.panel') }}" class="btn btn-outline-secondary">
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
            <form id="routeForm" method="POST" data-routes-store-url="{{ route('routes.store') }}">
                @csrf
                <input type="hidden" id="route_id" name="route_id">
                <input type="hidden" id="method_field" name="_method">
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
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>All routes start from:</strong> Cebu North Bus Terminal (SM City)
                                <br><small class="text-muted">This is the main departure point for all bus routes</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map for End Location and Stops Selection -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Select Destination & Stops in Cebu</h6>
                                <small class="text-muted">Click on the map to set the destination (red marker). Add stops by clicking "Add Stop" and then clicking on the map for each stop.</small>
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
                                            Add Stops (Optional)
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
                                        <i class="fas fa-map-pin me-1"></i>Add Stop
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
                

                <!-- Route Information -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Route Information</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Distance</label>
                                        <div class="input-group">
                                            <input type="number" step="0.1" id="distance_km" name="distance_km" class="form-control" readonly placeholder="Auto-calculated">
                                            <span class="input-group-text">km</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Duration</label>
                                        <div class="input-group">
                                            <input type="number" id="estimated_duration" name="estimated_duration" class="form-control" readonly placeholder="Auto-calculated">
                                            <span class="input-group-text">mins</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Suggested Regular Fare</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" step="0.01" id="suggested_regular" class="form-control" readonly placeholder="Auto-calculated">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Suggested Aircon Fare</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" step="0.01" id="suggested_aircon" class="form-control" readonly placeholder="Auto-calculated">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="regular_price" class="form-label">Regular Fare <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" id="regular_price" name="regular_price" class="form-control" required placeholder="15.00">
                        </div>
                        <small class="text-muted">Base: ₱15 + ₱2.50-3.50/km</small>
                        <div class="invalid-feedback" id="regular_price_error"></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="aircon_price" class="form-label">Aircon Fare</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" id="aircon_price" name="aircon_price" class="form-control" placeholder="20.00">
                        </div>
                        <small class="text-muted">Usually +30% of regular fare</small>
                        <div class="invalid-feedback" id="aircon_price_error"></div>
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
                        <label for="fare_type" class="form-label">Fare Calculation</label>
                        <select id="fare_type" class="form-select" onchange="calculateFare()">
                            <option value="low">Conservative (₱2.50/km)</option>
                            <option value="mid" selected>Standard (₱3.00/km)</option>
                            <option value="high">Premium (₱3.50/km)</option>
                        </select>
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
    <div class="card border-0 bg-white shadow-sm">
        <div class="card-body">
            @if(isset($routes) && $routes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Regular Fare</th>
                            <th>Aircon Fare</th>
                            <th>Duration</th>
                            <th>Distance</th>
                            <th>Status</th>
                            <th width="140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($routes as $route)
                        <tr>
                            <td class="fw-bold">{{ $route->code }}</td>
                            <td>{{ $route->name }}</td>
                            <td>{{ $route->start_location }}</td>
                            <td>{{ $route->end_location }}</td>
                            <td>₱{{ number_format($route->regular_price, 2) }}</td>
                            <td>₱{{ number_format($route->aircon_price ?? 0, 2) }}</td>
                            <td>{{ $route->estimated_duration ?? '-' }} mins</td>
                            <td>{{ $route->distance_km ?? '-' }} km</td>
                            <td>
                                @if($route->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-info" onclick="viewRoute({{ $route->id }})" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editRoute({{ $route->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRoute({{ $route->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Bootstrap Pagination -->
            @if($routes->hasPages())
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">
                        Showing {{ $routes->firstItem() }} to {{ $routes->lastItem() }} of {{ $routes->total() }} results
                    </p>
                </div>
                <nav aria-label="Routes pagination">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if ($routes->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $routes->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($routes->getUrlRange(1, $routes->lastPage()) as $page => $url)
                            @if ($page == $routes->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}&{{ http_build_query(request()->except('page')) }}">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($routes->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $routes->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-route fa-3x text-muted mb-3"></i>
                <h4>No routes found</h4>
                @if(request()->hasAny(['search', 'status']))
                    <p class="text-muted">No routes match your search criteria.</p>
                    <a href="{{ route('routes.panel') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> View All Routes
                    </a>
                @else
                    <p class="text-muted">Add your first route using the button above.</p>
                @endif
            </div>
            @endif
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
@endsection

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
<script>
    mapboxgl.accessToken = 'pk.eyJ1Ijoic2Vlam83IiwiYSI6ImNtY3ZqcWJ1czBic3QycHEycnM0d2xtaXEifQ.DdQ8QFpf5LlgTDtejDgJSA';
</script>
@vite('resources/js/panels/routes.js')
@endpush