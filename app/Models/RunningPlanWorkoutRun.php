<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunningPlanWorkoutRun extends Model
{
    protected $guarded = [];

    public function workout()
    {
        return $this->belongsTo(RunningPlanWorkout::class, 'running_plan_workout_id');
    }

    public function runningRun()
    {
        return $this->belongsTo(RunningRun::class, 'run_id');
    }
}
