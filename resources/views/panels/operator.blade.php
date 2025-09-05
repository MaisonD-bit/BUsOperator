@extends('layouts.app')

@section('title', 'Operator Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-tachometer-alt me-3 text-primary fs-4"></i>
            <h2 class="mb-0 fw-bold">Operator Dashboard</h2>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="quickSchedule()">
                <i class="fas fa-plus me-1"></i> Quick Schedule
            </button>
        </div>
    </div>
    
    <!-- Dashboard Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-primary border-2 bg-white shadow-sm h-100 dashboard-card" onclick="redirectTo('{{ route('routes.index') }}')">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-route fs-1 text-primary"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1">{{ $activeRoutes ?? 0 }}</h2>
                    <p class="text-muted mb-0">Active Routes</p>
                    <small class="text-primary">Click to manage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-success border-2 bg-white shadow-sm h-100 dashboard-card" onclick="redirectTo('{{ route('buses.panel') }}')">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-bus fs-1 text-success"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1">{{ $activeBuses ?? 0 }}</h2>
                    <p class="text-muted mb-0">Buses in Operation</p>
                    <small class="text-success">Click to manage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-info border-2 bg-white shadow-sm h-100 dashboard-card" onclick="redirectTo('{{ route('drivers.panel') }}')">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fs-1 text-info"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1">{{ $activeDrivers ?? 0 }}</h2>
                    <p class="text-muted mb-0">Active Drivers</p>
                    <small class="text-info">Click to manage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-warning border-2 bg-white shadow-sm h-100 dashboard-card" onclick="showIssuesModal()">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-circle fs-1 text-warning"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1">{{ $issues ?? 0 }}</h2>
                    <p class="text-muted mb-0">Issues Reported</p>
                    <small class="text-warning">Click to view</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="mb-1" id="totalSchedulesToday">{{ $todaySchedules ?? 0 }}</h4>
                    <small>Today's Schedules</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="mb-1" id="activeSchedules">{{ $activeSchedules ?? 0 }}</h4>
                    <small>Currently Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 class="mb-1" id="completedSchedules">{{ $completedSchedules ?? 0 }}</h4>
                    <small>Completed Today</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 class="mb-1" id="pendingSchedules">{{ $pendingSchedules ?? 0 }}</h4>
                    <small>Pending Schedules</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Schedules -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Recent Schedules</h5>
            <div class="d-flex gap-2 align-items-center">
                <select id="statusFilter" class="form-select" style="width: 150px; height: 32px; font-size: 0.875rem;">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            @if(isset($recentSchedules) && $recentSchedules->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="schedulesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Route</th>
                            <th>Driver</th>
                            <th>Bus</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSchedules as $schedule)
                        <tr data-status="{{ $schedule->status }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-route text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $schedule->route->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $schedule->route->code ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-user text-info"></i>
                                    </div>
                                    {{ $schedule->driver->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fas fa-bus text-success"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ $schedule->bus->bus_number ?? 'N/A' }}</span>
                                        <br><small class="text-muted">{{ $schedule->bus->model ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</span>
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($schedule->date)->format('l') }}</small>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($recentSchedules->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Showing {{ $recentSchedules->firstItem() }} to {{ $recentSchedules->lastItem() }} 
                        of {{ $recentSchedules->total() }} schedules
                    </small>
                </div>
                <div>
                    {{ $recentSchedules->links('pagination::bootstrap-4') }}
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Recent Schedules</h4>
                <p class="text-muted">Start by creating your first schedule</p>
                <a href="{{ route('schedule.panel') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Create Schedule
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Daily Performance</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-success mb-1">{{ $performanceStats['onTime'] ?? 0 }}%</h4>
                                <small class="text-muted">On Time</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-warning mb-1">{{ $performanceStats['delayed'] ?? 0 }}%</h4>
                                <small class="text-muted">Delayed</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-danger mb-1">{{ $performanceStats['cancelled'] ?? 0 }}%</h4>
                            <small class="text-muted">Cancelled</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Quick Alerts</h6>
                </div>
                <div class="card-body">
                    <div id="alertsList">
                        <div class="alert alert-warning alert-sm py-2 mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small>Bus CEB-123 needs maintenance check</small>
                        </div>
                        <div class="alert alert-info alert-sm py-2 mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Route DT01 has heavy traffic reported</small>
                        </div>
                        <div class="alert alert-success alert-sm py-2 mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            <small>All drivers checked in for today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Issues Modal -->
<div class="modal fade" id="issuesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-circle me-2"></i>Reported Issues
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group" id="issuesList">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Bus Engine Issue</h6>
                            <small>2 hours ago</small>
                        </div>
                        <p class="mb-1">Bus CEB-456 reported engine overheating on Route CB02</p>
                        <small class="text-muted">Driver: Maria Santos</small>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Route Blockage</h6>
                            <small>4 hours ago</small>
                        </div>
                        <p class="mb-1">Construction work blocking main route on DT01</p>
                        <small class="text-muted">Reported by: Traffic Control</small>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Driver Late</h6>
                            <small>6 hours ago</small>
                        </div>
                        <p class="mb-1">Driver Juan dela Cruz was 15 minutes late for shift</p>
                        <small class="text-muted">Status: Resolved</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning">View All Issues</button>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Details Modal -->
<div class="modal fade" id="scheduleDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Schedule Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="scheduleDetailsContent">
                <!-- Content loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editFromModal()">Edit Schedule</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/panels/operator.js')
@endpush