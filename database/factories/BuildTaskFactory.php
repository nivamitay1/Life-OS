<?php

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use App\Models\BuildProject;
use App\Models\BuildTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildTaskFactory extends Factory
{
    protected $model = BuildTask::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'build_project_id' => BuildProject::factory(),
            'title' => $this->faker->sentence(4),
            'status' => $this->faker->randomElement(TaskStatusEnum::cases()),
            'is_blocker' => $this->faker->boolean(20),
        ];
    }
}
