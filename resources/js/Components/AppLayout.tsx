import { Head, Link, router, usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import type { SharedPageProps } from '../types';

type AppLayoutProps = PropsWithChildren<{ title: string }>;

export default function AppLayout({ children, title }: AppLayoutProps) {
    const { auth, errors, flash } = usePage<SharedPageProps>().props;
    const user = auth.user;
    const messages = Object.values(errors).flatMap((message) =>
        Array.isArray(message) ? message : [message],
    );
    const successMessage = flash.success ?? flash.status;

    return (
        <>
            <Head title={title} />
            <div className="min-h-screen">
                <header className="sticky top-0 z-30 border-b border-border/80 bg-white/90 backdrop-blur-xl">
                    <div className="mx-auto flex max-w-7xl items-center justify-between gap-5 px-4 py-3 sm:px-6 lg:px-8">
                        <Link className="flex shrink-0 items-center gap-3" href={user ? '/appointments' : '/login'}>
                            <span className="flex size-10 items-center justify-center rounded-xl bg-brand text-white shadow-sm">
                                <span aria-hidden="true" className="text-xl">♡</span>
                            </span>
                            <span className="leading-tight">
                                <span className="block text-sm font-semibold tracking-tight">Maison Studio</span>
                                <span className="mt-0.5 block text-[11px] tracking-wide text-subtle">BEAUTY &amp; WELLNESS</span>
                            </span>
                        </Link>

                        {user ? (
                            <>
                                <nav aria-label="Main navigation" className="hidden items-center gap-1 md:flex">
                                    {user.is_admin && <Link className={`rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-muted ${window.location.pathname.startsWith('/dashboard') ? 'bg-muted text-foreground' : 'text-subtle'}`} href="/dashboard">Dashboard</Link>}
                                    <Link className={`rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-muted ${window.location.pathname.startsWith('/appointments') ? 'bg-muted text-foreground' : 'text-subtle'}`} href="/appointments">Appointments</Link>
                                    <Link className={`rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-muted ${window.location.pathname.startsWith('/services') ? 'bg-muted text-foreground' : 'text-subtle'}`} href="/services">Services</Link>
                                    {!user.is_admin && <Link className={`rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-muted ${window.location.pathname.startsWith('/profile') ? 'bg-muted text-foreground' : 'text-subtle'}`} href="/profile">Profile</Link>}
                                </nav>
                                <div className="flex items-center gap-3">
                                    <div className="hidden text-right sm:block">
                                        <p className="text-sm font-medium">{user.name}</p>
                                        <p className="text-xs text-subtle">{user.is_admin ? 'Studio administrator' : 'Customer account'}</p>
                                    </div>
                                    <span className="flex size-9 items-center justify-center rounded-full bg-accent text-sm font-semibold text-brand">
                                        {user.name.slice(0, 1).toUpperCase()}
                                    </span>
                                    <button
                                        className="inline-flex h-9 items-center gap-2 rounded-md border border-border bg-white px-3 text-sm font-medium transition-colors hover:bg-muted"
                                        onClick={() => router.post('/logout')}
                                        type="button"
                                    >
                                        <span className="hidden sm:inline">Log out</span>
                                    </button>
                                </div>
                            </>
                        ) : (
                            <div className="flex items-center gap-2">
                                <Link className="rounded-md px-3 py-2 text-sm font-medium text-subtle transition-colors hover:bg-muted hover:text-foreground" href="/login">Log in</Link>
                                <Link className="inline-flex h-9 items-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="/register">Create account</Link>
                            </div>
                        )}
                    </div>
                    {user && (
                        <nav aria-label="Mobile navigation" className="flex gap-1 border-t border-border/70 px-4 py-2 md:hidden">
                            {user.is_admin && <Link className="rounded-md px-3 py-1.5 text-sm font-medium text-subtle" href="/dashboard">Dashboard</Link>}
                            <Link className="rounded-md px-3 py-1.5 text-sm font-medium text-subtle" href="/appointments">Appointments</Link>
                            <Link className="rounded-md px-3 py-1.5 text-sm font-medium text-subtle" href="/services">Services</Link>
                            {!user.is_admin && <Link className="rounded-md px-3 py-1.5 text-sm font-medium text-subtle" href="/profile">Profile</Link>}
                        </nav>
                    )}
                </header>

                <main className="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                    {successMessage && (
                        <div className="mb-6 flex items-start gap-3 rounded-lg border border-[#d6e8da] bg-success px-4 py-3 text-sm text-[#315d3d] shadow-sm" role="status">
                            <span aria-hidden="true">✓</span>
                            <span>{successMessage}</span>
                        </div>
                    )}
                    {messages.length > 0 && (
                        <div className="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm" role="alert">
                            <p className="font-medium">Please check the following:</p>
                            <ul className="mt-1 list-inside list-disc space-y-0.5">
                                {messages.map((message, index) => <li key={`${index}-${message}`}>{message}</li>)}
                            </ul>
                        </div>
                    )}
                    {children}
                </main>
                <footer className="mx-auto max-w-7xl px-4 pb-8 text-center text-xs text-subtle sm:px-6 lg:px-8">
                    <span>Maison Studio</span><span className="mx-1.5">·</span><span>Beauty, thoughtfully booked.</span>
                </footer>
            </div>
        </>
    );
}
