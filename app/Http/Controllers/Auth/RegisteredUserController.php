<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Client;
use App\Models\User;
use App\Services\SalonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'isInitialSetup' => User::query()->doesntExist(),
        ]);
    }

    public function store(RegisterRequest $request, SalonService $salonService): RedirectResponse
    {
        $user = DB::transaction(function () use ($request, $salonService): User {
            $isInitialSetup = User::query()->doesntExist();
            $data = $request->safe()->only(['name', 'email', 'password']);
            $user = User::query()->create($data);
            $user->role = $isInitialSetup ? User::ROLE_STUDIO : User::ROLE_CUSTOMER;
            $user->save();

            if (! $isInitialSetup) {
                Client::query()->create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'phone' => $salonService->cleanPhone($request->validated('phone')),
                ]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('appointments.index');
    }
}
