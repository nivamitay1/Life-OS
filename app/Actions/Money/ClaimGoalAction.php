<?php

declare(strict_types=1);

namespace App\Actions\Money;

use App\Enums\ModuleEnum;
use App\Models\MoneyGoal;
use App\Services\ProgressEngine;
use Illuminate\Support\Facades\DB;

class ClaimGoalAction
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function handle(int $userId, MoneyGoal $goal): void
    {
        DB::transaction(function () use ($userId, $goal) {
            if ($goal->current_amount >= $goal->target_amount && !$goal->is_claimed) {
                $goal->is_claimed = true;
                $goal->save();

                $this->progressEngine->addXp($userId, ModuleEnum::MONEY, 100, 'Claimed goal: ' . $goal->name);
            }
        });
    }
}
