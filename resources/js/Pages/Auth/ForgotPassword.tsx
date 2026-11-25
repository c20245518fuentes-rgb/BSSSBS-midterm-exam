import { Link, usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError } from '../../Components/FormFields';
import type { SharedPageProps } from '../../types';

export default function ForgotPassword() {
    const { old } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="Reset password">
            <div className="mx-auto max-w-md py-4 sm:py-8">
                <div className="mb-6 text-center lg:text-left">
                    <p className="text-sm font-medium text-brand">Account recovery</p>
                    <h1 className="mt-1 text-3xl font-semibold tracking-tight">Forgot your password?</h1>
                    <p className="mt-2 text-sm leading-6 text-subtle">Enter the email address linked to your account and we’ll send you a reset link.</p>
                </div>
                <form action="/forgot-password" className="rounded-3xl border border-border bg-surface p-6 shadow-card sm:p-8" method="post">
                    <CsrfField />
                    <label className="block text-sm font-medium text-foreground" htmlFor="email">Email address
                        <input autoComplete="email" autoFocus className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.email ?? ''} id="email" name="email" placeholder="you@example.com" required type="email" />
                        <FieldError name="email" />
                    </label>
                    <button className="mt-5 inline-flex h-11 w-full items-center justify-center rounded-xl bg-brand px-4 text-sm font-medium text-white shadow-sm transition hover:bg-brand-dark" type="submit">Send reset link</button>
                    <Link className="mt-5 flex items-center justify-center text-sm font-medium text-subtle transition hover:text-foreground" href="/login">← Back to sign in</Link>
                </form>
            </div>
        </AppLayout>
    );
}
