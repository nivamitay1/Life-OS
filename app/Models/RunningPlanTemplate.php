<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunningPlanTemplate extends Model
{
    protected $guarded = [];

    public function templateWeeks()
    {
        return $this->hasMany(RunningPlanTemplateWeek::class, 'running_plan_template_id');
    }

    public function runningPlans()
    {
        return $this->hasMany(RunningPlan::class, 'template_id');
    }
}
