<?php

declare(strict_types=1);

namespace App\Actions\Build;

use App\Enums\ModuleEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\TaskStatusEnum;
use App\Models\BuildProject;
use App\Models\BuildTask;
use App\Services\ProgressEngine;
use Illuminate\Support\Facades\DB;

class CreateProjectAction
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function handle(int $userId, array $validated): void
    {
        DB::transaction(function () use ($userId, $validated) {
            $project = BuildProject::create([
                'user_id' => $userId,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => ProjectStatusEnum::ACTIVE->value,
            ]);

            if (!empty($validated['template'])) {
                $tasks = [];
                if ($validated['template'] === 'software') {
                    $tasks = ['Define Scope & Architecture', 'Scaffold Database', 'Build Core API', 'Implement UI', 'Deploy to Production'];
                } elseif ($validated['template'] === 'content') {
                    $tasks = ['Research Topic', 'Write Outline', 'Draft Content', 'Edit and Polish', 'Publish'];
                }
                
                foreach ($tasks as $title) {
                    BuildTask::create([
                        'user_id' => $userId,
                        'build_project_id' => $project->id,
                        'title' => $title,
                        'status' => TaskStatusEnum::TODO->value,
                        'is_blocker' => false
                    ]);
                }
            }

            $this->progressEngine->addXp($userId, ModuleEnum::BUILD, 50, 'Started new project');
        });
    }
}
