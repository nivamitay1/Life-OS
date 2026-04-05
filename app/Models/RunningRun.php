<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WorkoutTypeEnum;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RunningRun extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'date', 'distance', 'duration',
        'pace', 'type', 'effort', 'notes',
    ];

    protected $casts = [
        'type' => WorkoutTypeEnum::class,
        'date' => 'date',
    ];

    public function matchedWorkouts(): HasMany
    {
        return $this->hasMany(RunningPlanWorkoutRun::class, 'run_id');
    }
}
