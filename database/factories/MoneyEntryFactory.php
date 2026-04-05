<?php

namespace Database\Factories;

use App\Enums\EntryTypeEnum;
use App\Models\MoneyEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoneyEntryFactory extends Factory
{
    protected $model = MoneyEntry::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(EntryTypeEnum::cases()),
            'amount' => $this->faker->randomFloat(2, 5, 2000),
            'currency' => 'USD',
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'description' => $this->faker->sentence(),
        ];
    }
}
