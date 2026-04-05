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
        Schema::create('learn_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['book', 'course', 'article', 'podcast', 'other'])->default('book');
            $table->string('title');
            $table->string('author')->nullable();
            $table->enum('status', ['active', 'completed', 'dropped', 'wishlist'])->default('active');
            $table->integer('total_units')->nullable();
            $table->integer('current_unit')->default(0);
            $table->enum('unit_label', ['page', 'chapter', 'lesson', 'module', 'episode', 'article', 'other'])->nullable();
            $table->string('current_position_label')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_session_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learn_resources');
    }
};
