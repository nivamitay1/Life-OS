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
        Schema::create('running_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('running_plan_templates')->nullOnDelete();
            $table->string('name');
            $table->string('goal_type')->nullable();
            $table->string('status')->default('draft'); // draft, active, paused, completed, abandoned, archived
            $table->date('start_date');
            $table->date('end_date');
            $table->date('target_event_date')->nullable();
            $table->decimal('target_distance', 8, 2)->nullable();
            $table->string('target_time')->nullable(); // e.g. "03:59:59"
            $table->integer('current_week_number')->default(1);
            $table->integer('progress_percent')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('running_plans');
    }
};
