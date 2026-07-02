<?php

namespace App\Http\Controllers\Customer;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Http\Controllers\Controller;
use App\Models\AddressBook;
use App\Models\Carrier;
use App\Models\Shipment;
use App\Services\QuoteService;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ShipmentController extends Controller
{
    public function __construct(
        private ShipmentService $shipmentService,
        private QuoteService $quoteService,
    ) {}

    public function create()
    {
        $carriers = Carrier::orderBy('name')->get();
        return view('customer.shipments.create', compact('carriers'));
    }

    /**
     * Show edit form for a shipment owned by this agent.
     */

    /**
     * Show the standalone address book page for agents.
     */
    public function addressBookPage()
    {
        return view('customer.address-book');
    }

    /**
     * GET /agent/address-book — Return the agent's saved addresses as JSON.
     */
    public function getAddressBook()
    {
        $entries = auth()->user()->addressBook()->orderBy('name')->get();
        return response()->json($entries);
    }

    /**
     * POST /customer/address-book — Save a new address to the book.
     */
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

    /**
     * DELETE /customer/address-book/{id} — Remove an address book entry.
     */
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

    /**
     * GET /customer/carriers?country=AE — return carriers that serve this country.
     */
    public function getCarriers(Request $request)
    {
        $countryCode = $this->resolveCountryCode($request->get('country_code', $request->get('country', '')));
        $weight = max((float) $request->get('weight', 1.0), 0.5);
        $shipmentType = $request->get('type', 'non_document');

        $quotes = $this->quoteService->getQuotes($countryCode, $weight, $shipmentType);

        $carriers = collect($quotes['options'] ?? [])
            ->map(function (array $option) {
                $carrier = Carrier::find($option['carrier_id'] ?? null);

                if (!$carrier) {
                    return null;
                }

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

    /**
     * Normalize a country input to a 2-letter ISO code.
     */
    private function resolveCountryCode(string $value): string
    {
        $input = strtoupper(trim($value));

        if ($input === '') {
            return '';
        }

        if (strlen($input) === 2) {
            return $input;
        }

        $aliases = [
            'UAE' => 'AE',
            'UNITED ARAB EMIRATES' => 'AE',
            'USA' => 'US',
            'UNITED STATES' => 'US',
            'UNITED STATES OF AMERICA' => 'US',
            'KOREA (SOUTH)' => 'KR',
            'SOUTH KOREA' => 'KR',
            'VIET NAM' => 'VN',
        ];

        return $aliases[$input] ?? $input;
    }

    /**
     * Normalize address book input before storing or comparing it.
     */
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

    /**
     * Compare two address book entries after normalizing whitespace and case.
     */
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

    private function normalizeAddressBookValue(mixed $value): string
    {
        $value = trim((string) ($value ?? ''));

        return mb_strtolower(preg_replace('/\s+/u', ' ', $value));
    }

    /**
     * POST /customer/shipments — Persist the new shipment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // Sender
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
            // Receiver
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
            // Content
            'shipment_type'          => 'required|in:document,non_document',
            'document_description'   => 'nullable|string|max:500',
            'items'                  => 'nullable|array',
            'items.*.name'           => 'exclude_if:shipment_type,document|required_if:shipment_type,non_document|string|max:191',
            'items.*.quantity'       => 'exclude_if:shipment_type,document|required_if:shipment_type,non_document|integer|min:1',
            'items.*.value_per_item' => 'exclude_if:shipment_type,document|required_if:shipment_type,non_document|numeric|min:0',
            // Packages
            'packages'               => 'required|array|min:1',
            'packages.*.weight'      => 'required|numeric|min:0.01',
            'packages.*.quantity'    => 'required|integer|min:1',
            // Carrier
            'carrier_id'             => 'required|exists:carriers,id',
        ]);

        // Calculate totals
        $totalPackages = 0;
        $totalWeight   = 0;
        foreach ($data['packages'] as $pkg) {
            $totalPackages += (int) $pkg['quantity'];
            $totalWeight   += (float) $pkg['weight'] * (int) $pkg['quantity'];
        }

        $carrier = Carrier::findOrFail($data['carrier_id']);

        $user = auth()->user();
        $isAgent = $user?->isAgent() ?? false;

        $shipmentData = array_merge($data, [
            'tracking_number'     => $this->generateTrackingNumber(),
            'awb_number'          => Shipment::generateAwbNumber(),
            'invoice_number'      => 'INV-' . strtoupper(Str::random(6)) . '-' . now()->format('Ymd'),
            'total_packages'      => $totalPackages,
            'total_weight'        => $totalWeight,
            'weight'              => $totalWeight, // legacy field
            'carrier_name'        => $carrier->name,
            'status'              => Shipment::STATUS_PENDING,
            'agent_id'            => $isAgent ? $user?->id : null,
            'created_by_agent_id' => $isAgent ? $user?->id : null,
            'sender_id'           => $user?->id,
        ]);

        $shipment = Shipment::create($shipmentData);

        $shipment->trackingEvents()->create([
            'location'    => $shipmentData['sender_city'] . ', ' . $shipmentData['sender_country'],
            'status'      => Shipment::STATUS_PENDING,
            'notes'       => 'Shipment created by customer.',
            'occurred_at' => now(),
        ]);

        $isAdmin = $user?->isAdmin() ?? false;

        // Only admins should get a waybill URL. Agents and customers receive no waybill link.
        $waybillUrl = $isAdmin
            ? route('admin.shipments.waybill', $shipment->id)
            : null;

        // Invoice links: admin -> admin invoice, agent -> agent invoice, customer/guest -> customer invoice
        if ($isAdmin) {
            $invoiceUrl = route('admin.shipments.invoice', $shipment->id);
        } elseif ($isAgent) {
            $invoiceUrl = route('agent.shipments.invoice', $shipment->id);
        } else {
            $invoiceUrl = route('customer.shipments.invoice', $shipment->id);
        }

        return response()->json([
            'success'    => true,
            'shipment'   => $shipment,
            'waybill_url' => $waybillUrl,
            'invoice_url' => $invoiceUrl,
        ]);
    }

    /**
     * Generate unique EP tracking number.
     */
    private function generateTrackingNumber(): string
    {
        do {
            $number = 'EP' . strtoupper(Str::random(2)) . now()->format('ymd') . rand(1000, 9999);
        } while (Shipment::where('tracking_number', $number)->exists());
        return $number;
    }

}
