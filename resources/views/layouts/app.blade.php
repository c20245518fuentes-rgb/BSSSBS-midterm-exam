<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#faf9f7">
    <title>@yield('title', 'Studio — Salon Booking')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#faf9f7',
                        foreground: '#24211f',
                        muted: '#f1efec',
                        subtle: '#77716c',
                        border: '#e8e4df',
                        brand: '#75575d',
                        'brand-dark': '#60464b',
                        accent: '#f3e9e9',
                        success: '#eaf4ed',
                        danger: '#b33b37'
                    },
                    boxShadow: {
                        soft: '0 12px 40px -20px rgba(48, 37, 32, .18)'
                    }
                }
            }
        };
    </script>
    <style>
        [x-cloak] { display: none !important; }
        html { background: #faf9f7; }
        body { min-height: 100vh; }
        input, select, button { font: inherit; }
        input[type="checkbox"] { accent-color: #75575d; }
        summary { cursor: pointer; }
        :focus-visible { outline: 2px solid #9a737b; outline-offset: 2px; }
    </style>
    @stack('head')
</head>
<body class="bg-background text-foreground antialiased">
    <div class="min-h-screen">
        <header class="sticky top-0 z-30 border-b border-border/80 bg-white/90 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-5 px-4 py-3 sm:px-6 lg:px-8">
                <a class="flex shrink-0 items-center gap-3" href="{{ auth()->check() ? route('appointments.index') : route('login') }}">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-brand text-white shadow-sm">
                        <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
                            <path d="M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 13c1.2-.15 2.45-.9 3-2.5.55 1.6 1.8 2.35 3 2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="leading-tight">
                        <span class="block text-sm font-semibold tracking-tight">Maison Studio</span>
                        <span class="mt-0.5 block text-[11px] tracking-wide text-subtle">BEAUTY &amp; WELLNESS</span>
                    </span>
                </a>

                @auth
                    <nav class="hidden items-center gap-1 md:flex" aria-label="Main navigation">
                        <a href="{{ route('appointments.index') }}" @class([
                            'rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-muted',
                            'bg-muted text-foreground' => request()->routeIs('appointments.*'),
                            'text-subtle' => ! request()->routeIs('appointments.*'),
                        ])>Appointments</a>
                        <a href="{{ route('services.index') }}" @class([
                            'rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-muted',
                            'bg-muted text-foreground' => request()->routeIs('services.*'),
                            'text-subtle' => ! request()->routeIs('services.*'),
                        ])>Services</a>
                        @unless (auth()->user()->canManageStudio())
                            <a href="{{ route('profile.show') }}" @class([
                                'rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-muted',
                                'bg-muted text-foreground' => request()->routeIs('profile.*'),
                                'text-subtle' => ! request()->routeIs('profile.*'),
                            ])>My profile</a>
                        @endunless
                    </nav>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-subtle">{{ auth()->user()->role === \App\Models\User::ROLE_ADMIN ? 'Admin account' : (auth()->user()->canManageStudio() ? 'Studio account' : 'Customer account') }}</p>
                        </div>
                        <span class="flex size-9 items-center justify-center rounded-full bg-accent text-sm font-semibold text-brand">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="inline-flex h-9 items-center gap-2 rounded-md border border-border bg-white px-3 text-sm font-medium transition-colors hover:bg-muted" type="submit">
                                <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                    <path d="M10 17l5-5-5-5M15 12H3m9-8h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="hidden sm:inline">Log out</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a class="rounded-md px-3 py-2 text-sm font-medium text-subtle transition-colors hover:bg-muted hover:text-foreground" href="{{ route('login') }}">Log in</a>
                        <a class="inline-flex h-9 items-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="{{ route('register') }}">Get started</a>
                    </div>
                @endauth
            </div>

            @auth
                <nav class="flex gap-1 border-t border-border/70 px-4 py-2 md:hidden" aria-label="Mobile navigation">
                    <a href="{{ route('appointments.index') }}" @class([
                        'rounded-md px-3 py-1.5 text-sm font-medium',
                        'bg-muted text-foreground' => request()->routeIs('appointments.*'),
                        'text-subtle' => ! request()->routeIs('appointments.*'),
                    ])>Appointments</a>
                    <a href="{{ route('services.index') }}" @class([
                        'rounded-md px-3 py-1.5 text-sm font-medium',
                        'bg-muted text-foreground' => request()->routeIs('services.*'),
                        'text-subtle' => ! request()->routeIs('services.*'),
                    ])>Services</a>
                    @unless (auth()->user()->canManageStudio())
                        <a href="{{ route('profile.show') }}" @class([
                            'rounded-md px-3 py-1.5 text-sm font-medium',
                            'bg-muted text-foreground' => request()->routeIs('profile.*'),
                            'text-subtle' => ! request()->routeIs('profile.*'),
                        ])>My profile</a>
                    @endunless
                </nav>
            @endauth
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
            @if (session('success') || session('status'))
                <div role="status" class="mb-6 flex items-start gap-3 rounded-lg border border-[#d6e8da] bg-success px-4 py-3 text-sm text-[#315d3d] shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" class="mt-0.5 size-4 shrink-0" aria-hidden="true">
                        <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ session('success') ?? session('status') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div role="alert" class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
                    <p class="font-medium">Please check the following:</p>
                    <ul class="mt-1 list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
        <footer class="mx-auto max-w-7xl px-4 pb-8 text-center text-xs text-subtle sm:px-6 lg:px-8">
            <span>Maison Studio</span>
            <span class="mx-1.5">·</span>
            <span>Beauty, thoughtfully booked.</span>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
