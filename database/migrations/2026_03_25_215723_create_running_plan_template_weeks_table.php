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
        Schema::create('running_plan_template_weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('running_plan_template_id')->constrained()->cascadeOnDelete();
            $table->integer('week_number');
            $table->string('theme_label')->nullable();
            $table->boolean('is_deload')->default(false);
            $table->text('goal_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('running_plan_template_weeks');
    }
};
