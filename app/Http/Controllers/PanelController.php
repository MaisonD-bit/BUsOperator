<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Route;
use App\Models\Bus;
use App\Models\Driver; 
use Illuminate\Http\Request;
use Carbon\Carbon;

class PanelController extends Controller
{
    public function operatorPanel()
    {
        $recentSchedules = Schedule::with(['route', 'bus', 'driver'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        $activeRoutes = Route::where('status', 'active')->count();
        $activeBuses = Bus::where('status', 'active')->count();
        $activeDrivers = Driver::where('status', 'active')->count();

        $todaySchedules = Schedule::whereDate('date', today())->count();
        $activeSchedules = Schedule::where('status', 'active')->count();
        $completedSchedules = Schedule::whereDate('date', today())->where('status', 'completed')->count();
        $pendingSchedules = Schedule::whereDate('date', today())->where('status', 'scheduled')->count();

        $issues = 3; // Example

        $performanceStats = [
            'onTime' => 85,
            'delayed' => 12,
            'cancelled' => 3
        ];

        return view('panels.operator', compact(
            'activeRoutes',
            'activeBuses',
            'activeDrivers',
            'todaySchedules',
            'activeSchedules',
            'completedSchedules',
            'pendingSchedules',
            'issues',
            'recentSchedules',
            'performanceStats'
        ));
    }

    public function notifications()
    {
        return view('panels.notifications');
    }

    public function getOperatorStats()
    {
        return response()->json([
            'total_routes' => Route::count(),
            'active_routes' => Route::where('status', 'active')->count(),
            'total_buses' => Bus::count(),
            'active_buses' => Bus::where('status', 'active')->count(),
            'total_drivers' => Driver::count(),
            'active_drivers' => Driver::where('status', 'active')->count(),
            'today_schedules' => Schedule::whereDate('date', today())->count(),
            'active_schedules' => Schedule::where('status', 'active')->count(),
        ]);
    }

}