@extends('layouts.app')

@section('title', 'Choose a new password — Maison Studio')

@section('content')
    <div class="mx-auto max-w-md py-5 sm:py-10">
        <div class="mb-7 text-center">
            <span class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-accent text-brand">
                <svg viewBox="0 0 24 24" fill="none" class="size-7" aria-hidden="true">
                    <rect x="4" y="10" width="16" height="11" rx="2" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </span>
            <p class="text-sm font-medium text-brand">Almost there</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Choose a new password</h1>
            <p class="mt-2 text-sm text-subtle">Use a secure password with at least 8 characters.</p>
        </div>
        <form class="rounded-xl border border-border bg-white p-6 shadow-soft sm:p-8" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="space-y-4">
                <label class="block text-sm font-medium" for="email">Email address
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="email" type="email" name="email" value="{{ old('email', $email) }}" placeholder="you@example.com" autocomplete="email" required autofocus>
                </label>
                <label class="block text-sm font-medium" for="password">New password
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="password" type="password" name="password" placeholder="At least 8 characters" autocomplete="new-password" minlength="8" required>
                </label>
                <label class="block text-sm font-medium" for="password_confirmation">Confirm new password
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="password_confirmation" type="password" name="password_confirmation" placeholder="Re-enter your new password" autocomplete="new-password" minlength="8" required>
                </label>
                <button class="inline-flex h-10 w-full items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2" type="submit">Save new password</button>
            </div>
        </form>
    </div>
@endsection
