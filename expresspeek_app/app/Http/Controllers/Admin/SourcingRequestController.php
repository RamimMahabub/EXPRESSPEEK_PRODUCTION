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
        $sourcingRequest->load('user', 'items', 'carrier');
        $statuses = SourcingRequest::statuses();
        $carriers = \App\Models\Carrier::orderBy('name')->get();
        return view('admin.sourcing.show', compact('sourcingRequest', 'statuses', 'carriers'));
    }

    /**
     * Update status, notes, and quote for a sourcing request.
     */
    public function update(Request $request, SourcingRequest $sourcingRequest)
    {
        $data = $request->validate([
            'status'          => ['required', Rule::in(array_keys(SourcingRequest::statuses()))],
            'admin_notes'     => 'nullable|string|max:2000',
            'awb_number'      => 'nullable|string|max:191',
            'carrier_id'      => 'nullable|exists:carriers,id',
            'shipment_id'     => 'nullable|exists:shipments,id',
        ]);

        $sourcingRequest->update($data);

        return redirect()
            ->route('admin.sourcing-requests.show', $sourcingRequest)
            ->with('success', 'Sourcing request updated successfully.');
    }

    public function updateInline(Request $request, SourcingRequest $sourcingRequest)
    {
        $data = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(SourcingRequest::statuses()))],
            'carrier_tracking_number' => 'nullable|string|max:191',
            'carrier_id' => 'nullable|exists:carriers,id',
        ]);

        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'No data provided.'], 422);
        }

        if (array_key_exists('carrier_tracking_number', $data)) {
            $data['awb_number'] = $data['carrier_tracking_number'];
            unset($data['carrier_tracking_number']);
        }

        $sourcingRequest->update($data);

        return response()->json([
            'success' => true,
            'shipment' => [
                'id' => $sourcingRequest->id,
                'status' => $sourcingRequest->status,
                'carrier_tracking_number' => $sourcingRequest->awb_number,
                'status_label' => $sourcingRequest->status_label
            ]
        ]);
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
