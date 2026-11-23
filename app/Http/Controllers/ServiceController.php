<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Services\SalonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(Request $request): Response
    {
        if (! $request->user()->canManageStudio()) {
            return Inertia::render('Services/Catalog', [
                'services' => Service::query()->orderBy('name')->get(),
            ]);
        }

        return Inertia::render('Services/Index', [
            'services' => Service::query()
                ->withCount('appointments')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()->canManageStudio(), 403);

        return Inertia::render('Services/Create');
    }

    public function store(StoreServiceRequest $request, SalonService $salonService): RedirectResponse
    {
        abort_unless($request->user()->canManageStudio(), 403);

        $salonService->createService($request->validated());

        return to_route('services.index')->with('success', 'Service created successfully.');
    }

    public function update(
        UpdateServiceRequest $request,
        Service $service,
        SalonService $salonService,
    ): RedirectResponse {
        abort_unless($request->user()->canManageStudio(), 403);

        $salonService->updateService($service, $request->validated());

        return to_route('services.index')->with('success', 'Service updated successfully.');
    }
}
