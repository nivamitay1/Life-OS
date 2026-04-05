<?php

namespace Database\Factories;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word() . ' Master',
            'module_slug' => $this->faker->randomElement(['Money', 'Running', 'Build', 'Learn']),
            'icon' => $this->faker->randomElement(['🏆', '🔥', '💎', '🚀', '⭐']),
        ];
    }
}
