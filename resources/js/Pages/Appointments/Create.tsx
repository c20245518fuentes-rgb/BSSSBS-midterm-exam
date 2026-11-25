import { Link, usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, FieldError, formatMoney } from '../../Components/FormFields';
import type { Client, Service, SharedPageProps, Stylist } from '../../types';

type Props = {
    stylists: Pick<Stylist, 'id' | 'name' | 'specialization'>[];
    services: Pick<Service, 'id' | 'name' | 'duration_min' | 'price'>[];
    clients: Pick<Client, 'id' | 'name' | 'phone'>[];
    isStudio: boolean;
};

export default function CreateAppointment({ stylists, services, clients, isStudio }: Props) {
    const { old } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="Book appointment">
            <div className="mx-auto max-w-4xl space-y-6">
                <Link className="inline-flex items-center gap-2 text-sm font-medium text-subtle transition hover:text-foreground" href="/appointments">← Back to appointments</Link>
                <section className="overflow-hidden rounded-3xl border border-border bg-surface shadow-card">
                    <div className="border-b border-border bg-gradient-to-r from-[#5f4549] to-[#76575d] px-5 py-5 text-white sm:px-6">
                        <p className="text-xs font-medium uppercase tracking-[0.2em] text-white/75">Online booking</p>
                        <h1 className="mt-1 text-2xl font-semibold tracking-tight">Book an appointment</h1>
                        <p className="mt-1 text-sm text-white/80">Choose a stylist, service, and a time that works for you.</p>
                    </div>
                    <form action="/appointments" method="post">
                        <CsrfField />
                        <div className="grid gap-5 p-5 sm:grid-cols-2 sm:p-6">
                            {isStudio && (
                                <label className="block text-sm font-medium text-foreground" htmlFor="client_id">Client
                                    <select className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.client_id ?? ''} id="client_id" name="client_id" required>
                                        <option value="">Select a client</option>
                                        {clients.map((client) => <option key={client.id} value={client.id}>{client.name} · {client.phone}</option>)}
                                    </select>
                                    <FieldError name="client_id" />
                                </label>
                            )}
                            <label className="block text-sm font-medium text-foreground" htmlFor="stylist_id">Stylist
                                <select className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.stylist_id ?? ''} id="stylist_id" name="stylist_id" required>
                                    <option value="">Select a stylist</option>
                                    {stylists.map((stylist) => <option key={stylist.id} value={stylist.id}>{stylist.name} · {stylist.specialization}</option>)}
                                </select>
                                <FieldError name="stylist_id" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="service_id">Service
                                <select className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.service_id ?? ''} id="service_id" name="service_id" required>
                                    <option value="">Select a service</option>
                                    {services.map((service) => <option key={service.id} value={service.id}>{service.name} · {service.duration_min} min · {formatMoney(service.price)}</option>)}
                                </select>
                                <FieldError name="service_id" />
                            </label>
                            <label className="block text-sm font-medium text-foreground" htmlFor="appointment_datetime">Date and time
                                <input className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.appointment_datetime ?? ''} id="appointment_datetime" name="appointment_datetime" required type="datetime-local" />
                                <FieldError name="appointment_datetime" />
                            </label>
                            {isStudio && (
                                <label className="block text-sm font-medium text-foreground" htmlFor="status">Status
                                    <select className="mt-2 block h-11 w-full rounded-xl border border-border bg-white px-4 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10" defaultValue={old.status ?? 'Scheduled'} id="status" name="status">
                                        <option value="Scheduled">Scheduled</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </label>
                            )}
                        </div>
                        <div className="mx-5 mb-5 rounded-2xl border border-border bg-background px-4 py-4 text-sm text-subtle sm:mx-6">
                            The stylist’s service duration is checked automatically to prevent overlapping bookings. An unpaid invoice will be created with this appointment.
                        </div>
                        <div className="flex flex-col-reverse justify-end gap-2 border-t border-border bg-muted/40 px-5 py-4 sm:flex-row sm:px-6">
                            <Link className="inline-flex h-11 items-center justify-center rounded-xl border border-border bg-white px-4 text-sm font-medium transition hover:bg-muted" href="/appointments">Cancel</Link>
                            <button className="inline-flex h-11 items-center justify-center rounded-xl bg-brand px-5 text-sm font-medium text-white shadow-sm transition hover:bg-brand-dark" type="submit">Confirm booking</button>
                        </div>
                    </form>
                </section>
            </div>
        </AppLayout>
    );
}
