<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MoneyGoal;
use App\Models\User;

class MoneyGoalPolicy
{
    public function update(User $user, MoneyGoal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    public function delete(User $user, MoneyGoal $goal): bool
    {
        return $user->id === $goal->user_id;
    }
}
