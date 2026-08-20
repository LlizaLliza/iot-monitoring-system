<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')
                ->constrained('machines')
                ->onDelete('no action');
            $table->foreignId('sensor_id')
                ->constrained('sensors')
                ->cascadeOnDelete();
            $table->enum('status', ['ON', 'OFF']);
            $table->decimal('metric_value', 10, 2);
            $table->unsignedInteger('output_qty')->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->unique(['sensor_id', 'recorded_at'], 'uniq_sensor_recorded');

            $table->index(['machine_id', 'recorded_at']);
            $table->index('recorded_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
