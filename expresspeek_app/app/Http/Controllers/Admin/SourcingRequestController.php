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
            'carrier_tracking_number' => 'nullable|string|max:191',
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

        $sourcingRequest->update($data);

        return response()->json([
            'success' => true,
            'shipment' => [
                'id' => $sourcingRequest->id,
                'status' => $sourcingRequest->status,
                'carrier_tracking_number' => $sourcingRequest->carrier_tracking_number,
                'status_label' => $sourcingRequest->status_label
            ]
        ]);
    }

    /**
     * Delete a sourcing request.
     */
    public function destroy(SourcingRequest $sourcingRequest)
    {
        $reference = $sourcingRequest->reference_number;
        $sourcingRequest->delete();

        return redirect()
            ->route('admin.sourcing-requests.index')
            ->with('success', "Request {$reference} has been deleted successfully.");
    }

    /**
     * Delete an item from a sourcing request.
     */
    public function destroyItem(SourcingRequest $sourcingRequest, $item)
    {
        $sourcingItem = $sourcingRequest->items()->findOrFail($item);
        $itemDescription = $sourcingItem->product_description;
        
        $sourcingItem->delete();

        // Create whatsapp notification link
        $phone = $sourcingRequest->full_whatsapp;
        $msg = urlencode(
            "Hi {$sourcingRequest->customer_name}, this is ExpressPeek regarding your sourcing request {$sourcingRequest->reference_number}. We are sorry to inform you that we could not source the following item:\n\n{$itemDescription}"
        );
        $whatsappLink = "https://wa.me/{$phone}?text={$msg}";

        return redirect()
            ->route('admin.sourcing-requests.show', $sourcingRequest)
            ->with('success', 'Product removed successfully.')
            ->with('whatsapp_notify_link', $whatsappLink);
    }
}
