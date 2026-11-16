import { Link } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { formatMoney } from '../../Components/FormFields';
import type { Service } from '../../types';

export default function Catalog({ services }: { services: Service[] }) {
    return (
        <AppLayout title="Services">
            <div className="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div><p className="text-sm font-medium text-brand">Our menu</p><h1 className="mt-1 text-3xl font-semibold tracking-tight">Services</h1><p className="mt-2 text-sm text-subtle">Explore our services and choose one when booking.</p></div>
                <Link className="inline-flex h-10 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="/appointments/create">Book an appointment</Link>
            </div>
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {services.length ? services.map((service) => (
                    <article className="rounded-xl border border-border bg-white p-5 shadow-sm" key={service.id}>
                        <p className="text-xs font-medium uppercase tracking-wide text-brand">{service.category}</p>
                        <h2 className="mt-2 text-lg font-semibold">{service.name}</h2>
                        <p className="mt-2 text-sm text-subtle">{service.duration_min} minutes</p>
                        <p className="mt-4 text-lg font-semibold">{formatMoney(service.price)}</p>
                    </article>
                )) : <div className="rounded-xl border border-dashed border-border bg-white px-6 py-16 text-center sm:col-span-2 lg:col-span-3"><h2 className="text-base font-semibold">No services available yet</h2><p className="mt-1 text-sm text-subtle">Please check back later.</p></div>}
            </div>
        </AppLayout>
    );
}
