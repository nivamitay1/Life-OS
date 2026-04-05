<?php

declare(strict_types=1);

namespace App\Actions\Running;

use App\Models\RunningPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\PlanStatusEnum;

class CreateCustomPlanAction
{
    public function handle(int $userId, array $validated): void
    {
        DB::transaction(function () use ($userId, $validated) {
            $startDate = Carbon::now()->startOfWeek();

            $plan = RunningPlan::create([
                'user_id' => $userId,
                'name' => $validated['name'],
                'goal_type' => $validated['goal_type'] ?? null,
                'start_date' => $startDate,
                'end_date' => $startDate->copy()->addWeeks((int)$validated['duration_weeks'] - 1)->endOfWeek(),
                'status' => PlanStatusEnum::ACTIVE,
                'started_at' => now(),
            ]);

            for ($w = 1; $w <= (int)$validated['duration_weeks']; $w++) {
                $weekStart = $startDate->copy()->addWeeks($w - 1);
                $plan->weeks()->create([
                    'week_number' => $w,
                    'start_date' => $weekStart,
                    'end_date' => $weekStart->copy()->endOfWeek(),
                    'theme_label' => 'Training Week ' . $w
                ]);
            }
        });
    }
}
