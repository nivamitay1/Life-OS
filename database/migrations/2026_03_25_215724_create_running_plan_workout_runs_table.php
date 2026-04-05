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
        Schema::create('running_plan_workout_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('running_plan_workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('run_id')->constrained('running_runs')->cascadeOnDelete();
            $table->string('match_type')->default('auto'); // auto, manual, substitute, partial
            $table->integer('completion_credit')->default(100); // 0-100 percentage
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('running_plan_workout_runs');
    }
};
