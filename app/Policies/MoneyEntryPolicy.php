<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MoneyEntry;
use App\Models\User;

class MoneyEntryPolicy
{
    public function delete(User $user, MoneyEntry $entry): bool
    {
        return $user->id === $entry->user_id;
    }
}
