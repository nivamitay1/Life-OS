<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunningPlanTemplateWeek extends Model
{
    protected $guarded = [];

    public function template()
    {
        return $this->belongsTo(RunningPlanTemplate::class, 'running_plan_template_id');
    }

    public function templateWorkouts()
    {
        return $this->hasMany(RunningPlanTemplateWorkout::class, 'running_plan_template_week_id');
    }
}
