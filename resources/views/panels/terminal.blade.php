@extends('layouts.app')

@section('content')
<div class="container-fluid p-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-building me-2 text-primary fs-4"></i>
            <h2 class="mb-0 fw-bold">North Bus Terminal Layout</h2>
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
                        <span class="badge bg-danger">&nbsp;</span><small class="ms-2 me-3">Reserved</small>
                        <span class="badge bg-primary">&nbsp;</span><small class="ms-2 me-3">Selected</small>
                        <span class="badge bg-warning">&nbsp;</span><small class="ms-2 me-3">Boarding Gates</small>
                        <span class="badge bg-info">&nbsp;</span><small class="ms-2">Parking Spaces</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terminal Layout -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-secondary">
                <div class="card-body p-3">
                    <!-- Top Boarding Gates (Portrait, now 6, closer gap, increased height) -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-center flex-wrap" style="gap: 12px;">
                                @for($i = 1; $i <= 6; $i++)
                                <div>
                                    <div class="card border-warning text-center top-gate-space gate-card"
                                         style="width:80px; height:140px; cursor:pointer; transition:box-shadow 0.2s;"
                                         data-space-id="T{{ $i }}"
                                         data-type="top_boarding">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center p-2 h-100">
                                            <div class="fw-bold text-dark mb-1">T{{ $i }}</div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary change-route-btn mt-1"
                                                    data-gate="T{{ $i }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#changeRouteModal">
                                                Change
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Main Parking Area -->
                    <div class="row">
                        <!-- Left Boarding Gates (Rectangle, label left, button right, fixed padding) -->
                        <div class="col-md-2">
                            <div class="row row-cols-1 g-2">
                                @for($i = 1; $i <= 6; $i++)
                                <div class="col d-flex justify-content-end">
                                    <div class="card border-warning left-gate-space gate-card"
                                         style="width:160px; height:60px; cursor:pointer; transition:box-shadow 0.2s; overflow:hidden;"
                                         data-space-id="L{{ $i }}"
                                         data-type="loading">
                                        <div class="card-body d-flex flex-row align-items-center justify-content-between px-2 py-2 h-100">
                                            <div class="d-flex flex-column align-items-start flex-grow-1" style="min-width:0;">
                                                <div class="fw-bold text-dark mb-0 text-truncate" id="gate-label-L{{ $i }}" style="max-width:100%;">L{{ $i }}</div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary change-route-btn ms-2 flex-shrink-0"
                                                    data-gate="L{{ $i }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#changeRouteModal">
                                                Change
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Center Parking Area -->
                        <div class="col-md-8">
                            <div class="card bg-light border-secondary h-100">
                                <div class="card-header text-center py-2 bg-secondary text-white">
                                    <h5 class="mb-0">CENTRAL PARKING AREA</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h6 class="text-muted">Parking Spaces</h6>
                                    </div>
                                    <div class="row row-cols-2 row-cols-md-3 g-2 justify-content-center">
                                        @for($i = 1; $i <= 12; $i++)
                                        <div class="col d-flex justify-content-center">
                                            <div class="card border-info parking-space gate-card"
                                                 style="width:160px; height:60px; cursor:pointer; transition:box-shadow 0.2s;"
                                                 data-space-id="P{{ $i }}"
                                                 data-type="parking"
                                                 data-location="center">
                                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-2 h-100">
                                                    <div class="fw-bold mb-1">P{{ $i }}</div>
                                                    <small class="text-success fw-bold space-status">Available</small>
                                                </div>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Boarding Gates (Rectangle, label left, button right, fixed padding) -->
                        <div class="col-md-2">
                            <div class="row row-cols-1 g-2">
                                @for($i = 1; $i <= 6; $i++)
                                <div class="col d-flex justify-content-start">
                                    <div class="card border-warning right-gate-space gate-card"
                                         style="width:160px; height:60px; cursor:pointer; transition:box-shadow 0.2s; overflow:hidden;"
                                         data-space-id="R{{ $i }}"
                                         data-type="loading">
                                        <div class="card-body d-flex flex-row align-items-center justify-content-between px-2 py-2 h-100">
                                            <div class="d-flex flex-column align-items-start flex-grow-1" style="min-width:0;">
                                                <div class="fw-bold text-dark mb-0 text-truncate" id="gate-label-R{{ $i }}" style="max-width:100%;">R{{ $i }}</div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary change-route-btn ms-2 flex-shrink-0"
                                                    data-gate="R{{ $i }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#changeRouteModal">
                                                Change
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Bookings Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Today's Reservations</h5>
                </div>
                <div class="card-body py-3">
                    <div id="bookings-summary">
                        <p class="text-muted text-center mb-0">Loading reservations...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Route Modal -->
<div class="modal fade" id="changeRouteModal" tabindex="-1" aria-labelledby="changeRouteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="changeRouteForm">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="changeRouteModalLabel">
                        <i class="fas fa-route me-1"></i> Change Route: <span id="modal-gate-name"></span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <input type="hidden" id="selectedGateId" name="gate_id">
                    <div class="mb-2">
                        <label for="gate_route_id" class="form-label small">Select Route</label>
                        <select class="form-select form-select-sm" id="gate_route_id" name="route_id" required>
                            <option value="">Select Route</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->code }} - {{ $route->start_location }} to {{ $route->end_location }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-check me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Gate Reservation Modal -->
<div class="modal fade" id="gateReservationModal" tabindex="-1" aria-labelledby="gateReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="gateReservationForm">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="gateReservationModalLabel">
                        <i class="fas fa-bus me-1"></i> Reserve Gate: <span id="modal-reserve-gate-name"></span> <span id="modal-reserve-gate-route" class="ms-2 text-primary"></span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <input type="hidden" id="reserveGateId" name="gate_id">
                    <div class="mb-2">
                        <label for="reserve_driver_id" class="form-label small">Driver <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="reserve_driver_id" name="driver_id" required>
                            <option value="">Select Driver</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="reserve_bus_id" class="form-label small">Bus <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="reserve_bus_id" name="bus_id" required>
                            <option value="">Select Bus</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}">{{ $bus->plate_number }} ({{ $bus->capacity }} seats)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="reserve_minutes" class="form-label small">Time Limit (minutes)</label>
                        <select class="form-select form-select-sm" id="reserve_minutes" name="minutes" required>
                            <option value="15">15 minutes</option>
                            <option value="30">30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-check me-1"></i> Reserve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/panels/terminal.js')
@endpush