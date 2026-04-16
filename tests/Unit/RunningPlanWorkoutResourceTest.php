<?php

declare(strict_types=1);

use App\Enums\WorkoutStatusEnum;
use App\Enums\WorkoutTypeEnum;
use App\Http\Resources\RunningPlanWorkoutResource;
use App\Models\RunningPlanWorkout;
use Illuminate\Http\Request;

it('serializes workout dates as plain date strings', function () {
    $workout = new RunningPlanWorkout([
        'title' => 'Tempo Run',
        'workout_type' => WorkoutTypeEnum::EASY,
        'status' => WorkoutStatusEnum::SCHEDULED,
        'scheduled_date' => '2026-04-16',
        'target_distance_km' => 8,
    ]);

    $payload = (new RunningPlanWorkoutResource($workout))->toArray(new Request());

    expect($payload['scheduled_date'])->toBe('2026-04-16');
    expect($payload['workout_type'])->toBe('easy');
    expect($payload['status'])->toBe('scheduled');
});
