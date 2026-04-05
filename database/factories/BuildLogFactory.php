<?php

namespace Database\Factories;

use App\Models\BuildLog;
use App\Models\BuildProject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildLogFactory extends Factory
{
    protected $model = BuildLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'build_project_id' => BuildProject::factory(),
            'type' => $this->faker->randomElement(['code', 'design', 'research', 'fix']),
            'content' => $this->faker->paragraph(),
        ];
    }
}
