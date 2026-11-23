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

        return Inertia::render('Profile/Show', [
            'user' => $request->user()->load('client'), // SALA - NEED FURTHER OPTIMIZATION
        ]);
    }
}
