<?php

namespace Database\Factories;

use App\Models\LearnResource;
use App\Models\LearnSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LearnSessionFactory extends Factory
{
    protected $model = LearnSession::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'learn_resource_id' => LearnResource::factory(),
            'duration_minutes' => $this->faker->numberBetween(15, 120),
            'units_completed' => $this->faker->numberBetween(1, 3),
            'ending_position_label' => 'Chapter ' . $this->faker->numberBetween(1, 10),
            'notes' => $this->faker->sentence(),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
