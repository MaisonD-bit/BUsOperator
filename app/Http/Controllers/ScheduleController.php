<?php
// filepath: c:\Users\User\Desktop\Laravel BusOp\BusOperator\app\Http\Controllers\ScheduleController.php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Driver;
use App\Models\Route;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function schedulePanel()
    {
        try {
            $routes = Route::all();
            $buses = Bus::all();
            $drivers = Driver::all();

            // Start query builder
            $query = Schedule::with(['route', 'bus', 'driver']);

            // Apply filters if present
            if (request()->filled('driver')) {
                $query->where('driver_id', request('driver'));
            }
            if (request()->filled('route')) {
                $query->where('route_id', request('route'));
            }
            if (request()->filled('date')) {
                $query->whereDate('date', request('date'));
            }
            if (request()->filled('status')) {
                $query->where('status', request('status'));
            }

            $schedules = $query->orderBy('date', 'desc')->paginate(10);

            return view('panels.schedule', compact('routes', 'buses', 'drivers', 'schedules'));

        } catch (\Exception $e) {
            Log::error("Error loading schedule panel: " . $e->getMessage());

            return view('panels.schedule', [
                'routes' => collect(),
                'buses' => collect(),
                'drivers' => collect(),
                'schedules' => collect(),
                'error' => 'Error loading schedule data'
            ]);
        }
    }
    
    /**
     * Store a new schedule (WEB)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'driver_id' => 'required|exists:drivers,id',
                'route_id' => 'required|exists:routes,id',
                'bus_id' => 'required|exists:buses,id',
                'date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'status' => 'required|in:scheduled,active,completed,cancelled',
                'notes' => 'nullable|string|max:500'
            ]);
            
            // Get route details for fare calculation
            $route = Route::find($validated['route_id']);
            $bus = Bus::find($validated['bus_id']);
            
            // Calculate fare based on bus type
            $fare_regular = $route->regular_price ?? 0;
            $fare_aircon = $route->aircon_price ?? $fare_regular;
            
            $isAircon = $bus && $bus->accommodation_type === 'air-conditioned';
            
            $schedule = Schedule::create([
                'driver_id' => $validated['driver_id'],
                'route_id' => $validated['route_id'],
                'bus_id' => $validated['bus_id'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'status' => 'scheduled', // <-- force status to 'scheduled'
                'fare_regular' => $fare_regular,
                'fare_aircon' => $fare_aircon,
                'notes' => $validated['notes']
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Schedule created successfully',
                    'schedule' => $schedule->load(['driver', 'route', 'bus'])
                ], 201);
            }
            
            return redirect()->route('schedule.panel')->with('success', 'Schedule created successfully');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error("Error creating schedule: " . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating schedule: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error creating schedule')->withInput();
        }
    }
    
    /**
     * Display a specific schedule (WEB)
     */
    public function show($id)
    {
        try {
            $schedule = Schedule::with(['driver', 'route', 'bus'])->findOrFail($id);
            return response()->json($schedule);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
            ], 404);
        }
    }
    
    /**
     * Update a schedule (WEB)
     */
    public function update(Request $request, $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            
            $validated = $request->validate([
                'driver_id' => 'required|exists:drivers,id',
                'route_id' => 'required|exists:routes,id',
                'bus_id' => 'required|exists:buses,id',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'status' => 'required|in:scheduled,active,completed,cancelled,accepted,declined',
                'notes' => 'nullable|string|max:500'
            ]);
            
            $schedule->update($validated);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Schedule updated successfully',
                    'schedule' => $schedule->load(['driver', 'route', 'bus'])
                ]);
            }
            
            return redirect()->route('schedule.panel')->with('success', 'Schedule updated successfully');
            
        } catch (\Exception $e) {
            Log::error("Error updating schedule: " . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating schedule: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating schedule');
        }
    }
    
    /**
     * Delete a schedule (WEB)
     */
    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error deleting schedule: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===================================
    // API METHODS (return JSON responses for mobile app)
    // ===================================

    /**
     * Get all schedules (API - admin view)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Schedule::with(['route', 'bus', 'driver']);
            
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('date')) {
                $query->whereDate('date', $request->date);
            }
            
            if ($request->has('driver_id')) {
                $query->where('driver_id', $request->driver_id);
            }
            
            $perPage = $request->get('per_page', 15);
            $schedules = $query->orderBy('date', 'desc')
                             ->orderBy('start_time', 'asc')
                             ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'schedules' => $schedules
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error fetching schedules: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching schedules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver schedules for mobile app (API)
     */
    public function getDriverSchedules($driverId): JsonResponse
    {
        try {
            $driver = Driver::find($driverId);
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            $today = Carbon::now()->format('Y-m-d');

            // Get all schedules for this driver (including scheduled, accepted, active, completed)
            $allSchedules = Schedule::with(['route', 'bus'])
                ->where('driver_id', $driverId)
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            // Categorize schedules
            $todaySchedules = $allSchedules->filter(function($schedule) use ($today) {
                return $schedule->date === $today;
            })->values();

            $upcomingSchedules = $allSchedules->filter(function($schedule) use ($today) {
                return $schedule->date > $today;
            })->values();

            return response()->json([
                'success' => true,
                'driver' => [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'email' => $driver->email
                ],
                'schedules' => [
                    'today' => $todaySchedules,
                    'upcoming' => $upcomingSchedules,
                    'all' => $allSchedules->values()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching schedules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept a schedule (API)
     */
    public function acceptSchedule(Request $request, $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);

            // Ensure the driver is assigned to this schedule
            // if ($schedule->driver_id !== auth()->id()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'You are not authorized to accept this schedule.'
            //     ], 403);
            // }

            // Update the schedule status to 'accepted'
            $schedule->status = 'accepted';
            $schedule->save();

            return response()->json([
                'success' => true,
                'message' => 'Schedule accepted successfully.',
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept schedule.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function declineSchedule($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->status = 'declined';
            $schedule->save();

            return response()->json([
                'success' => true,
                'message' => 'Schedule declined successfully',
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to decline schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start a schedule (API)
     */
    public function startSchedule($scheduleId): JsonResponse
    {
        try {
            Log::info("Attempting to start schedule ID: {$scheduleId}");

            $schedule = Schedule::find($scheduleId);

            if (!$schedule) {
                Log::error("Schedule not found: $scheduleId");
                return response()->json(['error' => 'Schedule not found'], 404);
            }

            $schedule = Schedule::with(['route', 'bus', 'driver'])->find($scheduleId);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found'
                ], 404);
            }

            if ($schedule->status !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot start schedule. Current status: {$schedule->status}. Schedule must be accepted first."
                ], 400);
            }

            // $today = Carbon::now()->format('Y-m-d');
            // if (trim($schedule->date) != trim($today)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Schedule can only be started on the scheduled date'
            //     ], 400);
            // }

            $schedule->status = 'active';
            $schedule->started_at = Carbon::now();
            $schedule->save();

            Log::info("Schedule {$scheduleId} started by driver {$schedule->driver_id}");

            return response()->json([
                'success' => true,
                'message' => 'Trip started successfully',
                'schedule' => $schedule,
                'action' => 'started'
            ]);

        } catch (\Exception $e) {
            Log::error("Error starting schedule {$scheduleId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error starting schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete a schedule (API)
     */
    public function completeSchedule($scheduleId): JsonResponse
    {
        try {
            Log::info("Attempting to complete schedule ID: {$scheduleId}");

            $schedule = Schedule::with(['route', 'bus', 'driver'])->find($scheduleId);
            
            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found'
                ], 404);
            }

            Log::info("DEBUG: Schedule date={$schedule->date}, Today=" . Carbon::now()->format('Y-m-d'));

            if ($schedule->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot complete schedule. Current status: {$schedule->status}. Schedule must be active."
                ], 400);
            }

            $schedule->status = 'completed';
            $schedule->completed_at = Carbon::now();
            $schedule->save();

            Log::info("Schedule {$scheduleId} completed by driver {$schedule->driver_id}");

            return response()->json([
                'success' => true,
                'message' => 'Trip completed successfully',
                'schedule' => $schedule,
                'action' => 'completed'
            ]);

        } catch (\Exception $e) {
            Log::error("Error completing schedule {$scheduleId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error completing schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign schedule to driver (API - for admin use)
     */
    public function assignToDriver(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'driver_id' => 'required|exists:drivers,id',
                'route_id' => 'required|exists:routes,id',
                'bus_id' => 'required|exists:buses,id',
                'date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'fare_regular' => 'required|numeric|min:0',
                'fare_aircon' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:500'
            ]);

            $schedule = Schedule::create([
                'driver_id' => $request->driver_id,
                'route_id' => $request->route_id,
                'bus_id' => $request->bus_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'scheduled',
                'fare_regular' => $request->fare_regular,
                'fare_aircon' => $request->fare_aircon ?? $request->fare_regular,
                'notes' => $request->notes
            ]);

            Log::info("New schedule created and assigned to driver {$request->driver_id}");

            return response()->json([
                'success' => true,
                'message' => 'Schedule assigned successfully',
                'schedule' => $schedule->load(['route', 'bus', 'driver'])
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error assigning schedule: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error assigning schedule: ' . $e->getMessage()
            ], 500);
        }
    }
}