<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SourcingRequest;
use App\Models\SourcingInvoice;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SourcingInvoiceController extends Controller
{
    public function create(SourcingRequest $sourcingRequest)
    {
        $sourcingRequest->load('items');

        return view('admin.sourcing.invoice-create', compact('sourcingRequest'));
    }

    public function store(Request $request, SourcingRequest $sourcingRequest)
    {
        $data = $request->validate([
            'currency' => ['required', 'string', 'max:10'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        $items = collect($data['items'])->map(function (array $item) {
            return [
                'description' => trim($item['description']),
                'amount' => round((float) $item['amount'], 2),
            ];
        })->values()->all();

        $totalAmount = round(collect($items)->sum(function (array $item) {
            return (float) $item['amount'];
        }), 2);

        if ($totalAmount > 9999999999.99) {
            throw ValidationException::withMessages([
                'items' => 'The combined sourcing charges exceed the maximum invoice amount.',
            ]);
        }

        $sourcingRequest->invoices()->create([
            'invoice_number' => SourcingInvoice::generateInvoiceNumber(),
            'currency' => strtoupper($data['currency']),
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
        $sourcingInvoice->load('sourcingRequest.items');

        $pdf = app('dompdf.wrapper')->loadView('sourcing.invoice-pdf', compact('sourcingInvoice'));
        return $pdf->stream($sourcingInvoice->invoice_number . '.pdf');
    }
}
