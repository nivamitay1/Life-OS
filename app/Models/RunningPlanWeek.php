<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RunningPlanWeek extends Model
{
    protected $fillable = [
        'running_plan_id', 'week_number', 'start_date', 'end_date', 'theme_label'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function runningPlan(): BelongsTo
    {
        return $this->belongsTo(RunningPlan::class);
    }

    public function workouts(): HasMany
    {
        return $this->hasMany(RunningPlanWorkout::class);
    }
}
