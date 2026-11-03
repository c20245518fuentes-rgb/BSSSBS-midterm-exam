<?php

namespace Database\Factories;

use App\Models\Stylist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stylist>
 */
class StylistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('+639#########'),
            'specialization' => fake()->jobTitle(),
        ];
    }
}
