<?php

namespace Database\Factories;

use App\Enums\BillingCycleEnum;
use App\Models\MoneySubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoneySubscriptionFactory extends Factory
{
    protected $model = MoneySubscription::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company() . ' Subscription',
            'amount' => $this->faker->randomFloat(2, 5, 50),
            'billing_cycle' => $this->faker->randomElement(BillingCycleEnum::cases()),
            'next_billing_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'is_active' => true,
        ];
    }
}
