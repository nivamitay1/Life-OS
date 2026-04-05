<?php

declare(strict_types=1);

namespace App\Actions\Money;

use App\Enums\EntryTypeEnum;
use App\Enums\ModuleEnum;
use App\Models\MoneyEntry;
use App\Services\ProgressEngine;
use Illuminate\Support\Facades\DB;

class LogEntryAction
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function handle(int $userId, array $validated): void
    {
        DB::transaction(function () use ($userId, $validated) {
            $amount = $validated['type'] === EntryTypeEnum::EXPENSE->value
                ? -abs((float)$validated['amount']) 
                : abs((float)$validated['amount']);

            MoneyEntry::create([
                'user_id' => $userId,
                'type' => $validated['type'],
                'amount' => $amount,
                'currency' => 'USD',
                'date' => $validated['date'],
                'description' => $validated['description'],
            ]);

            $this->progressEngine->addXp($userId, ModuleEnum::MONEY, 10, 'Logged a transaction');
        });
    }
}
