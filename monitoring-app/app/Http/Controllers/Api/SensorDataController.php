<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSensorDataRequest;
use App\Models\Machine;
use App\Models\Sensor;
use App\Models\SensorReading;
use Illuminate\Database\QueryException;

class SensorDataController extends Controller
{
    public function store(StoreSensorDataRequest $request)
    {
        $validated = $request->validated();

        $machine = Machine::where('code', $validated['machine_code'])->firstOrFail();

        $sensor = Sensor::where('machine_id', $machine->id)
            ->where('sensor_code', $validated['sensor_code'])
            ->first();

        if (! $sensor) {
            return response()->json([
                'message' => "Sensor '{$validated['sensor_code']}' tidak terdaftar untuk mesin ini.",
            ], 404);
        }

        try {
            $reading = SensorReading::create([
                'machine_id' => $machine->id,
                'sensor_id' => $sensor->id,
                'status' => $validated['status'],
                'metric_value' => $validated['metric_value'],
                'output_qty' => $validated['output_qty'] ?? 0,
                'recorded_at' => $validated['recorded_at'],
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'Data duplikat: sensor ini sudah mengirim data pada timestamp yang sama.',
                ], 409);
            }

            throw $e;
        }

        $machine->update(['last_maintenance_at' => $machine->last_maintenance_at]);

        return response()->json([
            'message' => 'Data sensor berhasil disimpan.',
            'data' => $reading,
        ], 201);
    }
}
