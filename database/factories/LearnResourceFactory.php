<?php

namespace Database\Factories;

use App\Enums\LearnResourceStatusEnum;
use App\Models\LearnResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LearnResourceFactory extends Factory
{
    protected $model = LearnResource::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['book', 'course', 'article', 'podcast', 'other']),
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'status' => $this->faker->randomElement(LearnResourceStatusEnum::cases()),
            'total_units' => $this->faker->numberBetween(10, 50),
            'current_unit' => 0,
            'unit_label' => $this->faker->randomElement(['page', 'chapter', 'lesson', 'module', 'episode', 'article', 'other']),
            'started_at' => null,
            'last_session_at' => null,
            'completed_at' => null,
        ];
    }
}
