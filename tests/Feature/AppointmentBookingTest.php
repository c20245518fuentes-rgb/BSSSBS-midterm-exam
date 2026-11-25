<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\BillingInvoice;
use App\Models\Client;
use App\Models\Service;
use App\Models\Stylist;
use App\Models\User;
use App\Services\ClientService;
use App\Services\ServiceCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_booking_creates_an_invoice_and_rejects_overlapping_stylist_slots(): void
    {
        $client = $this->signInAsCustomer();
        $stylist = Stylist::factory()->create();
        $service = Service::factory()->create(['duration_min' => 120, 'price' => 2500]);

        $this->post(route('appointments.store'), [
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
                'stylist_id' => $stylist->id,
                'service_id' => $service->id,
                'appointment_datetime' => '2030-05-10 11:30:00',
            ])
            ->assertRedirect(route('appointments.create'))
            ->assertSessionHasErrors('appointment_datetime');

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseCount('billing_invoices', 1);
    }

    public function test_customers_book_for_themselves_and_only_see_their_own_appointments(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $client = Client::factory()->create(['user_id' => $customer->id]);
        $otherClient = Client::factory()->create();
        $stylist = Stylist::factory()->create();
        $service = Service::factory()->create();
        $this->actingAs($customer);

        $this->get(route('appointments.create'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Appointments/Create')
                ->where('isStudio', false)
                ->has('clients', 0));

        $this->post(route('appointments.store'), [
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => '2030-05-10 10:00:00',
        ])->assertRedirect(route('appointments.index'));

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
        ]);

        $this->post(route('appointments.store'), [
            'client_id' => $otherClient->id,
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => '2030-05-11 10:00:00',
        ])->assertSessionHasErrors('client_id');

        Appointment::factory()->create(['client_id' => $otherClient->id]);

        $this->get(route('appointments.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Appointments/CustomerIndex')
                ->has('appointments.data', 1)
                ->where('appointments.data.0.client_id', $client->id));
    }

    public function test_customers_cannot_access_studio_management_actions(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        Client::factory()->create(['user_id' => $customer->id]);
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create();
        $invoice = BillingInvoice::factory()->create(['appointment_id' => $appointment->id]);
        $this->actingAs($customer);

        $this->get(route('services.index'))
            ->assertInertia(fn (Assert $page) => $page->component('Services/Catalog'));
        $this->get(route('services.create'))->assertForbidden();
        $this->put(route('services.update', $service), [])->assertForbidden();
        $this->put(route('appointments.update', $appointment), [])->assertForbidden();
        $this->post(route('billing-invoices.pay', $invoice))->assertForbidden();
    }

    public function test_customers_can_view_only_their_own_profile_details(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        Client::factory()->create([
            'user_id' => $customer->id,
            'phone' => '+639171234567',
        ]);

        $this->actingAs($customer)
            ->get(route('profile.show'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/Show')
                ->where('user.name', $customer->name)
                ->where('user.email', $customer->email)
                ->where('user.client.phone', '+639171234567'));
    }

    public function test_studio_and_admin_accounts_cannot_open_the_customer_profile(): void
    {
        foreach ([User::ROLE_STUDIO, User::ROLE_ADMIN] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('profile.show'))
                ->assertForbidden();
        }
    }

    public function test_admins_can_manage_the_studio_and_see_appointments_in_every_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $statuses = [
            Appointment::STATUS_SCHEDULED,
            Appointment::STATUS_CANCELLED,
            Appointment::STATUS_COMPLETED,
        ];

        foreach ($statuses as $status) {
            Appointment::factory()->create(['status' => $status]);
        }

        $this->actingAs($admin)
            ->get(route('appointments.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Appointments/Index')
                ->has('appointments.data', count($statuses)));

        $this->get(route('services.create'))
            ->assertInertia(fn (Assert $page) => $page->component('Services/Create'));
    }

    public function test_studio_dashboard_shows_totals_and_rejects_customers(): void
    {
        $stylist = Stylist::factory()->create();
        $service = Service::factory()->create(['price' => 750]);
        $todayAppointment = Appointment::factory()->create([
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => now()->startOfDay()->addHours(10),
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        BillingInvoice::factory()->create([
            'appointment_id' => $todayAppointment->id,
            'total_amount' => 750,
            'payment_status' => BillingInvoice::PAYMENT_UNPAID,
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Index')
                ->where('metrics.today_count', 1)
                ->where('metrics.scheduled_count', 1)
                ->where('metrics.unpaid_amount', 750)
                ->has('todayAppointments', 1)
                ->has('workload', 1));

        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        Client::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_adjacent_appointments_do_not_count_as_overlapping(): void
    {
        $this->signInAsCustomer();
        $stylist = Stylist::factory()->create();
        $service = Service::factory()->create(['duration_min' => 45]);

        $this->post(route('appointments.store'), [
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => '2030-05-10 10:00:00',
        ])->assertRedirect(route('appointments.index'));

        $this->post(route('appointments.store'), [
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => '2030-05-10 10:45:00',
        ])->assertRedirect(route('appointments.index'));

        $this->assertDatabaseCount('appointments', 2);
        $this->assertDatabaseCount('billing_invoices', 2);
    }

    public function test_long_service_overlap_is_detected_across_day_boundaries(): void
    {
        $this->signInAsCustomer();
        $stylist = Stylist::factory()->create();
        $service = Service::factory()->create([
            'duration_min' => Service::MAX_DURATION_MINUTES,
        ]);

        $this->post(route('appointments.store'), [
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => '2030-05-10 10:00:00',
        ])->assertRedirect(route('appointments.index'));

        $this->from(route('appointments.create'))
            ->post(route('appointments.store'), [
                'stylist_id' => $stylist->id,
                'service_id' => $service->id,
                'appointment_datetime' => '2030-05-11 09:00:00',
            ])
            ->assertRedirect(route('appointments.create'))
            ->assertSessionHasErrors('appointment_datetime');

        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_customer_booking_uses_their_profile_and_only_shows_their_appointments(): void
    {
        $customer = User::factory()->create();
        $client = $customer->client()->create([
            'name' => $customer->name,
            'phone' => '+639171234567',
        ]);
        $otherClient = Client::factory()->create();
        $stylist = Stylist::factory()->create();
        $otherService = Service::factory()->create();
        $service = Service::factory()->create();
        Appointment::factory()->create([
            'client_id' => $otherClient->id,
            'stylist_id' => $stylist->id,
            'service_id' => $otherService->id,
            'appointment_datetime' => '2030-05-10 10:00:00',
        ]);

        $this->actingAs($customer)
            ->get(route('appointments.create'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Appointments/Create')
                ->where('isStudio', false)
                ->has('stylists')
                ->has('services'));

        $this->post(route('appointments.store'), [
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'appointment_datetime' => '2030-05-11 10:00:00',
        ])->assertRedirect(route('appointments.index'));

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'appointment_datetime' => '2030-05-11 10:00:00',
        ]);

        $this->get(route('appointments.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Appointments/CustomerIndex')
                ->has('appointments.data', 1)
                ->where('appointments.data.0.service.name', $service->name));
    }

    public function test_customers_cannot_manage_staff_features_or_pay_invoices(): void
    {
        $customer = User::factory()->create();
        $customer->client()->create([
            'name' => $customer->name,
            'phone' => '+639171234567',
        ]);
        $appointment = Appointment::factory()->create();
        $invoice = $appointment->billingInvoice()->create([
            'total_amount' => 500,
            'payment_status' => BillingInvoice::PAYMENT_UNPAID,
        ]);

        $this->actingAs($customer)
            ->get(route('services.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Services/Catalog')
                ->has('services'));

        $this->get(route('appointments.create'))
            ->assertInertia(fn (Assert $page) => $page->component('Appointments/Create'));
        $this->get(route('appointments.index'))
            ->assertInertia(fn (Assert $page) => $page->component('Appointments/CustomerIndex'));

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('appointments.create'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Appointments/Create')
                ->where('isStudio', true)
                ->has('clients'));
        $this->post(route('appointments.store'), [
            'stylist_id' => $appointment->stylist_id,
            'service_id' => $appointment->service_id,
            'appointment_datetime' => '2030-05-10 12:00:00',
        ])->assertSessionHasErrors('client_id');
        $this->get(route('appointments.index'))
            ->assertInertia(fn (Assert $page) => $page->component('Appointments/Index'));

        $this->actingAs($customer);
        $this->get(route('services.create'))->assertForbidden();

        $this->put(route('appointments.update', $appointment), [
            'client_id' => $appointment->client_id,
            'stylist_id' => $appointment->stylist_id,
            'service_id' => $appointment->service_id,
            'appointment_datetime' => '2030-05-10 10:00:00',
        ])->assertForbidden();

        $this->post(route('billing-invoices.pay', $invoice))->assertForbidden();

        $this->assertDatabaseHas('billing_invoices', [
            'id' => $invoice->id,
            'payment_status' => BillingInvoice::PAYMENT_UNPAID,
        ]);
    }

    public function test_appointment_updates_recheck_availability_and_refresh_unpaid_invoice_totals(): void
    {
        $client = $this->signInAsCustomer();
        $stylist = Stylist::factory()->create();
        $originalService = Service::factory()->create(['duration_min' => 45, 'price' => 500]);
        $newService = Service::factory()->create(['duration_min' => 60, 'price' => 1200]);

        $this->post(route('appointments.store'), [
            'stylist_id' => $stylist->id,
            'service_id' => $originalService->id,
            'appointment_datetime' => '2030-05-10 10:00:00',
        ])->assertRedirect(route('appointments.index'));

        $this->actingAs(User::factory()->admin()->create());
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

    public function test_service_titles_and_client_phone_numbers_are_cleaned_by_the_domain_services(): void
    {
        $serviceCatalogService = app(ServiceCatalogService::class);
        $clientService = app(ClientService::class);

        $service = $serviceCatalogService->createService([
            'category' => '<i>hair</i>',
            'name' => '  <b>hAiR cOlOr</b> ',
            'duration_min' => 45,
            'price' => '500.009',
        ]);
        $client = $clientService->createClient([
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

    private function signInAsCustomer(): Client
    {
        $customer = User::factory()->create();
        $client = $customer->client()->create([
            'name' => $customer->name,
            'phone' => fake()->unique()->numerify('+639#########'),
        ]);

        $this->actingAs($customer);

        return $client;
    }
}
