<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $data = $request->safe()->only(['name', 'email', 'password']);
            $data['is_admin'] = false;

            $user = User::query()->create($data);
            $user->client()->create([
                'name' => $user->name,
                'phone' => $request->validated('phone'),
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('appointments.index');
    }
}
