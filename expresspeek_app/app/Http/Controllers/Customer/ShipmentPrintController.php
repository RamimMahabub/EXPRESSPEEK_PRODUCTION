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
    public function printWaybill(Request $request, Shipment $shipment)
    {
        $this->authorizeShipment($shipment);

        $qrPayload = route('track', [
            'tracking' => (string) $shipment->tracking_number,
        ]);

        $renderer = new ImageRenderer(
            new RendererStyle(140),
            new SvgImageBackEnd()
        );
        $qrSvg = (new Writer($renderer))->writeString($qrPayload);

        $pdf = app('dompdf.wrapper')
            ->setPaper('a4', 'portrait')
            ->loadView('agent.shipments.waybill', compact('shipment', 'qrSvg'));

        return $pdf->stream('waybill-' . $shipment->awb_number . '.pdf');
    }

    public function printInvoice(Request $request, Shipment $shipment)
    {
        $this->authorizeShipment($shipment);

        $pdf = app('dompdf.wrapper')->loadView('agent.shipments.invoice', compact('shipment'));

        return $pdf->stream('invoice-' . $shipment->invoice_number . '.pdf');
    }

    private function authorizeShipment(Shipment $shipment): void
    {
        abort_unless(auth()->check(), 403);
        abort_if((int) $shipment->sender_id !== (int) auth()->id(), 403);
    }
}
