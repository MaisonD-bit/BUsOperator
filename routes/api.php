<?php
// filepath: c:\Users\User\Desktop\Laravel BusOp\BusOperator\routes\api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\BusController;

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {
    
    // Authentication routes for mobile app
    Route::post('/auth/login', [AuthController::class, 'apiLogin']);
    Route::post('/auth/register', [AuthController::class, 'apiRegister']);
    Route::post('/auth/logout', [AuthController::class, 'apiLogout']);
    Route::get('/auth/user', [AuthController::class, 'getAuthenticatedUser']);
    
    // Driver routes for mobile app
    Route::prefix('drivers')->group(function () {
        // Get driver schedules
        Route::get('/{driverId}/schedules', [ScheduleController::class, 'getDriverSchedules']);
        
        // Get driver profile
        Route::get('/{id}', [DriverController::class, 'show']);

        // ADD THIS LINE - Driver registration from mobile app
        Route::post('/register', [DriverController::class, 'registerFromApp']);
        
        // Update driver status
        Route::put('/{id}/status', [DriverController::class, 'updateStatus']);
    });
    
    // Schedule management routes for mobile app
    Route::prefix('schedules')->group(function () {
        // Get all schedules (admin view)
        Route::get('/', [ScheduleController::class, 'index']);
        
        // Get specific schedule
        Route::get('/{id}', [ScheduleController::class, 'webShow']);
        
        // Schedule actions for drivers
        Route::put('/{id}/accept', [ScheduleController::class, 'acceptSchedule']);
        Route::put('/{id}/decline', [ScheduleController::class, 'declineSchedule']);
        Route::put('/{id}/start', [ScheduleController::class, 'startSchedule']);
        Route::put('/{id}/complete', [ScheduleController::class, 'completeSchedule']);
        
        // Create new schedule (admin)
        Route::post('/', [ScheduleController::class, 'assignToDriver']);
    });
    
    // Route information for mobile app
    Route::prefix('routes')->group(function () {
        Route::get('/', [RouteController::class, 'index']);
        Route::get('/{id}', [RouteController::class, 'show']);
    });
    
    // Bus information for mobile app
    Route::prefix('buses')->group(function () {
        Route::get('/', [BusController::class, 'index']);
        Route::get('/{id}', [BusController::class, 'show']);
    });

    Route::get('drivers', [DriverController::class, 'index']);
});

// Legacy routes for backward compatibility (these might be used by your web panel AJAX calls)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Web panel API routes
    Route::get('/schedules/{id}', [ScheduleController::class, 'webShow']);
    Route::get('/drivers/{id}', [DriverController::class, 'show']);
});

// Alternative routes without version prefix (for your current Ionic setup)
Route::group(['middleware' => 'api'], function () {
    
    // Driver schedules - THE MAIN ROUTE YOUR IONIC APP NEEDS
    Route::get('drivers/{driverId}/schedules', [ScheduleController::class, 'getDriverSchedules']);
    
    // Schedule actions
    Route::put('schedules/{id}/accept', [ScheduleController::class, 'acceptSchedule']);
    Route::put('schedules/{id}/decline', [ScheduleController::class, 'declineSchedule']);
    Route::put('schedules/{id}/start', [ScheduleController::class, 'startSchedule']);
    Route::put('schedules/{id}/complete', [ScheduleController::class, 'completeSchedule']);
    
    // Other API endpoints
    Route::get('schedules', [ScheduleController::class, 'index']);
    Route::get('schedules/{id}', [ScheduleController::class, 'webShow']);
    Route::post('schedules', [ScheduleController::class, 'assignToDriver']);
    
    Route::get('drivers/{id}', [DriverController::class, 'show']);
    Route::get('routes', [RouteController::class, 'index']);
    Route::get('buses', [BusController::class, 'index']);
    Route::get('drivers', [DriverController::class, 'index']);
});