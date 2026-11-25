<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
