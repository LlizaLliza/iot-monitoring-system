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
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')
                ->constrained('machines')
                ->cascadeOnDelete();
            $table->string('sensor_code', 50);
            $table->string('metric_type', 50); 
            $table->string('unit', 20)->nullable();
            $table->timestamps();

            $table->unique(['machine_id', 'sensor_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
