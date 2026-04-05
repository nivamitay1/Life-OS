<?php

declare(strict_types=1);

namespace App\Actions\Running;

use App\Enums\ModuleEnum;
use App\Enums\PlanStatusEnum;
use App\Enums\WorkoutStatusEnum;
use App\Models\RunningPlan;
use App\Models\RunningPlanWorkout;
use App\Models\RunningPlanWorkoutRun;
use App\Models\RunningRun;
use App\Services\ProgressEngine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LogRunAction
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function handle(int $userId, array $validated): void
    {
        DB::transaction(function () use ($userId, $validated) {
            $timeParts = explode(':', $validated['duration']);
            $totalMinutes = 0;
            if (count($timeParts) === 3) {
                $totalMinutes = ((int)$timeParts[0] * 60) + (int)$timeParts[1] + ((int)$timeParts[2] / 60);
            } elseif (count($timeParts) === 2) {
                $totalMinutes = (int)$timeParts[0] + ((int)$timeParts[1] / 60);
            }

            $pace = '0:00';
            if ((float)$validated['distance'] > 0 && $totalMinutes > 0) {
                $paceDecimal = $totalMinutes / (float)$validated['distance'];
                $pace = sprintf('%d:%02d', floor($paceDecimal), round(($paceDecimal - floor($paceDecimal)) * 60));
            }

            $notes = ($validated['name'] ?? '') . "\n" . ($validated['notes'] ?? '');

            $run = RunningRun::create([
                'user_id' => $userId, 'date' => $validated['date'],
                'distance' => (float)$validated['distance'], 'duration' => $validated['duration'],
                'pace' => $pace, 'type' => $validated['type'], 'effort' => 5,
                'notes' => trim($notes),
            ]);

            $activePlan = RunningPlan::forUser($userId)->where('status', PlanStatusEnum::ACTIVE)->first();

            if ($activePlan) {
                $potentialWorkout = null;
                if (!empty($validated['plan_workout_id'])) {
                    $potentialWorkout = RunningPlanWorkout::where('id', $validated['plan_workout_id'])
                        ->whereHas('runningPlanWeek', fn($q) => $q->where('running_plan_id', $activePlan->id))
                        ->first();
                }
                if (!$potentialWorkout) {
                    $potentialWorkout = RunningPlanWorkout::whereHas('runningPlanWeek', fn($q) => $q->where('running_plan_id', $activePlan->id))
                        ->whereBetween('scheduled_date', [Carbon::parse($validated['date'])->subDay()->toDateString(), Carbon::parse($validated['date'])->addDay()->toDateString()])
                        ->where('workout_type', $validated['type'])
                        ->where('status', '!=', WorkoutStatusEnum::COMPLETED)
                        ->first();
                }
                if ($potentialWorkout) {
                    $credit = $potentialWorkout->target_distance_km > 0
                        ? min(100, (int)round(((float)$validated['distance'] / $potentialWorkout->target_distance_km) * 100))
                        : 100;
                    RunningPlanWorkoutRun::create([
                        'running_plan_workout_id' => $potentialWorkout->id, 'run_id' => $run->id,
                        'match_type' => 'auto', 'completion_credit' => $credit,
                    ]);
                    $potentialWorkout->update([
                        'status' => $credit >= 90 ? WorkoutStatusEnum::COMPLETED : WorkoutStatusEnum::PARTIALLY_COMPLETED,
                        'target_distance_km' => $run->distance,
                        'target_duration_sec' => $totalMinutes > 0 ? $totalMinutes * 60 : $potentialWorkout->target_duration_sec,
                    ]);
                }
            }

            $this->progressEngine->addXp($userId, ModuleEnum::RUNNING, 50, 'Completed run');
        });
    }
}
