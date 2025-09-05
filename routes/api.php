<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\BusController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Test routes - THESE SHOULD WORK AT /api/test
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
        'server' => 'Laravel',
        'method' => 'GET',
        'status' => 'success'
    ]);
});

Route::post('/test', function () {
    return response()->json([
        'message' => 'POST request working!',
        'timestamp' => now(),
        'server' => 'Laravel',
        'method' => 'POST',
        'status' => 'success',
        'data_received' => request()->all()
    ]);
});

// v1 test routes - THESE SHOULD WORK AT /api/v1/test
Route::prefix('v1')->group(function () {
    Route::get('/test', function () {
        return response()->json([
            'message' => 'API v1 is working!',
            'timestamp' => now(),
            'server' => 'Laravel',
            'method' => 'GET',
            'status' => 'success',
            'version' => 'v1'
        ]);
    });

    Route::post('/test', function () {
        return response()->json([
            'message' => 'POST request working in v1!',
            'timestamp' => now(),
            'server' => 'Laravel',
            'method' => 'POST',
            'status' => 'success',
            'version' => 'v1',
            'data_received' => request()->all()
        ]);
    });

    // Driver Registration and Authentication for Mobile
    Route::post('/drivers/register', [DriverController::class, 'registerFromApp'])->name('api.drivers.register');
    Route::post('/drivers/login', [DriverController::class, 'loginFromApp'])->name('api.drivers.login');
    
    // Driver Profile and Schedule Management for Mobile
    Route::get('/drivers/{id}/profile', [DriverController::class, 'getProfile'])->name('api.drivers.profile');
    Route::put('/drivers/{id}/profile', [DriverController::class, 'updateProfile'])->name('api.drivers.update-profile');
    Route::get('/drivers/{id}/schedules', [DriverController::class, 'getDriverSchedules'])->name('api.drivers.schedules');
    Route::put('/drivers/{id}/schedules/{scheduleId}/status', [DriverController::class, 'updateScheduleStatus'])->name('api.drivers.schedule-status');
    
    // Schedule Management for Mobile App
    Route::post('/schedules/assign', [ScheduleController::class, 'assignToDriver'])->name('api.schedules.assign');
    Route::get('/schedules/driver/{driverId}', [ScheduleController::class, 'getDriverSchedules'])->name('api.schedules.driver');
    Route::put('/schedules/{id}/accept', [ScheduleController::class, 'acceptSchedule'])->name('api.schedules.accept');
    Route::put('/schedules/{id}/decline', [ScheduleController::class, 'declineSchedule'])->name('api.schedules.decline');
    
    // Routes and Bus information for Mobile
    Route::get('/routes', [RouteController::class, 'apiIndex'])->name('api.routes.index');
    Route::get('/routes/{id}', [RouteController::class, 'apiShow'])->name('api.routes.show');
    Route::get('/buses', [BusController::class, 'apiIndex'])->name('api.buses.index');
    Route::get('/buses/{id}', [BusController::class, 'apiShow'])->name('api.buses.show');
});