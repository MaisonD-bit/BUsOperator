<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bus extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'plate_number',
        'bus_number',
        'model',
        'capacity',
        'bus_company',
        'accommodation_type',
        'status',
        'description'
    ];
    
    protected $casts = [
        'capacity' => 'integer',
    ];
    
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'bus_routes');
    }

    
    public function activeSchedules()
    {
        return $this->hasMany(Schedule::class)->where('status', 'active');
    }
    
    public function isAirConditioned()
    {
        return in_array($this->accommodation_type, ['air-conditioned', 'deluxe', 'super-deluxe']);
    }
    
    public function getRoutePrice(Route $route)
    {
        if ($this->isAirConditioned() && !is_null($route->aircon_price)) {
            return $route->aircon_price;
        }
        
        return $route->regular_price;
    }
    
    public function getDisplayNameAttribute()
    {
        return "{$this->bus_number} - {$this->model}";
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getAccommodationLabel()
    {
        return match($this->accommodation_type) {
            'regular' => 'Regular',
            'air-conditioned' => 'Air-Conditioned',
            'deluxe' => 'Deluxe',
            'super-deluxe' => 'Super Deluxe',
            default => 'Regular'
        };
    }
}