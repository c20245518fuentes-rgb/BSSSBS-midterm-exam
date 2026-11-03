@extends('layouts.app')

@section('title', 'Appointments — Maison Studio')

@section('content')
    <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-brand">Studio schedule</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight">Appointments</h1>
            <p class="mt-2 text-sm text-subtle">Keep your day running smoothly, one booking at a time.</p>
        </div>
        <a class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="{{ route('appointments.create') }}">
            <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                <path d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Book appointment
        </a>
    </div>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-sm text-subtle">
            <span class="flex size-8 items-center justify-center rounded-lg border border-border bg-white">
                <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                    <path d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            {{ $appointments->total() }} {{ \Illuminate\Support\Str::plural('appointment', $appointments->total()) }}
        </div>
        <span class="text-xs text-subtle">Most recent first</span>
    </div>

    <div class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] border-collapse text-left text-sm">
                <thead>
                    <tr class="border-b border-border bg-background/80 text-xs font-medium uppercase tracking-wide text-subtle">
                        <th class="px-4 py-3.5 sm:px-5">Date &amp; time</th>
                        <th class="px-4 py-3.5">Client</th>
                        <th class="px-4 py-3.5">Stylist</th>
                        <th class="px-4 py-3.5">Service</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Invoice</th>
                        <th class="px-4 py-3.5 text-right">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($appointments as $appointment)
                        <tr class="align-top transition-colors hover:bg-background/60">
                            <td class="whitespace-nowrap px-4 py-4 sm:px-5">
                                <p class="font-medium text-foreground">{{ $appointment->appointment_datetime->format('M j, Y') }}</p>
                                <p class="mt-1 text-xs text-subtle">{{ $appointment->appointment_datetime->format('g:i A') }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-medium">{{ $appointment->client->name }}</p>
                                <p class="mt-1 text-xs text-subtle">{{ $appointment->client->phone }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-medium">{{ $appointment->stylist->name }}</p>
                                <p class="mt-1 text-xs text-subtle">{{ $appointment->stylist->specialization }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-medium">{{ $appointment->service->name }}</p>
                                <p class="mt-1 text-xs text-subtle">{{ $appointment->service->duration_min }} min · ₱{{ number_format((float) $appointment->service->price, 2) }}</p>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium',
                                    'bg-blue-50 text-blue-700' => $appointment->status === \App\Models\Appointment::STATUS_SCHEDULED,
                                    'bg-green-50 text-green-700' => $appointment->status === \App\Models\Appointment::STATUS_COMPLETED,
                                    'bg-muted text-subtle' => $appointment->status === \App\Models\Appointment::STATUS_CANCELLED,
                                ])>
                                    <span @class([
                                        'size-1.5 rounded-full',
                                        'bg-blue-500' => $appointment->status === \App\Models\Appointment::STATUS_SCHEDULED,
                                        'bg-green-500' => $appointment->status === \App\Models\Appointment::STATUS_COMPLETED,
                                        'bg-stone-400' => $appointment->status === \App\Models\Appointment::STATUS_CANCELLED,
                                    ])></span>
                                    {{ $appointment->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if ($appointment->billingInvoice)
                                    <p class="font-medium">₱{{ number_format((float) $appointment->billingInvoice->total_amount, 2) }}</p>
                                    <p @class([
                                        'mt-1 text-xs font-medium',
                                        'text-amber-700' => $appointment->billingInvoice->payment_status === \App\Models\BillingInvoice::PAYMENT_UNPAID,
                                        'text-green-700' => $appointment->billingInvoice->payment_status === \App\Models\BillingInvoice::PAYMENT_PAID,
                                        'text-subtle' => $appointment->billingInvoice->payment_status === \App\Models\BillingInvoice::PAYMENT_REFUNDED,
                                    ])>{{ $appointment->billingInvoice->payment_status }}</p>
                                    @if ($appointment->billingInvoice->payment_status === \App\Models\BillingInvoice::PAYMENT_UNPAID && $appointment->status !== \App\Models\Appointment::STATUS_CANCELLED)
                                        <form class="mt-2" method="POST" action="{{ route('billing-invoices.pay', $appointment->billingInvoice) }}">
                                            @csrf
                                            <button class="inline-flex h-8 items-center rounded-md border border-border bg-white px-2.5 text-xs font-medium transition-colors hover:bg-muted" type="submit">Mark paid</button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-subtle">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right">
                                @if ($appointment->status !== \App\Models\Appointment::STATUS_COMPLETED)
                                    <details class="relative inline-block text-left">
                                        <summary class="inline-flex h-8 list-none items-center gap-1.5 rounded-md border border-border bg-white px-2.5 text-xs font-medium transition-colors hover:bg-muted">
                                            <svg viewBox="0 0 24 24" fill="none" class="size-3.5" aria-hidden="true">
                                                <path d="m15.5 5.5 3 3M4 20l4.3-.9L19.2 8.2a2.1 2.1 0 0 0-3-3L5.3 16.1 4 20Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Edit
                                        </summary>
                                        <div class="absolute right-0 z-20 mt-2 w-72 rounded-lg border border-border bg-white p-4 text-left shadow-soft">
                                            <form method="POST" action="{{ route('appointments.update', $appointment->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <p class="mb-4 text-sm font-semibold">Edit appointment</p>
                                                <div class="space-y-3">
                                                    <label class="block text-xs font-medium text-subtle">Client
                                                        <select class="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" name="client_id" required>
                                                            @foreach ($clients as $client)
                                                                <option value="{{ $client->id }}" @selected($appointment->client_id === $client->id)>{{ $client->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </label>
                                                    <label class="block text-xs font-medium text-subtle">Stylist
                                                        <select class="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" name="stylist_id" required>
                                                            @foreach ($stylists as $stylist)
                                                                <option value="{{ $stylist->id }}" @selected($appointment->stylist_id === $stylist->id)>{{ $stylist->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </label>
                                                    <label class="block text-xs font-medium text-subtle">Service
                                                        <select class="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" name="service_id" required>
                                                            @foreach ($services as $service)
                                                                <option value="{{ $service->id }}" @selected($appointment->service_id === $service->id)>{{ $service->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </label>
                                                    <label class="block text-xs font-medium text-subtle">Date and time
                                                        <input class="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" type="datetime-local" name="appointment_datetime" value="{{ $appointment->appointment_datetime->format('Y-m-d\TH:i') }}" required>
                                                    </label>
                                                    <label class="block text-xs font-medium text-subtle">Status
                                                        <select class="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" name="status" required>
                                                            <option value="{{ \App\Models\Appointment::STATUS_SCHEDULED }}" @selected($appointment->status === \App\Models\Appointment::STATUS_SCHEDULED)>Scheduled</option>
                                                            <option value="{{ \App\Models\Appointment::STATUS_CANCELLED }}" @selected($appointment->status === \App\Models\Appointment::STATUS_CANCELLED)>Cancelled</option>
                                                        </select>
                                                    </label>
                                                </div>
                                                <button class="mt-4 inline-flex h-9 w-full items-center justify-center rounded-md bg-brand px-3 text-sm font-medium text-white transition-colors hover:bg-brand-dark" type="submit">Save changes</button>
                                            </form>
                                        </div>
                                    </details>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="mx-auto flex size-12 items-center justify-center rounded-xl bg-accent text-brand">
                                    <svg viewBox="0 0 24 24" fill="none" class="size-6" aria-hidden="true">
                                        <path d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <h2 class="mt-4 text-base font-semibold">No appointments yet</h2>
                                <p class="mt-1 text-sm text-subtle">Your bookings will appear here once they’re scheduled.</p>
                                <a class="mt-5 inline-flex h-9 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white transition-colors hover:bg-brand-dark" href="{{ route('appointments.create') }}">Book your first appointment</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($appointments->hasPages())
        <div class="mt-6">{{ $appointments->links() }}</div>
    @endif
@endsection
