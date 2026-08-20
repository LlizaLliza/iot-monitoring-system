<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'sensor_id',
        'status',
        'metric_value',
        'output_qty',
        'recorded_at',
    ];

    protected $casts = [
        'metric_value' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}
