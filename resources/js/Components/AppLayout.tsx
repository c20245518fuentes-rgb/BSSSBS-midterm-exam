import { Head, Link, router, usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import type { SharedPageProps } from '../types';

type AppLayoutProps = PropsWithChildren<{ title: string }>;

type NavItem = { href: string; label: string; icon: string; match: (path: string) => boolean; show?: boolean };

export default function AppLayout({ children, title }: AppLayoutProps) {
    const { auth, errors, flash } = usePage<SharedPageProps>().props;
    const user = auth.user;
    const messages = Object.values(errors).flatMap((message) => (Array.isArray(message) ? message : [message]));
    const successMessage = flash.success ?? flash.status;
    const path = typeof window !== 'undefined' ? window.location.pathname : '';

    if (!user) {
        return (
            <>
                <Head title={title} />
                <div className="min-h-screen bg-background">
                    <header className="mx-auto flex max-w-6xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                        <Link className="flex items-center gap-3" href="/login">
                            <span className="flex size-10 items-center justify-center rounded-2xl bg-brand text-white shadow-card">♡</span>
                            <span className="leading-tight">
                                <span className="block text-sm font-semibold tracking-tight text-foreground">Maison Studio</span>
                                <span className="block text-[10px] tracking-[0.2em] text-subtle">BEAUTY &amp; WELLNESS</span>
                            </span>
                        </Link>
                        <div className="flex items-center gap-2">
                            <Link className="rounded-full px-4 py-2 text-sm font-medium text-subtle transition hover:bg-white hover:text-foreground" href="/login">Log in</Link>
                            <Link className="inline-flex h-10 items-center rounded-full bg-brand px-4 text-sm font-medium text-white shadow-card transition hover:bg-brand-dark" href="/register">Create account</Link>
                        </div>
                    </header>
                    <main className="mx-auto w-full max-w-6xl px-4 pb-12 sm:px-6 lg:px-8">
                        {successMessage && <Alert kind="success">{successMessage}</Alert>}
                        {messages.length > 0 && <Alert kind="error">{messages}</Alert>}
                        {children}
                    </main>
                </div>
            </>
        );
    }

    const nav: NavItem[] = [
        { href: '/dashboard', label: 'Dashboard', icon: '⌂', match: (p: string) => p.startsWith('/dashboard'), show: user.can_manage_studio },
        { href: '/appointments', label: 'Appointments', icon: '▦', match: (p: string) => p.startsWith('/appointments') },
        { href: '/services', label: 'Services', icon: '✦', match: (p: string) => p.startsWith('/services') },
        { href: '/profile', label: 'Profile', icon: '◌', match: (p: string) => p.startsWith('/profile'), show: !user.can_manage_studio },
    ].filter((item) => item.show !== false);

    return (
        <>
            <Head title={title} />
            <div className="min-h-screen bg-background lg:flex">
                <aside className="hidden w-[270px] shrink-0 flex-col bg-sidebar text-white lg:flex lg:sticky lg:top-0 lg:h-screen">
                    <div className="px-6 py-6">
                        <Link className="flex items-center gap-3" href={user.can_manage_studio ? '/dashboard' : '/appointments'}>
                            <span className="flex size-11 items-center justify-center rounded-2xl bg-white/12 text-lg">♡</span>
                            <span className="leading-tight">
                                <span className="block text-[15px] font-semibold tracking-tight">Maison Studio</span>
                                <span className="block text-[10px] tracking-[0.22em] text-sidebar-muted">BEAUTY &amp; WELLNESS</span>
                            </span>
                        </Link>
                    </div>
                    <nav aria-label="Main navigation" className="flex-1 space-y-1 px-3">
                        {nav.map((item) => {
                            const active = item.match(path);
                            return (
                                <Link
                                    className={`flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition ${active ? 'bg-white/14 text-white shadow-inner' : 'text-sidebar-muted hover:bg-white/8 hover:text-white'}`}
                                    href={item.href}
                                    key={item.href}
                                >
                                    <span className={`flex size-7 items-center justify-center rounded-lg text-xs ${active ? 'bg-white/15' : 'bg-white/5'}`}>{item.icon}</span>
                                    {item.label}
                                </Link>
                            );
                        })}
                    </nav>
                    <div className="px-4 pb-5 pt-3">
                        <div className="overflow-hidden rounded-2xl bg-sidebar-deep/70 p-4">
                            <p className="text-[11px] font-medium uppercase tracking-[0.18em] text-sidebar-muted">Maison Studio</p>
                            <p className="mt-2 text-sm font-semibold leading-snug text-white">Look good. Feel great.</p>
                            <p className="mt-1 text-xs leading-relaxed text-sidebar-muted">Book your next visit and keep your schedule in one place.</p>
                            {!user.can_manage_studio && (
                                <Link className="mt-3 inline-flex h-9 items-center rounded-full bg-white px-4 text-xs font-medium text-sidebar transition hover:bg-brand-soft" href="/appointments/create">
                                    Book appointment
                                </Link>
                            )}
                        </div>
                        <button className="mt-3 flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-sidebar-muted transition hover:bg-white/8 hover:text-white" onClick={() => router.post('/logout')} type="button">
                            <span className="flex size-7 items-center justify-center rounded-lg bg-white/5 text-xs">↪</span>
                            Log out
                        </button>
                    </div>
                </aside>

                <div className="flex min-w-0 flex-1 flex-col">
                    <header className="sticky top-0 z-20 border-b border-border/70 bg-background/85 backdrop-blur-xl">
                        <div className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                            <div className="flex min-w-0 items-center gap-3 lg:hidden">
                                <span className="flex size-9 items-center justify-center rounded-xl bg-brand text-white">♡</span>
                                <span className="truncate text-sm font-semibold">Maison Studio</span>
                            </div>
                            <div className="hidden min-w-0 items-center gap-2 rounded-full border border-border bg-white px-4 py-2 text-sm text-subtle shadow-sm sm:flex sm:w-full sm:max-w-md">
                                <span aria-hidden="true">⌕</span>
                                <span className="truncate">Search appointments, services, or clients</span>
                            </div>
                            <div className="ml-auto flex items-center gap-3">
                                <div className="hidden text-right sm:block">
                                    <p className="text-sm font-semibold text-foreground">{user.name}</p>
                                    <p className="text-xs text-subtle">{user.can_manage_studio ? 'Studio administrator' : 'Customer account'}</p>
                                </div>
                                <span className="flex size-10 items-center justify-center rounded-full bg-brand-soft text-sm font-semibold text-brand ring-2 ring-white shadow-sm">
                                    {user.name.slice(0, 1).toUpperCase()}
                                </span>
                                <button className="inline-flex h-9 items-center rounded-full border border-border bg-white px-3 text-sm font-medium text-subtle transition hover:bg-muted hover:text-foreground lg:hidden" onClick={() => router.post('/logout')} type="button">Log out</button>
                            </div>
                        </div>
                        <nav aria-label="Mobile navigation" className="flex gap-1 overflow-x-auto px-4 pb-3 lg:hidden sm:px-6">
                            {nav.map((item) => {
                                const active = item.match(path);
                                return (
                                    <Link className={`whitespace-nowrap rounded-full px-3 py-1.5 text-sm font-medium ${active ? 'bg-brand text-white' : 'bg-white text-subtle border border-border'}`} href={item.href} key={item.href}>
                                        {item.label}
                                    </Link>
                                );
                            })}
                        </nav>
                    </header>

                    <main className="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                        {successMessage && <Alert kind="success">{successMessage}</Alert>}
                        {messages.length > 0 && <Alert kind="error">{messages}</Alert>}
                        {children}
                    </main>
                </div>
            </div>
        </>
    );
}

function Alert({ kind, children }: { kind: 'success' | 'error'; children: string | string[] }) {
    if (kind === 'success') {
        return (
            <div className="mb-6 flex items-start gap-3 rounded-2xl border border-[#d8e7dc] bg-success px-4 py-3 text-sm text-[#2f5a3b] shadow-sm" role="status">
                <span aria-hidden="true">✓</span>
                <span>{children as string}</span>
            </div>
        );
    }

    const list = children as string[];
    return (
        <div className="mb-6 rounded-2xl border border-red-200/80 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm" role="alert">
            <p className="font-medium">Please check the following:</p>
            <ul className="mt-1 list-inside list-disc space-y-0.5">{list.map((message, index) => <li key={`${index}-${message}`}>{message}</li>)}</ul>
        </div>
    );
}
