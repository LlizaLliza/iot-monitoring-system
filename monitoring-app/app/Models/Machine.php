<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Machine extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code',
        'name',
        'location',
        'type',
        'install_date',
        'is_active',
        'last_maintenance_at',
    ];

    protected $casts = [
        'install_date' => 'date',
        'is_active' => 'boolean',
        'last_maintenance_at' => 'datetime',
    ];

    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }

    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    public function latestReading()
    {
        return $this->hasOne(SensorReading::class)->latestOfMany('recorded_at');
    }
}
