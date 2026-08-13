<?php

namespace App\Http\Controllers\Admin;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Shipment;
use App\Support\Code128Barcode;
use App\Support\PackageWeightCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Services\QuoteService;
use App\Models\AddressBook;

class ShipmentController extends Controller
{
    public function __construct(private QuoteService $quoteService) {}

    public function addressBookPage()
    {
        return view('admin.address-book');
    }

    public function getAddressBook()
    {
        $entries = auth()->user()->addressBook()->orderBy('name')->get();
        return response()->json($entries);
    }

    public function saveAddress(Request $request)
    {
        $data = $request->validate([
            'label'        => 'nullable|string|max:100',
            'name'         => 'required|string|max:191',
            'company'      => 'nullable|string|max:191',
            'is_business'  => 'boolean',
            'country_code' => 'nullable|string|max:5',
            'country_name' => 'nullable|string|max:191',
            'address'      => 'required|string|max:500',
            'address2'     => 'nullable|string|max:255',
            'address3'     => 'nullable|string|max:255',
            'postal_code'  => 'nullable|string|max:20',
            'city'         => 'required|string|max:100',
            'state'        => 'nullable|string|max:100',
            'email'        => 'nullable|email|max:191',
            'phone_type'   => 'nullable|string|max:20',
            'phone_code'   => 'nullable|string|max:10',
            'phone'        => 'nullable|string|max:30',
        ]);

        $data = $this->normalizeAddressBookData($data);
        $data['user_id'] = auth()->id();

        $existingEntry = AddressBook::query()
            ->where('user_id', $data['user_id'])
            ->get()
            ->first(function (AddressBook $entry) use ($data) {
                return $this->addressBookMatches($entry, $data);
            });

        if ($existingEntry) {
            return response()->json([
                'success' => true,
                'duplicate' => true,
                'message' => 'This address is already saved.',
                'entry' => $existingEntry,
            ]);
        }

        $entry = AddressBook::create($data);

        return response()->json(['success' => true, 'entry' => $entry], 201);
    }

    public function deleteAddress(AddressBook $addressBook)
    {
        if ((int) $addressBook->user_id !== (int) auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to delete this address.',
            ], 403);
        }

        $addressBook->delete();
        return response()->json(['success' => true]);
    }

    private function normalizeAddressBookData(array $data): array
    {
        $stringFields = [
            'label', 'name', 'company', 'country_code', 'country_name', 'address',
            'address2', 'address3', 'postal_code', 'city', 'state', 'email',
            'phone_type', 'phone_code', 'phone',
        ];

        foreach ($stringFields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null) {
                continue;
            }

            $value = trim((string) $data[$field]);
            $data[$field] = $value === '' ? null : preg_replace('/\s+/u', ' ', $value);
        }

        $data['country_code'] = $data['country_code'] ? strtoupper((string) $data['country_code']) : null;
        $data['phone_code'] = $data['phone_code'] ? trim((string) $data['phone_code']) : null;
        $data['phone_type'] = $data['phone_type'] ?: 'Office';
        $data['is_business'] = (bool) ($data['is_business'] ?? false);

        return $data;
    }

    private function addressBookMatches(AddressBook $entry, array $data): bool
    {
        $fields = [
            'name', 'company', 'country_code', 'country_name', 'address', 'address2',
            'address3', 'postal_code', 'city', 'state', 'email', 'phone_type',
            'phone_code', 'phone', 'is_business',
        ];

        foreach ($fields as $field) {
            $entryValue = $entry->{$field};
            $inputValue = $data[$field] ?? null;

            if ($field === 'is_business') {
                if ((bool) $entryValue !== (bool) $inputValue) {
                    return false;
                }
                continue;
            }

            if ($this->normalizeAddressBookValue($entryValue) !== $this->normalizeAddressBookValue($inputValue)) {
                return false;
            }
        }

        return true;
    }

    private function normalizeAddressBookValue($value): string
    {
        if ($value === null) {
            return '';
        }
        return strtolower(preg_replace('/\s+/u', ' ', trim((string) $value)));
    }

    public function create()
    {
        $carriers = Carrier::orderBy('name')->get();
        return view('admin.shipments.create', compact('carriers'));
    }

    public function getCarriers(Request $request)
    {
        $countryCode = $this->resolveCountryCode($request->get('country_code', $request->get('country', '')));
        $weight = max((float) $request->get('weight', 1.0), 0.5);
        $shipmentType = $request->get('type', 'non_document');

        $quotes = $this->quoteService->getQuotes($countryCode, $weight, $shipmentType);

        $carriers = collect($quotes['options'] ?? [])
            ->map(function (array $option) {
                $carrier = Carrier::find($option['carrier_id'] ?? null);
                if (!$carrier) return null;
                return [
                    'id' => $carrier->id,
                    'name' => $option['carrier'],
                    'currency' => $carrier->currency,
                    'price' => $option['total_price'],
                    'price_bdt' => $option['total_price_bdt'],
                ];
            })
            ->filter()
            ->values();

        return response()->json($carriers);
    }

    private function resolveCountryCode(string $value): string
    {
        $input = strtoupper(trim($value));
        if ($input === '') return '';
        if (strlen($input) === 2) return $input;
        $aliases = ['UAE' => 'AE', 'UNITED ARAB EMIRATES' => 'AE', 'USA' => 'US', 'UNITED STATES' => 'US', 'UNITED STATES OF AMERICA' => 'US', 'KOREA (SOUTH)' => 'KR', 'SOUTH KOREA' => 'KR', 'VIET NAM' => 'VN'];
        return $aliases[$input] ?? $input;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sender_name'         => 'required|string|max:191',
            'sender_company'      => 'nullable|string|max:191',
            'sender_is_business'  => 'boolean',
            'sender_country_code' => 'nullable|string|max:5',
            'sender_country'      => 'required|string|max:191',
            'sender_address'      => 'required|string|max:500',
            'sender_address2'     => 'nullable|string|max:255',
            'sender_address3'     => 'nullable|string|max:255',
            'sender_postal_code'  => 'nullable|string|max:20',
            'sender_city'         => 'required|string|max:100',
            'sender_state'        => 'nullable|string|max:100',
            'sender_email'        => 'nullable|email|max:191',
            'sender_phone_type'   => 'nullable|string|max:20',
            'sender_phone_code'   => 'nullable|string|max:10',
            'sender_phone'        => 'nullable|string|max:30',
            'receiver_name'         => 'required|string|max:191',
            'receiver_company'      => 'nullable|string|max:191',
            'receiver_is_business'  => 'boolean',
            'receiver_country_code' => 'nullable|string|max:5',
            'receiver_country'      => 'required|string|max:191',
            'receiver_address'      => 'required|string|max:500',
            'receiver_address2'     => 'nullable|string|max:255',
            'receiver_address3'     => 'nullable|string|max:255',
            'receiver_postal_code'  => 'nullable|string|max:20',
            'receiver_city'         => 'required|string|max:100',
            'receiver_state'        => 'nullable|string|max:100',
            'receiver_email'        => 'nullable|email|max:191',
            'receiver_phone_type'   => 'nullable|string|max:20',
            'receiver_phone_code'   => 'nullable|string|max:10',
            'receiver_phone'        => 'nullable|string|max:30',
            'shipment_type'          => 'required|in:document,non_document',
            'document_description'   => 'nullable|string|max:500',
            'shipment_purpose'       => 'nullable|string|max:191',
            'items'                  => 'nullable|array',
            'items.*.name'           => 'exclude_if:shipment_type,document|required_if:shipment_type,non_document|string|max:191',
            'items.*.quantity'       => 'exclude_if:shipment_type,document|required_if:shipment_type,non_document|integer|min:1',
            'items.*.value_per_item' => 'exclude_if:shipment_type,document|required_if:shipment_type,non_document|numeric|min:0',
            'packages'               => 'required|array|min:1',
            'packages.*.weight'      => 'required|numeric|min:0.01',
            'packages.*.length'      => 'nullable|numeric|min:0.01',
            'packages.*.width'       => 'nullable|numeric|min:0.01',
            'packages.*.height'      => 'nullable|numeric|min:0.01',
            'packages.*.quantity'    => 'required|integer|min:1',
            'carrier_id'             => 'required|exists:carriers,id',
        ]);

        $data['packages'] = PackageWeightCalculator::normalize($data['packages']);
        $packageTotals = PackageWeightCalculator::totals($data['packages']);
        $totalPackages = $packageTotals['packages'];
        $totalWeight = $packageTotals['weight'];

        $carrier = Carrier::findOrFail($data['carrier_id']);
        $user = auth()->user();

        $shipmentData = array_merge($data, [
            'tracking_number'     => $this->generateTrackingNumber(),
            'invoice_number'      => 'INV-' . strtoupper(\Illuminate\Support\Str::random(6)) . '-' . now()->format('Ymd'),
            'total_packages'      => $totalPackages,
            'total_weight'        => $totalWeight,
            'weight'              => $totalWeight,
            'carrier_name'        => $carrier->name,
            'status'              => Shipment::STATUS_PENDING,
            'sender_id'           => $user?->id,
        ]);

        $shipment = Shipment::create($shipmentData);

        $shipment->trackingEvents()->create([
            'location'    => $shipmentData['sender_city'] . ', ' . $shipmentData['sender_country'],
            'status'      => Shipment::STATUS_PENDING,
            'notes'       => $shipment->creation_note,
            'occurred_at' => now(),
        ]);

        return response()->json([
            'success'    => true,
            'shipment'   => $shipment,
            'waybill_url' => route('admin.shipments.waybill', $shipment->id),
            'invoice_url' => route('admin.shipments.invoice', $shipment->id),
        ]);
    }

    private function generateTrackingNumber(): string
    {
        return Shipment::generateTrackingNumber();
    }
    public function index(Request $request)
    {
        $recordType = in_array($request->get('record_type', 'shipment'), ['shipment', 'sourcing', 'all'], true)
            ? $request->get('record_type', 'shipment')
            : 'shipment';
        
        if ($recordType === 'sourcing' || $recordType === 'all') {
            $query = \App\Models\SourcingRequest::with(['user', 'carrier'])->latest();

            if ($request->filled('start_date')) {
                $query->whereDate('sourcing_requests.created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('sourcing_requests.created_at', '<=', $request->end_date);
            }
            if ($request->filled('carriers')) {
                $query->whereIn('carrier_id', $request->carriers);
            }
            if ($request->filled('statuses')) {
                $query->whereIn('status', $request->statuses);
            }
            if ($request->filled('agents')) {
                // Sourcing requests belong to the shared desk and do not have an agent_id.
                $query->whereRaw('1 = 0');
            }
            if ($request->filled('search')) {
                $search = trim((string) $request->search);
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('tracking_number', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhere('awb_number', 'like', "%{$search}%");
                });
            }

            $sourcingRequests = $recordType === 'all' ? $query->get() : $query->paginate(15)->withQueryString();
            
            $collection = $recordType === 'all' ? $sourcingRequests : $sourcingRequests->getCollection();
            $collection->transform(function ($req) {
                $req->is_sourcing = true;
                $req->tracking_number = $req->tracking_number;
                $req->agent = (object) ['name' => $req->user?->name ?? '—'];
                $req->receiver_name = $req->customer_name;
                $req->receiver_city = $req->destination_city;
                $req->receiver_country = $req->destination_country;
                return $req;
            });

            if ($recordType === 'sourcing') {
                $shipments = $sourcingRequests;
            }
        }

        if ($recordType === 'shipment' || $recordType === 'all') {
            $query = \App\Models\Shipment::with(['sender', 'agent'])->latest();

            if ($request->filled('start_date')) {
                $query->whereDate('shipments.created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('shipments.created_at', '<=', $request->end_date);
            }
            if ($request->filled('carriers')) {
                $query->whereIn('carrier_id', $request->carriers);
            }
            if ($request->filled('agents')) {
                $query->whereIn('agent_id', $request->agents);
            }
            if ($request->filled('statuses')) {
                $query->whereIn('status', $request->statuses);
            }
            if ($request->filled('search')) {
                $search = trim((string) $request->search);
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('sender_name', 'like', "%{$search}%")
                        ->orWhere('tracking_number', 'like', "%{$search}%")
                        ->orWhere('carrier_tracking_number', 'like', "%{$search}%")
                        ->orWhereHas('sender', function ($senderQuery) use ($search) {
                            $senderQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $standardShipments = $recordType === 'all' ? $query->get() : $query->paginate(15)->withQueryString();
            
            $collection = $recordType === 'all' ? $standardShipments : $standardShipments->getCollection();
            $collection->transform(function ($shipment) {
                $shipment->is_sourcing = false;
                return $shipment;
            });

            if ($recordType === 'shipment') {
                $shipments = $standardShipments;
            }
        }

        if ($recordType === 'all') {
            $all = collect();
            if (isset($sourcingRequests)) {
                $all = $all->concat($sourcingRequests);
            }
            if (isset($standardShipments)) {
                $all = $all->concat($standardShipments);
            }
            
            $all = $all->sortByDesc('created_at')->values();
            
            $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
            $perPage = 15;
            $shipments = new \Illuminate\Pagination\LengthAwarePaginator(
                $all->forPage($page, $perPage),
                $all->count(),
                $perPage,
                $page,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        }

        $carriers = Carrier::all();
        $agents = \App\Models\User::role('agent')->get();
        $statuses = match ($recordType) {
            'sourcing' => \App\Models\SourcingRequest::statuses(),
            'all' => Shipment::statuses() + \App\Models\SourcingRequest::statuses(),
            default => Shipment::statuses(),
        };

        return view('admin.shipments.index', compact('shipments', 'carriers', 'agents', 'statuses', 'recordType'));
    }

    public function edit(Shipment $shipment)
    {
        $invoiceSystemReady = Schema::hasTable('shipment_invoices');

        $shipment->load('agent');
        if ($invoiceSystemReady) {
            $shipment->load('invoices');
        } else {
            // Keep shipment management available while a newly deployed
            // invoice migration is still waiting to be applied.
            $shipment->setRelation('invoices', collect());
        }

        $carriers = Carrier::orderBy('name')->get(['id', 'name', 'currency']);

        return view('admin.shipments.edit', compact('shipment', 'carriers', 'invoiceSystemReady'));
    }

    public function update(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            // Sender
            'sender_name'         => 'required|string|max:191',
            'sender_company'      => 'nullable|string|max:191',
            'sender_is_business'  => 'nullable|boolean',
            'sender_country_code' => 'nullable|string|max:5',
            'sender_country'      => 'required|string|max:191',
            'sender_address'      => 'required|string|max:500',
            'sender_address2'     => 'nullable|string|max:255',
            'sender_address3'     => 'nullable|string|max:255',
            'sender_postal_code'  => 'nullable|string|max:20',
            'sender_city'         => 'required|string|max:100',
            'sender_state'        => 'nullable|string|max:100',
            'sender_email'        => 'nullable|email|max:191',
            'sender_phone_type'   => 'nullable|string|max:20',
            'sender_phone_code'   => 'nullable|string|max:10',
            'sender_phone'        => 'nullable|string|max:30',
            // Receiver
            'receiver_name'         => 'required|string|max:191',
            'receiver_company'      => 'nullable|string|max:191',
            'receiver_is_business'  => 'nullable|boolean',
            'receiver_country_code' => 'nullable|string|max:5',
            'receiver_country'      => 'required|string|max:191',
            'receiver_address'      => 'required|string|max:500',
            'receiver_address2'     => 'nullable|string|max:255',
            'receiver_address3'     => 'nullable|string|max:255',
            'receiver_postal_code'  => 'nullable|string|max:20',
            'receiver_city'         => 'required|string|max:100',
            'receiver_state'        => 'nullable|string|max:100',
            'receiver_email'        => 'nullable|email|max:191',
            'receiver_phone_type'   => 'nullable|string|max:20',
            'receiver_phone_code'   => 'nullable|string|max:10',
            'receiver_phone'        => 'nullable|string|max:30',
            // Shipment
            'shipment_type'         => 'required|in:document,non_document',
            'document_description'  => 'nullable|string|max:500',
            'carrier_id'            => 'nullable|exists:carriers,id',
            'status'                => ['required', Rule::in(array_keys(Shipment::statuses()))],
            'estimated_delivery'    => 'nullable|date',
            'notes'                 => 'nullable|string|max:2000',
            'carrier_tracking_number' => 'nullable|string|max:100',
            'total_packages'        => 'nullable|integer|min:1',
            'total_weight'          => 'nullable|numeric|min:0.01',
            // Structured editors
            'items'                 => 'nullable|array',
            'items.*.name'          => 'exclude_if:shipment_type,document|required_with:items|string|max:191',
            'items.*.quantity'      => 'exclude_if:shipment_type,document|required_with:items|integer|min:1',
            'items.*.value_per_item'=> 'exclude_if:shipment_type,document|required_with:items|numeric|min:0',
            'packages'              => 'nullable|array|min:1',
            'packages.*.weight'     => 'required_with:packages|numeric|min:0.01',
            'packages.*.length'     => 'nullable|numeric|min:0.01',
            'packages.*.width'      => 'nullable|numeric|min:0.01',
            'packages.*.height'     => 'nullable|numeric|min:0.01',
            'packages.*.quantity'   => 'required_with:packages|integer|min:1',
        ]);

        $data['carrier_tracking_number'] = filled($data['carrier_tracking_number'] ?? null)
            ? trim((string) $data['carrier_tracking_number'])
            : null;

        $items = $shipment->items;
        if ($request->has('items') && is_array($request->input('items'))) {
            $items = collect($request->input('items'))
                ->filter(fn ($row) => is_array($row) && !empty(trim((string) ($row['name'] ?? ''))))
                ->map(function (array $row) {
                    return [
                        'name' => trim((string) ($row['name'] ?? '')),
                        'quantity' => max((int) ($row['quantity'] ?? 1), 1),
                        'value_per_item' => max((float) ($row['value_per_item'] ?? 0), 0),
                    ];
                })
                ->values()
                ->all();
        }

        $packages = $shipment->packages;
        if ($request->has('packages') && is_array($request->input('packages'))) {
            $packageRows = collect($request->input('packages'))
                ->filter(fn ($row) => is_array($row) && ((float) ($row['weight'] ?? 0)) > 0)
                ->values()
                ->all();

            $packages = PackageWeightCalculator::normalize($packageRows);
        }

        $totalPackages = (int) ($data['total_packages'] ?? ($shipment->total_packages ?: 1));
        $totalWeight = (float) ($data['total_weight'] ?? ($shipment->total_weight ?: 0));

        if (is_array($packages) && !empty($packages)) {
            $calcPackages = 0;
            $calcWeight = 0.0;

            foreach ($packages as $pkg) {
                $qty = max((int) ($pkg['quantity'] ?? 1), 1);
                $wt = (float) ($pkg['weight'] ?? 0);
                $calcPackages += $qty;
                $calcWeight += ($wt * $qty);
            }

            if ($calcPackages > 0) {
                $totalPackages = $calcPackages;
            }

            if ($calcWeight > 0) {
                $totalWeight = $calcWeight;
            }
        }

        $carrier = null;
        if (!empty($data['carrier_id'])) {
            $carrier = Carrier::find($data['carrier_id']);
        }

        $originalStatus = $shipment->status;
        $labelBecameAvailable = blank($shipment->carrier_tracking_number)
            && filled($data['carrier_tracking_number'] ?? null);

        $shipment->update([
            'sender_name' => $data['sender_name'],
            'sender_company' => $data['sender_company'] ?? null,
            'sender_is_business' => (bool) ($data['sender_is_business'] ?? false),
            'sender_country_code' => $data['sender_country_code'] ?? null,
            'sender_country' => $data['sender_country'],
            'sender_address' => $data['sender_address'],
            'sender_address2' => $data['sender_address2'] ?? null,
            'sender_address3' => $data['sender_address3'] ?? null,
            'sender_postal_code' => $data['sender_postal_code'] ?? null,
            'sender_city' => $data['sender_city'],
            'sender_state' => $data['sender_state'] ?? null,
            'sender_email' => $data['sender_email'] ?? null,
            'sender_phone_type' => $data['sender_phone_type'] ?? null,
            'sender_phone_code' => $data['sender_phone_code'] ?? null,
            'sender_phone' => $data['sender_phone'] ?? null,

            'receiver_name' => $data['receiver_name'],
            'receiver_company' => $data['receiver_company'] ?? null,
            'receiver_is_business' => (bool) ($data['receiver_is_business'] ?? false),
            'receiver_country_code' => $data['receiver_country_code'] ?? null,
            'receiver_country' => $data['receiver_country'],
            'receiver_address' => $data['receiver_address'],
            'receiver_address2' => $data['receiver_address2'] ?? null,
            'receiver_address3' => $data['receiver_address3'] ?? null,
            'receiver_postal_code' => $data['receiver_postal_code'] ?? null,
            'receiver_city' => $data['receiver_city'],
            'receiver_state' => $data['receiver_state'] ?? null,
            'receiver_email' => $data['receiver_email'] ?? null,
            'receiver_phone_type' => $data['receiver_phone_type'] ?? null,
            'receiver_phone_code' => $data['receiver_phone_code'] ?? null,
            'receiver_phone' => $data['receiver_phone'] ?? null,

            'shipment_type' => $data['shipment_type'],
            'document_description' => $data['document_description'] ?? null,
            'items' => $items,
            'packages' => $packages,
            'total_packages' => $totalPackages,
            'total_weight' => $totalWeight,
            'weight' => $totalWeight,

            'carrier_id' => $carrier?->id,
            'carrier_name' => $carrier?->name,
            'status' => $data['status'],
            'estimated_delivery' => $data['estimated_delivery'] ?? null,
            'notes' => $data['notes'] ?? null,
            'carrier_tracking_number' => $data['carrier_tracking_number'] ?? null,
        ]);

        if ($originalStatus !== $shipment->status) {
            $shipment->trackingEvents()->create([
                'location' => ($shipment->receiver_city ?: 'Destination') . ', ' . ($shipment->receiver_country ?: ''),
                'status' => $shipment->status,
                'notes' => 'Status updated by admin from ' . ($originalStatus ?: 'unknown') . ' to ' . $shipment->status . '.',
                'occurred_at' => now(),
            ]);
        }

        $redirect = redirect()
            ->route('admin.shipments.edit', $shipment)
            ->with('success', 'Shipment updated successfully.');

        if ($labelBecameAvailable) {
            $redirect->with('label_ready', route('admin.shipments.label', $shipment));
        }

        return $redirect;
    }

    public function updateInline(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Shipment::statuses()))],
            'carrier_tracking_number' => 'nullable|string|max:100',
            'carrier_id' => 'nullable|exists:carriers,id',
        ]);

        if (array_key_exists('carrier_tracking_number', $data)) {
            $data['carrier_tracking_number'] = filled($data['carrier_tracking_number'])
                ? trim((string) $data['carrier_tracking_number'])
                : null;
        }

        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'No data provided.'], 422);
        }

        if (array_key_exists('carrier_id', $data)) {
             $carrier = \App\Models\Carrier::find($data['carrier_id']);
             $data['carrier_name'] = $carrier?->name;
        }

        $originalStatus = $shipment->status;
        $shipment->update($data);

        if (isset($data['status']) && $originalStatus !== $shipment->status) {
            $shipment->trackingEvents()->create([
                'location' => ($shipment->receiver_city ?: 'Destination') . ', ' . ($shipment->receiver_country ?: ''),
                'status' => $shipment->status,
                'notes' => 'Status updated inline by admin from ' . ($originalStatus ?: 'unknown') . ' to ' . $shipment->status . '.',
                'occurred_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'shipment' => [
                'id' => $shipment->id,
                'status' => $shipment->status,
                'carrier_tracking_number' => $shipment->carrier_tracking_number,
                'status_label' => $shipment->status_label,
                'label_url' => (filled($shipment->carrier_tracking_number) || $shipment->status === 'connected_to_carrier')
                    ? route('admin.shipments.label', $shipment)
                    : null,
            ]
        ]);
    }

    public function printLabel(Shipment $shipment)
    {
        abort_if(
            blank($shipment->carrier_tracking_number) && $shipment->status !== 'connected_to_carrier',
            404,
            'Add a carrier tracking number or update status to Connected to Carrier before generating this label.'
        );

        $shipment->loadMissing('carrier');
        $trackingNumber = (string) $shipment->tracking_number;
        $trackingUrl = route('track', ['tracking' => $trackingNumber]);
        // Hide QR on labels for shipments created by agents
        $createdByAgent = $shipment->createdByAgent || $shipment->agent;
        $hideTrackingQr = (bool) $createdByAgent;
        $qrSvg = $this->generateQrSvg($trackingUrl, 220);
        $barcodeSvg = Code128Barcode::svg($trackingNumber, 58, 2);
        // Embedded crop of the official Express Peek mark. Keeping it inside the
        // PDF payload avoids missing-file and unsupported-WebP fallbacks on cPanel.
        $logoDataUri = 'data:image/jpeg;base64,' . <<<'LOGO'
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
LOGO;
        $logoMarkSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">
  <circle cx="60" cy="56" r="42" fill="none" stroke="#0754a6" stroke-width="7"/>
  <path d="M17 56 103 20 70 99 55 68z" fill="#073b91"/>
  <path d="m17 56 38 12 48-48-61 37z" fill="#fff"/>
  <path d="M69 99 55 68l48-48z" fill="#086fc2"/>
  <path d="M11 92 35 78M8 103l29-18" fill="none" stroke="#164b9c" stroke-width="3"/>
</svg>
SVG;
        $logoDataUri = 'data:image/svg+xml;base64,' . base64_encode($logoMarkSvg);
        $pdfLogoPath = resource_path('images/express-peek-logo-pdf.png');
        if (is_file($pdfLogoPath) && is_readable($pdfLogoPath)) {
            $logoDataUri = 'data:image/png;base64,' . base64_encode(file_get_contents($pdfLogoPath));
        }

        return view('admin.shipments.label-print-v2', compact(
            'shipment',
            'trackingUrl',
            'qrSvg',
            'barcodeSvg',
            'logoDataUri',
            'hideTrackingQr'
        ));
    }

    public function printWaybill(Shipment $shipment)
    {
        $qrPayload = route('track', [
            'tracking' => (string) $shipment->tracking_number,
        ]);

        $qrSvg = $this->generateQrSvg($qrPayload);

        return view('agent.shipments.waybill', compact('shipment', 'qrSvg'));
    }

    public function printInvoice(Shipment $shipment)
    {
        return view('agent.shipments.invoice', compact('shipment'));
    }

    private function generateQrSvg(string $text, int $size = 140): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString($text);
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return redirect()
            ->route('admin.shipments.index')
            ->with('success', 'Shipment deleted successfully.');
    }
}
