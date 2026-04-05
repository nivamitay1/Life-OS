<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RunningPlan;
use App\Models\User;

class RunningPlanPolicy
{
    public function delete(User $user, RunningPlan $plan): bool
    {
        return $user->id === $plan->user_id;
    }
}
