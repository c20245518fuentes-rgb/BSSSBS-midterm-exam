import { usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError } from '../../Components/FormFields';
import type { SharedPageProps } from '../../types';

export default function ResetPassword({ token, email }: { token: string; email: string }) {
    const old = usePage<SharedPageProps>().props.old;

    return (
        <AppLayout title="Choose a new password">
            <div className="mx-auto max-w-md py-4 sm:py-8">
                <div className="mb-6 text-center lg:text-left">
                    <p className="text-sm font-medium text-brand">Almost there</p>
                    <h1 className="mt-1 text-3xl font-semibold tracking-tight">Choose a new password</h1>
                    <p className="mt-2 text-sm text-subtle">Use a secure password with at least 8 characters.</p>
                </div>
                <form action="/reset-password" className="rounded-3xl border border-border bg-surface p-6 shadow-card sm:p-8" method="post">
                    <CsrfField />
                    <input name="token" type="hidden" value={token} />
                    <div className="space-y-4">
                        <label className="block text-sm font-medium text-foreground" htmlFor="email">Email address
                            <input autoComplete="email" autoFocus className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.email ?? email} id="email" name="email" placeholder="you@example.com" required type="email" />
                            <FieldError name="email" />
                        </label>
                        <label className="block text-sm font-medium text-foreground" htmlFor="password">New password
                            <input autoComplete="new-password" className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" id="password" minLength={8} name="password" placeholder="At least 8 characters" required type="password" />
                            <FieldError name="password" />
                        </label>
                        <label className="block text-sm font-medium text-foreground" htmlFor="password_confirmation">Confirm new password
                            <input autoComplete="new-password" className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" id="password_confirmation" minLength={8} name="password_confirmation" placeholder="Re-enter your password" required type="password" />
                        </label>
                        <button className="inline-flex h-11 w-full items-center justify-center rounded-xl bg-brand px-4 text-sm font-medium text-white shadow-sm transition hover:bg-brand-dark" type="submit">Save new password</button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
