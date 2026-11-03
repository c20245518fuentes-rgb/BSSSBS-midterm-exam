@extends('layouts.app')

@section('title', 'Log in — Maison Studio')

@section('content')
    <div class="mx-auto max-w-md py-5 sm:py-10">
        <div class="mb-7 text-center">
            <span class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-accent text-brand">
                <svg viewBox="0 0 24 24" fill="none" class="size-7" aria-hidden="true">
                    <path d="M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 13c1.2-.15 2.45-.9 3-2.5.55 1.6 1.8 2.35 3 2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </span>
            <p class="text-sm font-medium text-brand">Welcome back</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Sign in to your studio</h1>
            <p class="mt-2 text-sm text-subtle">Manage appointments, services, and invoices.</p>
        </div>
        <form class="rounded-xl border border-border bg-white p-6 shadow-soft sm:p-8" method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="space-y-5">
                <label class="block text-sm font-medium" for="email">Email address
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
                </label>
                <div>
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-sm font-medium" for="password">Password</label>
                        <a class="text-xs font-medium text-brand hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                </div>
                <label class="flex cursor-pointer items-center gap-2.5 text-sm text-subtle">
                    <input class="size-4 rounded border-border" type="checkbox" name="remember" value="1">
                    Keep me signed in
                </label>
                <button class="inline-flex h-10 w-full items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2" type="submit">
                    Sign in
                    <svg viewBox="0 0 24 24" fill="none" class="ml-2 size-4" aria-hidden="true">
                        <path d="M5 12h14m-7-7 7 7-7 7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            @if (\App\Models\User::query()->doesntExist())
                <p class="mt-6 border-t border-border pt-5 text-center text-sm text-subtle">
                    Setting up your studio?
                    <a class="font-medium text-brand hover:underline" href="{{ route('register') }}">Create the first account</a>
                </p>
            @endif
        </form>
        <p class="mt-6 text-center text-xs text-subtle">Your studio workspace, all in one place.</p>
    </div>
@endsection
