<?php

declare(strict_types=1);

namespace App\Actions\Money;

use App\Enums\EntryTypeEnum;
use App\Enums\ModuleEnum;
use App\Models\MoneyEntry;
use App\Models\MoneyGoal;
use App\Services\ProgressEngine;
use Illuminate\Support\Facades\DB;

class FundGoalAction
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function handle(int $userId, MoneyGoal $goal, float $addAmount): void
    {
        DB::transaction(function () use ($userId, $goal, $addAmount) {
            $goal->current_amount += $addAmount;
            $goal->save();

            MoneyEntry::create([
                'user_id' => $userId,
                'type' => EntryTypeEnum::EXPENSE->value,
                'amount' => -abs($addAmount),
                'currency' => 'USD',
                'date' => now()->toDateString(),
                'description' => 'Funded Goal: ' . $goal->name,
            ]);

            $this->progressEngine->addXp($userId, ModuleEnum::MONEY, 25, 'Saved towards goal: ' . $goal->name);
        });
    }
}
