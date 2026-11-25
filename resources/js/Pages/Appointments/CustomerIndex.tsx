import { Link, usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { formatDateTime, formatMoney, Pagination } from '../../Components/FormFields';
import type { Appointment, Paginator, Service, SharedPageProps, Stylist } from '../../types';

type CustomerAppointment = Omit<Appointment, 'client' | 'stylist' | 'service'> & {
    stylist: Pick<Stylist, 'id' | 'name'>;
    service: Pick<Service, 'id' | 'name'>;
};

export default function CustomerIndex({ appointments }: { appointments: Paginator<CustomerAppointment> }) {
    const { auth, timezone } = usePage<SharedPageProps>().props;
    const name = auth.user?.name ?? 'there';
    const upcoming = appointments.data.filter((a) => a.status !== 'Cancelled').slice(0, 3);

    return (
        <AppLayout title="My appointments">
            <div className="space-y-6">
                <section className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#5f4549] via-[#76575d] to-[#9a7467] p-6 text-white shadow-soft sm:p-8">
                    <div className="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p className="text-xs font-medium uppercase tracking-[0.22em] text-white/75">Welcome back</p>
                            <h1 className="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Hello, {name.split(' ')[0]}.</h1>
                            <p className="mt-3 max-w-xl text-sm leading-7 text-white/80">Your beauty appointments, services, and profile details are all here. Ready for your next visit?</p>
                            <div className="mt-5 flex flex-wrap gap-3">
                                <Link className="inline-flex h-11 items-center justify-center rounded-xl bg-surface px-5 text-sm font-medium text-[#5f4549] shadow-sm transition hover:bg-brand-soft" href="/appointments/create">Book an appointment</Link>
                                <Link className="inline-flex h-11 items-center justify-center rounded-xl border border-white/25 bg-surface/10 px-5 text-sm font-medium text-white transition hover:bg-surface/15" href="/services">Browse services</Link>
                            </div>
                        </div>
                    </div>
                    <div className="pointer-events-none absolute -right-12 -top-12 size-48 rounded-full bg-surface/10 blur-3xl" />
                </section>

                <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <QuickCard href="/appointments" title="My appointments" text="View and track your bookings." icon="▦" />
                    <QuickCard href="/services" title="Our services" text="Explore treatments and pricing." icon="✦" />
                    <QuickCard href="/profile" title="My profile" text="Update your details and password." icon="◌" />
                    <QuickCard href="/appointments/create" title="Book now" text="Schedule your next visit." icon="＋" />
                </section>

                <section className="overflow-hidden rounded-3xl border border-border bg-surface shadow-card">
                    <div className="flex flex-col gap-2 border-b border-border px-5 py-4 sm:flex-row sm:items-end sm:justify-between sm:px-6">
                        <div>
                            <h2 className="text-lg font-semibold">Appointments</h2>
                            <p className="mt-1 text-sm text-subtle">Your recent and upcoming bookings.</p>
                        </div>
                        <span className="text-sm text-subtle">{appointments.total} total</span>
                    </div>
                    <div className="space-y-3 p-4 sm:p-5">
                        {appointments.data.length > 0 ? appointments.data.map((appointment) => {
                            const time = formatDateTime(appointment.appointment_datetime, timezone);
                            const invoice = appointment.billing_invoice ?? appointment.billingInvoice;

                            return (
                                <article className="grid gap-4 rounded-2xl border border-border bg-surface p-4 shadow-sm sm:grid-cols-[1fr_auto] sm:items-center sm:p-5" key={appointment.id}>
                                    <div className="flex items-start gap-4">
                                        <div className="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-brand-soft text-brand">✦</div>
                                        <div>
                                            <p className="font-semibold text-foreground">{appointment.service.name}</p>
                                            <p className="mt-1 text-sm text-subtle">{time.date} at {time.time}</p>
                                            <p className="mt-1 text-sm text-subtle">with {appointment.stylist.name}</p>
                                        </div>
                                    </div>
                                    <div className="flex flex-wrap items-center gap-3 sm:justify-end sm:text-right">
                                        <span className={`inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ${appointment.status === 'Scheduled' ? 'bg-[#eef5ff] text-[#355c8a]' : appointment.status === 'Completed' ? 'bg-success text-[#315d3d]' : 'bg-muted text-subtle'}`}>{appointment.status}</span>
                                        {invoice && (
                                            <div>
                                                <p className="font-medium">{formatMoney(invoice.total_amount)}</p>
                                                <p className="mt-0.5 text-xs text-subtle">{invoice.payment_status}</p>
                                            </div>
                                        )}
                                    </div>
                                </article>
                            );
                        }) : (
                            <div className="rounded-2xl border border-dashed border-border bg-background/60 px-6 py-14 text-center">
                                <h3 className="text-base font-semibold">No appointments yet</h3>
                                <p className="mt-1 text-sm text-subtle">Your bookings will appear here once they’re scheduled.</p>
                                <Link className="mt-5 inline-flex h-10 items-center justify-center rounded-xl bg-brand px-5 text-sm font-medium text-white transition hover:bg-brand-dark" href="/appointments/create">Book your first appointment</Link>
                            </div>
                        )}
                    </div>
                    <div className="px-4 pb-5 sm:px-5"><Pagination currentPage={appointments.current_page} lastPage={appointments.last_page} path={appointments.path} /></div>
                </section>
            </div>
        </AppLayout>
    );
}

function QuickCard({ href, title, text, icon }: { href: string; title: string; text: string; icon: string }) {
    return (
        <Link className="group rounded-3xl border border-border bg-surface p-5 shadow-card transition hover:-translate-y-0.5 hover:shadow-soft" href={href}>
            <span className="flex size-11 items-center justify-center rounded-2xl bg-brand-soft text-lg text-brand">{icon}</span>
            <p className="mt-4 font-semibold text-foreground">{title}</p>
            <p className="mt-1 text-sm text-subtle">{text}</p>
        </Link>
    );
}
