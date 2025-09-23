<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BusController extends Controller
{
    /**
     * Display buses panel
     */
    public function index()
    {
        $buses = Bus::with(['schedules' => function($query) {
            $query->where('date', '>=', now()->toDateString())
                  ->orderBy('date')
                  ->orderBy('start_time');
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        $stats = [
            'total_buses' => Bus::count(),
            'available_buses' => Bus::where('status', 'available')->count(),
            'in_service_buses' => Bus::where('status', 'in_service')->count(),
            'maintenance_buses' => Bus::where('status', 'maintenance')->count(),
        ];

        return view('panels.buses', compact('buses', 'stats'));
    }

    /**
     * Store a new bus
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bus_number' => 'required|string|max:20|unique:buses',
            'plate_number' => 'required|string|max:20|unique:buses',
            'capacity' => 'required|integer|min:1',
            'bus_type' => 'required|string|in:regular,aircon',
            'manufacturer' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'required|string|in:available,in_service,maintenance,out_of_service',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bus = Bus::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Bus created successfully',
                'bus' => $bus
            ], 201);

        } catch (\Exception $e) {
            Log::error('Bus creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bus'
            ], 500);
        }
    }

    /**
     * Show bus details
     */
    public function show($id)
    {
        try {
            $bus = Bus::with(['schedules.driver', 'schedules.route'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'bus' => [
                    'id' => $bus->id,
                    'bus_number' => $bus->bus_number,
                    'plate_number' => $bus->plate_number,
                    'capacity' => $bus->capacity,
                    'bus_type' => $bus->bus_type,
                    'manufacturer' => $bus->manufacturer,
                    'model' => $bus->model,
                    'year' => $bus->year,
                    'status' => $bus->status,
                    'schedules' => $bus->schedules
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bus not found'
            ], 404);
        }
    }

    /**
     * Update bus
     */
    public function update(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bus_number' => 'string|max:20|unique:buses,bus_number,' . $id,
            'plate_number' => 'string|max:20|unique:buses,plate_number,' . $id,
            'capacity' => 'integer|min:1',
            'bus_type' => 'string|in:regular,aircon',
            'manufacturer' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'string|in:available,in_service,maintenance,out_of_service',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bus->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Bus updated successfully',
                'bus' => $bus
            ]);

        } catch (\Exception $e) {
            Log::error('Bus update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bus'
            ], 500);
        }
    }

    /**
     * Delete bus
     */
    public function destroy($id)
    {
        try {
            $bus = Bus::findOrFail($id);
            
            $activeSchedules = $bus->schedules()
                ->whereIn('status', ['scheduled', 'active'])
                ->where('date', '>=', now()->toDateString())
                ->count();

            if ($activeSchedules > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete bus with active schedules'
                ], 400);
            }

            $bus->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bus deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Bus deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bus'
            ], 500);
        }
    }

    // ===== API METHODS FOR MOBILE APP =====

    /**
     * Get all buses for mobile app
     */
    public function apiIndex()
    {
        try {
            $buses = Bus::whereIn('status', ['available', 'in_service'])
                ->select('id', 'bus_number', 'plate_number', 'capacity', 'bus_type', 'status')
                ->orderBy('bus_number')
                ->get();

            return response()->json([
                'success' => true,
                'buses' => $buses
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get buses API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load buses'
            ], 500);
        }
    }

    /**
     * Get bus details for mobile app
     */
    public function apiShow($id)
    {
        try {
            $bus = Bus::whereIn('status', ['available', 'in_service'])
                ->select('id', 'bus_number', 'plate_number', 'capacity', 'bus_type', 'manufacturer', 'model', 'year', 'status')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'bus' => $bus
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bus not found'
            ], 404);
        }
    }

    /**
     * Get available buses for scheduling
     */
    public function getAvailableBuses(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');
        $excludeScheduleId = $request->get('exclude_schedule_id');

        $query = Bus::where('status', 'available')
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

        $availableBuses = $query->select('id', 'bus_number', 'plate_number', 'capacity', 'bus_type')->get();

        return response()->json([
            'success' => true,
            'buses' => $availableBuses
        ]);
    }

    /**
     * Search buses
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $status = $request->get('status', '');
        $busType = $request->get('bus_type', '');

        $buses = Bus::query()
            ->when($query, function ($q) use ($query) {
                $q->where('bus_number', 'like', "%{$query}%")
                  ->orWhere('plate_number', 'like', "%{$query}%");
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($busType, function ($q) use ($busType) {
                $q->where('bus_type', $busType);
            })
            ->select('id', 'bus_number', 'plate_number', 'capacity', 'bus_type', 'status')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'buses' => $buses
        ]);
    }

    /**
     * Get bus statistics
     */
    public function getBusStats()
    {
        $stats = [
            'total_buses' => Bus::count(),
            'available_buses' => Bus::where('status', 'available')->count(),
            'in_service_buses' => Bus::where('status', 'in_service')->count(),
            'maintenance_buses' => Bus::where('status', 'maintenance')->count(),
            'out_of_service_buses' => Bus::where('status', 'out_of_service')->count(),
            'regular_buses' => Bus::where('bus_type', 'regular')->count(),
            'aircon_buses' => Bus::where('bus_type', 'aircon')->count(),
            'total_capacity' => Bus::sum('capacity'),
            'average_capacity' => Bus::avg('capacity'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}