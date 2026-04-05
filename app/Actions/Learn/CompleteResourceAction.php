<?php

declare(strict_types=1);

namespace App\Actions\Learn;

use App\Enums\LearnResourceStatusEnum;
use App\Enums\ModuleEnum;
use App\Models\LearnResource;
use App\Models\LearnSession;
use App\Services\ProgressEngine;

class CompleteResourceAction
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    /**
     * Mark a Learn resource as completed, optionally recording a final reflection.
     *
     * Awards a flat 50 XP bonus for completing a resource.
     */
    public function handle(int $userId, LearnResource $resource, array $validated): void
    {
        if ($resource->status === LearnResourceStatusEnum::COMPLETED->value) {
            return;
        }

        if (!empty($validated['reflection_notes'])) {
            LearnSession::create([
                'user_id' => $userId,
                'learn_resource_id' => $resource->id,
                'duration_minutes' => 0,
                'units_completed' => 0,
                'notes' => "FINAL REFLECTION:\n" . $validated['reflection_notes'],
                'date' => $validated['date'],
            ]);
        }

        $resource->update([
            'status' => LearnResourceStatusEnum::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $this->progressEngine->addXp(
            $userId,
            ModuleEnum::LEARN,
            50,
            "Completed Resource: {$resource->title}",
        );
    }
}
