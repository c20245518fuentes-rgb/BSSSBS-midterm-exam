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
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        if (! $user->canManageStudio()) {
            $client = $user->client()->firstOrFail();

            return Inertia::render('Appointments/CustomerIndex', [
                'appointments' => $client->appointments()
                    ->select(['id', 'client_id', 'stylist_id', 'service_id', 'appointment_datetime', 'status'])
                    ->with([
                        'stylist:id,name',
                        'service:id,name',
                        'billingInvoice:id,appointment_id,total_amount,payment_status',
                    ])
                    ->orderByDesc('appointment_datetime')
                    ->paginate(15),
            ]);
        }

        return Inertia::render('Appointments/Index', [
            'appointments' => Appointment::query()
                ->select(['id', 'client_id', 'stylist_id', 'service_id', 'appointment_datetime', 'status'])
                ->with([
                    'client:id,name,phone',
                    'stylist:id,name,specialization',
                    'service:id,name,duration_min,price',
                    'billingInvoice:id,appointment_id,total_amount,payment_status',
                ])
                ->orderByDesc('appointment_datetime')
                ->paginate(15),
            'clients' => Client::query()->select(['id', 'name'])->orderBy('name')->get(),
            'stylists' => Stylist::query()->select(['id', 'name'])->orderBy('name')->get(),
            'services' => Service::query()->select(['id', 'name'])->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): Response
    {
        $canManageStudio = $request->user()->canManageStudio();

        return Inertia::render('Appointments/Create', [
            'isStudio' => $canManageStudio,
            'clients' => $canManageStudio
                ? Client::query()->select(['id', 'name', 'phone'])->orderBy('name')->get()
                : collect(),
            'stylists' => Stylist::query()
                ->select(['id', 'name', 'specialization'])
                ->orderBy('name')
                ->get(),
            'services' => Service::query()
                ->select(['id', 'name', 'duration_min', 'price'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreAppointmentRequest $request, SalonService $salonService): RedirectResponse
    {
        $data = $request->validated();
        if (! $request->user()->canManageStudio()) {
            $data['client_id'] = $request->user()->client()->firstOrFail()->id;
        }

        $salonService->createAppointment($data);

        return to_route('appointments.index')->with('success', 'Appointment booked and invoice created.');
    }

    public function update(
        UpdateAppointmentRequest $request,
        string $id,
        SalonService $salonService,
    ): RedirectResponse {
        abort_unless($request->user()->canManageStudio(), 403);

        $salonService->updateAppointment((int) $id, $request->validated());

        return to_route('appointments.index')->with('success', 'Appointment updated successfully.');
    }
}
