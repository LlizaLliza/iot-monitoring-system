<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeWithLiveStatus(Builder $query): Builder
    {
        return $query
            ->addSelect('machines.*')
            ->addSelect(['last_on_at' => \App\Models\SensorReading::selectRaw('MAX(recorded_at)')
                ->whereColumn('machine_id', 'machines.id')
                ->where('status', 'ON'),
            ])
            ->addSelect(['current_status' => \App\Models\SensorReading::select('status')
                ->whereColumn('machine_id', 'machines.id')
                ->orderByDesc('recorded_at')
                ->limit(1),
            ]);
    }

    public function getNeedsMaintenanceAttribute(): bool
    {
        $thresholdMinutes = config('monitoring.off_threshold_minutes');

        if (! $this->last_on_at) {
            return true; 
        }

        return \Carbon\Carbon::parse($this->last_on_at)->lt(now()->subMinutes($thresholdMinutes));
    }
}
