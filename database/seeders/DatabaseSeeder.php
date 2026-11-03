<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\BillingInvoice;
use App\Models\Client;
use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $clients = collect([
            ['Juan Dela Cruz', '+639171234567'],
            ['Maria Santos', '+639187654321'],
            ['Pedro Reyes', '+639221112222'],
            ['Ana Garcia', '+639333334444'],
            ['Luis Mendoza', '+639455556666'],
        ])->map(fn (array $client): Client => Client::query()->firstOrCreate(
            ['phone' => $client[1]],
            ['name' => $client[0]],
        ));

        $stylists = collect([
            ['Alex Ramos', '+639991234567', 'Hair Colorist'],
            ['Bella Cruz', '+639992345678', 'Senior Stylist'],
            ['Carlo Diaz', '+639993456789', 'Barber'],
            ['Diana Lim', '+639994567890', 'Nail Technician'],
            ['Ethan Tan', '+639995678901', 'Makeup Artist'],
        ])->map(fn (array $stylist): Stylist => Stylist::query()->firstOrCreate(
            ['phone' => $stylist[1]],
            ['name' => $stylist[0], 'specialization' => $stylist[2]],
        ));

        $services = collect([
            ['Hair', 'Haircut', 45, 500],
            ['Hair', 'Hair Color', 120, 2500],
            ['Nails', 'Manicure', 60, 400],
            ['Nails', 'Pedicure', 60, 500],
            ['Makeup', 'Event Makeup', 90, 1500],
        ])->map(fn (array $service): Service => Service::query()->firstOrCreate(
            ['name' => $service[1]],
            [
                'category' => $service[0],
                'duration_min' => $service[2],
                'price' => $service[3],
            ],
        ));

        $appointments = collect([
            [0, 1, 0, '2026-05-10 10:00:00', Appointment::STATUS_COMPLETED],
            [1, 0, 1, '2026-05-10 13:30:00', Appointment::STATUS_SCHEDULED],
            [2, 2, 0, '2026-05-11 09:00:00', Appointment::STATUS_SCHEDULED],
            [3, 3, 3, '2026-05-11 11:00:00', Appointment::STATUS_CANCELLED],
            [4, 4, 4, '2026-05-12 15:00:00', Appointment::STATUS_COMPLETED],
        ])->map(fn (array $appointment): Appointment => Appointment::query()->firstOrCreate(
            [
                'client_id' => $clients[$appointment[0]]->id,
                'stylist_id' => $stylists[$appointment[1]]->id,
                'service_id' => $services[$appointment[2]]->id,
                'appointment_datetime' => $appointment[3],
            ],
            ['status' => $appointment[4]],
        ));

        collect([
            [0, 500, BillingInvoice::PAYMENT_PAID, '2026-05-10 11:00:00'],
            [1, 2500, BillingInvoice::PAYMENT_UNPAID, null],
            [2, 500, BillingInvoice::PAYMENT_UNPAID, null],
            [3, 500, BillingInvoice::PAYMENT_REFUNDED, '2026-05-11 09:30:00'],
            [4, 1500, BillingInvoice::PAYMENT_PAID, '2026-05-12 16:45:00'],
        ])->each(fn (array $invoice): BillingInvoice => BillingInvoice::query()->firstOrCreate(
            ['appointment_id' => $appointments[$invoice[0]]->id],
            [
                'total_amount' => $invoice[1],
                'payment_status' => $invoice[2],
                'paid_at' => $invoice[3],
            ],
        ));
    }
}
