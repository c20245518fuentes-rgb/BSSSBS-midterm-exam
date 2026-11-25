import { Link, usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError } from '../../Components/FormFields';
import type { SharedPageProps } from '../../types';

export default function Login() {
    const { old } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="Log in">
            <div className="mx-auto grid max-w-5xl items-center gap-8 py-4 lg:grid-cols-[1.05fr_.95fr]">
                <div className="hidden rounded-[2rem] bg-sidebar p-10 text-white shadow-soft lg:block">
                    <p className="text-xs font-medium uppercase tracking-[0.22em] text-sidebar-muted">Maison Studio</p>
                    <h1 className="mt-4 text-4xl font-semibold leading-tight">Beauty, thoughtfully booked.</h1>
                    <p className="mt-4 max-w-sm text-sm leading-7 text-sidebar-muted">Manage appointments, services, and your profile in one calm, elegant space.</p>
                    <div className="mt-10 space-y-3 text-sm text-sidebar-muted">
                        <p>• Easy online booking</p>
                        <p>• Personal appointment history</p>
                        <p>• Secure account access</p>
                    </div>
                </div>
                <div className="mx-auto w-full max-w-md">
                    <div className="mb-6 text-center lg:text-left">
                        <p className="text-sm font-medium text-brand">Welcome back</p>
                        <h2 className="mt-1 text-3xl font-semibold tracking-tight text-foreground">Sign in</h2>
                        <p className="mt-2 text-sm text-subtle">Access your appointments and account details.</p>
                    </div>
                    <form action="/login" className="rounded-3xl border border-border bg-surface p-6 shadow-card sm:p-8" method="post">
                        <CsrfField />
                        <div className="space-y-5">
                            <label className="block text-sm font-medium text-foreground" htmlFor="email">Email address
                                <input autoComplete="email" autoFocus className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.email ?? ''} id="email" name="email" placeholder="you@example.com" required type="email" />
                                <FieldError name="email" />
                            </label>
                            <div>
                                <div className="flex items-center justify-between gap-3">
                                    <label className="text-sm font-medium text-foreground" htmlFor="password">Password</label>
                                    <Link className="text-xs font-medium text-brand hover:underline" href="/forgot-password">Forgot password?</Link>
                                </div>
                                <input autoComplete="current-password" className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" id="password" name="password" placeholder="Enter your password" required type="password" />
                                <FieldError name="password" />
                            </div>
                            <label className="flex cursor-pointer items-center gap-2.5 text-sm text-subtle">
                                <input className="size-4 rounded border-border" name="remember" type="checkbox" value="1" />
                                Keep me signed in
                            </label>
                            <button className="inline-flex h-11 w-full items-center justify-center rounded-xl bg-brand px-4 text-sm font-medium text-white shadow-sm transition hover:bg-brand-dark" type="submit">Sign in</button>
                        </div>
                        <p className="mt-6 border-t border-border pt-5 text-center text-sm text-subtle">
                            New to Maison Studio? <Link className="font-medium text-brand hover:underline" href="/register">Create an account</Link>
                        </p>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
