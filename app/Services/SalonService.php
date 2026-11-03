<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BillingInvoice;
use App\Models\Client;
use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SalonService
{
    /**
     * @param  array{category: string, name: string, duration_min: int|string, price: int|float|string}  $data
     */
    public function createService(array $data): Service
    {
        return DB::transaction(fn (): Service => Service::query()->create($this->sanitizeServiceData($data)));
    }

    /**
     * @param  array{category: string, name: string, duration_min: int|string, price: int|float|string}  $data
     */
    public function updateService(int $id, array $data): Service
    {
        return DB::transaction(function () use ($id, $data): Service {
            $service = Service::query()->findOrFail($id);
            $service->update($this->sanitizeServiceData($data));

            return $service->refresh();
        });
    }

    /**
     * @param  array{name: string, phone: string}  $data
     */
    public function createClient(array $data): Client
    {
        return DB::transaction(fn (): Client => Client::query()->create([
            'name' => $this->cleanText($data['name']),
            'phone' => $this->cleanPhone($data['phone']),
        ]));
    }

    /**
     * @param  array{name: string, phone: string}  $data
     */
    public function updateClient(int $id, array $data): Client
    {
        return DB::transaction(function () use ($id, $data): Client {
            $client = Client::query()->findOrFail($id);
            $client->update([
                'name' => $this->cleanText($data['name']),
                'phone' => $this->cleanPhone($data['phone']),
            ]);

            return $client->refresh();
        });
    }

    /**
     * Removes formatting while preserving an optional leading international plus.
     */
    public function cleanPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone)) ?? '';

        return str_starts_with(trim($phone), '+') ? '+'.$digits : $digits;
    }

    /**
     * @param  array{client_id: int|string, stylist_id: int|string, service_id: int|string, appointment_datetime: string, status?: string}  $data
     */
    public function createAppointment(array $data): Appointment
    {
        return DB::transaction(function () use ($data): Appointment {
            $stylist = Stylist::query()->lockForUpdate()->findOrFail($data['stylist_id']);
            $service = Service::query()->findOrFail($data['service_id']);
            $startsAt = Carbon::parse($data['appointment_datetime']);

            if (($data['status'] ?? Appointment::STATUS_SCHEDULED) !== Appointment::STATUS_CANCELLED) {
                $this->ensureStylistIsAvailable($stylist, $service, $startsAt);
            }

            $appointment = Appointment::query()->create([
                'client_id' => $data['client_id'],
                'stylist_id' => $stylist->id,
                'service_id' => $service->id,
                'appointment_datetime' => $startsAt,
                'status' => $data['status'] ?? Appointment::STATUS_SCHEDULED,
            ]);

            $appointment->billingInvoice()->create([
                'total_amount' => $service->price,
                'payment_status' => BillingInvoice::PAYMENT_UNPAID,
            ]);

            return $appointment->load(['client', 'stylist', 'service', 'billingInvoice']);
        });
    }

    /**
     * @param  array{client_id: int|string, stylist_id: int|string, service_id: int|string, appointment_datetime: string, status?: string}  $data
     */
    public function updateAppointment(int $id, array $data): Appointment
    {
        return DB::transaction(function () use ($id, $data): Appointment {
            $appointment = Appointment::query()->lockForUpdate()->findOrFail($id);

            if ($appointment->billingInvoice()->where('payment_status', BillingInvoice::PAYMENT_PAID)->exists()) {
                throw ValidationException::withMessages([
                    'appointment' => 'A paid appointment cannot be rescheduled or changed.',
                ]);
            }

            $stylist = Stylist::query()->lockForUpdate()->findOrFail($data['stylist_id']);
            $service = Service::query()->findOrFail($data['service_id']);
            $startsAt = Carbon::parse($data['appointment_datetime']);

            if (($data['status'] ?? $appointment->status) !== Appointment::STATUS_CANCELLED) {
                $this->ensureStylistIsAvailable($stylist, $service, $startsAt, $appointment->id);
            }

            $appointment->update([
                'client_id' => $data['client_id'],
                'stylist_id' => $stylist->id,
                'service_id' => $service->id,
                'appointment_datetime' => $startsAt,
                'status' => $data['status'] ?? $appointment->status,
            ]);

            $invoice = $appointment->billingInvoice;
            if ($invoice?->payment_status === BillingInvoice::PAYMENT_UNPAID) {
                $invoice->update(['total_amount' => $service->price]);
            }

            return $appointment->refresh()->load(['client', 'stylist', 'service', 'billingInvoice']);
        });
    }

    /**
     * @param  array{category: string, name: string, duration_min: int|string, price: int|float|string}  $data
     * @return array{category: string, name: string, duration_min: int, price: float}
     */
    private function sanitizeServiceData(array $data): array
    {
        return [
            'category' => Str::title($this->cleanText($data['category'])),
            'name' => Str::title($this->cleanText($data['name'])),
            'duration_min' => (int) $data['duration_min'],
            'price' => round((float) $data['price'], 2),
        ];
    }

    private function cleanText(string $value): string
    {
        return trim(strip_tags($value));
    }

    private function ensureStylistIsAvailable(
        Stylist $stylist,
        Service $service,
        Carbon $startsAt,
        ?int $ignoreAppointmentId = null,
    ): void {
        $endsAt = $startsAt->copy()->addMinutes($service->duration_min);

        $appointments = Appointment::query()
            ->where('stylist_id', $stylist->id)
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->when($ignoreAppointmentId !== null, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))
            ->with('service')
            ->get();

        foreach ($appointments as $existingAppointment) {
            $existingStart = Carbon::parse($existingAppointment->appointment_datetime);
            $existingEnd = $existingStart->copy()->addMinutes($existingAppointment->service->duration_min);

            if ($startsAt->lt($existingEnd) && $endsAt->gt($existingStart)) {
                throw ValidationException::withMessages([
                    'appointment_datetime' => 'This stylist already has an appointment during that time.',
                ]);
            }
        }
    }
}
