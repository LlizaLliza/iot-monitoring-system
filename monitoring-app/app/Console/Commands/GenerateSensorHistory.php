<?php

namespace App\Console\Commands;

use App\Models\Sensor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateSensorHistory extends Command
{
    /**
     * Execute the console command.
     */

    protected $signature = 'generate:sensor-history {--rows=100000} {--days=90}';
    protected $description = 'Generate dummy data historis sensor dalam jumlah besar untuk uji performa';
    private const BATCH_SIZE = 200;
    

    public function handle(): int
    {
        $targetRows = (int) $this->option('rows');
        $days = (int) $this->option('days');

        $sensors = Sensor::with('machine')->get();

        if ($sensors->isEmpty()) {
            $this->error('Belum ada sensor di database. Buat mesin dulu lewat CRUD (minimal 1 mesin, sensor otomatis terbuat).');
            return self::FAILURE;
        }

        $readingsPerSensor = (int) ceil($targetRows / $sensors->count());
        $intervalMinutes = ($days * 24 * 60) / $readingsPerSensor;

        $this->info("Sensor terdaftar: {$sensors->count()}");
        $this->info("Target baris: {$targetRows} (~{$readingsPerSensor} baris/sensor, interval ~" . round($intervalMinutes, 1) . " menit)");

        $startTime = microtime(true);
        $totalInserted = 0;
        $batch = [];

        $bar = $this->output->createProgressBar($targetRows);
        $bar->start();

        DB::connection()->disableQueryLog();

        foreach ($sensors as $sensor) {
            $timestamp = Carbon::now()->subDays($days);

            for ($i = 0; $i < $readingsPerSensor; $i++) {
                $status = (mt_rand(1, 100) <= 90) ? 'ON' : 'OFF';

                $batch[] = [
                    'machine_id' => $sensor->machine_id,
                    'sensor_id' => $sensor->id,
                    'status' => $status,
                    'metric_value' => round(mt_rand(300, 900) / 10, 2),
                    'output_qty' => $status === 'ON' ? mt_rand(5, 25) : 0,
                    'recorded_at' => $timestamp->copy(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $timestamp->addMinutes($intervalMinutes);

                if (count($batch) >= self::BATCH_SIZE) {
                    DB::table('sensor_readings')->insert($batch);
                    $totalInserted += count($batch);
                    $bar->advance(count($batch));
                    $batch = [];
                }
            }
        }

        if (! empty($batch)) {
            DB::table('sensor_readings')->insert($batch);
            $totalInserted += count($batch);
            $bar->advance(count($batch));
        }

        $bar->finish();
        $this->newLine(2);

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Selesai. Total {$totalInserted} baris diinsert dalam {$duration} detik.");

        return self::SUCCESS;
    }
}
