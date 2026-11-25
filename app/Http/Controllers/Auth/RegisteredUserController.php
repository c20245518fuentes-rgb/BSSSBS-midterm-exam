<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'isInitialSetup' => User::query()->doesntExist(),
        ]);
    }

    public function store(RegisterRequest $request, ClientService $clientService): RedirectResponse
    {
        $user = DB::transaction(function () use ($request, $clientService): User {
            $isInitialSetup = User::query()->doesntExist();
            $data = $request->safe()->only(['name', 'email', 'password']);
            $user = User::query()->create($data);
            $user->role = $isInitialSetup ? User::ROLE_STUDIO : User::ROLE_CUSTOMER;
            $user->is_admin = false;
            $user->save();

            if (! $isInitialSetup) {
                Client::query()->create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'phone' => $clientService->cleanPhone($request->validated('phone')),
                ]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('appointments.index');
    }
}
