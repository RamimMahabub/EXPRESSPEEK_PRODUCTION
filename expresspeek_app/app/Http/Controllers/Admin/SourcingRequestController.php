<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SourcingRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CountryZone;
use Illuminate\Support\Facades\DB;
use App\Helpers\CountryHelper;

class SourcingRequestController extends Controller
{
    public function create()
    {
        $storeRoute = route('admin.sourcing-requests.store');
        $isAdmin = true;
        $quoteCountries = \App\Models\CountryZone::query()
            ->select('country_code', \Illuminate\Support\Facades\DB::raw('MAX(country_name) as original_name'))
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->get()
            ->map(fn ($country) => (object) [
                'country_code' => $country->country_code,
                'country_name' => \App\Helpers\CountryHelper::getCanonicalName($country->country_code),
            ])
            ->filter(fn ($country) => !empty($country->country_name))
            ->sortBy('country_name')
            ->values();
        return view('sourcing.create-wizard', compact('storeRoute', 'isAdmin', 'quoteCountries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:191',
            'whatsapp_country_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:30',
            'destination_country' => 'required|string|max:255',
            'destination_country_code' => 'required|string|size:2',
            'destination_address' => 'required|string|max:1000',
            'destination_address2' => 'nullable|string|max:1000',
            'destination_address3' => 'nullable|string|max:1000',
            'destination_city' => 'required|string|max:255',
            'destination_state' => 'nullable|string|max:255',
            'destination_postal_code' => 'nullable|string|max:50',
            'products' => 'required|array|min:1',
            'products.*.description' => 'required|string|max:1000',
            'products.*.link' => 'required|string|max:2000',
            'products.*.image' => 'nullable|image|max:5120',
        ]);

        $sourcingRequest = SourcingRequest::create([
            'reference_number' => SourcingRequest::generateReferenceNumber(),
            'customer_name' => $validated['customer_name'],
            'whatsapp_country_code' => $validated['whatsapp_country_code'],
            'whatsapp_number' => $validated['whatsapp_number'],
            'destination_country' => $validated['destination_country'],
            'destination_country_code' => $validated['destination_country_code'],
            'destination_address' => trim($validated['destination_address'] . ' ' . ($validated['destination_address2'] ?? '') . ' ' . ($validated['destination_address3'] ?? '')),
            'destination_city' => $validated['destination_city'],
            'destination_state' => $validated['destination_state'],
            'destination_postal_code' => $validated['destination_postal_code'],
            'status' => 'new',
            'user_id' => auth()->id(), // Alternatively, look up user by phone number
        ]);

        foreach ($validated['products'] as $productData) {
            $imagePath = null;
            if (isset($productData['image']) && $productData['image']) {
                $imagePath = $productData['image']->store('sourcing-requests', 'public');
            }

            $sourcingRequest->items()->create([
                'description' => $productData['description'],
                'link' => $productData['link'],
                'image' => $imagePath,
                'status' => 'pending',
                'quantity' => 1,
            ]);
        }

        return redirect()->route('admin.sourcing-requests.show', $sourcingRequest)
            ->with('success', 'Sourcing Request created successfully.');
    }

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

    public function edit(SourcingRequest $sourcingRequest)
    {
        $sourcingRequest->load('items');
        
        $countries = CountryZone::query()
            ->select('country_code', DB::raw('MAX(country_name) as original_name'))
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->get()
            ->map(fn ($country) => (object) [
                'country_code' => $country->country_code,
                'country_name' => CountryHelper::getCanonicalName($country->country_code),
            ])
            ->filter(fn ($country) => $country->country_name !== '')
            ->sortBy('country_name')
            ->values();

        $formAction = route('admin.sourcing-requests.update-details', $sourcingRequest);
        return view('sourcing.edit', compact('sourcingRequest', 'formAction', 'countries'));
    }

    /**
     * Update details, customer info, items (from the edit view)
     */
    public function updateDetails(Request $request, SourcingRequest $sourcingRequest)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'whatsapp_country_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:50',
            'destination_country' => 'required|string|max:255',
            'destination_country_code' => 'required|string|size:2',
            'destination_address' => 'required|string|max:1000',
            'destination_city' => 'required|string|max:255',
            'destination_state' => 'nullable|string|max:255',
            'destination_postal_code' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:1000',
            'items.*.link' => 'nullable|url|max:2000',
            'items.*.image' => 'nullable|image|max:5120',
            'items.*.id' => 'nullable|exists:sourcing_request_items,id'
        ]);

        $sourcingRequest->update([
            'customer_name' => $validated['customer_name'],
            'whatsapp_country_code' => $validated['whatsapp_country_code'],
            'whatsapp_number' => $validated['whatsapp_number'],
            'destination_country' => $validated['destination_country'],
            'destination_country_code' => $validated['destination_country_code'],
            'destination_address' => $validated['destination_address'],
            'destination_city' => $validated['destination_city'],
            'destination_state' => $validated['destination_state'],
            'destination_postal_code' => $validated['destination_postal_code'],
        ]);

        $existingItemIds = [];
        foreach ($validated['items'] as $itemData) {
            $imagePath = null;
            if (isset($itemData['image'])) {
                $imagePath = $itemData['image']->store('sourcing_images', 'public');
            }

            if (isset($itemData['id'])) {
                $existingItemIds[] = $itemData['id'];
                $item = $sourcingRequest->items()->find($itemData['id']);
                if ($item) {
                    $updateData = [
                        'product_description' => $itemData['description'],
                        'product_link' => $itemData['link'] ?? null,
                    ];
                    if ($imagePath) {
                        $updateData['product_image'] = $imagePath;
                    }
                    $item->update($updateData);
                }
            } else {
                $newItem = $sourcingRequest->items()->create([
                    'product_description' => $itemData['description'],
                    'product_link' => $itemData['link'] ?? null,
                    'product_image' => $imagePath,
                ]);
                $existingItemIds[] = $newItem->id;
            }
        }

        $sourcingRequest->items()->whereNotIn('id', $existingItemIds)->delete();

        if ($request->filled('return_to')) {
            return redirect($request->return_to)
                ->with('success', 'Sourcing request details updated successfully!');
        }

        return redirect()->route('admin.sourcing-requests.show', $sourcingRequest)
            ->with('success', 'Sourcing request details updated successfully!');
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

        $originalStatus = $sourcingRequest->status;
        $sourcingRequest->update($data);

        if (array_key_exists('status', $data) && $originalStatus !== $sourcingRequest->status) {
            $this->notifySourcingStatusChange($sourcingRequest);
        }

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

        $originalStatus = $sourcingRequest->status;
        $sourcingRequest->update($data);

        if (array_key_exists('status', $data) && $originalStatus !== $sourcingRequest->status) {
            $this->notifySourcingStatusChange($sourcingRequest);
        }

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

    private function notifySourcingStatusChange(SourcingRequest $sourcingRequest): void
    {
        $notification = new \App\Notifications\SourcingRequestStatusUpdatedNotification($sourcingRequest);

        if ($sourcingRequest->user) {
            $sourcingRequest->user->notify($notification);
        }

        if ($sourcingRequest->shipment?->agent && $sourcingRequest->shipment->agent->id !== $sourcingRequest->user_id) {
            $sourcingRequest->shipment->agent->notify($notification);
        }
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

    /**
     * Reject an item from a sourcing request.
     */
    public function rejectItem(\Illuminate\Http\Request $request, SourcingRequest $sourcingRequest, $item)
    {
        $data = $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $sourcingItem = $sourcingRequest->items()->findOrFail($item);
        $sourcingItem->update([
            'status' => 'rejected',
            'rejection_reason' => $data['rejection_reason']
        ]);

        // Send in-app notification if it's a registered user
        if ($sourcingRequest->user) {
            $sourcingRequest->user->notify(new \App\Notifications\SourcingItemRejectedNotification($sourcingItem));
        }

        // Create whatsapp notification link for guests or quick chat
        $phone = $sourcingRequest->full_whatsapp;
        $msg = urlencode(
            "Hi {$sourcingRequest->customer_name}, this is ExpressPeek regarding your sourcing request {$sourcingRequest->reference_number}. We are sorry to inform you that we cannot source the following item:\n\n{$sourcingItem->product_description}\n\nReason: {$data['rejection_reason']}"
        );
        $whatsappLink = "https://wa.me/{$phone}?text={$msg}";

        return redirect()
            ->route('admin.sourcing-requests.show', $sourcingRequest)
            ->with('success', 'Product marked as rejected.')
            ->with('whatsapp_notify_link', $whatsappLink);
    }
}
