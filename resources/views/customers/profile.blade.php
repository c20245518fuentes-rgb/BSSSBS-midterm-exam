@extends('layouts.app')

@section('title', 'My profile — Maison Studio')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <p class="text-sm font-medium text-brand">Customer account</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">My profile</h1>
            <p class="mt-1 text-sm text-subtle">Your account and contact details.</p>
        </div>

        <section class="overflow-hidden rounded-xl border border-border bg-white shadow-soft" aria-labelledby="profile-details">
            <div class="border-b border-border px-5 py-4 sm:px-6">
                <h2 id="profile-details" class="text-base font-semibold">Profile details</h2>
            </div>
            <dl class="divide-y divide-border">
                <div class="grid gap-1 px-5 py-4 sm:grid-cols-[12rem_1fr] sm:gap-4 sm:px-6">
                    <dt class="text-sm text-subtle">Full name</dt>
                    <dd class="text-sm font-medium">{{ $user->name }}</dd>
                </div>
                <div class="grid gap-1 px-5 py-4 sm:grid-cols-[12rem_1fr] sm:gap-4 sm:px-6">
                    <dt class="text-sm text-subtle">Email address</dt>
                    <dd class="text-sm font-medium">{{ $user->email }}</dd>
                </div>
                <div class="grid gap-1 px-5 py-4 sm:grid-cols-[12rem_1fr] sm:gap-4 sm:px-6">
                    <dt class="text-sm text-subtle">Phone number</dt>
                    <dd class="text-sm font-medium">{{ $user->client?->phone ?? 'Not provided' }}</dd>
                </div>
            </dl>
            <div class="border-t border-border bg-background/70 px-5 py-4 sm:px-6">
                <a class="inline-flex h-10 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="{{ route('appointments.index') }}">Back to appointments</a>
            </div>
        </section>
    </div>
@endsection
