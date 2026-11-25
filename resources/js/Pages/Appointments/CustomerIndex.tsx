import { Link } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { formatDateTime, formatMoney, Pagination } from '../../Components/FormFields';
import type { Appointment, Paginator, Service, SharedPageProps, Stylist } from '../../types';

type CustomerAppointment = Omit<Appointment, 'client' | 'stylist' | 'service'> & {
    stylist: Pick<Stylist, 'id' | 'name'>;
    service: Pick<Service, 'id' | 'name'>;
};

export default function CustomerIndex({ appointments }: { appointments: Paginator<CustomerAppointment> }) {
    const { timezone } = usePage<SharedPageProps>().props;

    return (
        <AppLayout title="My appointments">
            <div className="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="text-sm font-medium text-brand">Your bookings</p>
                    <h1 className="mt-1 text-3xl font-semibold tracking-tight">My appointments</h1>
                    <p className="mt-2 text-sm text-subtle">Review your appointments or book your next visit.</p>
                </div>
                <Link className="inline-flex h-10 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="/appointments/create">Book an appointment</Link>
            </div>
            <div className="space-y-3">
                {appointments.data.length > 0 ? appointments.data.map((appointment) => {
                    const time = formatDateTime(appointment.appointment_datetime, timezone);
                    const invoice = appointment.billing_invoice ?? appointment.billingInvoice;

                    return (
                        <article className="grid gap-4 rounded-xl border border-border bg-white p-5 shadow-sm sm:grid-cols-[1fr_auto] sm:items-center" key={appointment.id}>
                            <div>
                                <p className="font-semibold">{appointment.service.name}</p>
                                <p className="mt-1 text-sm text-subtle">{time.date} at {time.time} with {appointment.stylist.name}</p>
                            </div>
                            <div className="sm:text-right">
                                <p className="mt-1 text-sm text-subtle">{appointment.status}</p>
                                {invoice && <>
                                    <p className="font-medium">{formatMoney(invoice.total_amount)}</p>
                                    <p className="mt-1 text-xs text-subtle">Payment: {invoice.payment_status}</p>
                                </>}
                            </div>
                        </article>
                    );
                }) : (
                    <div className="rounded-xl border border-dashed border-border bg-white px-6 py-16 text-center">
                        <h2 className="text-base font-semibold">No appointments yet</h2>
                        <p className="mt-1 text-sm text-subtle">Your bookings will appear here once they’re scheduled.</p>
                        <Link className="mt-5 inline-flex h-9 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white transition-colors hover:bg-brand-dark" href="/appointments/create">Book your first appointment</Link>
                    </div>
                )}
            </div>
            <Pagination currentPage={appointments.current_page} lastPage={appointments.last_page} path={appointments.path} />
        </AppLayout>
    );
}
