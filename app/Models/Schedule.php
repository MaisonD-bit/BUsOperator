<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'route_id',
        'bus_id',
        'driver_id',
        'start_time',
        'end_time',
        'date',
        'status',
        'fare_regular',
        'fare_aircon',
        'terminal_space',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'date' => 'date',
        'fare_regular' => 'decimal:2',
        'fare_aircon' => 'decimal:2',
    ];

    // Relationships
    public function route()
    {
        return $this->belongsTo(\App\Models\Route::class);
    }

    public function bus()
    {
        return $this->belongsTo(\App\Models\Bus::class);
    }

    public function driver()
    {
        return $this->belongsTo(\App\Models\Driver::class);
    }
}