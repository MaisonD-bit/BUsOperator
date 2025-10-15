<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\RouteDistanceCalculator;

class Route extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     * Automatically calculate distance when geometry is set
     */
    protected static function booted()
    {
        static::saving(function ($route) {
            // Auto-calculate distance if geometry exists and distance is not manually set
            if ($route->geometry && $route->isDirty('geometry')) {
                $calculatedDistance = RouteDistanceCalculator::calculateDistance($route->geometry);
                if ($calculatedDistance > 0) {
                    $route->distance_km = $calculatedDistance;
                }
            }
        });
    }

    protected $fillable = [
        'name',
        'code',
        'start_location',
        'end_location',
        'start_coordinates',
        'end_coordinates',
        'distance_km',
        'estimated_duration',
        'description',
        'regular_price',
        'aircon_price',
        'status',
        'geometry',
        'stops_data'
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'aircon_price' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'estimated_duration' => 'integer',
        'stops_data' => 'array'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function stops()
    {
        return $this->belongsToMany(Stop::class, 'route_stops', 'route_id', 'stop_id')
            ->withPivot('stop_order', 'estimated_minutes')
            ->orderBy('route_stops.stop_order');
    }
}