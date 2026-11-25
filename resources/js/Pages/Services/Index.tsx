import { Link } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, Pagination } from '../../Components/FormFields';
import type { Paginator, Service } from '../../types';

export default function ServiceIndex({ services }: { services: Paginator<Service> }) {
    return (
        <AppLayout title="Services">
            <div className="space-y-6">
                <section className="flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-[#5f4549] via-[#76575d] to-[#8b6a64] p-6 text-white shadow-soft sm:flex-row sm:items-end sm:justify-between sm:p-8">
                    <div>
                        <p className="text-xs font-medium uppercase tracking-[0.22em] text-white/75">Your menu</p>
                        <h1 className="mt-2 text-3xl font-semibold tracking-tight">Services</h1>
                        <p className="mt-2 text-sm text-white/80">Manage treatments, pricing, and appointment durations.</p>
                    </div>
                    <Link className="inline-flex h-11 items-center justify-center rounded-xl bg-white px-5 text-sm font-medium text-[#5f4549] shadow-sm transition hover:bg-brand-soft" href="/services/create">+ Add service</Link>
                </section>

                {services.data.length > 0 && <div className="text-sm text-subtle">{services.total} {services.total === 1 ? 'service' : 'services'} in your catalog</div>}
                <div className="space-y-3">
                    {services.data.length ? services.data.map((service) => (
                        <article className="overflow-hidden rounded-3xl border border-border bg-surface shadow-card" key={service.id}>
                            <form action={`/services/${service.id}`} method="post">
                                <CsrfField /><input name="_method" type="hidden" value="PUT" />
                                <div className="grid gap-4 p-4 sm:grid-cols-[minmax(0,1.1fr)_minmax(0,1.5fr)_minmax(110px,.7fr)_minmax(130px,.8fr)_auto] sm:items-end sm:gap-4 sm:p-5">
                                    <label className="block text-xs font-medium text-subtle" htmlFor={`category-${service.id}`}>Category<input className="mt-1.5 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm font-medium text-foreground shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={service.category} id={`category-${service.id}`} maxLength={100} name="category" required /></label>
                                    <label className="block text-xs font-medium text-subtle" htmlFor={`name-${service.id}`}>Service title<input className="mt-1.5 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm font-medium text-foreground shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={service.name} id={`name-${service.id}`} maxLength={150} name="name" required /></label>
                                    <label className="block text-xs font-medium text-subtle" htmlFor={`duration-${service.id}`}>Minutes<input className="mt-1.5 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm font-medium text-foreground shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={service.duration_min} id={`duration-${service.id}`} max={1440} min={1} name="duration_min" required type="number" /></label>
                                    <label className="block text-xs font-medium text-subtle" htmlFor={`price-${service.id}`}>Price (₱)<input className="mt-1.5 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm font-medium text-foreground shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={service.price} id={`price-${service.id}`} min="0" name="price" required step="0.01" type="number" /></label>
                                    <button className="inline-flex h-11 items-center justify-center rounded-xl border border-border bg-white px-4 text-sm font-medium transition hover:bg-muted sm:self-end" type="submit">Save</button>
                                </div>
                                <div className="border-t border-border bg-muted/40 px-4 py-2.5 text-xs text-subtle sm:px-5">{service.appointments_count ?? 0} {(service.appointments_count ?? 0) === 1 ? 'appointment' : 'appointments'} booked</div>
                            </form>
                        </article>
                    )) : (
                        <div className="rounded-3xl border border-dashed border-border bg-surface px-6 py-14 text-center">
                            <h2 className="text-base font-semibold">Your service menu is empty</h2>
                            <p className="mt-1 text-sm text-subtle">Add your first treatment to start building your salon menu.</p>
                            <Link className="mt-5 inline-flex h-10 items-center justify-center rounded-xl bg-brand px-5 text-sm font-medium text-white transition hover:bg-brand-dark" href="/services/create">Add your first service</Link>
                        </div>
                    )}
                </div>
                <Pagination currentPage={services.current_page} lastPage={services.last_page} path={services.path} />
            </div>
        </AppLayout>
    );
}
