<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropIndex(['recorded_at']);
            $table->dropIndex(['status']);
        });

        // Index komposit untuk subquery deteksi maintenance:
        // WHERE machine_id = ? AND status = 'ON' ORDER BY recorded_at DESC
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->index(['machine_id', 'status', 'recorded_at'], 'idx_machine_status_recorded');
        });

        // Covering index untuk query rekap output: filter by recorded_at (range),
        DB::statement('
            CREATE INDEX idx_recorded_at_covering
            ON sensor_readings (recorded_at)
            INCLUDE (machine_id, status, output_qty)
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX idx_recorded_at_covering ON sensor_readings');

        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropIndex('idx_machine_status_recorded');
        });

        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->index('recorded_at');
            $table->index('status');
        });
    }
};