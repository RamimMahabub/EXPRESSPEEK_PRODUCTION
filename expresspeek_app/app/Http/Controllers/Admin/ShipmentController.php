<?php

namespace App\Http\Controllers\Admin;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Shipment;
use Illuminate\Http\Request;
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
            'items'                  => 'nullable|array',
            'items.*.name'           => 'exclude_if:shipment_type,document|required_if:shipment_type,non_document|string|max:191',
            'items.*.quantity'       => 'exclude_if:shipment_type,document|required_if:shipment_type,non_document|integer|min:1',
            'items.*.value_per_item' => 'exclude_if:shipment_type,document|required_if:shipment_type,non_document|numeric|min:0',
            'packages'               => 'required|array|min:1',
            'packages.*.weight'      => 'required|numeric|min:0.01',
            'packages.*.quantity'    => 'required|integer|min:1',
            'carrier_id'             => 'required|exists:carriers,id',
        ]);

        $totalPackages = 0;
        $totalWeight   = 0;
        foreach ($data['packages'] as $pkg) {
            $totalPackages += (int) $pkg['quantity'];
            $totalWeight   += (float) $pkg['weight'] * (int) $pkg['quantity'];
        }

        $carrier = Carrier::findOrFail($data['carrier_id']);
        $user = auth()->user();

        $shipmentData = array_merge($data, [
            'tracking_number'     => $this->generateTrackingNumber(),
            'awb_number'          => Shipment::generateAwbNumber(),
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
            'notes'       => 'Shipment created by admin.',
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
        do {
            $number = 'EP' . strtoupper(\Illuminate\Support\Str::random(2)) . now()->format('ymd') . rand(1000, 9999);
        } while (Shipment::where('tracking_number', $number)->exists());
        return $number;
    }
    public function index()
    {
        $shipments = Shipment::with(['sender', 'agent'])
            ->latest()
            ->paginate(20);

        return view('admin.shipments.index', compact('shipments'));
    }

    public function edit(Shipment $shipment)
    {
        $carriers = Carrier::orderBy('name')->get(['id', 'name', 'currency']);

        return view('admin.shipments.edit', compact('shipment', 'carriers'));
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
            'packages.*.quantity'   => 'required_with:packages|integer|min:1',
        ]);

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
            $packages = collect($request->input('packages'))
                ->filter(fn ($row) => is_array($row) && ((float) ($row['weight'] ?? 0)) > 0)
                ->map(function (array $row) {
                    return [
                        'weight' => max((float) ($row['weight'] ?? 0), 0.01),
                        'quantity' => max((int) ($row['quantity'] ?? 1), 1),
                    ];
                })
                ->values()
                ->all();
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

        return redirect()
            ->route('admin.shipments.edit', $shipment)
            ->with('success', 'Shipment updated successfully.');
    }

    public function printWaybill(Shipment $shipment)
    {
        $qrPayload = implode('|', [
            'tracking:' . ($shipment->tracking_number ?? ''),
            'awb:' . ($shipment->awb_number ?? ''),
            'to:' . ($shipment->receiver_name ?? ''),
            'country:' . ($shipment->receiver_country_code ?? $shipment->receiver_country ?? ''),
            'pieces:' . (string) ($shipment->total_packages ?? 0),
            'weight:' . (string) ($shipment->total_weight ?? 0),
        ]);

        $qrSvg = $this->generateQrSvg($qrPayload);

        $pdf = app('dompdf.wrapper')
            ->setPaper([0, 0, 288, 432], 'portrait')
            ->loadView('agent.shipments.waybill', compact('shipment', 'qrSvg'));

        return $pdf->stream('waybill-' . $shipment->awb_number . '.pdf');
    }

    public function printInvoice(Shipment $shipment)
    {
        $pdf = app('dompdf.wrapper')->loadView('agent.shipments.invoice', compact('shipment'));
        return $pdf->stream('invoice-' . $shipment->invoice_number . '.pdf');
    }

    private function generateQrSvg(string $text): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(140),
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
