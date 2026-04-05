<?php

namespace Database\Factories;

use App\Enums\WorkoutTypeEnum;
use App\Models\RunningRun;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RunningRunFactory extends Factory
{
    protected $model = RunningRun::class;

    public function definition(): array
    {
        $distance = $this->faker->randomFloat(2, 2, 21);
        $minutes = $distance * $this->faker->numberBetween(5, 7);
        $duration = sprintf('%02d:%02d:00', floor($minutes / 60), $minutes % 60);

        return [
            'user_id' => User::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'distance' => $distance,
            'duration' => $duration,
            'pace' => $this->faker->numberBetween(4, 7) . ':' . $this->faker->numberBetween(10, 59),
            'type' => $this->faker->randomElement(WorkoutTypeEnum::cases()),
            'effort' => $this->faker->numberBetween(1, 10),
            'notes' => $this->faker->sentence(),
        ];
    }
}
