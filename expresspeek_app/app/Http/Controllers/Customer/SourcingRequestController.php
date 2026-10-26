<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SourcingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\CountryZone;
use Illuminate\Support\Facades\DB;
use App\Helpers\CountryHelper;

class SourcingRequestController extends Controller
{
    public function create()
    {
        $storeRoute = route('customer.sourcing-requests.store');
        $isAdmin = false;
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
            'customer_name'         => 'required|string|max:255',
            'customer_email'        => 'nullable|email|max:255',
            'customer_phone_type'   => 'nullable|string|max:20',
            'whatsapp_country_code' => 'required|string|max:10',
            'whatsapp_number'       => 'required|string|max:50',
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
            'products.*.link' => 'nullable|string|max:2000',
            'products.*.image' => 'nullable|image|max:5120',
            'sourcing_request_id' => 'nullable|integer',
        ]);

        $sourcingRequest = SourcingRequest::create([
            'reference_number'        => SourcingRequest::generateReferenceNumber(),
            'customer_name'           => $validated['customer_name'],
            'customer_email'          => $validated['customer_email'] ?? auth()->user()->email,
            'customer_phone_type'     => $validated['customer_phone_type'] ?? 'Mobile',
            'whatsapp_country_code'   => $validated['whatsapp_country_code'],
            'whatsapp_number'         => $validated['whatsapp_number'],
            'destination_country'     => $validated['destination_country'],
            'destination_country_code'=> $validated['destination_country_code'],
            'destination_address'     => trim($validated['destination_address'] . ' ' . ($validated['destination_address2'] ?? '') . ' ' . ($validated['destination_address3'] ?? '')),
            'destination_city'        => $validated['destination_city'],
            'destination_state'       => $validated['destination_state'],
            'destination_postal_code' => $validated['destination_postal_code'],
            'status'                  => 'new',
            'user_id'                 => auth()->id(),
        ]);

        foreach ($validated['products'] as $productData) {
            $imagePath = null;
            if (isset($productData['image']) && $productData['image']) {
                $imagePath = $productData['image']->store('sourcing-requests', 'public');
            }

            $sourcingRequest->items()->create([
                'product_description' => $productData['description'],
                'product_link'        => $productData['link'] ?? null,
                'product_image'       => $imagePath,
                'status'              => 'pending',
            ]);
        }

        if (!empty($validated['sourcing_request_id'])) {
            SourcingRequest::where('id', $validated['sourcing_request_id'])
                ->where('user_id', auth()->id())
                ->where('status', SourcingRequest::STATUS_DRAFT)
                ->delete();
        }

        return redirect()->route('customer.sourcing-requests.index')
            ->with('success', 'Your Shop from BD request has been submitted successfully. We will get back to you shortly.');
    }

    public function index()
    {
        $sourcingRequests = SourcingRequest::with(['items', 'invoices'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
            
        return view('customer.sourcing-requests.index', compact('sourcingRequests'));
    }

    public function show(SourcingRequest $sourcingRequest)
    {
        abort_if($sourcingRequest->user_id !== auth()->id(), 403);
        $sourcingRequest->load(['items', 'invoices']);
        
        return view('customer.sourcing-requests.show', compact('sourcingRequest'));
    }

    public function edit(SourcingRequest $sourcingRequest)
    {
        abort_if($sourcingRequest->user_id !== auth()->id(), 403);
        
        $hasRejectedItems = $sourcingRequest->items()->where('status', 'rejected')->exists();

        // Only allow edit if there is a rejected item or it's a draft
        if (!$hasRejectedItems && $sourcingRequest->status !== SourcingRequest::STATUS_DRAFT) {
            return redirect()->route('customer.sourcing-requests.index')
                ->with('error', 'You can only edit requests that have rejected products.');
        }

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

        $formAction = route('customer.sourcing-requests.update', $sourcingRequest);
        
        return view('sourcing.edit', compact('sourcingRequest', 'formAction', 'countries'));
    }

    public function update(Request $request, SourcingRequest $sourcingRequest)
    {
        abort_if($sourcingRequest->user_id !== auth()->id(), 403);

        $hasRejectedItems = $sourcingRequest->items()->where('status', 'rejected')->exists();

        if (!$hasRejectedItems && $sourcingRequest->status !== SourcingRequest::STATUS_DRAFT) {
            return redirect()->route('customer.sourcing-requests.index')
                ->with('error', 'You can only edit requests that have rejected products.');
        }

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

        // Process items
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
                    // Clear rejection status since it has been edited
                    $updateData['status'] = null;
                    $updateData['rejection_reason'] = null;
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

        // Delete removed items
        $sourcingRequest->items()->whereNotIn('id', $existingItemIds)->delete();

        if ($request->filled('return_to')) {
            return redirect($request->return_to)
                ->with('success', 'Sourcing request updated successfully!');
        }

        return redirect()->route('customer.sourcing-requests.index')
            ->with('success', 'Sourcing request updated successfully!');
    }

    public function saveDraft(Request $request)
    {
        $data = $request->all();

        $sourcingRequest = null;
        if (!empty($data['sourcing_request_id'])) {
            $sourcingRequest = SourcingRequest::where('id', $data['sourcing_request_id'])
                ->where('user_id', auth()->id())
                ->where('status', SourcingRequest::STATUS_DRAFT)
                ->first();
        }

        if (!$sourcingRequest) {
            $sourcingRequest = new SourcingRequest();
            $sourcingRequest->reference_number = SourcingRequest::generateReferenceNumber();
            $sourcingRequest->status = SourcingRequest::STATUS_DRAFT;
            $sourcingRequest->user_id = auth()->id();
        }

        $sourcingRequest->customer_is_business = $data['customer_is_business'] ?? false;
        $sourcingRequest->customer_company = $data['customer_company'] ?? null;
        $sourcingRequest->customer_name = $data['customer_name'] ?? '[Draft Name]';
        $sourcingRequest->customer_country = $data['customer_country'] ?? null;
        $sourcingRequest->customer_country_code = $data['customer_country_code'] ?? null;
        $sourcingRequest->customer_address = $data['customer_address'] ?? null;
        $sourcingRequest->customer_address2 = $data['customer_address2'] ?? null;
        $sourcingRequest->customer_address3 = $data['customer_address3'] ?? null;
        $sourcingRequest->customer_city = $data['customer_city'] ?? null;
        $sourcingRequest->customer_state = $data['customer_state'] ?? null;
        $sourcingRequest->customer_postal_code = $data['customer_postal_code'] ?? null;
        $sourcingRequest->customer_email = $data['customer_email'] ?? null;
        $sourcingRequest->customer_phone_type = $data['customer_phone_type'] ?? null;
        $sourcingRequest->whatsapp_country_code = $data['whatsapp_country_code'] ?? '+880';
        $sourcingRequest->whatsapp_number = $data['whatsapp_number'] ?? '[Draft Number]';
        $sourcingRequest->tracking_number = null;
        $sourcingRequest->destination_country = $data['destination_country'] ?? '[Draft Country]';
        $sourcingRequest->destination_country_code = $data['destination_country_code'] ?? null;
        $sourcingRequest->destination_address = $data['destination_address'] ?? '[Draft Address]';
        $sourcingRequest->destination_city = $data['destination_city'] ?? '[Draft City]';
        $sourcingRequest->destination_state = $data['destination_state'] ?? null;
        $sourcingRequest->destination_postal_code = $data['destination_postal_code'] ?? null;
        $sourcingRequest->save();

        if (isset($data['products']) && is_array($data['products'])) {
            $sourcingRequest->items()->delete();
            foreach ($data['products'] as $productData) {
                if (!empty($productData['description'])) {
                    $sourcingRequest->items()->create([
                        'product_description' => $productData['description'],
                        'product_link'        => $productData['link'] ?? null,
                    ]);
                }
            }
        }

        return response()->json(['sourcing_request_id' => $sourcingRequest->id]);
    }

    public function destroy(SourcingRequest $sourcingRequest)
    {
        abort_if($sourcingRequest->user_id !== auth()->id(), 403);
        abort_if($sourcingRequest->status !== SourcingRequest::STATUS_DRAFT, 403, 'Only drafts can be deleted.');

        $sourcingRequest->delete();

        return redirect()->back()->with('success', 'Draft request removed successfully.');
    }
}
