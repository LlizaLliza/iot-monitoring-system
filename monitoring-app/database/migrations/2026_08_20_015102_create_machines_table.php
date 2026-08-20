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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('location', 150)->nullable();
            $table->string('type', 100)->nullable();
            $table->date('install_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_maintenance_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('location');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
