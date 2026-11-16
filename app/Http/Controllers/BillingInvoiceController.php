<?php

namespace App\Http\Controllers;

use App\Models\BillingInvoice;
use App\Services\BillingInvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BillingInvoiceController extends Controller
{
    public function pay(
        Request $request,
        BillingInvoice $invoice,
        BillingInvoiceService $billingInvoiceService,
    ): RedirectResponse {
        abort_unless($request->user()->is_admin, 403);

        $billingInvoiceService->markAsPaid($invoice->id);

        return to_route('appointments.index')->with('success', 'Invoice paid; appointment marked completed.');
    }
}
