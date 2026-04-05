<?php

namespace Database\Factories;

use App\Models\MoneyGoal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoneyGoalFactory extends Factory
{
    protected $model = MoneyGoal::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'target_amount' => $this->faker->randomFloat(2, 1000, 10000),
            'current_amount' => 0,
            'target_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'color' => $this->faker->safeColorName(),
            'is_claimed' => false,
        ];
    }
}
