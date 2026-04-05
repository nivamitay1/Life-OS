<?php

declare(strict_types=1);

namespace App\Actions\Learn;

use App\Enums\LearnResourceStatusEnum;
use App\Models\LearnResource;

class TrackResourceAction
{
    /**
     * Create a new Learn resource for tracking.
     */
    public function handle(int $userId, array $validated): LearnResource
    {
        return LearnResource::create([
            ...$validated,
            'user_id' => $userId,
            'status' => LearnResourceStatusEnum::ACTIVE->value,
        ]);
    }
}
