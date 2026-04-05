<?php

declare(strict_types=1);

namespace App\Actions\Build;

use App\Enums\ModuleEnum;
use App\Enums\TaskStatusEnum;
use App\Models\BuildTask;
use App\Services\ProgressEngine;
use Illuminate\Support\Facades\DB;

class UpdateTaskStatusAction
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function handle(int $userId, BuildTask $task, array $validated): void
    {
        DB::transaction(function () use ($userId, $task, $validated) {
            $wasNotDone = $task->status->value !== TaskStatusEnum::DONE->value;
            
            $task->update($validated);

            if (isset($validated['status']) && $validated['status'] === TaskStatusEnum::DONE->value && $wasNotDone) {
                $this->progressEngine->addXp($userId, ModuleEnum::BUILD, 20, 'Completed task: ' . $task->title);
            }
        });
    }
}
