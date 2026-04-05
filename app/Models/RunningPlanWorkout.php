<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\WorkoutStatusEnum;
use App\Enums\WorkoutTypeEnum;

class RunningPlanWorkout extends Model
{
    protected $fillable = [
        'running_plan_week_id', 'scheduled_date', 'workout_type',
        'title', 'target_distance_km', 'target_duration_sec',
        'instructions', 'is_key_workout', 'status'
    ];

    protected $casts = [
        'status' => WorkoutStatusEnum::class,
        'workout_type' => WorkoutTypeEnum::class,
        'scheduled_date' => 'date',
        'is_key_workout' => 'boolean',
    ];

    public function runningPlanWeek(): BelongsTo
    {
        return $this->belongsTo(RunningPlanWeek::class);
    }

    public function matchedRuns(): HasMany
    {
        return $this->hasMany(RunningPlanWorkoutRun::class);
    }
}
