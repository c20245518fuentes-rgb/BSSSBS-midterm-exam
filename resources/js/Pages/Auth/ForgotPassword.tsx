import { Link } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError } from '../../Components/FormFields';
import type { SharedPageProps } from '../../types';

export default function ForgotPassword() {
    const { old } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="Reset password">
            <div className="mx-auto max-w-md py-5 sm:py-10">
                <div className="mb-7 text-center">
                    <span className="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-accent text-brand text-2xl">⌑</span>
                    <p className="text-sm font-medium text-brand">Account recovery</p>
                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">Forgot your password?</h1>
                    <p className="mt-2 text-sm leading-6 text-subtle">Enter the email address linked to your account and we’ll send you a reset link.</p>
                </div>
                <form action="/forgot-password" className="rounded-xl border border-border bg-white p-6 shadow-soft sm:p-8" method="post">
                    <CsrfField />
                    <label className="block text-sm font-medium" htmlFor="email">Email address
                        <input autoComplete="email" autoFocus className="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" defaultValue={old.email ?? ''} id="email" name="email" placeholder="you@example.com" required type="email" />
                        <FieldError name="email" />
                    </label>
                    <button className="mt-5 inline-flex h-10 w-full items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2" type="submit">Send reset link</button>
                    <Link className="mt-5 flex items-center justify-center gap-2 text-sm font-medium text-subtle transition-colors hover:text-foreground" href="/login">← Back to sign in</Link>
                </form>
            </div>
        </AppLayout>
    );
}
