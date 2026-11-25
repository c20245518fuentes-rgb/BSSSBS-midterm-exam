import { Link } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError } from '../../Components/FormFields';
import type { CustomerProfile } from '../../types';

export default function ProfileShow({ user }: { user: CustomerProfile }) {
    return (
        <AppLayout title="My profile">
            <div className="mx-auto max-w-5xl space-y-6">
                <section className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#5f4549] via-[#76575d] to-[#8b6a64] p-6 text-white shadow-soft sm:p-8">
                    <div className="relative z-10 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p className="text-xs font-medium uppercase tracking-[0.22em] text-white/75">Customer account</p>
                            <h1 className="mt-2 text-3xl font-semibold tracking-tight">My profile</h1>
                            <p className="mt-2 max-w-xl text-sm text-white/80">Manage your personal information and keep your account secure.</p>
                        </div>
                        <div className="flex items-center gap-3 rounded-2xl bg-surface/10 px-4 py-3 backdrop-blur-sm">
                            <span className="flex size-11 items-center justify-center rounded-full bg-surface/20 text-base font-semibold">{user.name.slice(0, 1).toUpperCase()}</span>
                            <div>
                                <p className="text-sm font-medium">{user.name}</p>
                                <p className="text-xs text-white/75">{user.email}</p>
                            </div>
                        </div>
                    </div>
                    <div className="pointer-events-none absolute -right-10 -top-10 size-40 rounded-full bg-surface/10 blur-2xl" />
                </section>

                <div className="grid gap-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(320px,.85fr)]">
                    <section aria-labelledby="profile-information" className="overflow-hidden rounded-3xl border border-border bg-surface shadow-card">
                        <div className="border-b border-border px-5 py-4 sm:px-6">
                            <h2 className="text-lg font-semibold" id="profile-information">Personal information</h2>
                            <p className="mt-1 text-sm text-subtle">Keep your account and contact details up to date.</p>
                        </div>
                        <form action="/profile" className="space-y-4 p-5 sm:p-6" method="post">
                            <CsrfField />
                            <input name="_method" type="hidden" value="PUT" />
                            <label className="block text-sm font-medium text-foreground" htmlFor="name">Full name
                                <input autoComplete="name" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={user.name} id="name" maxLength={255} name="name" required />
                                <FieldError name="name" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="email">Email address
                                <input autoComplete="email" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={user.email} id="email" name="email" required type="email" />
                                <FieldError name="email" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="phone">Phone number
                                <input autoComplete="tel" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={user.client?.phone ?? ''} id="phone" inputMode="tel" maxLength={13} minLength={11} name="phone" placeholder="09171234567" required type="tel" />
                                <FieldError name="phone" />
                            </label>
                            <div className="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                                <Link className="inline-flex h-11 items-center justify-center rounded-xl border border-border bg-surface px-4 text-sm font-medium text-foreground transition hover:bg-muted" href="/appointments">Back to appointments</Link>
                                <button className="inline-flex h-11 items-center justify-center rounded-xl bg-brand px-5 text-sm font-medium text-white shadow-sm transition hover:bg-brand-dark" type="submit">Save changes</button>
                            </div>
                        </form>
                    </section>

                    <section aria-labelledby="change-password" className="overflow-hidden rounded-3xl border border-border bg-surface shadow-card">
                        <div className="border-b border-border px-5 py-4 sm:px-6">
                            <h2 className="text-lg font-semibold" id="change-password">Change password</h2>
                            <p className="mt-1 text-sm text-subtle">Use your current password to set a new one.</p>
                        </div>
                        <form action="/profile/password" className="space-y-4 p-5 sm:p-6" method="post">
                            <CsrfField />
                            <input name="_method" type="hidden" value="PUT" />
                            <label className="block text-sm font-medium text-foreground" htmlFor="current_password">Current password
                                <input autoComplete="current-password" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" id="current_password" name="current_password" required type="password" />
                                <FieldError name="current_password" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="password">New password
                                <input autoComplete="new-password" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" id="password" minLength={8} name="password" required type="password" />
                                <FieldError name="password" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="password_confirmation">Confirm new password
                                <input autoComplete="new-password" className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" id="password_confirmation" minLength={8} name="password_confirmation" required type="password" />
                            </label>
                            <div className="flex justify-end pt-2">
                                <button className="inline-flex h-11 items-center justify-center rounded-xl bg-brand px-5 text-sm font-medium text-white shadow-sm transition hover:bg-brand-dark" type="submit">Update password</button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </AppLayout>
    );
}
