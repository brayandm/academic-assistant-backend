<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AiModelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'option' => fake()->name(),
            'usage_type' => fake()->name(),
        ];
    }
}
