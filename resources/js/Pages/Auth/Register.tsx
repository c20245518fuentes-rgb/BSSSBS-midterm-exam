import { Link, usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError } from '../../Components/FormFields';
import type { SharedPageProps } from '../../types';

export default function Register({ isInitialSetup }: { isInitialSetup: boolean }) {
    const { old } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="Create account">
            <div className="mx-auto grid max-w-5xl items-center gap-8 py-4 lg:grid-cols-[1.05fr_.95fr]">
                <div className="hidden rounded-[2rem] bg-sidebar p-10 text-white shadow-soft lg:block">
                    <p className="text-xs font-medium uppercase tracking-[0.22em] text-sidebar-muted">Maison Studio</p>
                    <h1 className="mt-4 text-4xl font-semibold leading-tight">{isInitialSetup ? 'Set up your studio.' : 'Begin your beauty journey.'}</h1>
                    <p className="mt-4 max-w-sm text-sm leading-7 text-sidebar-muted">
                        {isInitialSetup ? 'Create the first administrator account to manage appointments and services.' : 'Create an account to book appointments and keep your details up to date.'}
                    </p>
                </div>
                <div className="mx-auto w-full max-w-md">
                    <div className="mb-6 text-center lg:text-left">
                        <p className="text-sm font-medium text-brand">Welcome to Maison Studio</p>
                        <h2 className="mt-1 text-3xl font-semibold tracking-tight text-foreground">Create account</h2>
                        <p className="mt-2 text-sm text-subtle">{isInitialSetup ? 'Set up the studio administrator account.' : 'Book and manage your appointments with ease.'}</p>
                    </div>
                    <form action="/register" className="rounded-3xl border border-border bg-surface p-6 shadow-card sm:p-8" method="post">
                        <CsrfField />
                        <div className="space-y-4">
                            <label className="block text-sm font-medium text-foreground" htmlFor="name">Full name
                                <input autoComplete="name" autoFocus className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.name ?? ''} id="name" maxLength={255} name="name" placeholder="Your name" required />
                                <FieldError name="name" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="email">Email address
                                <input autoComplete="email" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.email ?? ''} id="email" name="email" placeholder="you@example.com" required type="email" />
                                <FieldError name="email" />
                            </label>
                            {!isInitialSetup && (
                                <label className="block text-sm font-medium text-foreground" htmlFor="phone">Phone number
                                    <input autoComplete="tel" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.phone ?? ''} id="phone" inputMode="tel" maxLength={13} minLength={11} name="phone" placeholder="09171234567" required type="tel" />
                                    <FieldError name="phone" />
                                </label>
                            )}
                            <label className="block text-sm font-medium text-foreground" htmlFor="password">Password
                                <input autoComplete="new-password" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" id="password" minLength={8} name="password" placeholder="At least 8 characters" required type="password" />
                                <FieldError name="password" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="password_confirmation">Confirm password
                                <input autoComplete="new-password" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" id="password_confirmation" minLength={8} name="password_confirmation" placeholder="Re-enter your password" required type="password" />
                            </label>
                            <button className="inline-flex h-11 w-full items-center justify-center rounded-xl bg-brand px-4 text-sm font-medium text-white shadow-sm transition hover:bg-brand-dark" type="submit">Create account</button>
                        </div>
                        <p className="mt-6 border-t border-border pt-5 text-center text-sm text-subtle">
                            Already have an account? <Link className="font-medium text-brand hover:underline" href="/login">Sign in</Link>
                        </p>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
