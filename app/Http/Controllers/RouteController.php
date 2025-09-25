<?php

namespace App\Http\Controllers;

use App\Models\Route as BusRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RouteController extends Controller
{
    /**
     * Display routes panel
     */
    public function index()
    {
        $routes = BusRoute::orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total_routes' => BusRoute::count(),
            'active_routes' => BusRoute::where('status', 'active')->count(),
            'inactive_routes' => BusRoute::where('status', 'inactive')->count(),
        ];

        return view('panels.routes', compact('routes', 'stats'));
    }

    /**
     * Store a new route
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:routes',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'aircon_price' => 'required|numeric|min:0',
            'distance_km' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
            'status' => 'required|string|in:active,inactive',
            'geometry' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $route = BusRoute::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Route created successfully',
                'route' => $route
            ], 201);

        } catch (\Exception $e) {
            Log::error('Route creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create route'
            ], 500);
        }
    }

    /**
     * Show route details
     */
    public function show($id)
    {
        try {
            $route = BusRoute::with(['stops'])->findOrFail($id);

            // Return geometry AS RAW STRING (do NOT parse it)
            $geometry = $route->geometry ?? '';

            // Parse stops for display
            $stopsArr = [];
            if ($route->relationLoaded('stops') && $route->stops) {
                $stopsArr = $route->stops->map(function($stop) {
                    return [
                        'name' => $stop->name ?? '',
                        'lat' => $stop->lat ?? null,
                        'lng' => $stop->lng ?? null,
                        'stop_order' => $stop->stop_order ?? null
                    ];
                })->toArray();
            }

            return response()->json([
                'success' => true,
                'route' => [
                    'id' => $route->id,
                    'name' => $route->name,
                    'code' => $route->code,
                    'start_location' => $route->start_location,
                    'end_location' => $route->end_location,
                    'start_coordinates' => $route->start_coordinates ?? '',
                    'end_coordinates' => $route->end_coordinates ?? '',
                    'description' => $route->description,
                    'regular_price' => $route->regular_price,
                    'aircon_price' => $route->aircon_price,
                    'distance_km' => $route->distance_km,
                    'estimated_duration' => $route->estimated_duration,
                    'status' => $route->status,
                    'geometry' => $geometry,        // ← CHANGED: 'geometry' (raw string)
                    'stops_data' => $stopsArr
                ]
            ]);
        } catch (\Exception $e) {
            // Log the actual error
            \Log::error('Route show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Route not found'
            ], 404);
        }
    }

    /**
     * Update route
     */
    public function update(Request $request, $id)
    {
        $route = BusRoute::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'code' => 'string|max:10|unique:routes,code,' . $id,
            'start_location' => 'string|max:255',
            'end_location' => 'string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'numeric|min:0',
            'aircon_price' => 'numeric|min:0',
            'distance_km' => 'numeric|min:0',
            'estimated_duration' => 'integer|min:1',
            'status' => 'string|in:active,inactive',
            'geometry' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $route->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Route updated successfully',
                'route' => $route
            ]);

        } catch (\Exception $e) {
            Log::error('Route update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update route'
            ], 500);
        }
    }

    /**
     * Delete route
     */
    public function destroy($id)
    {
        try {
            $route = BusRoute::findOrFail($id);
            
            // Check if route has active schedules
            $activeSchedules = $route->schedules()
                ->whereIn('status', ['scheduled', 'active'])
                ->where('date', '>=', now()->toDateString())
                ->count();

            if ($activeSchedules > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete route with active schedules'
                ], 400);
            }

            $route->delete();

            return response()->json([
                'success' => true,
                'message' => 'Route deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Route deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete route'
            ], 500);
        }
    }

    // ===== API METHODS FOR MOBILE APP =====

    /**
     * Get all routes for mobile app
     */
    public function apiIndex()
    {
        try {
            $routes = BusRoute::where('status', 'active')
                ->select('id', 'name', 'code', 'start_location', 'end_location', 'regular_price', 'aircon_price', 'distance_km')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'routes' => $routes
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get routes API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load routes'
            ], 500);
        }
    }

    /**
     * Get route details for mobile app
     */
    public function apiShow($id)
    {
        try {
            $route = BusRoute::where('status', 'active')
                ->with(['stops'])
                ->select('id', 'name', 'code', 'start_location', 'end_location', 'description', 'regular_price', 'aircon_price', 'distance_km', 'estimated_duration')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'route' => $route
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Route not found'
            ], 404);
        }
    }

    /**
     * Get route coordinates for mapping
     */
    public function getRouteCoordinates($id)
    {
        try {
            $route = BusRoute::findOrFail($id);
            
            // Return geometry if available, otherwise return start/end coordinates
            $coordinates = [];
            
            if ($route->geometry) {
                $coordinates = json_decode($route->geometry, true);
            } else {
                // Default coordinates for start and end locations
                $coordinates = [
                    'start' => ['lat' => 0, 'lng' => 0], // Replace with actual coordinates
                    'end' => ['lat' => 0, 'lng' => 0]     // Replace with actual coordinates
                ];
            }

            return response()->json([
                'success' => true,
                'coordinates' => $coordinates,
                'route' => [
                    'id' => $route->id,
                    'name' => $route->name,
                    'start_location' => $route->start_location,
                    'end_location' => $route->end_location
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Route not found'
            ], 404);
        }
    }

    /**
     * Get available routes for scheduling
     */
    public function getAvailableRoutes()
    {
        $routes = BusRoute::where('status', 'active')
            ->select('id', 'name', 'code', 'start_location', 'end_location')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'routes' => $routes
        ]);
    }

    /**
     * Get route statistics
     */
    public function getRouteStats()
    {
        $stats = [
            'total_routes' => BusRoute::count(),
            'active_routes' => BusRoute::where('status', 'active')->count(),
            'inactive_routes' => BusRoute::where('status', 'inactive')->count(),
            'total_distance' => BusRoute::sum('distance_km'),
            'average_distance' => BusRoute::avg('distance_km'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}