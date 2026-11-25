import { Link, usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { CsrfField, formatDateTime, formatMoney, Pagination } from '../../Components/FormFields';
import type { Appointment, Client, Paginator, Service, SharedPageProps, Stylist } from '../../types';

type Props = {
    appointments: Paginator<Appointment>;
    clients: Client[];
    stylists: Stylist[];
    services: Service[];
};

export default function AppointmentIndex({ appointments, clients, stylists, services }: Props) {
    const { auth, timezone } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="Appointments">
            <div className="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="text-sm font-medium text-brand">Studio schedule</p>
                    <h1 className="mt-1 text-3xl font-semibold tracking-tight">Appointments</h1>
                    <p className="mt-2 text-sm text-subtle">Keep your day running smoothly, one booking at a time.</p>
                </div>
                {auth.user?.can_manage_studio && <Link className="inline-flex h-10 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="/appointments/create">Book appointment</Link>}
            </div>
            <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div className="text-sm text-subtle">{appointments.total} {appointments.total === 1 ? 'appointment' : 'appointments'}</div>
                <span className="text-xs text-subtle">Most recent first</span>
            </div>
            <div className="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full min-w-[980px] border-collapse text-left text-sm">
                        <thead><tr className="border-b border-border bg-background/80 text-xs font-medium uppercase tracking-wide text-subtle">
                            <th className="px-4 py-3.5 sm:px-5">Date &amp; time</th><th className="px-4 py-3.5">Client</th><th className="px-4 py-3.5">Stylist</th><th className="px-4 py-3.5">Service</th><th className="px-4 py-3.5">Status</th><th className="px-4 py-3.5">Invoice</th><th className="px-4 py-3.5 text-right">Manage</th>
                        </tr></thead>
                        <tbody className="divide-y divide-border">
                            {appointments.data.map((appointment) => {
                                const datetime = formatDateTime(appointment.appointment_datetime, timezone);
                                const invoice = appointment.billing_invoice ?? appointment.billingInvoice;

                                return (
                                    <tr className="align-top transition-colors hover:bg-background/60" key={appointment.id}>
                                        <td className="whitespace-nowrap px-4 py-4 sm:px-5"><p className="font-medium">{datetime.date}</p><p className="mt-1 text-xs text-subtle">{datetime.time}</p></td>
                                        <td className="px-4 py-4"><p className="font-medium">{appointment.client.name}</p><p className="mt-1 text-xs text-subtle">{appointment.client.phone}</p></td>
                                        <td className="px-4 py-4"><p className="font-medium">{appointment.stylist.name}</p><p className="mt-1 text-xs text-subtle">{appointment.stylist.specialization}</p></td>
                                        <td className="px-4 py-4"><p className="font-medium">{appointment.service.name}</p><p className="mt-1 text-xs text-subtle">{appointment.service.duration_min} min · {formatMoney(appointment.service.price)}</p></td>
                                        <td className="whitespace-nowrap px-4 py-4"><span className="inline-flex items-center rounded-full bg-muted px-2.5 py-1 text-xs font-medium">{appointment.status}</span></td>
                                        <td className="px-4 py-4">
                                            {invoice ? <>
                                                <p className="font-medium">{formatMoney(invoice.total_amount)}</p><p className="mt-1 text-xs text-subtle">{invoice.payment_status}</p>
                                                {invoice.payment_status === 'Unpaid' && appointment.status !== 'Cancelled' && (
                                                    <form action={`/billing-invoices/${invoice.id}/pay`} className="mt-2" method="post">
                                                        <CsrfField />
                                                        <button className="inline-flex h-8 items-center rounded-md border border-border bg-white px-2.5 text-xs font-medium transition-colors hover:bg-muted" type="submit">Mark paid</button>
                                                    </form>
                                                )}
                                            </> : <span className="text-subtle"></span>}
                                        </td>
                                        <td className="px-4 py-4 text-right">
                                            {appointment.status !== 'Completed' && (
                                                <details className="relative inline-block text-left">
                                                    <summary className="inline-flex h-8 list-none items-center gap-1.5 rounded-md border border-border bg-white px-2.5 text-xs font-medium transition-colors hover:bg-muted">Edit</summary>
                                                    <div className="absolute right-0 z-20 mt-2 w-72 rounded-lg border border-border bg-white p-4 text-left shadow-soft">
                                                        <form action={`/appointments/${appointment.id}`} method="post">
                                                            <CsrfField /><input name="_method" type="hidden" value="PUT" />
                                                            <p className="mb-4 text-sm font-semibold">Edit appointment</p>
                                                            <div className="space-y-3">
                                                                <label className="block text-xs font-medium text-subtle">Client
                                                                    <select className="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground" defaultValue={appointment.client_id} name="client_id" required>{clients.map((client) => <option key={client.id} value={client.id}>{client.name}</option>)}</select>
                                                                </label>
                                                                <label className="block text-xs font-medium text-subtle">Stylist
                                                                    <select className="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground" defaultValue={appointment.stylist_id} name="stylist_id" required>{stylists.map((stylist) => <option key={stylist.id} value={stylist.id}>{stylist.name}</option>)}</select>
                                                                </label>
                                                                <label className="block text-xs font-medium text-subtle">Service
                                                                    <select className="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground" defaultValue={appointment.service_id} name="service_id" required>{services.map((service) => <option key={service.id} value={service.id}>{service.name}</option>)}</select>
                                                                </label>
                                                                <label className="block text-xs font-medium text-subtle">Date and time
                                                                    <input className="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground" defaultValue={datetime.localInput} name="appointment_datetime" required type="datetime-local" />
                                                                </label>
                                                                <label className="block text-xs font-medium text-subtle">Status
                                                                    <select className="mt-1 block h-9 w-full rounded-md border border-border bg-white px-2.5 text-sm text-foreground" defaultValue={appointment.status} name="status" required><option value="Scheduled">Scheduled</option><option value="Cancelled">Cancelled</option></select>
                                                                </label>
                                                            </div>
                                                            <button className="mt-4 inline-flex h-9 w-full items-center justify-center rounded-md bg-brand px-3 text-sm font-medium text-white transition-colors hover:bg-brand-dark" type="submit">Save changes</button>
                                                        </form>
                                                    </div>
                                                </details>
                                            )}
                                        </td>
                                    </tr>
                                );
                            })}
                            {appointments.data.length === 0 && <tr><td className="px-6 py-16 text-center" colSpan={7}><h2 className="text-base font-semibold">No appointments yet</h2><p className="mt-1 text-sm text-subtle">Customer bookings will appear here once they’re scheduled.</p></td></tr>}
                        </tbody>
                    </table>
                </div>
            </div>
            <Pagination currentPage={appointments.current_page} lastPage={appointments.last_page} path={appointments.path} />
        </AppLayout>
    );
}
