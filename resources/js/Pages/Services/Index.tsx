import { Link } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, Pagination } from '../../Components/FormFields';
import type { Paginator, Service } from '../../types';

export default function ServiceIndex({ services }: { services: Paginator<Service> }) {
    return (
        <AppLayout title="Services">
            <div className="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div><p className="text-sm font-medium text-brand">Your menu</p><h1 className="mt-1 text-3xl font-semibold tracking-tight">Services</h1><p className="mt-2 text-sm text-subtle">Manage treatments, pricing, and appointment durations.</p></div>
                <Link className="inline-flex h-10 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="/services/create">+ Add service</Link>
            </div>
            {services.data.length > 0 && <div className="mb-4 text-sm text-subtle">{services.total} {services.total === 1 ? 'service' : 'services'} in your catalog</div>}
            <div className="space-y-3">
                {services.data.length ? services.data.map((service) => (
                    <article className="overflow-hidden rounded-xl border border-border bg-white shadow-sm transition-shadow hover:shadow-soft" key={service.id}>
                        <form action={`/services/${service.id}`} method="post">
                            <CsrfField /><input name="_method" type="hidden" value="PUT" />
                            <div className="grid gap-4 p-4 sm:grid-cols-[minmax(0,1.1fr)_minmax(0,1.5fr)_minmax(110px,.7fr)_minmax(130px,.8fr)_auto] sm:items-end sm:gap-4 sm:p-5">
                                <label className="block text-xs font-medium text-subtle" htmlFor={`category-${service.id}`}>Category<input className="mt-1.5 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm font-medium text-foreground shadow-sm" defaultValue={service.category} id={`category-${service.id}`} maxLength={100} name="category" required /></label>
                                <label className="block text-xs font-medium text-subtle" htmlFor={`name-${service.id}`}>Service title<input className="mt-1.5 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm font-medium text-foreground shadow-sm" defaultValue={service.name} id={`name-${service.id}`} maxLength={150} name="name" required /></label>
                                <label className="block text-xs font-medium text-subtle" htmlFor={`duration-${service.id}`}>Minutes<input className="mt-1.5 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm font-medium text-foreground shadow-sm" defaultValue={service.duration_min} id={`duration-${service.id}`} max={1440} min={1} name="duration_min" required type="number" /></label>
                                <label className="block text-xs font-medium text-subtle" htmlFor={`price-${service.id}`}>Price (₱)<input className="mt-1.5 block h-10 w-full rounded-md border border-border bg-white px-3 text-sm font-medium text-foreground shadow-sm" defaultValue={service.price} id={`price-${service.id}`} min="0" name="price" required step="0.01" type="number" /></label>
                                <button className="inline-flex h-10 items-center justify-center rounded-md border border-border bg-white px-3 text-sm font-medium transition-colors hover:bg-muted sm:self-end" type="submit">Save</button>
                            </div>
                            <div className="border-t border-border bg-background/60 px-4 py-2.5 text-xs text-subtle">{service.appointments_count ?? 0} {(service.appointments_count ?? 0) === 1 ? 'appointment' : 'appointments'} booked</div>
                        </form>
                    </article>
                )) : (
                    <div className="rounded-xl border border-dashed border-border bg-white px-6 py-16 text-center">
                        <h2 className="text-base font-semibold">Your service menu is empty</h2><p className="mt-1 text-sm text-subtle">Add your first treatment to start building your salon menu.</p>
                        <Link className="mt-5 inline-flex h-9 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white transition-colors hover:bg-brand-dark" href="/services/create">Add your first service</Link>
                    </div>
                )}
            </div>
            <Pagination currentPage={services.current_page} lastPage={services.last_page} path={services.path} />
        </AppLayout>
    );
}
