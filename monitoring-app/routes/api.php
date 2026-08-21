<?php

use App\Http\Controllers\Api\SensorDataController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.key')->group(function () {
    Route::post('/sensor-data', [SensorDataController::class, 'store']);
});
