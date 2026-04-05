<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Build\CreateProjectAction;
use App\Actions\Build\UpdateTaskStatusAction;
use App\Enums\ModuleEnum;
use App\Enums\TaskStatusEnum;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\BuildLogResource;
use App\Http\Resources\BuildProjectResource;
use App\Http\Resources\BuildTaskResource;
use App\Models\BuildLog;
use App\Models\BuildProject;
use App\Models\BuildTask;
use App\Services\ProgressEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BuildController extends Controller
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        $projects = BuildProject::forUser($userId)
            ->withCount(['tasks as total_tasks', 'tasks as completed_tasks' => function ($query) {
                $query->where('status', TaskStatusEnum::DONE->value);
            }])
            ->get();

        $tasks = BuildTask::forUser($userId)
            ->where('status', '!=', TaskStatusEnum::DONE->value)
            ->with('project:id,name')
            ->orderBy('is_blocker', 'desc')
            ->take(50)
            ->get();

        $logs = BuildLog::forUser($userId)
            ->with('project:id,name')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Build/Index', [
            'projects' => BuildProjectResource::collection($projects),
            'tasks' => BuildTaskResource::collection($tasks),
            'logs' => BuildLogResource::collection($logs),
            'levelData' => $this->progressEngine->getUserStats($userId)['modules']['Build'] ?? null
        ]);
    }

    public function show(Request $request, BuildProject $project): Response
    {
        $this->authorize('view', $project);
        $tasks = BuildTask::where('build_project_id', $project->id)->orderBy('created_at', 'desc')->get();
        return Inertia::render('Build/Show', [
            'project' => [
                'id' => $project->id, 'name' => $project->name,
                'description' => $project->description,
                'status' => optional($project->status)->value ?? $project->status,
            ],
            'tasks' => $tasks,
            'levelData' => $this->progressEngine->getUserStats($request->user()->id)['modules']['Build'] ?? null
        ]);
    }

    public function storeProject(StoreProjectRequest $request, CreateProjectAction $action): RedirectResponse
    {
        $action->handle($request->user()->id, $request->validated());
        return redirect()->back();
    }

    public function storeTask(StoreTaskRequest $request): RedirectResponse
    {
        BuildTask::create([
            'user_id' => $request->user()->id,
            'build_project_id' => $request->validated()['project_id'],
            'title' => $request->validated()['title'],
            'status' => $request->validated()['status'] ?? TaskStatusEnum::TODO->value,
            'is_blocker' => false
        ]);
        $this->progressEngine->addXp($request->user()->id, ModuleEnum::BUILD, 5, 'Created task');
        return redirect()->back();
    }

    public function updateTask(UpdateTaskRequest $request, BuildTask $task, UpdateTaskStatusAction $action): RedirectResponse
    {
        $this->authorize('update', $task);
        $action->handle($request->user()->id, $task, $request->validated());
        return redirect()->back();
    }

    public function destroyTask(Request $request, BuildTask $task): RedirectResponse
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->back();
    }

    public function seedBlueprint(Request $request): RedirectResponse
    {
        $userId = $request->user()->id;
        $project = BuildProject::create([
            'user_id' => $userId, 'name' => 'SaaS Launchpad',
            'description' => 'A starter blueprint for building out a full-stack SaaS.',
            'status' => \App\Enums\ProjectStatusEnum::ACTIVE->value,
        ]);
        $tasks = [
            ['title' => 'Design Database Schema', 'status' => TaskStatusEnum::DONE->value, 'is_blocker' => false],
            ['title' => 'Scaffold Authentication structure', 'status' => TaskStatusEnum::IN_PROGRESS->value, 'is_blocker' => true],
            ['title' => 'Build Main App Layout', 'status' => TaskStatusEnum::TODO->value, 'is_blocker' => false],
            ['title' => 'Integrate Stripe Billing', 'status' => TaskStatusEnum::TODO->value, 'is_blocker' => false],
            ['title' => 'Draft Landing Page Copy', 'status' => TaskStatusEnum::TODO->value, 'is_blocker' => false],
        ];
        foreach ($tasks as $t) {
            BuildTask::create(['user_id' => $userId, 'build_project_id' => $project->id, ...$t]);
        }
        $this->progressEngine->addXp($userId, ModuleEnum::BUILD, 50, 'Deployed SaaS Launchpad Blueprint');
        return redirect()->back();
    }
}
