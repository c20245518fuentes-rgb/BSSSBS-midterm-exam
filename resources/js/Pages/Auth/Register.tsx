import { Link } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError } from '../../Components/FormFields';
import type { SharedPageProps } from '../../types';

    export default function Register({ isInitialSetup }: { isInitialSetup: boolean }) {
    const { old } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="Create account">
            <div className="mx-auto max-w-md py-5 sm:py-10">
                <div className="mb-7 text-center">
                    <span className="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-accent text-brand text-2xl">+</span>
                    <p className="text-sm font-medium text-brand">Welcome to Maison Studio</p>
                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">Create your account</h1>
                    <p className="mt-2 text-sm text-subtle">{isInitialSetup ? 'Set up the studio administrator account.' : 'Create an account to book and manage your appointments.'}</p>
                </div>
                <form action="/register" className="rounded-xl border border-border bg-white p-6 shadow-soft sm:p-8" method="post">
                    <CsrfField />
                    <div className="space-y-4">
                        <label className="block text-sm font-medium" htmlFor="name">Full name
                            <input autoComplete="name" autoFocus className="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" defaultValue={old.name ?? ''} id="name" maxLength={255} name="name" placeholder="Your name" required />
                            <FieldError name="name" />
                        </label>
                        <label className="block text-sm font-medium" htmlFor="email">Email address
                            <input autoComplete="email" className="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" defaultValue={old.email ?? ''} id="email" name="email" placeholder="you@example.com" required type="email" />
                            <FieldError name="email" />
                        </label>
                        {!isInitialSetup && (
                            <label className="block text-sm font-medium" htmlFor="phone">Phone number
                                <input autoComplete="tel" className="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" defaultValue={old.phone ?? ''} id="phone" inputMode="tel" maxLength={13} minLength={11} name="phone" placeholder="09171234567" required type="tel" />
                                <FieldError name="phone" />
                            </label>
                        )}
                        <label className="block text-sm font-medium" htmlFor="password">Password
                            <input autoComplete="new-password" className="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="password" minLength={8} name="password" placeholder="At least 8 characters" required type="password" />
                            <FieldError name="password" />
                        </label>
                        <label className="block text-sm font-medium" htmlFor="password_confirmation">Confirm password
                            <input autoComplete="new-password" className="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="password_confirmation" minLength={8} name="password_confirmation" placeholder="Re-enter your password" required type="password" />
                        </label>
                        <button className="inline-flex h-10 w-full items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2" type="submit">Create account</button>
                    </div>
                    <p className="mt-6 border-t border-border pt-5 text-center text-sm text-subtle">
                        Already have an account? <Link className="font-medium text-brand hover:underline" href="/login">Sign in</Link>
                    </p>
                </form>
            </div>
        </AppLayout>
    );
}
