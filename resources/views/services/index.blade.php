@extends('layouts.app')

@section('title', 'Services — Maison Studio')

@section('content')
    <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-brand">{{ auth()->user()->canManageStudio() ? 'Your menu' : 'Our menu' }}</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight">Services</h1>
            <p class="mt-2 text-sm text-subtle">{{ auth()->user()->canManageStudio() ? 'Manage treatments, pricing, and appointment durations.' : 'Explore our treatments and choose one for your next appointment.' }}</p>
        </div>
        @if (auth()->user()->canManageStudio())
            <a class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="{{ route('services.create') }}">
                <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                    <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
                Add service
            </a>
        @else
            <a class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="{{ route('appointments.create') }}">
                Book an appointment
            </a>
        @endif
    </div>

    @if ($services->isNotEmpty())
        <div class="mb-4 flex items-center gap-2 text-sm text-subtle">
            <span class="flex size-8 items-center justify-center rounded-lg border border-border bg-white">
                <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            {{ $services->total() }} {{ \Illuminate\Support\Str::plural('service', $services->total()) }} {{ auth()->user()->canManageStudio() ? 'in your catalog' : 'available' }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse ($services as $service)
            <article class="overflow-hidden rounded-xl border border-border bg-white shadow-sm transition-shadow hover:shadow-soft">
                @if (auth()->user()->canManageStudio())
                    <form method="POST" action="{{ route('services.update', $service->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4 p-4 sm:grid-cols-[minmax(0,1.1fr)_minmax(0,1.5fr)_minmax(110px,.7fr)_minmax(130px,.8fr)_auto] sm:items-end sm:gap-4 sm:p-5">
                            <label class="block text-xs font-medium text-subtle" for="category-{{ $service->id }}">Category
                                <input class="mt-1.5 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm font-medium text-foreground shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="category-{{ $service->id }}" name="category" value="{{ $service->category }}" required maxlength="100">
                            </label>
                            <label class="block text-xs font-medium text-subtle" for="name-{{ $service->id }}">Service title
                                <input class="mt-1.5 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm font-medium text-foreground shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="name-{{ $service->id }}" name="name" value="{{ $service->name }}" required maxlength="150">
                            </label>
                            <label class="block text-xs font-medium text-subtle" for="duration-{{ $service->id }}">Minutes
                                <input class="mt-1.5 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm font-medium text-foreground shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="duration-{{ $service->id }}" type="number" name="duration_min" value="{{ $service->duration_min }}" min="1" max="1440" required>
                            </label>
                            <label class="block text-xs font-medium text-subtle" for="price-{{ $service->id }}">Price (₱)
                                <input class="mt-1.5 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm font-medium text-foreground shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="price-{{ $service->id }}" type="number" name="price" value="{{ $service->price }}" min="0" step="0.01" required>
                            </label>
                            <button class="inline-flex h-10 items-center justify-center rounded-md border border-border bg-white px-3 text-sm font-medium transition-colors hover:bg-muted sm:self-end" type="submit">Save</button>
                        </div>
                        <div class="border-t border-border bg-background/60 px-4 py-2.5 text-xs text-subtle sm:px-5">
                            {{ $service->appointments->count() }} {{ \Illuminate\Support\Str::plural('appointment', $service->appointments->count()) }} booked
                        </div>
                    </form>
                @else
                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-brand">{{ $service->category }}</p>
                            <h2 class="mt-1 text-lg font-semibold">{{ $service->name }}</h2>
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            <span class="text-subtle">{{ $service->duration_min }} min</span>
                            <span class="font-semibold">₱{{ number_format((float) $service->price, 2) }}</span>
                        </div>
                    </div>
                @endif
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-border bg-white px-6 py-16 text-center">
                <span class="mx-auto flex size-12 items-center justify-center rounded-xl bg-accent text-brand">
                    <svg viewBox="0 0 24 24" fill="none" class="size-6" aria-hidden="true">
                        <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </span>
                <h2 class="mt-4 text-base font-semibold">{{ auth()->user()->canManageStudio() ? 'Your service menu is empty' : 'No services available yet' }}</h2>
                <p class="mt-1 text-sm text-subtle">{{ auth()->user()->canManageStudio() ? 'Add your first treatment to start building your salon menu.' : 'Please check back soon for available treatments.' }}</p>
                @if (auth()->user()->canManageStudio())
                    <a class="mt-5 inline-flex h-9 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white transition-colors hover:bg-brand-dark" href="{{ route('services.create') }}">Add your first service</a>
                @endif
            </div>
        @endforelse
    </div>

    @if ($services->hasPages())
        <div class="mt-6">{{ $services->links() }}</div>
    @endif
@endsection
