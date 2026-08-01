<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ShipmentInvoice;

class ShipmentInvoiceController extends Controller
{
    public function downloadPdf(ShipmentInvoice $shipmentInvoice)
    {
        $shipmentInvoice->load('shipment');

        abort_if((int) $shipmentInvoice->shipment->sender_id !== (int) auth()->id(), 403);

        $pdf = app('dompdf.wrapper')->loadView('shipments.invoice-pdf', compact('shipmentInvoice'));

        return $pdf->stream($shipmentInvoice->invoice_number . '.pdf');
    }
}
