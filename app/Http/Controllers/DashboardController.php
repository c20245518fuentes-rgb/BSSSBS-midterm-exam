<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\BillingInvoice;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardService $dashboardService): Response
    {
        abort_unless($request->user()->canManageStudio(), 403);

        $today = Carbon::today();
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $now = Carbon::now();

        $todayAppointments = $dashboardService->getTodayAppointments($todayStart, $todayEnd);
        $workload = $dashboardService->getStylistWorkload($monthStart, $monthEnd);

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
