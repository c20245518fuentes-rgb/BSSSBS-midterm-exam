import { Link } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import type { CustomerProfile } from '../../types';

export default function ProfileShow({ user }: { user: CustomerProfile }) {
    return (
        <AppLayout title="My profile">
            <div className="mx-auto max-w-3xl">
                <div className="mb-6">
                    <p className="text-sm font-medium text-brand">Customer account</p>
                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">My profile</h1>
                    <p className="mt-1 text-sm text-subtle">Your account and contact details.</p>
                </div>
                <section aria-labelledby="profile-details" className="overflow-hidden rounded-xl border border-border bg-white shadow-soft">
                    <div className="border-b border-border px-5 py-4 sm:px-6"><h2 className="text-base font-semibold" id="profile-details">Profile details</h2></div>
                    <dl className="divide-y divide-border">
                        <div className="grid gap-1 px-5 py-4 sm:grid-cols-[12rem_1fr] sm:gap-4 sm:px-6"><dt className="text-sm text-subtle">Full name</dt><dd className="text-sm font-medium">{user.name}</dd></div>
                        <div className="grid gap-1 px-5 py-4 sm:grid-cols-[12rem_1fr] sm:gap-4 sm:px-6"><dt className="text-sm text-subtle">Email address</dt><dd className="text-sm font-medium">{user.email}</dd></div>
                        <div className="grid gap-1 px-5 py-4 sm:grid-cols-[12rem_1fr] sm:gap-4 sm:px-6"><dt className="text-sm text-subtle">Phone number</dt><dd className="text-sm font-medium">{user.client?.phone ?? 'Not provided'}</dd></div>
                    </dl>
                    <div className="border-t border-border bg-background/70 px-5 py-4 sm:px-6">
                        <Link className="inline-flex h-10 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="/appointments">Back to appointments</Link>
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
