@extends('layouts.app')

@section('title', 'Book appointment — Maison Studio')

@section('content')
    <div class="mx-auto max-w-4xl">
        <a class="mb-5 inline-flex items-center gap-2 text-sm text-subtle transition-colors hover:text-foreground" href="{{ route('appointments.index') }}">
            <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                <path d="m15 18-6-6 6-6M9 12h12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back to appointments
        </a>
        <div class="mb-6">
            <p class="text-sm font-medium text-brand">{{ $isStudio ? 'Booking desk' : 'Online booking' }}</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Book an appointment</h1>
            <p class="mt-1 text-sm text-subtle">{{ $isStudio ? 'Choose the client, stylist, service, and a time that works.' : 'Choose a stylist, service, and a time that works.' }}</p>
        </div>
        <form class="overflow-hidden rounded-xl border border-border bg-white shadow-soft" method="POST" action="{{ route('appointments.store') }}">
            @csrf
            <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6">
                @if ($isStudio)
                    <label class="block text-sm font-medium" for="client_id">Client
                        <select class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="client_id" name="client_id" required>
                            <option value="">Select a client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->name }} · {{ $client->phone }}</option>
                            @endforeach
                        </select>
                    </label>
                @else
                    <div class="rounded-md border border-border bg-background px-3 py-2.5 text-sm sm:col-span-2">
                        <p class="text-xs font-medium text-subtle">Booking for</p>
                        <p class="mt-0.5 font-medium">{{ auth()->user()->name }}</p>
                    </div>
                @endif
                <label class="block text-sm font-medium" for="stylist_id">Stylist
                    <select class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="stylist_id" name="stylist_id" required>
                        <option value="">Select a stylist</option>
                        @foreach ($stylists as $stylist)
                            <option value="{{ $stylist->id }}" @selected(old('stylist_id') == $stylist->id)>{{ $stylist->name }} · {{ $stylist->specialization }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm font-medium" for="service_id">Service
                    <select class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="service_id" name="service_id" required>
                        <option value="">Select a service</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->name }} · {{ $service->duration_min }} min · ₱{{ number_format((float) $service->price, 2) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm font-medium" for="appointment_datetime">Date and time
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="appointment_datetime" type="datetime-local" name="appointment_datetime" value="{{ old('appointment_datetime') }}" required>
                </label>
            </div>
            <div class="mx-5 mb-5 flex items-start gap-3 rounded-lg border border-border bg-background p-4 text-sm text-subtle sm:mx-6">
                <svg viewBox="0 0 24 24" fill="none" class="mt-0.5 size-4 shrink-0 text-brand" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <p>The stylist’s service duration is checked automatically to prevent overlapping bookings. An unpaid invoice will be created with this appointment.</p>
            </div>
            <div class="flex flex-col-reverse justify-end gap-2 border-t border-border bg-background/70 px-5 py-4 sm:flex-row sm:px-6">
                <a class="inline-flex h-10 items-center justify-center rounded-md border border-border bg-white px-4 text-sm font-medium transition-colors hover:bg-muted" href="{{ route('appointments.index') }}">Cancel</a>
                <button class="inline-flex h-10 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" type="submit">Confirm booking</button>
            </div>
        </form>
    </div>
@endsection
