<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BuildTask;
use App\Models\User;

class BuildTaskPolicy
{
    public function update(User $user, BuildTask $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function delete(User $user, BuildTask $task): bool
    {
        return $user->id === $task->user_id;
    }
}
