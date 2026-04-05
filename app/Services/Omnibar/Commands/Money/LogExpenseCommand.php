<?php

declare(strict_types=1);

namespace App\Services\Omnibar\Commands\Money;

use App\Enums\ModuleEnum;
use App\Models\MoneyEntry;
use App\Models\User;
use App\Services\Omnibar\Contracts\OmnibarCommand;
use App\Services\Omnibar\OmnibarAction;
use App\Services\Omnibar\OmnibarResult;
use App\Services\ProgressEngine;

class LogExpenseCommand implements OmnibarCommand
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function pattern(): string
    {
        return '/^spent\s+\$?(\d+(?:\.\d{2})?)\s+on\s+(.+)$/i';
    }

    public function preview(array $matches): string
    {
        return "Will log expense: \${$matches[1]} · {$matches[2]}";
    }

    public function handle(User $user, array $matches): OmnibarResult
    {
        $amount = -1 * abs((float) $matches[1]);
        $desc = trim($matches[2]);

        $entry = MoneyEntry::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'description' => $desc,
            'date' => now()->toDateString(),
            'type' => 'expense',
        ]);

        $this->progressEngine->addXp($user->id, ModuleEnum::MONEY, 5, 'Quick logging');

        return new OmnibarResult(
            action: OmnibarAction::MONEY_INSERT,
            data: ['entry_id' => $entry->id],
            message: 'Action Understood & Completed',
            detail: 'Logged Expense: $' . abs($amount) . ' for ' . $desc,
        );
    }
}
