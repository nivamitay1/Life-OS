<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Money\ClaimGoalAction;
use App\Actions\Money\FundGoalAction;
use App\Actions\Money\LogEntryAction;
use App\Enums\ModuleEnum;
use App\Http\Requests\StoreMoneyEntryRequest;
use App\Http\Requests\StoreMoneyGoalRequest;
use App\Http\Requests\UpdateMoneyGoalRequest;
use App\Http\Requests\StoreMoneySubscriptionRequest;
use App\Http\Resources\MoneyEntryResource;
use App\Http\Resources\MoneyGoalResource;
use App\Http\Resources\MoneySubscriptionResource;
use App\Models\MoneyEntry;
use App\Models\MoneyGoal;
use App\Models\MoneySubscription;
use App\Services\ProgressEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MoneyController extends Controller
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        
        $entries = MoneyEntry::forUser($userId)
            ->orderBy('date', 'desc')
            ->take(50)
            ->get();

        $subscriptions = MoneySubscription::forUser($userId)->get();
        $goals = MoneyGoal::forUser($userId)->get();

        $totalBalance = MoneyEntry::forUser($userId)->sum('amount');
        
        $monthlyExpenses = MoneyEntry::forUser($userId)
            ->where('amount', '<', 0)
            ->whereMonth('date', now()->month)
            ->sum('amount');

        $monthlyIncome = MoneyEntry::forUser($userId)
            ->where('amount', '>', 0)
            ->whereMonth('date', now()->month)
            ->sum('amount');

        $monthlyFixedExpenses = $subscriptions->where('is_active', true)->sum(function ($sub) {
            $cycle = $sub->billing_cycle->value ?? $sub->billing_cycle;
            return match ($cycle) {
                'weekly' => $sub->amount * 4.33,
                'yearly' => $sub->amount / 12,
                default => $sub->amount,
            };
        });

        return Inertia::render('Money/Index', [
            'entries' => MoneyEntryResource::collection($entries),
            'subscriptions' => MoneySubscriptionResource::collection($subscriptions),
            'goals' => MoneyGoalResource::collection($goals),
            'stats' => [
                'totalBalance' => '$' . number_format((float)$totalBalance, 2),
                'monthlyExpenses' => '$' . number_format(abs((float)$monthlyExpenses), 2),
                'monthlyIncome' => '$' . number_format((float)$monthlyIncome, 2),
                'savingsRate' => $monthlyIncome > 0 ? round(((float)(($monthlyIncome - abs((float)$monthlyExpenses)) / $monthlyIncome)) * 100) . '%' : '0%',
                'monthlyFixedExpenses' => '$' . number_format((float)$monthlyFixedExpenses, 2),
            ],
            'levelData' => $this->progressEngine->getUserStats($userId)['modules']['Money'] ?? null,
            'isFreshInstall' => $goals->isEmpty() && $subscriptions->isEmpty() && $entries->isEmpty()
        ]);
    }

    public function store(StoreMoneyEntryRequest $request, LogEntryAction $action): RedirectResponse
    {
        $action->handle($request->user()->id, $request->validated());
        return redirect()->back();
    }

    public function storeGoal(StoreMoneyGoalRequest $request): RedirectResponse
    {
        MoneyGoal::create([
            'user_id' => $request->user()->id,
            'name' => $request->validated()['name'],
            'target_amount' => $request->validated()['target_amount'],
            'target_date' => $request->validated()['target_date'] ?? null,
            'color' => $request->validated()['color'] ?? 'emerald'
        ]);
        $this->progressEngine->addXp($request->user()->id, ModuleEnum::MONEY, 15, 'Created a savings goal');
        return redirect()->back();
    }

    public function updateGoal(UpdateMoneyGoalRequest $request, MoneyGoal $goal, FundGoalAction $action): RedirectResponse
    {
        $this->authorize('update', $goal);
        $action->handle($request->user()->id, $goal, (float)$request->validated()['add_amount']);
        return redirect()->back();
    }

    public function claimGoal(Request $request, MoneyGoal $goal, ClaimGoalAction $action): RedirectResponse
    {
        $this->authorize('update', $goal);
        $action->handle($request->user()->id, $goal);
        return redirect()->back();
    }

    public function destroyGoal(Request $request, MoneyGoal $goal): RedirectResponse
    {
        $this->authorize('delete', $goal);
        $goal->delete();
        return redirect()->back();
    }

    public function storeSubscription(StoreMoneySubscriptionRequest $request): RedirectResponse
    {
        MoneySubscription::create([
            'user_id' => $request->user()->id,
            'name' => $request->validated()['name'],
            'amount' => $request->validated()['amount'],
            'billing_cycle' => $request->validated()['billing_cycle'],
            'next_billing_date' => $request->validated()['next_billing_date'],
            'is_active' => true,
        ]);
        $this->progressEngine->addXp($request->user()->id, ModuleEnum::MONEY, 10, 'Added a new subscription');
        return redirect()->back();
    }

    public function updateSubscription(StoreMoneySubscriptionRequest $request, MoneySubscription $subscription): RedirectResponse
    {
        $this->authorize('update', $subscription);
        $subscription->update([
            'name' => $request->validated()['name'],
            'amount' => $request->validated()['amount'],
            'billing_cycle' => $request->validated()['billing_cycle'],
            'next_billing_date' => $request->validated()['next_billing_date'],
        ]);
        return redirect()->back();
    }

    public function toggleSubscription(Request $request, MoneySubscription $subscription): RedirectResponse
    {
        $this->authorize('update', $subscription);
        $subscription->update(['is_active' => !$subscription->is_active]);
        return redirect()->back();
    }

    public function destroySubscription(Request $request, MoneySubscription $subscription): RedirectResponse
    {
        $this->authorize('delete', $subscription);
        $subscription->delete();
        return redirect()->back();
    }

    public function seedBlueprint(Request $request): RedirectResponse
    {
        $userId = $request->user()->id;
        if (MoneyGoal::forUser($userId)->exists()) {
            return redirect()->back();
        }
        MoneyGoal::create(['user_id' => $userId, 'name' => 'Emergency Fund', 'target_amount' => 10000, 'color' => 'blue']);
        MoneyGoal::create(['user_id' => $userId, 'name' => 'Vacation/Fun', 'target_amount' => 2000, 'color' => 'purple']);
        MoneySubscription::create([
            'user_id' => $userId, 'name' => 'Housing/Utility Blueprint', 'amount' => 1500,
            'billing_cycle' => \App\Enums\BillingCycleEnum::MONTHLY->value,
            'next_billing_date' => now()->addDays(5)->toDateString(), 'is_active' => true
        ]);
        $this->progressEngine->addXp($userId, ModuleEnum::MONEY, 50, 'Deployed 50/30/20 Budget Blueprint');
        return redirect()->back();
    }
}
