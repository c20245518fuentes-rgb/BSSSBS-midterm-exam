@extends('layouts.app')

@section('title', 'Add service — Maison Studio')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a class="mb-5 inline-flex items-center gap-2 text-sm text-subtle transition-colors hover:text-foreground" href="{{ route('services.index') }}">
            <svg viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                <path d="m15 18-6-6 6-6M9 12h12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back to services
        </a>
        <div class="mb-6">
            <p class="text-sm font-medium text-brand">Service catalog</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Add a service</h1>
            <p class="mt-1 text-sm text-subtle">Add a treatment to your salon’s booking menu.</p>
        </div>
        <form class="rounded-xl border border-border bg-white shadow-soft" method="POST" action="{{ route('services.store') }}">
            @csrf
            <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6">
                <label class="block text-sm font-medium" for="category">Category
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="category" name="category" value="{{ old('category') }}" placeholder="e.g. Hair, Nails" required maxlength="100">
                </label>
                <label class="block text-sm font-medium" for="name">Service title
                    <input class="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Signature Haircut" required maxlength="150">
                </label>
                <label class="block text-sm font-medium" for="duration_min">Duration
                    <span class="relative mt-2 block">
                        <input class="block h-10 w-full rounded-md border border-border bg-white px-3 pr-16 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="duration_min" type="number" name="duration_min" value="{{ old('duration_min') }}" placeholder="45" min="1" max="1440" required>
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-subtle">minutes</span>
                    </span>
                </label>
                <label class="block text-sm font-medium" for="price">Price
                    <span class="relative mt-2 block">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-subtle">₱</span>
                        <input class="block h-10 w-full rounded-md border border-border bg-white pl-8 pr-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="price" type="number" name="price" value="{{ old('price') }}" placeholder="500.00" min="0" step="0.01" required>
                    </span>
                </label>
            </div>
            <div class="flex flex-col-reverse justify-end gap-2 border-t border-border bg-background/70 px-5 py-4 sm:flex-row sm:px-6">
                <a class="inline-flex h-10 items-center justify-center rounded-md border border-border bg-white px-4 text-sm font-medium transition-colors hover:bg-muted" href="{{ route('services.index') }}">Cancel</a>
                <button class="inline-flex h-10 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" type="submit">Save service</button>
            </div>
        </form>
    </div>
@endsection
