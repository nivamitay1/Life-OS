<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\XpEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class XpEventFactory extends Factory
{
    protected $model = XpEvent::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'module_slug' => $this->faker->randomElement(['Money', 'Running', 'Build', 'Learn']),
            'amount' => $this->faker->numberBetween(5, 50),
            'reason' => $this->faker->sentence(),
        ];
    }
}
