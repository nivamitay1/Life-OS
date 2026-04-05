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
        Schema::create('running_plan_template_workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('running_plan_template_week_id')->constrained()->cascadeOnDelete();
            $table->integer('day_of_week'); // 1 = Monday, 7 = Sunday
            $table->string('workout_type')->default('easy');
            $table->string('title');
            $table->decimal('target_distance_km', 8, 2)->nullable();
            $table->integer('target_duration_sec')->nullable();
            $table->integer('target_pace_sec_per_km')->nullable();
            $table->integer('target_effort')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_key_workout')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('running_plan_template_workouts');
    }
};
