<?php

namespace Database\Seeders;

use App\Enums\BillingCycleEnum;
use App\Enums\EntryTypeEnum;
use App\Enums\GoalColorEnum;
use App\Enums\LearnResourceStatusEnum;
use App\Enums\ModuleEnum;
use App\Enums\PlanStatusEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\TaskStatusEnum;
use App\Enums\WorkoutStatusEnum;
use App\Enums\WorkoutTypeEnum;
use App\Models\Badge;
use App\Models\BuildLog;
use App\Models\BuildProject;
use App\Models\BuildTask;
use App\Models\LearnResource;
use App\Models\LearnSession;
use App\Models\MoneyEntry;
use App\Models\MoneyGoal;
use App\Models\MoneySubscription;
use App\Models\RunningPlan;
use App\Models\RunningRun;
use App\Models\User;
use App\Models\XpEvent;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $today = CarbonImmutable::today();

        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->seedMoney($user, $today);
        $this->seedBuild($user, $today);
        $this->seedRunning($user, $today);
        $this->seedLearn($user, $today);
        $this->seedProgress($user, $today);
    }

    private function seedMoney(User $user, CarbonImmutable $today): void
    {
        $this->createRecords(MoneySubscription::class, [
            [
                'user_id' => $user->id,
                'name' => 'Spotify Premium',
                'amount' => 10.99,
                'billing_cycle' => BillingCycleEnum::MONTHLY->value,
                'next_billing_date' => $today->addDays(8),
                'is_active' => true,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Linear',
                'amount' => 8.00,
                'billing_cycle' => BillingCycleEnum::MONTHLY->value,
                'next_billing_date' => $today->addDays(12),
                'is_active' => true,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Hetzner Cloud',
                'amount' => 14.00,
                'billing_cycle' => BillingCycleEnum::MONTHLY->value,
                'next_billing_date' => $today->addDays(16),
                'is_active' => true,
            ],
        ]);

        $this->createRecords(MoneyGoal::class, [
            [
                'user_id' => $user->id,
                'name' => 'Emergency Fund',
                'target_amount' => 12000,
                'current_amount' => 4600,
                'target_date' => $today->addMonths(10),
                'color' => GoalColorEnum::EMERALD->value,
                'is_claimed' => false,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Berlin Marathon Trip',
                'target_amount' => 2500,
                'current_amount' => 875,
                'target_date' => $today->addMonths(6),
                'color' => GoalColorEnum::BLUE->value,
                'is_claimed' => false,
            ],
        ]);

        $this->createRecords(MoneyEntry::class, [
            $this->moneyEntry($user, EntryTypeEnum::INCOME, 4200, $today->startOfMonth()->addDays(1), 'Monthly salary'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 1450, $today->startOfMonth()->addDays(2), 'Rent'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 82.40, $today->startOfMonth()->addDays(3), 'Groceries at Shuk HaCarmel'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 10.99, $today->startOfMonth()->addDays(4), 'Spotify Premium renewal'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 34.20, $today->subDays(10), 'Coffee meetings and snacks'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 59.00, $today->subDays(9), 'Running shoes installment'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 14.00, $today->subDays(8), 'Hetzner Cloud invoice'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 18.50, $today->subDays(7), 'Train ticket to long-run route'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 96.00, $today->subDays(6), 'Gym quarterly top-up'),
            $this->moneyEntry($user, EntryTypeEnum::INCOME, 250.00, $today->subDays(5), 'Freelance landing page deposit'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 42.90, $today->subDays(4), 'Team dinner after sprint review'),
            $this->moneyEntry($user, EntryTypeEnum::EXPENSE, 65.00, $today->subDays(2), 'Book order and notebook restock'),
        ]);
    }

    private function seedBuild(User $user, CarbonImmutable $today): void
    {
        $lifeOsProject = BuildProject::query()->create([
            'user_id' => $user->id,
            'name' => 'Life OS Mobile MVP',
            'description' => 'Ship a mobile-first version of the dashboard, quick logging, and weekly review flows.',
            'status' => ProjectStatusEnum::ACTIVE->value,
        ]);

        $this->createRecords(BuildTask::class, [
            $this->buildTask($user, $lifeOsProject, 'Design mobile onboarding flow', TaskStatusEnum::DONE, false, $today->subDays(11)),
            $this->buildTask($user, $lifeOsProject, 'Implement recurring subscription cards', TaskStatusEnum::IN_PROGRESS, false),
            $this->buildTask($user, $lifeOsProject, 'Fix training plan completion mismatch', TaskStatusEnum::IN_PROGRESS, true),
            $this->buildTask($user, $lifeOsProject, 'Add dashboard streak widgets', TaskStatusEnum::TODO, false),
            $this->buildTask($user, $lifeOsProject, 'Polish empty states for Learn module', TaskStatusEnum::TODO, false),
        ]);

        $this->createRecords(BuildLog::class, [
            [
                'user_id' => $user->id,
                'build_project_id' => $lifeOsProject->id,
                'type' => 'decision',
                'content' => 'Chose a bottom-tab navigation for mobile so logging a run or expense is reachable in one tap.',
            ],
            [
                'user_id' => $user->id,
                'build_project_id' => $lifeOsProject->id,
                'type' => 'progress',
                'content' => 'Finished the first pass of onboarding copy and connected the dashboard cards to live progress totals.',
            ],
            [
                'user_id' => $user->id,
                'build_project_id' => $lifeOsProject->id,
                'type' => 'progress',
                'content' => 'Verified seeded demo data across Money, Running, Build, and Learn to make the dashboard feel realistic.',
            ],
        ]);

        $financeProject = BuildProject::query()->create([
            'user_id' => $user->id,
            'name' => 'Personal Finance Dashboard',
            'description' => 'A focused side project for weekly cashflow review, recurring costs, and savings goal pacing.',
            'status' => ProjectStatusEnum::COMPLETED->value,
        ]);

        $this->createRecords(BuildTask::class, [
            $this->buildTask($user, $financeProject, 'Define monthly cashflow summary widgets', TaskStatusEnum::DONE, false, $today->subDays(28)),
            $this->buildTask($user, $financeProject, 'Build recurring subscription table', TaskStatusEnum::DONE, false, $today->subDays(24)),
            $this->buildTask($user, $financeProject, 'Add savings goal progress rings', TaskStatusEnum::DONE, false, $today->subDays(19)),
        ]);

        BuildLog::query()->create([
            'user_id' => $user->id,
            'build_project_id' => $financeProject->id,
            'type' => 'release_note',
            'content' => 'Shipped v1 with salary, rent, subscriptions, and goal tracking for a complete monthly review loop.',
        ]);
    }

    private function seedRunning(User $user, CarbonImmutable $today): void
    {
        $planStart = $today->startOfWeek()->subWeeks(3);

        $plan = RunningPlan::query()->create([
            'user_id' => $user->id,
            'template_id' => null,
            'name' => 'Berlin Marathon Base Block',
            'goal_type' => 'marathon',
            'status' => PlanStatusEnum::ACTIVE->value,
            'start_date' => $planStart,
            'end_date' => $planStart->addWeeks(5)->endOfWeek(),
            'started_at' => $planStart->startOfDay(),
        ]);

        $weekBlueprints = [
            [
                'theme_label' => 'Aerobic Foundation',
                'workouts' => [
                    ['day_offset' => 1, 'type' => WorkoutTypeEnum::EASY, 'title' => 'Easy Run', 'distance' => 6.2, 'duration' => null, 'status' => WorkoutStatusEnum::COMPLETED, 'instructions' => 'Keep it conversational and relaxed before work.'],
                    ['day_offset' => 3, 'type' => WorkoutTypeEnum::WORKOUT, 'title' => 'Threshold Repeats', 'distance' => 8.0, 'duration' => null, 'status' => WorkoutStatusEnum::COMPLETED, 'instructions' => '3 x 1 km threshold reps with short recovery.'],
                    ['day_offset' => 6, 'type' => WorkoutTypeEnum::LONG, 'title' => 'Long Run', 'distance' => 14.5, 'duration' => null, 'status' => WorkoutStatusEnum::COMPLETED, 'instructions' => 'Steady effort on rolling terrain.'],
                ],
            ],
            [
                'theme_label' => 'Volume Extension',
                'workouts' => [
                    ['day_offset' => 1, 'type' => WorkoutTypeEnum::EASY, 'title' => 'Recovery Run', 'distance' => 5.0, 'duration' => null, 'status' => WorkoutStatusEnum::COMPLETED, 'instructions' => 'Legs were heavy, so keep the effort low.'],
                    ['day_offset' => 4, 'type' => WorkoutTypeEnum::INTERVAL, 'title' => '800m Session', 'distance' => 9.1, 'duration' => null, 'status' => WorkoutStatusEnum::COMPLETED, 'instructions' => '6 x 800m with float recoveries.'],
                    ['day_offset' => 6, 'type' => WorkoutTypeEnum::LONG, 'title' => 'Progressive Long Run', 'distance' => 16.2, 'duration' => null, 'status' => WorkoutStatusEnum::COMPLETED, 'instructions' => 'Finish the final 3 km a little quicker.'],
                ],
            ],
            [
                'theme_label' => 'Controlled Build',
                'workouts' => [
                    ['day_offset' => 1, 'type' => WorkoutTypeEnum::EASY, 'title' => 'Easy Run', 'distance' => 7.4, 'duration' => null, 'status' => WorkoutStatusEnum::COMPLETED, 'instructions' => 'Evening shakeout with a short hill finish.'],
                    ['day_offset' => 3, 'type' => WorkoutTypeEnum::WORKOUT, 'title' => 'Tempo Sandwich', 'distance' => 10.0, 'duration' => null, 'status' => WorkoutStatusEnum::SKIPPED, 'instructions' => '20 min tempo in the middle of the run.'],
                    ['day_offset' => 6, 'type' => WorkoutTypeEnum::LONG, 'title' => 'Long Run', 'distance' => 18.0, 'duration' => null, 'status' => WorkoutStatusEnum::PARTIALLY_COMPLETED, 'instructions' => 'Cap the effort if fatigue is still present.'],
                ],
            ],
            [
                'theme_label' => 'Current Base Week',
                'workouts' => [
                    ['day_offset' => 1, 'type' => WorkoutTypeEnum::EASY, 'title' => 'Easy Aerobic Run', 'distance' => 8.0, 'duration' => null, 'status' => WorkoutStatusEnum::SCHEDULED, 'instructions' => 'Keep cadence smooth and relaxed.'],
                    ['day_offset' => 3, 'type' => WorkoutTypeEnum::WORKOUT, 'title' => 'Cruise Intervals', 'distance' => null, 'duration' => 3000, 'status' => WorkoutStatusEnum::SCHEDULED, 'instructions' => '15 min warmup, 4 x 5 min threshold, 10 min cooldown.'],
                    ['day_offset' => 6, 'type' => WorkoutTypeEnum::LONG, 'title' => 'Weekend Long Run', 'distance' => 19.0, 'duration' => null, 'status' => WorkoutStatusEnum::SCHEDULED, 'instructions' => 'Practice fueling every 30 minutes.'],
                ],
            ],
            [
                'theme_label' => 'Steady Progression',
                'workouts' => [
                    ['day_offset' => 1, 'type' => WorkoutTypeEnum::EASY, 'title' => 'Easy Run', 'distance' => 8.5, 'duration' => null, 'status' => WorkoutStatusEnum::SCHEDULED, 'instructions' => 'Keep this fully aerobic.'],
                    ['day_offset' => 3, 'type' => WorkoutTypeEnum::INTERVAL, 'title' => 'Hill Repeats', 'distance' => null, 'duration' => 2700, 'status' => WorkoutStatusEnum::SCHEDULED, 'instructions' => '10 x 60 sec uphill, jog back down.'],
                    ['day_offset' => 6, 'type' => WorkoutTypeEnum::LONG, 'title' => 'Long Run', 'distance' => 20.0, 'duration' => null, 'status' => WorkoutStatusEnum::SCHEDULED, 'instructions' => 'Finish feeling like you could run 2 km more.'],
                ],
            ],
            [
                'theme_label' => 'Deload Absorption',
                'workouts' => [
                    ['day_offset' => 1, 'type' => WorkoutTypeEnum::EASY, 'title' => 'Recovery Run', 'distance' => 6.0, 'duration' => null, 'status' => WorkoutStatusEnum::SCHEDULED, 'instructions' => 'Short and easy. Do not push the pace.'],
                    ['day_offset' => 3, 'type' => WorkoutTypeEnum::WORKOUT, 'title' => 'Short Tempo', 'distance' => null, 'duration' => 2100, 'status' => WorkoutStatusEnum::SCHEDULED, 'instructions' => 'Controlled 15-minute tempo effort.'],
                    ['day_offset' => 6, 'type' => WorkoutTypeEnum::LONG, 'title' => 'Cut-Back Long Run', 'distance' => 14.0, 'duration' => null, 'status' => WorkoutStatusEnum::SCHEDULED, 'instructions' => 'Keep it gentle and enjoy the lower volume.'],
                ],
            ],
        ];

        foreach ($weekBlueprints as $index => $weekBlueprint) {
            $weekStart = $planStart->addWeeks($index);
            $week = $plan->weeks()->create([
                'week_number' => $index + 1,
                'start_date' => $weekStart,
                'end_date' => $weekStart->endOfWeek(),
                'theme_label' => $weekBlueprint['theme_label'],
            ]);

            foreach ($weekBlueprint['workouts'] as $workoutBlueprint) {
                $week->workouts()->create([
                    'scheduled_date' => $weekStart->addDays($workoutBlueprint['day_offset']),
                    'workout_type' => $workoutBlueprint['type']->value,
                    'title' => $workoutBlueprint['title'],
                    'target_distance_km' => $workoutBlueprint['distance'],
                    'target_duration_sec' => $workoutBlueprint['duration'],
                    'instructions' => $workoutBlueprint['instructions'],
                    'is_key_workout' => $workoutBlueprint['type'] === WorkoutTypeEnum::LONG,
                    'status' => $workoutBlueprint['status']->value,
                ]);
            }
        }

        $this->createRecords(RunningRun::class, [
            $this->runningRun($user, $today->subDays(20), 6.20, 1860, WorkoutTypeEnum::EASY, 4, 'Kept it conversational and relaxed before work.'),
            $this->runningRun($user, $today->subDays(17), 8.00, 2860, WorkoutTypeEnum::WORKOUT, 7, '3 x 1 km threshold reps felt controlled.'),
            $this->runningRun($user, $today->subDays(13), 14.50, 5220, WorkoutTypeEnum::LONG, 6, 'Long run on the park loop, steady the whole way.'),
            $this->runningRun($user, $today->subDays(10), 5.00, 1500, WorkoutTypeEnum::EASY, 3, 'Legs were heavy, kept the effort low.'),
            $this->runningRun($user, $today->subDays(7), 9.10, 3380, WorkoutTypeEnum::INTERVAL, 8, '6 x 800m session with strong final rep.'),
            $this->runningRun($user, $today->subDays(4), 7.40, 2475, WorkoutTypeEnum::EASY, 5, 'Evening shakeout with a short hill finish.'),
            $this->runningRun($user, $today->subDay(), 16.20, 6120, WorkoutTypeEnum::LONG, 7, 'Practiced fueling and finished the last 3 km faster.'),
        ]);
    }

    private function seedLearn(User $user, CarbonImmutable $today): void
    {
        $book = LearnResource::query()->create([
            'user_id' => $user->id,
            'type' => 'book',
            'title' => 'Deep Work',
            'author' => 'Cal Newport',
            'status' => LearnResourceStatusEnum::ACTIVE->value,
            'total_units' => 17,
            'current_unit' => 0,
            'unit_label' => 'chapter',
            'started_at' => $today->subWeeks(3),
        ]);

        $this->createRecords(LearnSession::class, [
            $this->learnSession($user, $book, $today->subDays(16), 35, 2, 'Chapter 2', 'Strong framing on focus as a skill.'),
            $this->learnSession($user, $book, $today->subDays(12), 40, 2, 'Chapter 4', 'Marked a few ideas to adapt for morning work blocks.'),
            $this->learnSession($user, $book, $today->subDays(6), 30, 1, 'Chapter 5', 'Short session before bed, still progressing steadily.'),
        ]);

        $course = LearnResource::query()->create([
            'user_id' => $user->id,
            'type' => 'course',
            'title' => 'Laravel Performance Patterns',
            'author' => 'Laravel Daily',
            'status' => LearnResourceStatusEnum::COMPLETED->value,
            'total_units' => 12,
            'current_unit' => 0,
            'unit_label' => 'lesson',
        ]);

        $this->createRecords(LearnSession::class, [
            $this->learnSession($user, $course, $today->subDays(21), 50, 4, 'Lesson 4', 'Useful refresher on eager loading and query shape.'),
            $this->learnSession($user, $course, $today->subDays(18), 45, 4, 'Lesson 8', 'Captured a few notes for API resource optimization.'),
            $this->learnSession($user, $course, $today->subDays(15), 55, 4, 'Lesson 12', 'Finished the course and queued cleanup ideas for the app.'),
        ]);

        $course->forceFill([
            'completed_at' => $today->subDays(15),
            'last_session_at' => $today->subDays(15),
        ])->saveQuietly();

        LearnResource::query()->create([
            'user_id' => $user->id,
            'type' => 'podcast',
            'title' => 'The Knowledge Project: Systems vs. Goals',
            'author' => 'Shane Parrish',
            'status' => LearnResourceStatusEnum::ACTIVE->value,
            'total_units' => 1,
            'current_unit' => 0,
            'unit_label' => 'episode',
            'started_at' => $today->subDays(3),
            'last_session_at' => $today->subDays(3),
            'current_position_label' => '18 min',
        ]);
    }

    private function seedProgress(User $user, CarbonImmutable $today): void
    {
        $this->createRecords(XpEvent::class, [
            $this->xpEvent($user, ModuleEnum::BUILD, 40, 'Shipped the Personal Finance Dashboard milestone.'),
            $this->xpEvent($user, ModuleEnum::RUNNING, 30, 'Completed the weekend long run.'),
            $this->xpEvent($user, ModuleEnum::LEARN, 20, 'Finished Laravel Performance Patterns.'),
            $this->xpEvent($user, ModuleEnum::MONEY, 15, 'Logged salary, rent, and recurring costs for the month.'),
            $this->xpEvent($user, ModuleEnum::BUILD, 10, 'Closed onboarding design tasks for the mobile MVP.'),
            $this->xpEvent($user, ModuleEnum::LEARN, 10, 'Maintained a consistent reading streak in Deep Work.'),
        ]);

        $this->createRecords(Badge::class, [
            [
                'user_id' => $user->id,
                'name' => 'Budget Steward',
                'module_slug' => ModuleEnum::MONEY->value,
                'icon' => '💸',
            ],
            [
                'user_id' => $user->id,
                'name' => 'Marathon Base Builder',
                'module_slug' => ModuleEnum::RUNNING->value,
                'icon' => '🏃',
            ],
            [
                'user_id' => $user->id,
                'name' => 'Momentum Maker',
                'module_slug' => ModuleEnum::BUILD->value,
                'icon' => '🚀',
            ],
        ]);
    }

    private function createRecords(string $modelClass, array $records): void
    {
        foreach ($records as $record) {
            $modelClass::query()->create($record);
        }
    }

    private function moneyEntry(
        User $user,
        EntryTypeEnum $type,
        float $amount,
        CarbonImmutable $date,
        string $description
    ): array {
        return [
            'user_id' => $user->id,
            'type' => $type->value,
            'amount' => $amount,
            'currency' => 'USD',
            'date' => $date,
            'description' => $description,
        ];
    }

    private function buildTask(
        User $user,
        BuildProject $project,
        string $title,
        TaskStatusEnum $status,
        bool $isBlocker,
        ?CarbonImmutable $completedAt = null
    ): array {
        return [
            'user_id' => $user->id,
            'build_project_id' => $project->id,
            'title' => $title,
            'status' => $status->value,
            'is_blocker' => $isBlocker,
            'completed_at' => $completedAt,
        ];
    }

    private function runningRun(
        User $user,
        CarbonImmutable $date,
        float $distanceKm,
        int $durationSeconds,
        WorkoutTypeEnum $type,
        int $effort,
        string $notes
    ): array {
        return [
            'user_id' => $user->id,
            'date' => $date,
            'distance' => $distanceKm,
            'duration' => $durationSeconds,
            'pace' => round($durationSeconds / $distanceKm, 2),
            'type' => $type->value,
            'effort' => $effort,
            'notes' => $notes,
        ];
    }

    private function learnSession(
        User $user,
        LearnResource $resource,
        CarbonImmutable $date,
        int $durationMinutes,
        int $unitsCompleted,
        string $endingPositionLabel,
        string $notes
    ): array {
        return [
            'user_id' => $user->id,
            'learn_resource_id' => $resource->id,
            'duration_minutes' => $durationMinutes,
            'units_completed' => $unitsCompleted,
            'ending_position_label' => $endingPositionLabel,
            'notes' => $notes,
            'date' => $date,
        ];
    }

    private function xpEvent(User $user, ModuleEnum $module, int $amount, string $reason): array
    {
        return [
            'user_id' => $user->id,
            'module_slug' => $module->value,
            'amount' => $amount,
            'reason' => $reason,
        ];
    }
}
