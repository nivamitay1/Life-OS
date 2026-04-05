<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PlanStatusEnum;
use App\Enums\WorkoutStatusEnum;
use App\Models\Badge;
use App\Models\BuildTask;
use App\Models\MoneySubscription;
use App\Models\RunningPlanWorkout;
use Carbon\Carbon;

class DashboardAggregator
{
    /**
     * @return array<int, array{id: string, text: string, module: string, isOverdue: bool, actionText: string, url: string, urgency_score: float}>
     */
    public function getPriorities(int $userId): array
    {
        $priorities = collect()
            ->concat($this->getBuildPriorities($userId))
            ->concat($this->getMoneyPriorities($userId))
            ->concat($this->getRunningTodayPriorities($userId))
            ->concat($this->getRunningOverduePriorities($userId));

        return $priorities->sortByDesc('urgency_score')->values()->toArray();
    }

    /**
     * @return array<int, array{name: string, module: string, date: string, icon: string, color: string}>
     */
    public function getBadges(int $userId): array
    {
        $badges = Badge::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(fn ($b) => [
                'name' => $b->name, 'module' => $b->module_slug,
                'date' => $b->created_at->diffForHumans(),
                'icon' => $b->icon ?? '🏆',
                'color' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
            ])
            ->toArray();

        if (empty($badges)) {
            $badges = [[
                'name' => 'First Login', 'module' => 'System', 'date' => 'Just now',
                'icon' => '👋', 'color' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400',
            ]];
        }

        return $badges;
    }

    private function getBuildPriorities(int $userId): \Illuminate\Support\Collection
    {
        return BuildTask::forUser($userId)
            ->where('status', '!=', 'done')
            ->orderBy('is_blocker', 'desc')->orderBy('created_at', 'asc')
            ->take(3)->get()
            ->map(fn ($task) => [
                'id' => 'build_' . $task->id, 'text' => $task->text, 'module' => 'Build',
                'isOverdue' => (bool) $task->is_blocker, 'actionText' => 'Complete Task',
                'url' => route('build.project.show', $task->build_project_id),
                'urgency_score' => $task->is_blocker ? 100 : 40 - ($task->id * 0.001),
            ]);
    }

    private function getMoneyPriorities(int $userId): \Illuminate\Support\Collection
    {
        return MoneySubscription::forUser($userId)
            ->where('is_active', true)
            ->whereDate('next_billing_date', '<=', now()->addDays(3)->toDateString())
            ->get()
            ->map(function ($sub) {
                $isOverdue = $sub->next_billing_date < now()->toDateString();
                $isToday = $sub->next_billing_date === now()->toDateString();
                $diffDays = Carbon::parse($sub->next_billing_date)->startOfDay()->diffInDays(now()->startOfDay());
                $score = $isOverdue ? 90 + $diffDays : ($isToday ? 85 : 50 - $diffDays);
                return [
                    'id' => 'sub_' . $sub->id, 'text' => 'Approaching Bill: ' . $sub->name . ' ($' . $sub->amount . ')',
                    'module' => 'Money', 'isOverdue' => $isOverdue || $isToday, 'actionText' => 'View Ledger',
                    'url' => route('money.index'), 'urgency_score' => $score,
                ];
            });
    }

    private function getRunningTodayPriorities(int $userId): \Illuminate\Support\Collection
    {
        return RunningPlanWorkout::whereHas('runningPlanWeek.runningPlan', fn($q) => $q->where('user_id', $userId)->where('status', PlanStatusEnum::ACTIVE))
            ->where('status', WorkoutStatusEnum::SCHEDULED)
            ->whereDate('scheduled_date', now()->toDateString())
            ->get()
            ->map(fn ($w) => [
                'id' => 'run_today_' . $w->id, 'text' => "Today's Workout: " . $w->title, 'module' => 'Running',
                'isOverdue' => false, 'actionText' => 'Open Calendar', 'url' => route('running.index'),
                'urgency_score' => $w->is_key_workout ? 88 : 75,
            ]);
    }

    private function getRunningOverduePriorities(int $userId): \Illuminate\Support\Collection
    {
        return RunningPlanWorkout::whereHas('runningPlanWeek.runningPlan', fn($q) => $q->where('user_id', $userId)->where('status', PlanStatusEnum::ACTIVE))
            ->where('status', WorkoutStatusEnum::SCHEDULED)
            ->where('is_key_workout', true)
            ->whereDate('scheduled_date', '<', now()->toDateString())
            ->whereDate('scheduled_date', '>=', now()->subDays(7)->toDateString())
            ->orderBy('scheduled_date', 'desc')->take(2)->get()
            ->map(function ($w) {
                $daysOverdue = Carbon::parse($w->scheduled_date)->startOfDay()->diffInDays(now()->startOfDay());
                return [
                    'id' => 'run_overdue_' . $w->id, 'text' => 'Missed Key Workout: ' . $w->title,
                    'module' => 'Running', 'isOverdue' => true, 'actionText' => 'Reschedule / Do',
                    'url' => route('running.index'), 'urgency_score' => 95 - $daysOverdue,
                ];
            });
    }
}
