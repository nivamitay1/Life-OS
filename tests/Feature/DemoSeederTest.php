<?php

declare(strict_types=1);

use App\Enums\PlanStatusEnum;
use App\Models\RunningPlan;
use Database\Seeders\DemoSeeder;

it('seeds an active running plan with calendar weeks and workouts', function () {
    $this->seed(DemoSeeder::class);

    $plan = RunningPlan::query()
        ->where('status', PlanStatusEnum::ACTIVE->value)
        ->with('weeks.workouts')
        ->first();

    expect($plan)->not->toBeNull();
    expect($plan->weeks)->toHaveCount(6);

    $currentWeek = $plan->weeks->first(function ($week) {
        return $week->start_date->toDateString() <= today()->toDateString()
            && $week->end_date->toDateString() >= today()->toDateString();
    });

    expect($currentWeek)->not->toBeNull();
    expect($currentWeek->workouts)->toHaveCount(3);
});
