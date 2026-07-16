<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SourcingInvoice;

class SourcingInvoiceController extends Controller
{
    public function downloadPdf(SourcingInvoice $sourcingInvoice)
    {
        // Ensure the invoice belongs to a sourcing request owned by the authenticated customer
        if ((int) $sourcingInvoice->sourcingRequest->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $pdf = app('dompdf.wrapper')->loadView('sourcing.invoice-pdf', compact('sourcingInvoice'));
        return $pdf->stream($sourcingInvoice->invoice_number . '.pdf');
    }
}
