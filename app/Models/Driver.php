<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Driver extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password', // Add for app login
        'contact_number',
        'date_of_birth',
        'gender',
        'address',
        'license_number',
        'license_expiry',
        'emergency_name',
        'emergency_relation',
        'emergency_contact',
        'status',
        'notes',
        'photo_url',
        'app_registered', // Track if registered from app
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'license_expiry' => 'date',
        'app_registered' => 'boolean',
    ];

    // Set default attributes
    protected $attributes = [
        'status' => 'active',
        'app_registered' => false,
    ];

    // Relationships
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'schedules');
    }
}