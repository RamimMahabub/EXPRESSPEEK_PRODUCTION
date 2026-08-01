<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ShipmentInvoiceController extends Controller
{
    public function create(Shipment $shipment)
    {
        if (!Schema::hasTable('shipment_invoices')) {
            return redirect()
                ->route('admin.shipments.edit', $shipment)
                ->with('error', 'Invoice generation needs the pending database update. Open System Update and run the secure update.');
        }

        return view('admin.shipments.invoice-create', compact('shipment'));
    }

    public function store(Request $request, Shipment $shipment)
    {
        if (!Schema::hasTable('shipment_invoices')) {
            return redirect()
                ->route('admin.shipments.edit', $shipment)
                ->with('error', 'Invoice generation needs the pending database update. Open System Update and run the secure update.');
        }

        $data = $request->validate([
            'currency' => ['required', 'string', 'max:10'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'base_freight' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'other_charges' => ['nullable', 'array', 'max:20'],
            'other_charges.*.description' => ['required', 'string', 'max:255'],
            'other_charges.*.amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        $items = collect([
            [
                'type' => 'base_freight',
                'description' => 'Base Freight',
                'amount' => round((float) $data['base_freight'], 2),
            ],
        ])->concat(
            collect($data['other_charges'] ?? [])->map(function (array $charge) {
                return [
                    'type' => 'other_charge',
                    'description' => trim($charge['description']),
                    'amount' => round((float) $charge['amount'], 2),
                ];
            })
        )->values()->all();

        $totalAmount = round(collect($items)->sum(function (array $item) {
            return (float) $item['amount'];
        }), 2);

        if ($totalAmount > 9999999999.99) {
            throw ValidationException::withMessages([
                'base_freight' => 'The combined freight and other charges exceed the maximum invoice amount.',
            ]);
        }

        $shipment->invoices()->create([
            'invoice_number' => ShipmentInvoice::generateInvoiceNumber(),
            'currency' => strtoupper($data['currency']),
            'total_amount' => $totalAmount,
            'status' => ShipmentInvoice::STATUS_UNPAID,
            'due_date' => $data['due_date'] ?? null,
            'items' => $items,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.shipments.edit', $shipment)
            ->with('success', 'Invoice generated successfully.');
    }

    public function markAsPaid(ShipmentInvoice $shipmentInvoice)
    {
        $shipmentInvoice->update(['status' => ShipmentInvoice::STATUS_PAID]);

        return back()->with('success', 'Invoice marked as paid.');
    }

    public function destroy(ShipmentInvoice $shipmentInvoice)
    {
        $shipmentInvoice->delete();

        return back()->with('success', 'Invoice deleted successfully.');
    }

    public function downloadPdf(ShipmentInvoice $shipmentInvoice)
    {
        $shipmentInvoice->load('shipment');

        $pdf = app('dompdf.wrapper')->loadView('shipments.invoice-pdf', compact('shipmentInvoice'));

        return $pdf->stream($shipmentInvoice->invoice_number . '.pdf');
    }
}
