<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        abort_if(User::query()->exists(), 404);

        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        abort_if(User::query()->exists(), 404);

        $user = User::query()->create($request->safe()->only(['name', 'email', 'password']));

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('appointments.index');
    }
}
