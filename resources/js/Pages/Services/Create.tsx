import { Link, usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError } from '../../Components/FormFields';
import type { SharedPageProps } from '../../types';

export default function CreateService() {
    const { old } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="Add service">
            <div className="mx-auto max-w-3xl space-y-6">
                <Link className="inline-flex items-center gap-2 text-sm font-medium text-subtle transition hover:text-foreground" href="/services">← Back to services</Link>
                <section className="overflow-hidden rounded-3xl border border-border bg-surface shadow-card">
                    <div className="border-b border-border bg-gradient-to-r from-[#5f4549] to-[#76575d] px-5 py-5 text-white sm:px-6">
                        <p className="text-xs font-medium uppercase tracking-[0.2em] text-white/75">Service catalog</p>
                        <h1 className="mt-1 text-2xl font-semibold tracking-tight">Add a service</h1>
                        <p className="mt-1 text-sm text-white/80">Add a treatment to your salon’s booking menu.</p>
                    </div>
                    <form action="/services" method="post">
                        <CsrfField />
                        <div className="grid gap-5 p-5 sm:grid-cols-2 sm:p-6">
                            <label className="block text-sm font-medium text-foreground" htmlFor="category">Category
                                <input className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.category ?? ''} id="category" maxLength={100} name="category" placeholder="e.g. Hair, Nails" required />
                                <FieldError name="category" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="name">Service title
                                <input className="mt-2 block h-11 w-full rounded-xl border border-border bg-surface px-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.name ?? ''} id="name" maxLength={150} name="name" placeholder="e.g. Signature Haircut" required />
                                <FieldError name="name" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="duration_min">Duration
                                <span className="relative mt-2 block"><input className="block h-11 w-full rounded-xl border border-border bg-surface px-4 pr-20 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.duration_min ?? ''} id="duration_min" max={1440} min={1} name="duration_min" placeholder="45" required type="number" /><span className="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs text-subtle">minutes</span></span>
                                <FieldError name="duration_min" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="price">Price
                                <span className="relative mt-2 block"><span className="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm text-subtle">₱</span><input className="block h-11 w-full rounded-xl border border-border bg-surface pl-8 pr-4 text-sm shadow-sm placeholder:text-subtle/70 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.price ?? ''} id="price" min="0" name="price" placeholder="500.00" required step="0.01" type="number" /></span>
                                <FieldError name="price" />
                            </label>
                        </div>
                        <div className="flex flex-col-reverse justify-end gap-2 border-t border-border bg-muted/40 px-5 py-4 sm:flex-row sm:px-6">
                            <Link className="inline-flex h-11 items-center justify-center rounded-xl border border-border bg-surface px-4 text-sm font-medium transition hover:bg-muted" href="/services">Cancel</Link>
                            <button className="inline-flex h-11 items-center justify-center rounded-xl bg-brand px-5 text-sm font-medium text-white shadow-sm transition hover:bg-brand-dark" type="submit">Save service</button>
                        </div>
                    </form>
                </section>
            </div>
        </AppLayout>
    );
}
