<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardStreamController extends Controller
{
    public function stream(): StreamedResponse
    {
        return response()->stream(function () {
            $endTime = time() + 25; 

            while (time() < $endTime) {
                if (connection_aborted()) {
                    break;
                }

                $machines = Machine::query()
                    ->withLiveStatus()
                    ->where('is_active', true)
                    ->get()
                    ->map(fn ($m) => [
                        'id' => $m->id,
                        'status' => $m->current_status ?? 'OFF',
                        'needs_maintenance' => $m->needs_maintenance,
                    ]);

                echo 'data: ' . json_encode($machines) . "\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

                sleep(3);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }
}
