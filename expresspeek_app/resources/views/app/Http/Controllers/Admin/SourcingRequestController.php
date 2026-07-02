<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SourcingRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SourcingRequestController extends Controller
{
    /**
     * List all sourcing requests with optional status filter.
     */
    public function index(Request $request)
    {
        $query = SourcingRequest::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        $sourcingRequests = $query->paginate(20)->withQueryString();
        $statuses = SourcingRequest::statuses();

        // Count new requests for the badge
        $newCount = SourcingRequest::where('status', SourcingRequest::STATUS_NEW)->count();

        return view('admin.sourcing.index', compact('sourcingRequests', 'statuses', 'newCount'));
    }

    /**
     * Show a single sourcing request.
     */
    public function show(SourcingRequest $sourcingRequest)
    {
        $sourcingRequest->load('user', 'items');
        $statuses = SourcingRequest::statuses();
        return view('admin.sourcing.show', compact('sourcingRequest', 'statuses'));
    }

    /**
     * Update status, notes, and quote for a sourcing request.
     */
    public function update(Request $request, SourcingRequest $sourcingRequest)
    {
        $data = $request->validate([
            'status'          => ['required', Rule::in(array_keys(SourcingRequest::statuses()))],
            'admin_notes'     => 'nullable|string|max:5000',
            'quoted_price'    => 'nullable|numeric|min:0',
            'quoted_currency' => 'nullable|string|max:10',
            'shipment_id'     => 'nullable|exists:shipments,id',
        ]);

        $sourcingRequest->update($data);

        return redirect()
            ->route('admin.sourcing-requests.show', $sourcingRequest)
            ->with('success', 'Sourcing request updated successfully.');
    }

    /**
     * Cancel (soft-delete) a sourcing request.
     */
    public function destroy(SourcingRequest $sourcingRequest)
    {
        $sourcingRequest->update(['status' => SourcingRequest::STATUS_CANCELLED]);

        return redirect()
            ->route('admin.sourcing-requests.index')
            ->with('success', "Request {$sourcingRequest->reference_number} has been cancelled.");
    }
}
