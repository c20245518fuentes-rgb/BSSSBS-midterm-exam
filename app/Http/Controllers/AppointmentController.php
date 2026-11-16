<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Stylist;
use App\Services\SalonService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        if (! $user->is_admin) {
            $client = $user->client()->firstOrFail();

            return Inertia::render('Appointments/CustomerIndex', [
                'appointments' => $client->appointments()
                    ->with(['client', 'stylist', 'service', 'billingInvoice'])
                    ->orderByDesc('appointment_datetime')
                    ->paginate(15),
            ]);
        }

        return Inertia::render('Appointments/Index', [
            'appointments' => Appointment::query()
                ->with(['client', 'stylist', 'service', 'billingInvoice'])
                ->orderByDesc('appointment_datetime')
                ->paginate(15),
            'clients' => Client::query()->orderBy('name')->get(),
            'stylists' => Stylist::query()->orderBy('name')->get(),
            'services' => Service::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): Response
    {
        abort_if(auth()->user()->is_admin, 403);

        return Inertia::render('Appointments/Create', [
            'stylists' => Stylist::query()->orderBy('name')->get(),
            'services' => Service::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAppointmentRequest $request, SalonService $salonService): RedirectResponse
    {
        $data = $request->validated();
        $data['client_id'] = $request->user()->client()->firstOrFail()->id;

        $salonService->createAppointment($data);

        return to_route('appointments.index')->with('success', 'Appointment booked and invoice created.');
    }

    public function update(
        UpdateAppointmentRequest $request,
        string $id,
        SalonService $salonService,
    ): RedirectResponse {
        abort_unless($request->user()->is_admin, 403);

        $salonService->updateAppointment((int) $id, $request->validated());

        return to_route('appointments.index')->with('success', 'Appointment updated successfully.');
    }
}
