<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CustomerProfileController extends Controller
{
    public function show(Request $request): Response
    {
        abort_if($request->user()->canManageStudio(), 403);

        $user = $request->user();
        $client = $user->client()
            ->select(['id', 'user_id', 'phone'])
            ->first();

        return Inertia::render('Profile/Show', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'client' => $client?->only(['phone']),
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request, ClientService $clientService): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated, $clientService): void {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            $client = $user->client()->first();

            if ($client) {
                $clientService->updateClient($client->id, [
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                ]);
            } else {
                $clientService->createClient([
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                ])->update(['user_id' => $user->id]);
            }
        });

        return to_route('profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return to_route('profile.show')->with('success', 'Password updated successfully.');
    }
}
