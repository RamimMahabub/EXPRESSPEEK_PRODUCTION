<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Shipment;
use App\Models\User;
use App\Services\QuoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class PublicShipmentController extends Controller
{
    public function __construct(private QuoteService $quoteService)
    {
    }

    public function create(Request $request)
    {
        $selectedCarrierId = (int) $request->query('carrier_id', 0);
        $destinationCountry = strtoupper(trim((string) $request->query('country', '')));
        $shipmentType = (string) $request->query('type', 'non_document');
        $weight = (float) $request->query('weight', 1);

        $next = (string) $request->query('next', '');
        if ($next !== '' && ($destinationCountry === '' || $selectedCarrierId === 0)) {
            $parts = parse_url($next);
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $nextParams);
                $selectedCarrierId = $selectedCarrierId ?: (int) ($nextParams['carrier_id'] ?? 0);
                $destinationCountry = $destinationCountry ?: strtoupper(trim((string) ($nextParams['country'] ?? '')));
                $shipmentType = $shipmentType ?: (string) ($nextParams['type'] ?? 'non_document');
                $weight = $weight ?: (float) ($nextParams['weight'] ?? 1);
            }
        }

        $quotes = null;
        if ($destinationCountry !== '') {
            $quotes = $this->quoteService->getQuotes($destinationCountry, max($weight, 0.5), $shipmentType);
        }

        $selectedCarrier = $selectedCarrierId > 0
            ? Carrier::query()->find($selectedCarrierId)
            : null;

        $senderName = auth()->check() ? auth()->user()->name : (string) old('sender_name', '');

        return view('agent.shipments.create', compact(
            'selectedCarrier',
            'selectedCarrierId',
            'destinationCountry',
            'shipmentType',
            'weight',
            'quotes',
            'senderName'
        ) + ['guestMode' => true]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
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

            'shipment_type'          => 'required|in:document,non_document',
            'document_description'   => 'nullable|string|max:500',
            'items'                  => 'nullable|array',
            'items.*.name'           => 'required_if:shipment_type,non_document|string|max:191',
            'items.*.quantity'       => 'required_if:shipment_type,non_document|integer|min:1',
            'items.*.value_per_item' => 'required_if:shipment_type,non_document|numeric|min:0',
            'packages'               => 'required|array|min:1',
            'packages.*.weight'      => 'required|numeric|min:0.01',
            'packages.*.quantity'    => 'required|integer|min:1',
            'carrier_id'             => 'required|exists:carriers,id',
        ]);

        $totalPackages = 0;
        $totalWeight = 0.0;

        foreach ($data['packages'] as $package) {
            $totalPackages += (int) $package['quantity'];
            $totalWeight += (float) $package['weight'] * (int) $package['quantity'];
        }

        $items = [];
        if ($data['shipment_type'] === 'non_document' && !empty($data['items']) && is_array($data['items'])) {
            $items = collect($data['items'])
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

        $carrier = Carrier::query()->findOrFail($data['carrier_id']);
        $user = auth()->user();
        $senderUser = $user ?: $this->resolveGuestSenderUser($data);

        $shipment = Shipment::create([
            'tracking_number'     => $this->generateTrackingNumber(),
            'awb_number'          => Shipment::generateAwbNumber(),
            'invoice_number'      => 'INV-' . strtoupper(Str::random(6)) . '-' . now()->format('Ymd'),
            'sender_id'           => $senderUser->id,
            'sender_name'         => $data['sender_name'],
            'sender_company'      => $data['sender_company'] ?? null,
            'sender_is_business'  => (bool) ($data['sender_is_business'] ?? false),
            'sender_country_code' => $data['sender_country_code'] ?? null,
            'sender_country'      => $data['sender_country'],
            'sender_address'      => $data['sender_address'],
            'sender_address2'     => $data['sender_address2'] ?? null,
            'sender_address3'     => $data['sender_address3'] ?? null,
            'sender_postal_code'  => $data['sender_postal_code'] ?? null,
            'sender_city'         => $data['sender_city'],
            'sender_state'        => $data['sender_state'] ?? null,
            'sender_email'        => $data['sender_email'] ?? null,
            'sender_phone_type'   => $data['sender_phone_type'] ?? null,
            'sender_phone_code'   => $data['sender_phone_code'] ?? null,
            'sender_phone'        => $data['sender_phone'] ?? null,

            'receiver_name'         => $data['receiver_name'],
            'receiver_company'      => $data['receiver_company'] ?? null,
            'receiver_is_business'  => (bool) ($data['receiver_is_business'] ?? false),
            'receiver_country_code' => $data['receiver_country_code'] ?? null,
            'receiver_country'      => $data['receiver_country'],
            'receiver_address'      => $data['receiver_address'],
            'receiver_address2'     => $data['receiver_address2'] ?? null,
            'receiver_address3'     => $data['receiver_address3'] ?? null,
            'receiver_postal_code'  => $data['receiver_postal_code'] ?? null,
            'receiver_city'         => $data['receiver_city'],
            'receiver_state'        => $data['receiver_state'] ?? null,
            'receiver_email'        => $data['receiver_email'] ?? null,
            'receiver_phone_type'   => $data['receiver_phone_type'] ?? null,
            'receiver_phone_code'   => $data['receiver_phone_code'] ?? null,
            'receiver_phone'        => $data['receiver_phone'] ?? null,

            'shipment_type'         => $data['shipment_type'],
            'document_description'  => $data['document_description'] ?? null,
            'items'                 => $items,
            'packages'              => $data['packages'],
            'total_packages'        => $totalPackages,
            'total_weight'          => $totalWeight,
            'weight'                => $totalWeight,
            'carrier_id'            => $carrier->id,
            'carrier_name'          => $carrier->name,
            'status'                => Shipment::STATUS_PENDING,
            'agent_id'              => $user?->isAgent() ? $user->id : null,
            'created_by_agent_id'   => $user?->isAgent() ? $user->id : null,
            'estimated_delivery'    => null,
            'notes'                 => null,
        ]);

        $shipment->trackingEvents()->create([
            'location'    => $shipment->sender_city . ', ' . $shipment->sender_country,
            'status'      => Shipment::STATUS_PENDING,
            'notes'       => $user?->isAgent()
                ? 'Shipment created by agent.'
                : ($user?->isCustomer() ? 'Shipment created by customer.' : 'Shipment created by guest.'),
            'occurred_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'shipment' => $shipment,
            'waybill_url' => null,
            'invoice_url' => URL::signedRoute('shipment.invoice', ['shipment' => $shipment]),
            'message' => 'Shipment created successfully. Tracking number: ' . $shipment->tracking_number,
        ]);
    }

    public function printWaybill(Request $request, Shipment $shipment)
    {
        abort_unless($request->hasValidSignature(), 403);

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

    public function printInvoice(Request $request, Shipment $shipment)
    {
        abort_unless($request->hasValidSignature(), 403);

        $pdf = app('dompdf.wrapper')->loadView('agent.shipments.invoice', compact('shipment'));

        return $pdf->stream('invoice-' . $shipment->invoice_number . '.pdf');
    }

    private function generateTrackingNumber(): string
    {
        do {
            $number = 'EP' . strtoupper(Str::random(2)) . now()->format('ymd') . rand(1000, 9999);
        } while (Shipment::where('tracking_number', $number)->exists());

        return $number;
    }

    private function resolveGuestSenderUser(array $data): User
    {
        $email = strtolower(trim((string) ($data['sender_email'] ?? '')));
        $name = trim((string) ($data['sender_name'] ?? 'Guest Customer'));

        if ($email !== '') {
            $existing = User::query()->where('email', $email)->first();

            if ($existing) {
                return $existing;
            }
        }

        if ($email === '') {
            $slug = Str::slug($name) ?: 'guest-customer';
            $email = $slug . '+' . Str::lower(Str::random(8)) . '@expresspeek.local';
        }

        $guest = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(32)),
        ]);

        if (method_exists($guest, 'assignRole')) {
            $guest->assignRole('customer');
        }

        return $guest;
    }

    private function generateQrSvg(string $text): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(140),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString($text);
    }
}