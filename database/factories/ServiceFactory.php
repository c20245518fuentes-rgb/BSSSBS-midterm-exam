<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->word(),
            'name' => fake()->unique()->words(2, true),
            'duration_min' => fake()->numberBetween(15, 180),
            'price' => fake()->randomFloat(2, 100, 5000),
        ];
    }
}
