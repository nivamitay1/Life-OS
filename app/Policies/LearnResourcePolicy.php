<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LearnResource;
use App\Models\User;

class LearnResourcePolicy
{
    public function update(User $user, LearnResource $resource): bool
    {
        return $user->id === $resource->user_id;
    }

    public function delete(User $user, LearnResource $resource): bool
    {
        return $user->id === $resource->user_id;
    }
}
