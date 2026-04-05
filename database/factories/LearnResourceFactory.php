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
            'type' => $this->faker->randomElement(['book', 'course', 'video', 'article']),
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'status' => $this->faker->randomElement(LearnResourceStatusEnum::cases()),
            'total_units' => $this->faker->numberBetween(10, 50),
            'current_unit' => 0,
            'unit_label' => 'Chapters',
            'started_at' => null,
            'last_session_at' => null,
            'completed_at' => null,
        ];
    }
}
