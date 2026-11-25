import { Link, usePage } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { formatDateTime, formatMoney } from '../../Components/FormFields';
import type { Appointment, Client, Service, SharedPageProps, Stylist } from '../../types';

type TodayAppointment = Pick<Appointment, 'id' | 'appointment_datetime'> & {
    client: Pick<Client, 'name'>;
    stylist: Pick<Stylist, 'name'>;
    service: Pick<Service, 'name'>;
};

type Props = {
    metrics: {
        today_count: number;
        scheduled_count: number;
        unpaid_amount: number;
        monthly_paid_amount: number;
    };
    todayAppointments: TodayAppointment[];
    workload: (Stylist & { monthly_appointments_count: number })[];
};

export default function Dashboard({ metrics, todayAppointments, workload }: Props) {
    const { timezone } = usePage<SharedPageProps>().props;

    const cards = [
        { label: 'Appointments today', value: metrics.today_count.toString() },
        { label: 'Scheduled', value: metrics.scheduled_count.toString() },
        { label: 'Unpaid invoices', value: formatMoney(metrics.unpaid_amount) },
        { label: 'Paid this month', value: formatMoney(metrics.monthly_paid_amount) },
    ];

    return (
        <AppLayout title="Dashboard">
            <div className="space-y-6">
                <section className="flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-[#5f4549] via-[#76575d] to-[#8b6a64] p-6 text-white shadow-soft sm:flex-row sm:items-end sm:justify-between sm:p-8">
                    <div>
                        <p className="text-xs font-medium uppercase tracking-[0.22em] text-white/75">Studio overview</p>
                        <h1 className="mt-2 text-3xl font-semibold tracking-tight">Dashboard</h1>
                        <p className="mt-2 text-sm text-white/80">Today’s schedule and this month’s performance at a glance.</p>
                    </div>
                    <Link className="inline-flex h-11 items-center justify-center rounded-xl bg-white px-5 text-sm font-medium text-[#5f4549] shadow-sm transition hover:bg-brand-soft" href="/appointments">View appointments</Link>
                </section>

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {cards.map((card) => (
                        <div className="rounded-3xl border border-border bg-surface p-5 shadow-card" key={card.label}>
                            <p className="text-sm text-subtle">{card.label}</p>
                            <p className="mt-3 text-2xl font-semibold tracking-tight text-foreground">{card.value}</p>
                        </div>
                    ))}
                </div>

                <div className="grid gap-6 lg:grid-cols-[minmax(0,1.45fr)_minmax(280px,.75fr)]">
                    <section className="overflow-hidden rounded-3xl border border-border bg-surface shadow-card">
                        <div className="border-b border-border px-5 py-4 sm:px-6">
                            <h2 className="text-lg font-semibold">Today’s appointments</h2>
                        </div>
                        {todayAppointments.length > 0 ? (
                            <div className="divide-y divide-border">
                                {todayAppointments.map((appointment) => {
                                    const datetime = formatDateTime(appointment.appointment_datetime, timezone);
                                    return (
                                        <div className="flex items-center justify-between gap-4 px-5 py-4 sm:px-6" key={appointment.id}>
                                            <div className="flex items-center gap-4">
                                                <span className="flex size-10 items-center justify-center rounded-2xl bg-brand-soft text-sm font-semibold text-brand">{appointment.client.name.slice(0, 1)}</span>
                                                <div>
                                                    <p className="font-medium text-foreground">{appointment.client.name}</p>
                                                    <p className="mt-1 text-sm text-subtle">{appointment.service.name} with {appointment.stylist.name}</p>
                                                </div>
                                            </div>
                                            <span className="whitespace-nowrap rounded-full bg-muted px-3 py-1 text-sm font-medium text-foreground">{datetime.time}</span>
                                        </div>
                                    );
                                })}
                            </div>
                        ) : (
                            <p className="px-5 py-12 text-center text-sm text-subtle">No active appointments scheduled for today.</p>
                        )}
                    </section>

                    <section className="overflow-hidden rounded-3xl border border-border bg-surface shadow-card">
                        <div className="border-b border-border px-5 py-4 sm:px-6">
                            <h2 className="text-lg font-semibold">Stylist workload</h2>
                            <p className="mt-1 text-sm text-subtle">Active appointments this month</p>
                        </div>
                        {workload.length > 0 ? (
                            <div className="divide-y divide-border">
                                {workload.map((stylist) => (
                                    <div className="flex items-center justify-between gap-3 px-5 py-4 sm:px-6" key={stylist.id}>
                                        <div>
                                            <p className="font-medium text-foreground">{stylist.name}</p>
                                            <p className="mt-1 text-sm text-subtle">{stylist.specialization}</p>
                                        </div>
                                        <span className="rounded-full bg-brand-soft px-3 py-1 text-xs font-semibold text-brand">{stylist.monthly_appointments_count}</span>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="px-5 py-12 text-center text-sm text-subtle">No stylists available.</p>
                        )}
                    </section>
                </div>
            </div>
        </AppLayout>
    );
}
