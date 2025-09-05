<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Driver;
use App\Models\Bus;
use App\Models\Route as BusRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display schedule panel
     */
    public function index()
    {
        $schedules = Schedule::with(['driver', 'bus', 'route'])
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(15);

        $stats = [
            'total_schedules' => Schedule::count(),
            'today_schedules' => Schedule::whereDate('date', today())->count(),
            'active_schedules' => Schedule::where('status', 'active')->count(),
            'completed_schedules' => Schedule::where('status', 'completed')->count(),
            'pending_schedules' => Schedule::where('status', 'scheduled')->count(),
        ];

        $drivers = Driver::where('status', 'active')->get(['id', 'name', 'email']);
        $buses = Bus::where('status', 'available')->get(['id', 'bus_number', 'plate_number']);
        // FIXED: Use correct field names from your database
        $routes = BusRoute::all(['id', 'name', 'start_location', 'end_location']);

        return view('panels.schedule', compact('schedules', 'stats', 'drivers', 'buses', 'routes'));
    }

    /**
     * Store a new schedule
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'fare_regular' => 'required|numeric|min:0',
            'fare_aircon' => 'required|numeric|min:0',
            'status' => 'string|in:scheduled,active,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for conflicts
            $conflicts = $this->checkScheduleConflicts(
                $request->driver_id,
                $request->bus_id,
                $request->date,
                $request->start_time,
                $request->end_time
            );

            if (!empty($conflicts)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule conflicts detected',
                    'conflicts' => $conflicts
                ], 409);
            }

            $schedule = Schedule::create([
                'driver_id' => $request->driver_id,
                'bus_id' => $request->bus_id,
                'route_id' => $request->route_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'fare_regular' => $request->fare_regular,
                'fare_aircon' => $request->fare_aircon,
                'status' => $request->get('status', 'scheduled')
            ]);

            $schedule->load(['driver', 'bus', 'route']);

            Log::info('Schedule created', [
                'schedule_id' => $schedule->id,
                'driver_id' => $schedule->driver_id,
                'date' => $schedule->date
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Schedule created successfully',
                'schedule' => $schedule
            ], 201);

        } catch (\Exception $e) {
            Log::error('Schedule creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create schedule'
            ], 500);
        }
    }

    /**
     * Show schedule details
     */
    public function show($id)
    {
        try {
            $schedule = Schedule::with(['driver', 'bus', 'route'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'schedule' => [
                    'id' => $schedule->id,
                    'date' => $schedule->date,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'status' => $schedule->status,
                    'fare_regular' => $schedule->fare_regular,
                    'fare_aircon' => $schedule->fare_aircon,
                    'driver' => [
                        'id' => $schedule->driver->id,
                        'name' => $schedule->driver->name,
                        'email' => $schedule->driver->email,
                        'contact_number' => $schedule->driver->contact_number
                    ],
                    'bus' => [
                        'id' => $schedule->bus->id,
                        'bus_number' => $schedule->bus->bus_number,
                        'plate_number' => $schedule->bus->plate_number,
                        'capacity' => $schedule->bus->capacity
                    ],
                    'route' => [
                        'id' => $schedule->route->id,
                        'name' => $schedule->route->name,
                        // FIXED: Use correct field names
                        'start_location' => $schedule->route->start_location,
                        'end_location' => $schedule->route->end_location,
                        'distance' => $schedule->route->distance_km
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
            ], 404);
        }
    }

    /**
     * Update schedule
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'driver_id' => 'exists:drivers,id',
            'bus_id' => 'exists:buses,id',
            'route_id' => 'exists:routes,id',
            'date' => 'date|after_or_equal:today',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'fare_regular' => 'numeric|min:0',
            'fare_aircon' => 'numeric|min:0',
            'status' => 'string|in:scheduled,active,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for conflicts if time/date/driver/bus changed
            if ($request->has(['driver_id', 'bus_id', 'date', 'start_time', 'end_time'])) {
                $conflicts = $this->checkScheduleConflicts(
                    $request->get('driver_id', $schedule->driver_id),
                    $request->get('bus_id', $schedule->bus_id),
                    $request->get('date', $schedule->date),
                    $request->get('start_time', $schedule->start_time),
                    $request->get('end_time', $schedule->end_time),
                    $id // Exclude current schedule
                );

                if (!empty($conflicts)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Schedule conflicts detected',
                        'conflicts' => $conflicts
                    ], 409);
                }
            }

            $schedule->update($request->only([
                'driver_id', 'bus_id', 'route_id', 'date', 'start_time', 
                'end_time', 'fare_regular', 'fare_aircon', 'status'
            ]));

            $schedule->load(['driver', 'bus', 'route']);

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully',
                'schedule' => $schedule
            ]);

        } catch (\Exception $e) {
            Log::error('Schedule update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule'
            ], 500);
        }
    }

    /**
     * Delete schedule
     */
    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);

            // Don't allow deletion of active schedules
            if ($schedule->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete active schedule'
                ], 400);
            }

            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Schedule deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete schedule'
            ], 500);
        }
    }

    /**
     * Get available drivers for scheduling
     */
    public function getAvailableDrivers(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');
        $excludeScheduleId = $request->get('exclude_schedule_id');

        $query = Driver::where('status', 'active')
            ->whereDoesntHave('schedules', function ($scheduleQuery) use ($date, $startTime, $endTime, $excludeScheduleId) {
                $scheduleQuery->where('date', $date)
                      ->where(function ($timeQuery) use ($startTime, $endTime) {
                          $timeQuery->whereBetween('start_time', [$startTime, $endTime])
                                   ->orWhereBetween('end_time', [$startTime, $endTime])
                                   ->orWhere(function ($overlapQuery) use ($startTime, $endTime) {
                                       $overlapQuery->where('start_time', '<=', $startTime)
                                                   ->where('end_time', '>=', $endTime);
                                   });
                      });
                
                if ($excludeScheduleId) {
                    $scheduleQuery->where('id', '!=', $excludeScheduleId);
                }
            });

        $availableDrivers = $query->select('id', 'name', 'email', 'contact_number', 'license_number')->get();

        return response()->json([
            'success' => true,
            'drivers' => $availableDrivers
        ]);
    }

    /**
     * Get active schedules
     */
    public function getActiveSchedules()
    {
        $activeSchedules = Schedule::with(['driver', 'bus', 'route'])
            ->where('status', 'active')
            ->where('date', now()->toDateString())
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'schedules' => $activeSchedules
        ]);
    }

    /**
     * Get today's schedule for a specific driver
     */
    public function getTodayScheduleForDriver($driverId)
    {
        $todaySchedule = Schedule::with(['bus', 'route'])
            ->where('driver_id', $driverId)
            ->where('date', now()->toDateString())
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'schedules' => $todaySchedule
        ]);
    }

    // ===== MOBILE APP API METHODS =====

    /**
     * Assign schedule to driver (from web admin)
     */
    public function assignToDriver(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Get driver schedules for mobile app
     */
    public function getDriverSchedules($driverId)
    {
        try {
            $driver = Driver::find($driverId);
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            $schedules = Schedule::with(['route', 'bus'])
                ->where('driver_id', $driverId)
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            return response()->json([
                'success' => true,
                'schedules' => $schedules->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'date' => $schedule->date,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'status' => $schedule->status,
                        'fare_regular' => $schedule->fare_regular,
                        'fare_aircon' => $schedule->fare_aircon,
                        'route' => [
                            'id' => $schedule->route->id,
                            'name' => $schedule->route->name,
                            // FIXED: Use correct field names
                            'start_location' => $schedule->route->start_location,
                            'end_location' => $schedule->route->end_location,
                        ],
                        'bus' => [
                            'id' => $schedule->bus->id,
                            'bus_number' => $schedule->bus->bus_number,
                            'plate_number' => $schedule->bus->plate_number,
                        ]
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get driver schedules error', [
                'driver_id' => $driverId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load schedules'
            ], 500);
        }
    }

    /**
     * Accept schedule from mobile app
     */
    public function acceptSchedule($scheduleId)
    {
        try {
            $schedule = Schedule::find($scheduleId);
            
            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found'
                ], 404);
            }

            $schedule->update(['status' => 'accepted']);

            Log::info('Schedule accepted from mobile app', [
                'schedule_id' => $scheduleId,
                'driver_id' => $schedule->driver_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Schedule accepted successfully',
                'schedule' => [
                    'id' => $schedule->id,
                    'status' => $schedule->status,
                    'date' => $schedule->date,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Accept schedule error', [
                'schedule_id' => $scheduleId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to accept schedule'
            ], 500);
        }
    }

    /**
     * Decline schedule from mobile app
     */
    public function declineSchedule($scheduleId)
    {
        try {
            $schedule = Schedule::find($scheduleId);
            
            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found'
                ], 404);
            }

            $schedule->update(['status' => 'declined']);

            Log::info('Schedule declined from mobile app', [
                'schedule_id' => $scheduleId,
                'driver_id' => $schedule->driver_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Schedule declined',
                'schedule' => [
                    'id' => $schedule->id,
                    'status' => $schedule->status,
                    'date' => $schedule->date,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Decline schedule error', [
                'schedule_id' => $scheduleId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to decline schedule'
            ], 500);
        }
    }

    // ===== HELPER METHODS =====

    /**
     * Check for schedule conflicts
     */
    private function checkScheduleConflicts($driverId, $busId, $date, $startTime, $endTime, $excludeScheduleId = null)
    {
        $conflicts = [];

        // Check driver conflicts
        $driverConflicts = Schedule::where('driver_id', $driverId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($overlapQuery) use ($startTime, $endTime) {
                          $overlapQuery->where('start_time', '<=', $startTime)
                                      ->where('end_time', '>=', $endTime);
                      });
            })
            ->when($excludeScheduleId, function ($query) use ($excludeScheduleId) {
                $query->where('id', '!=', $excludeScheduleId);
            })
            ->exists();

        if ($driverConflicts) {
            $conflicts[] = 'Driver is already scheduled for this time slot';
        }

        // Check bus conflicts
        $busConflicts = Schedule::where('bus_id', $busId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($overlapQuery) use ($startTime, $endTime) {
                          $overlapQuery->where('start_time', '<=', $startTime)
                                      ->where('end_time', '>=', $endTime);
                      });
            })
            ->when($excludeScheduleId, function ($query) use ($excludeScheduleId) {
                $query->where('id', '!=', $excludeScheduleId);
            })
            ->exists();

        if ($busConflicts) {
            $conflicts[] = 'Bus is already scheduled for this time slot';
        }

        return $conflicts;
    }
}