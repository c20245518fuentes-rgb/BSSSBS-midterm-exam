<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'stylist_id' => Stylist::factory(),
            'service_id' => Service::factory(),
            'appointment_datetime' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'status' => Appointment::STATUS_SCHEDULED,
        ];
    }
}
