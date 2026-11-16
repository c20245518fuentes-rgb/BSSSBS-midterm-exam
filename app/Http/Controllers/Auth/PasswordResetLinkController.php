<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function store(SendPasswordResetLinkRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->safe()->only('email'));

        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors([
                'email' => 'Please wait before requesting another password reset link.',
            ]);
        }

        return back()->with('status', 'If an account exists for that email, a password reset link has been sent.');
    }
}
