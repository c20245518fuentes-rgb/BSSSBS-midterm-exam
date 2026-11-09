<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerProfileController extends Controller
{
    public function show(Request $request): View
    {
        abort_if($request->user()->canManageStudio(), 403);

        return view('customers.profile', [
            'user' => $request->user()->load('client'),
        ]);
    }
}
