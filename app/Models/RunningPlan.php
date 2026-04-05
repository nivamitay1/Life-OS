<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlanStatusEnum;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RunningPlan extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'template_id', 'name', 'start_date',
        'end_date', 'status', 'started_at', 'goal_type',
    ];

    protected $casts = [
        'status' => PlanStatusEnum::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'started_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(RunningPlanTemplate::class, 'template_id');
    }

    public function weeks(): HasMany
    {
        return $this->hasMany(RunningPlanWeek::class);
    }
}
