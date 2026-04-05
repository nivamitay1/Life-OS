<?php

namespace Database\Factories;

use App\Enums\ProjectStatusEnum;
use App\Models\BuildProject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildProjectFactory extends Factory
{
    protected $model = BuildProject::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(ProjectStatusEnum::cases()),
        ];
    }
}
