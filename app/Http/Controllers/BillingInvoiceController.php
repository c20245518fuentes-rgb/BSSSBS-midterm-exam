<?php

namespace App\Http\Controllers;

use App\Models\BillingInvoice;
use App\Services\BillingInvoiceService;
use Illuminate\Http\RedirectResponse;

class BillingInvoiceController extends Controller
{
    public function pay(BillingInvoice $invoice, BillingInvoiceService $billingInvoiceService): RedirectResponse
    {
        $billingInvoiceService->markAsPaid($invoice->id);

        return to_route('appointments.index')->with('success', 'Invoice paid; appointment marked completed.');
    }
}
