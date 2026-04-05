<?php

declare(strict_types=1);

namespace App\Actions\Learn;

use App\Enums\ModuleEnum;
use App\Models\LearnResource;
use App\Models\LearnSession;
use App\Services\ProgressEngine;

class LogSessionAction
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    /**
     * Log a study session and award XP if the session meets the minimum duration.
     *
     * Anti-spam: sessions under 10 minutes receive no XP.
     * XP formula: base(3) + floor(minutes / 5), capped at 20.
     */
    public function handle(int $userId, array $validated): LearnSession
    {
        $resource = LearnResource::forUser($userId)
            ->findOrFail($validated['learn_resource_id']);

        $session = LearnSession::create([
            ...$validated,
            'user_id' => $userId,
        ]);

        $mins = $session->duration_minutes;

        if ($mins >= 10) {
            $baseXp = 3;
            $durationXp = intval($mins / 5);
            $totalXp = min(20, $baseXp + $durationXp);

            $this->progressEngine->addXp(
                $userId, ModuleEnum::LEARN, $totalXp,
                "Study session: {$resource->title}",
            );
        }

        return $session;
    }
}
