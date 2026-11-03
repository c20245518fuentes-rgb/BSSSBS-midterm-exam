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
        return view('appointments.index', [
            'appointments' => Appointment::query()
                ->with(['client', 'stylist', 'service', 'billingInvoice'])
                ->orderByDesc('appointment_datetime')
                ->paginate(15),
            'clients' => Client::query()->orderBy('name')->get(),
            'stylists' => Stylist::query()->orderBy('name')->get(),
            'services' => Service::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('appointments.create', [
            'clients' => Client::query()->orderBy('name')->get(),
            'stylists' => Stylist::query()->orderBy('name')->get(),
            'services' => Service::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAppointmentRequest $request, SalonService $salonService): RedirectResponse
    {
        $salonService->createAppointment($request->validated());

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
