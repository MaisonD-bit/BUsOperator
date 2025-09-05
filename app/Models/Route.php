<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

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
        'geometry_data',
        'stops_data'
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'aircon_price' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'estimated_duration' => 'integer',
        'geometry_data' => 'array',
        'stops_data' => 'array'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}