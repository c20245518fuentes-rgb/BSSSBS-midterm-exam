<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Stylist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class DashboardService
{
    /**
     * @return Collection<int, Appointment>
     */
    public function getTodayAppointments(Carbon $todayStart, Carbon $todayEnd): Collection
    {
        return Appointment::query()
            ->select(['id', 'client_id', 'stylist_id', 'service_id', 'appointment_datetime'])
            ->whereBetween('appointment_datetime', [$todayStart, $todayEnd])
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->with([
                'client:id,name',
                'stylist:id,name',
                'service:id,name',
            ])
            ->orderBy('appointment_datetime')
            ->get();
    }

    /**
     * @return Collection<int, Stylist>
     */
    public function getStylistWorkload(Carbon $monthStart, Carbon $monthEnd): Collection
    {
        return Stylist::query()
            ->select(['id', 'name', 'specialization'])
            ->withCount(['appointments as monthly_appointments_count' => fn ($query) => $query
                ->whereBetween('appointment_datetime', [$monthStart, $monthEnd])
                ->where('status', '!=', Appointment::STATUS_CANCELLED)])
            ->orderByDesc('monthly_appointments_count')
            ->get();
    }
}
