import { Link } from '@inertiajs/react';
import AppLayout from '../../Components/AppLayout';
import { formatDateTime, formatMoney } from '../../Components/FormFields';
import { usePage } from '@inertiajs/react';
import type { Appointment, SharedPageProps, Stylist } from '../../types';

type Props = {
    metrics: {
        today_count: number;
        scheduled_count: number;
        unpaid_amount: number;
        monthly_paid_amount: number;
    };
    todayAppointments: Appointment[];
    workload: (Stylist & { monthly_appointments_count: number })[];
};

export default function Dashboard({ metrics, todayAppointments, workload }: Props) {
    const { timezone } = usePage<SharedPageProps>().props;

    const cards = [
        { label: 'Appointments today', value: metrics.today_count.toString(), href: '/appointments' },
        { label: 'Scheduled', value: metrics.scheduled_count.toString(), href: '/appointments' },
        { label: 'Unpaid invoices', value: formatMoney(metrics.unpaid_amount), href: '/appointments' },
        { label: 'Paid this month', value: formatMoney(metrics.monthly_paid_amount), href: '/appointments' },
    ];

    return (
        <AppLayout title="Dashboard">
            <div className="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="text-sm font-medium text-brand">Studio overview</p>
                    <h1 className="mt-1 text-3xl font-semibold tracking-tight">Dashboard</h1>
                    <p className="mt-2 text-sm text-subtle">Today’s schedule and this month’s performance.</p>
                </div>
                <Link className="inline-flex h-10 items-center justify-center rounded-md bg-brand px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-dark" href="/appointments">View appointments</Link>
            </div>

            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {cards.map((card) => (
                    <Link className="rounded-xl border border-border bg-white p-5 shadow-sm transition-shadow hover:shadow-soft" href={card.href} key={card.label}>
                        <p className="text-sm text-subtle">{card.label}</p>
                        <p className="mt-2 text-2xl font-semibold tracking-tight">{card.value}</p>
                    </Link>
                ))}
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(260px,.7fr)]">
                <section className="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
                    <div className="border-b border-border px-5 py-4">
                        <h2 className="font-semibold">Today’s appointments</h2>
                    </div>
                    {todayAppointments.length > 0 ? (
                        <div className="divide-y divide-border">
                            {todayAppointments.map((appointment) => {
                                const datetime = formatDateTime(appointment.appointment_datetime, timezone);

                                return (
                                    <div className="flex items-center justify-between gap-4 px-5 py-4" key={appointment.id}>
                                        <div>
                                            <p className="font-medium">{appointment.client.name}</p>
                                            <p className="mt-1 text-xs text-subtle">{appointment.service.name} with {appointment.stylist.name}</p>
                                        </div>
                                        <span className="whitespace-nowrap text-sm text-subtle">{datetime.time}</span>
                                    </div>
                                );
                            })}
                        </div>
                    ) : (
                        <p className="px-5 py-10 text-center text-sm text-subtle">No active appointments scheduled for today.</p>
                    )}
                </section>

                <section className="rounded-xl border border-border bg-white shadow-sm">
                    <div className="border-b border-border px-5 py-4">
                        <h2 className="font-semibold">Stylist workload</h2>
                        <p className="mt-1 text-xs text-subtle">Active appointments this month</p>
                    </div>
                    {workload.length > 0 ? (
                        <div className="divide-y divide-border">
                            {workload.map((stylist) => (
                                <div className="flex items-center justify-between gap-3 px-5 py-4" key={stylist.id}>
                                    <div>
                                        <p className="font-medium">{stylist.name}</p>
                                        <p className="mt-1 text-xs text-subtle">{stylist.specialization}</p>
                                    </div>
                                    <span className="rounded-full bg-muted px-2.5 py-1 text-xs font-medium">{stylist.monthly_appointments_count}</span>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="px-5 py-10 text-center text-sm text-subtle">No stylists available.</p>
                    )}
                </section>
            </div>
        </AppLayout>
    );
}
