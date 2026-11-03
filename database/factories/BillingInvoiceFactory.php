<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\BillingInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BillingInvoice>
 */
class BillingInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'total_amount' => fake()->randomFloat(2, 100, 5000),
            'payment_status' => BillingInvoice::PAYMENT_UNPAID,
            'paid_at' => null,
        ];
    }
}
