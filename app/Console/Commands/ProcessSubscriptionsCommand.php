<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ModuleEnum;
use App\Models\MoneySubscription;
use App\Models\MoneyEntry;
use App\Enums\EntryTypeEnum;
use App\Services\ProgressEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessSubscriptionsCommand extends Command
{
    protected $signature = 'subscriptions:process';

    protected $description = 'Process due money subscriptions and generate entries';

    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $today = now()->toDateString();

        $dueSubscriptions = MoneySubscription::where('is_active', true)
            ->whereDate('next_billing_date', '<=', $today)
            ->get();

        $processed = 0;

        foreach ($dueSubscriptions as $sub) {
            DB::transaction(function () use ($sub, &$processed) {
                MoneyEntry::create([
                    'user_id' => $sub->user_id,
                    'type' => EntryTypeEnum::EXPENSE->value,
                    'amount' => -abs((float)$sub->amount),
                    'currency' => 'USD',
                    'date' => now()->toDateString(),
                    'description' => 'Subscription: ' . $sub->name,
                ]);

                $this->progressEngine->addXp($sub->user_id, ModuleEnum::MONEY, 5, 'Paid subscription: ' . $sub->name);

                $cycle = $sub->billing_cycle->value ?? $sub->billing_cycle;
                $nextDate = Carbon::parse($sub->next_billing_date);
                
                do {
                    if ($cycle === 'weekly') {
                        $nextDate->addWeek();
                    } elseif ($cycle === 'monthly') {
                        $nextDate->addMonth();
                    } elseif ($cycle === 'yearly') {
                        $nextDate->addYear();
                    }
                } while ($nextDate->toDateString() <= now()->toDateString());

                $sub->next_billing_date = $nextDate;
                $sub->save();
                
                $processed++;
            });
        }

        $this->info("Processed {$processed} subscriptions successfully.");
    }
}
