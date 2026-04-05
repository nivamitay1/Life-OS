<?php

declare(strict_types=1);

namespace App\Actions\Running;

use App\Models\RunningPlan;
use App\Models\RunningPlanTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\PlanStatusEnum;
use App\Enums\WorkoutStatusEnum;

class StartTemplatePlanAction
{
    public function handle(int $userId, RunningPlanTemplate $template): void
    {
        DB::transaction(function () use ($userId, $template) {
            $startDate = Carbon::now()->startOfWeek(); 

            $plan = RunningPlan::create([
                'user_id' => $userId,
                'template_id' => $template->id,
                'name' => $template->name,
                'start_date' => $startDate,
                'end_date' => $startDate->copy()->addWeeks($template->duration_weeks - 1)->endOfWeek(),
                'status' => PlanStatusEnum::ACTIVE,
                'started_at' => now(),
            ]);

            foreach ($template->templateWeeks as $tWeek) {
                $weekStart = $startDate->copy()->addWeeks($tWeek->week_number - 1);
                $week = $plan->weeks()->create([
                    'week_number' => $tWeek->week_number,
                    'start_date' => $weekStart,
                    'end_date' => $weekStart->copy()->endOfWeek(),
                    'theme_label' => $tWeek->theme_label
                ]);

                foreach ($tWeek->templateWorkouts as $tWorkout) {
                    $workoutDate = $weekStart->copy()->addDays($tWorkout->day_of_week - 1);
                    $week->workouts()->create([
                        'scheduled_date' => $workoutDate,
                        'workout_type' => $tWorkout->workout_type,
                        'title' => $tWorkout->title,
                        'target_distance_km' => $tWorkout->target_distance_km,
                        'target_duration_sec' => $tWorkout->target_duration_sec,
                        'instructions' => $tWorkout->instructions,
                        'is_key_workout' => $tWorkout->is_key_workout,
                        'status' => WorkoutStatusEnum::SCHEDULED,
                    ]);
                }
            }
        });
    }
}
