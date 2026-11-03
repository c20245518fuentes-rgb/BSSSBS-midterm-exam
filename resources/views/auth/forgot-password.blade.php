@extends('layouts.app')

@section('title', 'Reset password — Maison Studio')

@section('content')
    <div class="mx-auto max-w-md py-5 sm:py-10">
        <div class="mb-7 text-center">
            <span class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-accent text-brand">
                <svg viewBox="0 0 24 24" fill="none" class="size-7" aria-hidden="true">
                    <rect x="4" y="10" width="16" height="11" rx="2" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </span>
            <p class="text-sm font-medium text-brand">Account recovery</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Forgot your password?</h1>
            <p class="mt-2 text-sm leading-6 text-subtle">No worries. Enter the email address linked to your account and we’ll send you a reset link.</p>
        </div>
        <form class="rounded-xl border border-border bg-white p-6 shadow-soft sm:p-8" method="POST" action="{{ route('password.email') }}">
            @csrf
            <label class="block text-sm font-medium" for="email">Email address
                <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
            </label>
            <button class="mt-5 inline-flex h-10 w-full items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2" type="submit">Send reset link</button>
            <a class="mt-5 flex items-center justify-center gap-2 text-sm font-medium text-subtle transition-colors hover:text-foreground" href="{{ route('login') }}">
                <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                    <path d="m15 18-6-6 6-6M9 12h12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to sign in
            </a>
        </form>
    </div>
@endsection
