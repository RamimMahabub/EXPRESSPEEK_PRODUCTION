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

        $sourcingInvoice->load('sourcingRequest.items');

        $pdf = app('dompdf.wrapper')->loadView('sourcing.invoice-pdf', compact('sourcingInvoice'));
        return $pdf->stream($sourcingInvoice->invoice_number . '.pdf');
    }

    public function show(SourcingInvoice $sourcingInvoice)
    {
        if ((int) $sourcingInvoice->sourcingRequest->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $sourcingInvoice->load('sourcingRequest.items');

        return view('customer.sourcing-invoices.show', compact('sourcingInvoice'));
    }

    public function pay(\Illuminate\Http\Request $request, SourcingInvoice $sourcingInvoice)
    {
        if ((int) $sourcingInvoice->sourcingRequest->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($sourcingInvoice->status !== 'unpaid') {
            return back()->with('error', 'This invoice is already paid or processing.');
        }

        $data = $request->validate([
            'payment_method' => ['required', 'string', 'in:bkash,bank'],
            'transaction_id' => ['required', 'string', 'max:255'],
            'proof_of_payment' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'], // max 5MB
        ]);

        $path = $request->file('proof_of_payment')->store('payment-proofs', 'public');

        $sourcingInvoice->update([
            'payment_method' => $data['payment_method'],
            'transaction_id' => $data['transaction_id'],
            'proof_of_payment_path' => $path,
            'status' => 'processing',
        ]);

        \Illuminate\Support\Facades\Notification::send(
            \App\Models\User::role('admin')->get(),
            new \App\Notifications\PaymentStatusUpdatedNotification($sourcingInvoice->fresh('sourcingRequest'))
        );

        return redirect()->route('customer.sourcing-requests.show', $sourcingInvoice->sourcingRequest)
            ->with('success', 'Payment details submitted successfully. We will verify and update the status shortly.');
    }
}
