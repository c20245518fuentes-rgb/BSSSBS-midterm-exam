@extends('layouts.app')

@section('title', 'Create account — Maison Studio')

@section('content')
    <div class="mx-auto max-w-md py-5 sm:py-10">
        <div class="mb-7 text-center">
            <span class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-accent text-brand">
                <svg viewBox="0 0 24 24" fill="none" class="size-7" aria-hidden="true">
                    <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </span>
            <p class="text-sm font-medium text-brand">Welcome to Maison Studio</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Create your account</h1>
            <p class="mt-2 text-sm text-subtle">Set up your studio workspace to get started.</p>
        </div>
        <form class="rounded-xl border border-border bg-white p-6 shadow-soft sm:p-8" method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="space-y-4">
                <label class="block text-sm font-medium" for="name">Full name
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="name" name="name" value="{{ old('name') }}" placeholder="Your name" autocomplete="name" maxlength="255" required autofocus>
                </label>
                <label class="block text-sm font-medium" for="email">Email address
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
                </label>
                <label class="block text-sm font-medium" for="password">Password
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="password" type="password" name="password" placeholder="At least 8 characters" autocomplete="new-password" minlength="8" required>
                </label>
                <label class="block text-sm font-medium" for="password_confirmation">Confirm password
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="password_confirmation" type="password" name="password_confirmation" placeholder="Re-enter your password" autocomplete="new-password" minlength="8" required>
                </label>
                <button class="inline-flex h-10 w-full items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2" type="submit">Create account</button>
            </div>
            <p class="mt-6 border-t border-border pt-5 text-center text-sm text-subtle">
                Already have an account?
                <a class="font-medium text-brand hover:underline" href="{{ route('login') }}">Sign in</a>
            </p>
        </form>
    </div>
@endsection
