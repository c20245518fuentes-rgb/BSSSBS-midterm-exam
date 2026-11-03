<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\BillingInvoice;
use App\Models\Client;
use App\Models\Service;
use App\Models\Stylist;
use App\Models\User;
use App\Services\SalonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_booking_creates_an_invoice_and_rejects_overlapping_stylist_slots(): void
    {
        $client = Client::factory()->create();
        $stylist = Stylist::factory()->create();
        $service = Service::factory()->create(['duration_min' => 120, 'price' => 2500]);

        $this->post(route('appointments.store'), [
            'client_id' => $client->id,
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => '2030-05-10 10:00:00',
        ])->assertRedirect(route('appointments.index'));

        $this->assertDatabaseCount('billing_invoices', 1);
        $this->assertDatabaseHas('billing_invoices', [
            'appointment_id' => 1,
            'total_amount' => '2500.00',
            'payment_status' => BillingInvoice::PAYMENT_UNPAID,
        ]);

        $this->from(route('appointments.create'))
            ->post(route('appointments.store'), [
                'client_id' => $client->id,
                'stylist_id' => $stylist->id,
                'service_id' => $service->id,
                'appointment_datetime' => '2030-05-10 11:30:00',
            ])
            ->assertRedirect(route('appointments.create'))
            ->assertSessionHasErrors('appointment_datetime');

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseCount('billing_invoices', 1);
    }

    public function test_adjacent_appointments_do_not_count_as_overlapping(): void
    {
        $client = Client::factory()->create();
        $stylist = Stylist::factory()->create();
        $service = Service::factory()->create(['duration_min' => 45]);

        $this->post(route('appointments.store'), [
            'client_id' => $client->id,
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => '2030-05-10 10:00:00',
        ])->assertRedirect(route('appointments.index'));

        $this->post(route('appointments.store'), [
            'client_id' => $client->id,
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => '2030-05-10 10:45:00',
        ])->assertRedirect(route('appointments.index'));

        $this->assertDatabaseCount('appointments', 2);
        $this->assertDatabaseCount('billing_invoices', 2);
    }

    public function test_appointment_updates_recheck_availability_and_refresh_unpaid_invoice_totals(): void
    {
        $client = Client::factory()->create();
        $stylist = Stylist::factory()->create();
        $originalService = Service::factory()->create(['duration_min' => 45, 'price' => 500]);
        $newService = Service::factory()->create(['duration_min' => 60, 'price' => 1200]);

        $this->post(route('appointments.store'), [
            'client_id' => $client->id,
            'stylist_id' => $stylist->id,
            'service_id' => $originalService->id,
            'appointment_datetime' => '2030-05-10 10:00:00',
        ])->assertRedirect(route('appointments.index'));

        $appointment = Appointment::query()->firstOrFail();
        $this->put(route('appointments.update', $appointment), [
            'client_id' => $client->id,
            'stylist_id' => $stylist->id,
            'service_id' => $newService->id,
            'appointment_datetime' => '2030-05-10 11:00:00',
            'status' => Appointment::STATUS_SCHEDULED,
        ])->assertRedirect(route('appointments.index'));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'service_id' => $newService->id,
        ]);
        $this->assertDatabaseHas('billing_invoices', [
            'appointment_id' => $appointment->id,
            'total_amount' => '1200.00',
            'payment_status' => BillingInvoice::PAYMENT_UNPAID,
        ]);
    }

    public function test_payment_marks_its_appointment_as_completed(): void
    {
        $appointment = Appointment::factory()->create();
        $invoice = $appointment->billingInvoice()->create([
            'total_amount' => 500,
            'payment_status' => BillingInvoice::PAYMENT_UNPAID,
        ]);

        $this->post(route('billing-invoices.pay', $invoice))
            ->assertRedirect(route('appointments.index'));

        $this->assertDatabaseHas('billing_invoices', [
            'id' => $invoice->id,
            'payment_status' => BillingInvoice::PAYMENT_PAID,
        ]);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        $this->assertNotNull($invoice->fresh()->paid_at);
    }

    public function test_service_titles_and_client_phone_numbers_are_cleaned_by_the_domain_service(): void
    {
        $salonService = app(SalonService::class);
        $service = $salonService->createService([
            'category' => '<i>hair</i>',
            'name' => '  <b>hAiR cOlOr</b> ',
            'duration_min' => 45,
            'price' => '500.009',
        ]);
        $client = $salonService->createClient([
            'name' => '  <b>Jamie Test</b> ',
            'phone' => ' +63 (917) 123-4567 ',
        ]);

        $this->assertSame('Hair Color', $service->name);
        $this->assertSame('Hair', $service->category);
        $this->assertSame('500.01', $service->price);
        $this->assertSame('Jamie Test', $client->name);
        $this->assertSame('+639171234567', $client->phone);
    }

    public function test_service_resource_pages_and_create_update_actions_work(): void
    {
        $this->get(route('services.index'))->assertOk();
        $this->get(route('services.create'))->assertOk();

        $this->post(route('services.store'), [
            'category' => 'hair',
            'name' => 'hair coloring',
            'duration_min' => 60,
            'price' => '800',
        ])->assertRedirect(route('services.index'));

        $service = Service::query()->firstOrFail();
        $this->assertSame('Hair Coloring', $service->name);

        $this->from(route('services.create'))
            ->post(route('services.store'), [
                'category' => 'hair',
                'name' => 'hair coloring',
                'duration_min' => 60,
                'price' => '800',
            ])
            ->assertRedirect(route('services.create'))
            ->assertSessionHasErrors('name');

        $this->put(route('services.update', $service), [
            'category' => 'hair',
            'name' => 'hair coloring',
            'duration_min' => 90,
            'price' => '950',
        ])->assertRedirect(route('services.index'));

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'duration_min' => 90,
            'price' => '950.00',
        ]);
    }

    public function test_database_seeder_loads_the_sample_normalized_salon_records(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('clients', 5);
        $this->assertDatabaseCount('stylists', 5);
        $this->assertDatabaseCount('services', 5);
        $this->assertDatabaseCount('appointments', 5);
        $this->assertDatabaseCount('billing_invoices', 5);
    }
}
