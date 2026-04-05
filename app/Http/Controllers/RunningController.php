<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Running\CreateCustomPlanAction;
use App\Actions\Running\LogRunAction;
use App\Actions\Running\StartTemplatePlanAction;
use App\Enums\ModuleEnum;
use App\Enums\PlanStatusEnum;
use App\Enums\WorkoutStatusEnum;
use App\Http\Requests\StoreCustomPlanRequest;
use App\Http\Requests\StoreRunningRunRequest;
use App\Http\Requests\StoreWorkoutRequest;
use App\Http\Requests\UpdateWorkoutStatusRequest;
use App\Http\Resources\RunningPlanResource;
use App\Http\Resources\RunningRunResource;
use App\Models\RunningPlan;
use App\Models\RunningPlanTemplate;
use App\Models\RunningPlanWeek;
use App\Models\RunningPlanWorkout;
use App\Models\RunningRun;
use App\Services\ProgressEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RunningController extends Controller
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        $runs = RunningRun::forUser($userId)->orderBy('date', 'desc')->take(5)->get();

        $weeklyMileage = RunningRun::forUser($userId)
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('distance');

        $activePlan = RunningPlan::with(['weeks.workouts' => fn($q) => $q->orderBy('scheduled_date', 'asc')])
            ->forUser($userId)->where('status', PlanStatusEnum::ACTIVE)->first();

        $templates = RunningPlanTemplate::all();

        return Inertia::render('Running/Index', [
            'runs' => RunningRunResource::collection($runs),
            'activePlan' => $activePlan ? new RunningPlanResource($activePlan) : null,
            'templates' => $templates,
            'stats' => ['weeklyMileage' => round((float)$weeklyMileage, 1) . ' km'],
            'levelData' => $this->progressEngine->getUserStats($userId)['modules']['Running'] ?? null,
            'draftDistance' => $request->query('draft_distance')
        ]);
    }

    public function store(StoreRunningRunRequest $request, LogRunAction $action): RedirectResponse
    {
        $action->handle($request->user()->id, $request->validated());
        return redirect()->back();
    }

    public function seedTemplate(Request $request): RedirectResponse
    {
        $template = RunningPlanTemplate::firstOrCreate([
            'name' => 'Couch to 5K BootCamp', 'type' => '5k',
            'experience_level' => 'beginner', 'duration_weeks' => 4
        ]);
        if ($template->templateWeeks()->count() === 0) {
            for ($w = 1; $w <= 4; $w++) {
                $week = $template->templateWeeks()->create(['week_number' => $w, 'theme_label' => "Base Building Week " . $w]);
                $week->templateWorkouts()->create(['day_of_week' => 2, 'workout_type' => 'easy', 'title' => 'Easy Run', 'target_duration_sec' => 1200 + ($w * 300), 'instructions' => 'Run at a conversational pace.', 'is_key_workout' => false]);
                $week->templateWorkouts()->create(['day_of_week' => 4, 'workout_type' => 'interval', 'title' => 'Speed Play', 'target_duration_sec' => 1800, 'instructions' => '10 min warmup. 5 x 1min hard / 1min easy.', 'is_key_workout' => false]);
                $week->templateWorkouts()->create(['day_of_week' => 7, 'workout_type' => 'long', 'title' => 'Long Run', 'target_distance_km' => 3 + $w, 'is_key_workout' => true, 'instructions' => 'Keep it slow and steady.']);
            }
        }
        return redirect()->back();
    }

    public function startPlan(Request $request, RunningPlanTemplate $template, StartTemplatePlanAction $action): RedirectResponse
    {
        $action->handle($request->user()->id, $template);
        return redirect()->back();
    }

    public function storeCustomPlan(StoreCustomPlanRequest $request, CreateCustomPlanAction $action): RedirectResponse
    {
        $action->handle($request->user()->id, $request->validated());
        return redirect()->back();
    }

    public function destroyPlan(RunningPlan $plan): RedirectResponse
    {
        $this->authorize('delete', $plan);
        $plan->delete();
        return redirect()->back();
    }

    public function storeWorkout(StoreWorkoutRequest $request, RunningPlanWeek $week): RedirectResponse
    {
        $validated = $request->validated();
        $validated['status'] = WorkoutStatusEnum::SCHEDULED;
        $week->workouts()->create($validated);
        return redirect()->back();
    }

    public function updateWorkout(StoreWorkoutRequest $request, RunningPlanWorkout $workout): RedirectResponse
    {
        $workout->update($request->validated());
        return redirect()->back();
    }

    public function destroyWorkout(RunningPlanWorkout $workout): RedirectResponse
    {
        $workout->delete();
        return redirect()->back();
    }

    public function updateWorkoutStatus(UpdateWorkoutStatusRequest $request, RunningPlanWorkout $workout): RedirectResponse
    {
        $workout->update(['status' => $request->validated()['status']]);
        
        if ($request->validated()['status'] === WorkoutStatusEnum::COMPLETED->value) {
            $this->progressEngine->addXp($request->user()->id, ModuleEnum::RUNNING, 10, 'Completed scheduled workout manually');
        }

        return redirect()->back();
    }
}
