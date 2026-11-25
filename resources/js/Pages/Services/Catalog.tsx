import { Link } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { formatMoney } from '../../Components/FormFields';
import type { Service } from '../../types';

export default function Catalog({ services }: { services: Service[] }) {
    return (
        <AppLayout title="Services">
            <div className="space-y-6">
                <section className="flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-[#5f4549] via-[#76575d] to-[#8b6a64] p-6 text-white shadow-soft sm:flex-row sm:items-end sm:justify-between sm:p-8">
                    <div>
                        <p className="text-xs font-medium uppercase tracking-[0.22em] text-white/75">Our menu</p>
                        <h1 className="mt-2 text-3xl font-semibold tracking-tight">Services</h1>
                        <p className="mt-2 text-sm text-white/80">Explore our treatments and choose one when booking.</p>
                    </div>
                    <Link className="inline-flex h-11 items-center justify-center rounded-xl bg-white px-5 text-sm font-medium text-[#5f4549] shadow-sm transition hover:bg-brand-soft" href="/appointments/create">Book an appointment</Link>
                </section>

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {services.length ? services.map((service) => (
                        <article className="group flex h-full flex-col rounded-3xl border border-border bg-surface p-5 shadow-card transition hover:-translate-y-0.5 hover:shadow-soft" key={service.id}>
                            <span className="inline-flex w-fit rounded-full bg-brand-soft px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand">{service.category}</span>
                            <h2 className="mt-3 text-lg font-semibold tracking-tight text-foreground">{service.name}</h2>
                            <p className="mt-1 text-sm text-subtle">{service.duration_min} minutes</p>
                            <div className="mt-auto border-t border-border pt-4 mt-6">
                                <p className="text-lg font-semibold text-foreground">{formatMoney(service.price)}</p>
                            </div>
                        </article>
                    )) : (
                        <div className="rounded-3xl border border-dashed border-border bg-surface px-6 py-14 text-center sm:col-span-2 lg:col-span-3">
                            <h2 className="text-base font-semibold">No services available yet</h2>
                            <p className="mt-1 text-sm text-subtle">Please check back later.</p>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
