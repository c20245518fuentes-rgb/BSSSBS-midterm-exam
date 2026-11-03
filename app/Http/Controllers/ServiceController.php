<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Services\SalonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('services.index', [
            'services' => Service::query()
                ->with('appointments')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('services.create');
    }

    public function store(StoreServiceRequest $request, SalonService $salonService): RedirectResponse
    {
        $salonService->createService($request->validated());

        return to_route('services.index')->with('success', 'Service created successfully.');
    }

    public function update(
        UpdateServiceRequest $request,
        string $id,
        SalonService $salonService,
    ): RedirectResponse {
        $salonService->updateService((int) $id, $request->validated());

        return to_route('services.index')->with('success', 'Service updated successfully.');
    }
}
