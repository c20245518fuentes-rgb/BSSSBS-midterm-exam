<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\BillingInvoice;
use App\Models\Stylist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->canManageStudio(), 403);

        $today = Carbon::today();
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $now = Carbon::now();

        //TRANSFER THIS TO SERVICES
        $todayAppointments = Appointment::query()
            ->select(['id', 'client_id', 'stylist_id', 'service_id', 'appointment_datetime', 'status']) // SALA - NEED FURTHER OPTIMIZATION
            ->whereBetween('appointment_datetime', [$todayStart, $todayEnd])
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->with(['client', 'stylist', 'service', 'billingInvoice'])
            ->orderBy('appointment_datetime')
            ->get();

        $workload = Stylist::query()
            ->select(['id', 'name', 'specialization']) // SALA - NEED FURTHER OPTIMIZATION
            ->withCount(['appointments as monthly_appointments_count' => fn ($query) => $query
                ->whereBetween('appointment_datetime', [$monthStart, $monthEnd])
                ->where('status', '!=', Appointment::STATUS_CANCELLED)])
            ->orderByDesc('monthly_appointments_count')
            ->get();

        return Inertia::render('Dashboard/Index', [
            'metrics' => [
                'today_count' => $todayAppointments->count(),
                'scheduled_count' => Appointment::query()
                    ->where('status', Appointment::STATUS_SCHEDULED)
                    ->where('appointment_datetime', '>=', $now)
                    ->count(),
                'unpaid_amount' => (float) BillingInvoice::query()
                    ->where('payment_status', BillingInvoice::PAYMENT_UNPAID)
                    ->sum('total_amount'),
                'monthly_paid_amount' => (float) BillingInvoice::query()
                    ->where('payment_status', BillingInvoice::PAYMENT_PAID)
                    ->whereBetween('paid_at', [$monthStart, $monthEnd])
                    ->sum('total_amount'),
            ],
            'todayAppointments' => $todayAppointments,
            'workload' => $workload,
        ]);
    }
}
