<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\NotificationsController;

// Authentication routes (no auth middleware)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Web Panel Routes (REQUIRE AUTH MIDDLEWARE - for web admin)
Route::middleware('auth')->group(function () {
    // Panel routes
    Route::get('/panel/operator', [PanelController::class, 'operatorPanel'])->name('operator.panel');
    
    // ✅ FIXED: Remove duplicate, keep only NotificationsController route
    Route::get('/panel/notifications', [NotificationsController::class, 'index'])->name('notifications.panel');    
    Route::get('/panel/schedule', [ScheduleController::class, 'schedulePanel'])->name('schedule.panel');
    Route::get('/panel/routes', [RouteController::class, 'index'])->name('routes.panel');
    Route::get('/panel/drivers', [DriverController::class, 'index'])->name('drivers.panel');
    Route::get('/panel/buses', [BusController::class, 'index'])->name('buses.panel');
    Route::get('/panel/terminal', [TerminalController::class, 'index'])->name('terminal.panel');

    // Driver profile route
    Route::get('/panel/profile/{id}', [DriverController::class, 'profile'])->name('drivers.profile');

    // Notification action routes
    Route::get('/notifications/unread-count', [NotificationsController::class, 'getUnreadCount']);
    Route::patch('/notifications/{id}/read', [NotificationsController::class, 'markAsRead']);
    Route::patch('/notifications/mark-all-read', [NotificationsController::class, 'markAllAsRead']);
    Route::delete('/notifications/clear-all', [NotificationsController::class, 'clearAll']);

    // Schedule management routes
    Route::prefix('schedules')->group(function () {
        Route::post('/', [ScheduleController::class, 'store'])->name('schedule.store');
        Route::get('/{id}', [ScheduleController::class, 'show'])->name('schedule.show');
        Route::put('/{id}', [ScheduleController::class, 'update'])->name('schedule.update');
        Route::delete('/{id}', [ScheduleController::class, 'destroy'])->name('schedule.destroy');
        Route::get('/available-drivers', [ScheduleController::class, 'getAvailableDrivers'])->name('schedule.available-drivers');
        Route::get('/active', [ScheduleController::class, 'getActiveSchedules'])->name('schedule.active');
        Route::post('/bulk', [ScheduleController::class, 'storeBulk'])->name('schedule.store.bulk');
    });

    // Driver management routes
    Route::prefix('drivers')->group(function () {
        Route::post('/', [DriverController::class, 'store'])->name('drivers.store');
        Route::get('/{id}', [DriverController::class, 'show'])->name('drivers.show');
        Route::put('/{id}', [DriverController::class, 'update'])->name('drivers.update');
        Route::delete('/{id}', [DriverController::class, 'destroy'])->name('drivers.destroy');
        Route::put('/{id}/status', [DriverController::class, 'updateStatus'])->name('drivers.status');
        Route::post('/{id}/reset-password', [DriverController::class, 'resetPassword'])->name('drivers.reset-password');
        Route::get('/available', [DriverController::class, 'getAvailableDrivers'])->name('drivers.available');
        Route::get('/search', [DriverController::class, 'search'])->name('drivers.search');
    });

    // Route management routes
    Route::prefix('routes')->group(function () {
        Route::get('/', [RouteController::class, 'index'])->name('routes.index');
        Route::post('/', [RouteController::class, 'store'])->name('routes.store');
        Route::get('/{id}', [RouteController::class, 'show'])->name('routes.show');
        Route::put('/{id}', [RouteController::class, 'update'])->name('routes.update');
        Route::delete('/{id}', [RouteController::class, 'destroy'])->name('routes.destroy');
    });

    // Bus management routes
    Route::prefix('buses')->group(function () {
        Route::post('/', [BusController::class, 'store'])->name('buses.store');
        Route::get('/{id}', [BusController::class, 'show'])->name('buses.show');
        Route::put('/{id}', [BusController::class, 'update'])->name('buses.update');
        Route::delete('/{id}', [BusController::class, 'destroy'])->name('buses.destroy');
        Route::get('/available', [BusController::class, 'getAvailableBuses'])->name('buses.available');
        Route::get('/search', [BusController::class, 'search'])->name('buses.search');
    });

    // Terminal management routes
    Route::prefix('terminal')->group(function () {
        Route::get('/', [TerminalController::class, 'index'])->name('terminal.panel');
        Route::get('/spaces', [TerminalController::class, 'getSpaces'])->name('terminal.spaces');
        Route::post('/spaces/book', [TerminalController::class, 'bookSpace'])->name('terminal.book');
        Route::put('/spaces/{id}/release', [TerminalController::class, 'releaseSpace'])->name('terminal.release');
        Route::get('/availability', [TerminalController::class, 'checkAvailability'])->name('terminal.availability');
        Route::get('/stats', [TerminalController::class, 'getStats'])->name('terminal.stats');
        Route::post('/assign-space', [TerminalController::class, 'assignSpace'])->name('terminal.assign-space');
        Route::get('/get-assignments', [TerminalController::class, 'getAssignments'])->name('terminal.get-assignments');
        Route::post('/remove-assignment', [TerminalController::class, 'removeAssignment'])->name('terminal.remove-assignment');
    });

    // Web API routes for AJAX calls (require web auth)
    Route::prefix('api')->group(function () {
        // Operator stats
        Route::get('/operator/stats', [PanelController::class, 'getOperatorStats'])->name('api.operator.stats');
        
        // Driver API routes
        Route::prefix('drivers')->group(function () {
            Route::get('/{id}', [DriverController::class, 'show'])->name('api.drivers.show');
        });
        
        // Bus API routes
        Route::prefix('buses')->group(function () {
            Route::get('/{id}', [BusController::class, 'show'])->name('api.buses.show');
            Route::get('/stats', [BusController::class, 'getBusStats'])->name('api.buses.stats');
            Route::get('/available', [BusController::class, 'getAvailableBuses'])->name('api.buses.available');
        });
        
        // Route API routes
        Route::prefix('routes')->group(function () {
            Route::get('/{id}', [RouteController::class, 'show'])->name('api.routes.show');
            Route::get('/{id}/coordinates', [RouteController::class, 'getRouteCoordinates'])->name('api.routes.coordinates');
            Route::get('/stats', [RouteController::class, 'getRouteStats'])->name('api.routes.stats');
            Route::get('/available', [RouteController::class, 'getAvailableRoutes'])->name('api.routes.available');
        });
        
        // Schedule API routes
        Route::prefix('schedules')->group(function () {
            Route::get('/{id}', [ScheduleController::class, 'show'])->name('api.schedules.show');
            Route::get('/available-drivers', [ScheduleController::class, 'getAvailableDrivers'])->name('api.schedules.available-drivers');
            Route::get('/active', [ScheduleController::class, 'getActiveSchedules'])->name('api.schedules.active');
            Route::get('/driver/{driverId}/today', [ScheduleController::class, 'getTodayScheduleForDriver'])->name('api.schedules.driver-today');
            Route::post('/bulk', [ScheduleController::class, 'storeBulk'])->name('schedule.store.bulk');
        });
    });

    // Standalone route for route coordinates API
    Route::get('/route-coordinates/{id}', [RouteController::class, 'getRouteCoordinates'])->name('route.coordinates.api');
});