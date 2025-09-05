@extends('layouts.app')

@section('title', 'Bus Schedule')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-calendar-alt me-3 text-primary fs-4"></i>
            <h2 class="mb-0 fw-bold">Schedule Management</h2>
        </div>
        <!-- Toggle Button for Route Assignment -->
        <button type="button" class="btn btn-sm btn-outline-primary ms-2 active" id="toggleScheduleFormBtn">
            <i class="fas fa-plus me-2"></i>Create New Schedule
        </button>
    </div>

    <!-- Route Assignment Section - Initially Hidden -->
    <div class="card border-0 mb-4 shadow-sm" id="scheduleFormCard" style="display: none;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-route me-2"></i>Assign Route to Driver</h5>
            <button type="button" class="btn btn-sm btn-outline-light" id="hideScheduleFormBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="card-body">
            <form id="scheduleForm" method="POST" action="{{ route('schedule.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="route" class="form-label fw-bold">Select Route <span class="text-danger">*</span></label>
                        <select id="route" name="route_id" class="form-select" required>
                            <option value="">-- Choose Route --</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" 
                                        data-start="{{ $route->start_location }}"
                                        data-end="{{ $route->end_location }}"
                                        data-regular="{{ $route->regular_price }}"
                                        data-aircon="{{ $route->aircon_price }}"
                                        data-duration="{{ $route->estimated_duration }}">
                                    {{ $route->code }} - {{ $route->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="route_id_error"></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="driver" class="form-label fw-bold">Select Driver <span class="text-danger">*</span></label>
                        <select id="driver" name="driver_id" class="form-select" required>
                            <option value="">-- Choose Driver --</option>
                            @if(isset($drivers) && $drivers->count() > 0)
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->status }})</option>
                                @endforeach
                            @else
                                <option disabled>No active drivers available</option>
                            @endif
                        </select>
                        <div class="invalid-feedback" id="driver_id_error"></div>
                        <!-- DEBUG INFO -->
                        @if(config('app.debug'))
                            <small class="text-muted">
                                Debug: {{ isset($drivers) ? $drivers->count() : 'No drivers variable' }} drivers found
                            </small>
                        @endif
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="bus" class="form-label fw-bold">Select Bus <span class="text-danger">*</span></label>
                        <select id="bus" name="bus_id" class="form-select" required>
                            <option value="">-- Choose Bus --</option>
                            @foreach($buses ?? [] as $bus)
                                <option value="{{ $bus->id }}" data-type="{{ $bus->model }}">
                                    {{ $bus->bus_number }} - {{ $bus->model }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="bus_id_error"></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date" class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                        <input type="date" id="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                        <div class="invalid-feedback" id="date_error"></div>
                    </div>
                </div>
                
                <!-- Route Details Display -->
                <div id="route-details" class="mb-4" style="display: none;">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Route Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-route text-primary mb-2"></i>
                                        <div class="small text-muted">Route Path</div>
                                        <div class="fw-bold" id="route-path">-</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-peso-sign text-success mb-2"></i>
                                        <div class="small text-muted">Regular Price</div>
                                        <div class="fw-bold">₱<span id="regular-price">0.00</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-snowflake text-info mb-2"></i>
                                        <div class="small text-muted">Aircon Price</div>
                                        <div class="fw-bold">₱<span id="aircon-price">0.00</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-clock text-warning mb-2"></i>
                                        <div class="small text-muted">Duration</div>
                                        <div class="fw-bold"><span id="duration">-</span> mins</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-bus text-secondary mb-2"></i>
                                        <div class="small text-muted">Bus Type</div>
                                        <div class="fw-bold" id="bus-type">-</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-success text-white rounded">
                                        <i class="fas fa-ticket-alt mb-2"></i>
                                        <div class="small">Final Fare</div>
                                        <div class="h5 mb-0">₱<span id="final-fare">0.00</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                        <input type="time" id="start_time" name="start_time" class="form-control" required>
                        <div class="invalid-feedback" id="start_time_error"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label fw-bold">End Time <span class="text-danger">*</span></label>
                        <input type="time" id="end_time" name="end_time" class="form-control" required>
                        <div class="invalid-feedback" id="end_time_error"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (Optional)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Additional notes for this schedule..."></textarea>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="button" class="btn btn-outline-secondary me-md-2" id="resetFormBtn">
                        <i class="fas fa-undo me-2"></i>Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-check me-2"></i><span id="submitText">Create Schedule</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Management -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Current Schedules</h5>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('schedule.panel') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="filterDriver" class="form-label">Filter by Driver</label>
                    <select id="filterDriver" name="driver" class="form-select">
                        <option value="">All Drivers</option>
                        @foreach($drivers ?? [] as $driver)
                            <option value="{{ $driver->id }}" {{ request('driver') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterRoute" class="form-label">Filter by Route</label>
                    <select id="filterRoute" name="route" class="form-select">
                        <option value="">All Routes</option>
                        @foreach($routes ?? [] as $route)
                            <option value="{{ $route->id }}" {{ request('route') == $route->id ? 'selected' : '' }}>{{ $route->name }} ({{ $route->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterDate" class="form-label">Filter by Date</label>
                    <input type="date" id="filterDate" name="date" class="form-control" 
                           value="{{ request('date') ? \Carbon\Carbon::parse(request('date'))->format('Y-m-d') : '' }}"
                           data-placeholder="dd/mm/yyyy">
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Filter by Status</label>
                    <select id="filterStatus" name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i> Clear Filters
                    </button>
                </div>
            </form>

            <!-- Schedule Table -->
            @if($schedules->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Driver</th>
                            <th>Bus</th>
                            <th>Route</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                        <tr data-schedule-id="{{ $schedule->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    {{ $schedule->driver->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-bus text-info"></i>
                                    </div>
                                    {{ $schedule->bus->bus_number ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-route text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $schedule->route->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $schedule->route->code ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</span>
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($schedule->date)->format('l') }}</small>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</div>
                                    <div class="text-muted">to</div>
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</div>
                                </div>
                            </td>
                            <td>
                                @switch($schedule->status)
                                    @case('active')
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-play me-1"></i>Active
                                        </span>
                                        @break
                                    @case('scheduled')
                                        <span class="badge bg-primary fs-6">
                                            <i class="fas fa-clock me-1"></i>Scheduled
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-secondary fs-6">
                                            <i class="fas fa-check me-1"></i>Completed
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-times me-1"></i>Cancelled
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary fs-6">{{ ucfirst($schedule->status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-info" onclick="viewSchedule({{ $schedule->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editSchedule({{ $schedule->id }})" title="Edit Schedule">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule({{ $schedule->id }})" title="Delete Schedule">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            @if($schedules->hasPages())
            <div class="row mt-4">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Showing {{ $schedules->firstItem() }} to {{ $schedules->lastItem() }} of {{ $schedules->total() }} schedules
                    </p>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Schedules pagination" class="d-flex justify-content-end">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if ($schedules->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $schedules->appends(request()->query())->previousPageUrl() }}">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            @endif

                            {{-- First Page --}}
                            @if($schedules->currentPage() > 3)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $schedules->appends(request()->query())->url(1) }}">1</a>
                                </li>
                                @if($schedules->currentPage() > 4)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                            @endif

                            {{-- Page Numbers --}}
                            @foreach(range(max(1, $schedules->currentPage() - 2), min($schedules->lastPage(), $schedules->currentPage() + 2)) as $page)
                                @if ($page == $schedules->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $schedules->appends(request()->query())->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Last Page --}}
                            @if($schedules->currentPage() < $schedules->lastPage() - 2)
                                @if($schedules->currentPage() < $schedules->lastPage() - 3)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $schedules->appends(request()->query())->url($schedules->lastPage()) }}">{{ $schedules->lastPage() }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($schedules->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $schedules->appends(request()->query())->nextPageUrl() }}">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4>No schedules found</h4>
                @if(request()->hasAny(['driver', 'route', 'date', 'status']))
                    <p class="text-muted">No schedules match your filter criteria.</p>
                    <button type="button" class="btn btn-outline-primary" onclick="clearFilters()">
                        <i class="fas fa-arrow-left me-1"></i> View All Schedules
                    </button>
                @else
                    <p class="text-muted">Create your first schedule using the button above</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- View Schedule Modal - CENTERED -->
<div class="modal fade" id="viewScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Schedule Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewScheduleContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal - CENTERED -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Schedule
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editScheduleForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_schedule_id" name="schedule_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_route_id" class="form-label">Route <span class="text-danger">*</span></label>
                            <select id="edit_route_id" name="route_id" class="form-select" required>
                                <option value="">-- Select Route --</option>
                                @foreach($routes ?? [] as $route)
                                    <option value="{{ $route->id }}">{{ $route->name }} ({{ $route->code }})</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="edit_route_id_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_bus_id" class="form-label">Bus <span class="text-danger">*</span></label>
                            <select id="edit_bus_id" name="bus_id" class="form-select" required>
                                <option value="">-- Select Bus --</option>
                                @foreach($buses ?? [] as $bus)
                                    <option value="{{ $bus->id }}">{{ $bus->bus_number }} ({{ $bus->model }})</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="edit_bus_id_error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_driver_id" class="form-label">Driver <span class="text-danger">*</span></label>
                            <select id="edit_driver_id" name="driver_id" class="form-select" required>
                                <option value="">-- Select Driver --</option>
                                @foreach($drivers ?? [] as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="edit_driver_id_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="scheduled">Scheduled</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <div class="invalid-feedback" id="edit_status_error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="edit_date" name="date" class="form-control" required>
                            <div class="invalid-feedback" id="edit_date_error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" id="edit_start_time" name="start_time" class="form-control" required>
                            <div class="invalid-feedback" id="edit_start_time_error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" id="edit_end_time" name="end_time" class="form-control" required>
                            <div class="invalid-feedback" id="edit_end_time_error"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea id="edit_notes" name="notes" class="form-control" rows="3" placeholder="Additional notes for this schedule..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveScheduleBtn">
                    <i class="fas fa-save me-2"></i><span id="saveScheduleText">Save Changes</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/panels/schedule.js')
@endpush