<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() === null ? null : [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'can_manage_studio' => $request->user()->canManageStudio(),
                ],
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'status' => fn () => $request->session()->get('status'),
            ],
            'old' => fn () => $request->session()->get('_old_input', []),
            'timezone' => config('app.timezone'),
        ];
    }
}
