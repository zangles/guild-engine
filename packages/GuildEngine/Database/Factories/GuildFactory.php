<?php

namespace GuildEngine\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GuildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'user_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
