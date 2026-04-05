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
        Schema::create('running_plan_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('5k'); // 5k, 10k, half, full, base
            $table->string('experience_level')->default('beginner');
            $table->text('description')->nullable();
            $table->integer('duration_weeks')->default(4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('running_plan_templates');
    }
};
