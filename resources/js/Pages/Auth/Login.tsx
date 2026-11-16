import { Link } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError } from '../../Components/FormFields';
import type { SharedPageProps } from '../../types';
import { usePage } from '@inertiajs/react';

export default function Login() {
    const { old } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="Log in">
            <div className="mx-auto max-w-md py-5 sm:py-10">
                <div className="mb-7 text-center">
                    <span className="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-accent text-brand text-2xl">♡</span>
                    <p className="text-sm font-medium text-brand">Welcome back</p>
                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">Sign in to your studio</h1>
                    <p className="mt-2 text-sm text-subtle">Manage appointments, services, and invoices.</p>
                </div>
                <form action="/login" className="rounded-xl border border-border bg-white p-6 shadow-soft sm:p-8" method="post">
                    <CsrfField />
                    <div className="space-y-5">
                        <label className="block text-sm font-medium" htmlFor="email">Email address
                            <input autoComplete="email" autoFocus className="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" defaultValue={old.email ?? ''} id="email" name="email" placeholder="you@example.com" required type="email" />
                            <FieldError name="email" />
                        </label>
                        <div>
                            <div className="flex items-center justify-between gap-3">
                                <label className="text-sm font-medium" htmlFor="password">Password</label>
                                <Link className="text-xs font-medium text-brand hover:underline" href="/forgot-password">Forgot password?</Link>
                            </div>
                            <input autoComplete="current-password" className="mt-2 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/15" id="password" name="password" placeholder="Enter your password" required type="password" />
                            <FieldError name="password" />
                        </div>
                        <label className="flex cursor-pointer items-center gap-2.5 text-sm text-subtle">
                            <input className="size-4 rounded border-border" name="remember" type="checkbox" value="1" />
                            Keep me signed in
                        </label>
                        <button className="inline-flex h-10 w-full items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2" type="submit">Sign in</button>
                    </div>
                    <p className="mt-6 border-t border-border pt-5 text-center text-sm text-subtle">
                        New to Maison Studio? <Link className="font-medium text-brand hover:underline" href="/register">Create an account</Link>
                    </p>
                </form>
                <p className="mt-6 text-center text-xs text-subtle">Your studio workspace, all in one place.</p>
            </div>
        </AppLayout>
    );
}
