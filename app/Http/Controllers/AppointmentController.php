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
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('appointments.index', [
            'appointments' => Appointment::query()
                ->with(['client', 'stylist', 'service', 'billingInvoice'])
                ->when(! $user->canManageStudio(), fn ($query) => $query->where('client_id', $user->client()->firstOrFail()->id))
                ->orderByDesc('appointment_datetime')
                ->paginate(15),
            'clients' => $user->canManageStudio() ? Client::query()->orderBy('name')->get() : collect(),
            'stylists' => Stylist::query()->orderBy('name')->get(),
            'services' => Service::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $canManageStudio = auth()->user()->canManageStudio();

        return view('appointments.create', [
            'isStudio' => $canManageStudio,
            'clients' => $canManageStudio ? Client::query()->orderBy('name')->get() : collect(),
            'stylists' => Stylist::query()->orderBy('name')->get(),
            'services' => Service::query()->orderBy('name')->get(),
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
        $salonService->updateAppointment((int) $id, $request->validated());

        return to_route('appointments.index')->with('success', 'Appointment updated successfully.');
    }
}
