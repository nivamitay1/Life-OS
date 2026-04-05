<?php

namespace Database\Factories;

use App\Enums\PlanStatusEnum;
use App\Models\RunningPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RunningPlanFactory extends Factory
{
    protected $model = RunningPlan::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'template_id' => null,
            'name' => $this->faker->word() . ' Training Plan',
            'status' => $this->faker->randomElement(PlanStatusEnum::cases()),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+2 months', '+4 months'),
            'goal_type' => $this->faker->randomElement(['5k', '10k', 'half_marathon', 'marathon']),
        ];
    }
}
