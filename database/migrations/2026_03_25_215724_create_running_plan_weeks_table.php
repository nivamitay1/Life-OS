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
        Schema::create('running_plan_weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('running_plan_id')->constrained()->cascadeOnDelete();
            $table->integer('week_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('planned_distance_total', 8, 2)->default(0);
            $table->integer('planned_workouts_total')->default(0);
            $table->text('goal_notes')->nullable();
            $table->boolean('is_deload')->default(false);
            $table->string('theme_label')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('running_plan_weeks');
    }
};
