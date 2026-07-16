<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SourcingRequest;
use App\Models\SourcingInvoice;
use Illuminate\Http\Request;

class SourcingInvoiceController extends Controller
{
    public function create(SourcingRequest $sourcingRequest)
    {
        return view('admin.sourcing.invoice-create', compact('sourcingRequest'));
    }

    public function store(Request $request, SourcingRequest $sourcingRequest)
    {
        $data = $request->validate([
            'currency' => 'required|string|max:10',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        $totalAmount = 0;
        $items = [];
        foreach ($data['items'] as $item) {
            $amount = (float) $item['amount'];
            $totalAmount += $amount;
            $items[] = [
                'description' => $item['description'],
                'amount' => $amount,
            ];
        }

        $sourcingRequest->invoices()->create([
            'invoice_number' => SourcingInvoice::generateInvoiceNumber(),
            'currency' => $data['currency'],
            'total_amount' => $totalAmount,
            'status' => 'unpaid',
            'due_date' => $data['due_date'] ?? null,
            'items' => $items,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.sourcing-requests.show', $sourcingRequest)
            ->with('success', 'Invoice generated successfully.');
    }

    public function markAsPaid(SourcingInvoice $sourcingInvoice)
    {
        $sourcingInvoice->update(['status' => 'paid']);
        return back()->with('success', 'Invoice marked as paid.');
    }

    public function destroy(SourcingInvoice $sourcingInvoice)
    {
        $sourcingInvoice->delete();
        return back()->with('success', 'Invoice deleted successfully.');
    }

    public function downloadPdf(SourcingInvoice $sourcingInvoice)
    {
        $pdf = app('dompdf.wrapper')->loadView('sourcing.invoice-pdf', compact('sourcingInvoice'));
        return $pdf->stream($sourcingInvoice->invoice_number . '.pdf');
    }
}
