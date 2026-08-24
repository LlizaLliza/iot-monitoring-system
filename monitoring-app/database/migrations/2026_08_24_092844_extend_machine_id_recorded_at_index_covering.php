<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop index lama, untuk ulang dengan INCLUDE supaya jadi covering index
        // untuk pola query "filter per machine_id + rentang recorded_at, butuh status & output_qty"
        DB::statement('DROP INDEX sensor_readings_machine_id_recorded_at_index ON sensor_readings');

        DB::statement('
            CREATE INDEX sensor_readings_machine_id_recorded_at_index
            ON sensor_readings (machine_id, recorded_at)
            INCLUDE (status, output_qty)
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX sensor_readings_machine_id_recorded_at_index ON sensor_readings');

        DB::statement('
            CREATE INDEX sensor_readings_machine_id_recorded_at_index
            ON sensor_readings (machine_id, recorded_at)
        ');
    }
};