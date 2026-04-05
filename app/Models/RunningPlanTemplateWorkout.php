<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunningPlanTemplateWorkout extends Model
{
    protected $guarded = [];

    public function templateWeek()
    {
        return $this->belongsTo(RunningPlanTemplateWeek::class, 'running_plan_template_week_id');
    }
}
