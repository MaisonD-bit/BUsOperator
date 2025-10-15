@extends('layouts.app')

@section('title', 'Bus Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fas fa-bus text-primary me-2"></i>Bus Management</h2>
        <button class="btn btn-sm btn-outline-primary ms-2 active" id="addBusBtn">
            <i class="fas fa-plus me-1"></i> Add New Bus
        </button>
    </div>

    <!-- Search and Filter Section -->
    <div class="card border-0 bg-white shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('buses.panel') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="Search by bus number, plate, or model..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select id="filter_status" name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filter_accommodation" name="accommodation_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="regular" {{ request('accommodation_type') === 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="air-conditioned" {{ request('accommodation_type') === 'air-conditioned' ? 'selected' : '' }}>Air-Conditioned</option>
                        <option value="deluxe" {{ request('accommodation_type') === 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                        <option value="super-deluxe" {{ request('accommodation_type') === 'super-deluxe' ? 'selected' : '' }}>Super Deluxe</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
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
        <div class="card-body">
            @if($buses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bus Number</th>
                            <th>Plate Number</th>
                            <th>Model</th>
                            <th>Capacity</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th width="140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($buses as $bus)
                        <tr>
                            <td class="fw-bold">{{ $bus->bus_number }}</td>
                            <td>{{ $bus->plate_number }}</td>
                            <td>{{ $bus->model }}</td>
                            <td>{{ $bus->capacity }} seats</td>
                            <td>{{ $bus->bus_company ?? '-' }}</td>
                            <td>
                                @if($bus->accommodation_type == 'regular')
                                    <span class="badge bg-secondary">Regular</span>
                                @elseif($bus->accommodation_type == 'air-conditioned')
                                    <span class="badge bg-info">Air-Conditioned</span>
                                @elseif($bus->accommodation_type == 'deluxe')
                                    <span class="badge bg-success">Deluxe</span>
                                @else
                                    <span class="badge bg-warning">Super Deluxe</span>
                                @endif
                            </td>
                            <td>
                                @if($bus->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($bus->status == 'maintenance')
                                    <span class="badge bg-warning">Maintenance</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editBus({{ $bus->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBus({{ $bus->id }})" title="Delete">
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
            @if($buses->hasPages())
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">
                        Showing {{ $buses->firstItem() }} to {{ $buses->lastItem() }} of {{ $buses->total() }} results
                    </p>
                </div>
                <nav aria-label="Buses pagination">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if ($buses->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $buses->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($buses->getUrlRange(1, $buses->lastPage()) as $page => $url)
                            @if ($page == $buses->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}&{{ http_build_query(request()->except('page')) }}">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($buses->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $buses->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-bus fa-3x text-muted mb-3"></i>
                <h4>No buses found</h4>
                @if(request()->hasAny(['search', 'status', 'accommodation_type']))
                    <p class="text-muted">No buses match your search criteria.</p>
                    <a href="{{ route('buses.panel') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> View All Buses
                    </a>
                @else
                    <p class="text-muted">Add your first bus using the button above.</p>
                @endif
            </div>
            @endif
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
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="busForm">
                    @csrf
                    <input type="hidden" id="bus_id" name="bus_id">
                    <input type="hidden" id="method_field" name="_method">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bus_number" class="form-label">Bus Number <span class="text-danger">*</span></label>
                            <input type="text" id="bus_number" name="bus_number" class="form-control" required placeholder="e.g., B-101">
                            <div class="invalid-feedback" id="bus_number_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="plate_number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" id="plate_number" name="plate_number" class="form-control" required placeholder="e.g., ABC-1234">
                            <div class="invalid-feedback" id="plate_number_error"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="model" class="form-label">Bus Model <span class="text-danger">*</span></label>
                            <input type="text" id="model" name="model" class="form-control" required placeholder="e.g., Hino RK1J">
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
                            <input type="text" id="bus_company" name="bus_company" class="form-control" placeholder="e.g., Metro Transit Co.">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="accommodation_type" class="form-label">Accommodation Type <span class="text-danger">*</span></label>
                            <select id="accommodation_type" name="accommodation_type" class="form-select" required>
                                <option value="regular">Regular</option>
                                <option value="air-conditioned">Air-Conditioned</option>
                                <option value="deluxe">Deluxe</option>
                                <option value="super-deluxe">Super Deluxe</option>
                            </select>
                            <small class="text-muted">This affects fare calculation and passenger comfort</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="bus_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select id="bus_status" name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" placeholder="Additional notes about the bus"></textarea>
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
@endsection

@push('scripts')
@vite('resources/js/panels/buses.js')
@endpush