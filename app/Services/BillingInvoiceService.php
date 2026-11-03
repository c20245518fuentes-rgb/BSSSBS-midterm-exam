<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BillingInvoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillingInvoiceService
{
    public function markAsPaid(int $invoiceId): BillingInvoice
    {
        return DB::transaction(function () use ($invoiceId): BillingInvoice {
            $invoice = BillingInvoice::query()->lockForUpdate()->findOrFail($invoiceId);
            $appointment = Appointment::query()->lockForUpdate()->findOrFail($invoice->appointment_id);

            if ($invoice->payment_status === BillingInvoice::PAYMENT_REFUNDED) {
                throw ValidationException::withMessages([
                    'invoice' => 'A refunded invoice cannot be marked as paid.',
                ]);
            }

            if ($appointment->status === Appointment::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'invoice' => 'A cancelled appointment cannot be paid.',
                ]);
            }

            if ($invoice->payment_status !== BillingInvoice::PAYMENT_PAID) {
                $invoice->update([
                    'payment_status' => BillingInvoice::PAYMENT_PAID,
                    'paid_at' => Carbon::now(),
                ]);
            }

            $appointment->update(['status' => Appointment::STATUS_COMPLETED]);

            return $invoice->refresh()->load('appointment');
        });
    }
}
