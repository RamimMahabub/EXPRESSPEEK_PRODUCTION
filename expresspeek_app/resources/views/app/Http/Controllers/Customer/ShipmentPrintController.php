<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ShipmentPrintController extends Controller
{
    

    public function printInvoice(Request $request, Shipment $shipment)
    {
        if (!auth()->check()) {
            abort(403);
        }

        abort_if((int) $shipment->sender_id !== (int) auth()->id(), 403);

        $pdf = app('dompdf.wrapper')->loadView('agent.shipments.invoice', compact('shipment'));

        return $pdf->stream('invoice-' . $shipment->invoice_number . '.pdf');
    }
}
