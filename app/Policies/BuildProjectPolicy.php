<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BuildProject;
use App\Models\User;

class BuildProjectPolicy
{
    public function view(User $user, BuildProject $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function update(User $user, BuildProject $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function delete(User $user, BuildProject $project): bool
    {
        return $user->id === $project->user_id;
    }
}
