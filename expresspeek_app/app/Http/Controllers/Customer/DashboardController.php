<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private ShipmentService $shipmentService) {}

    public function index(Request $request)
    {
        $user      = auth()->user();
        $shipments = $this->shipmentService->getShipmentsForUser($user);
        $trackingNumber = trim((string) $request->query('tracking', ''));
        $trackedShipment = null;
        $trackingError = null;
        $trackingLookupUnavailable = false;

        if ($trackingNumber !== '') {
            try {
                $trackedShipment = $this->shipmentService->findByTrackingNumber($trackingNumber);
            } catch (\Throwable $exception) {
                report($exception);
                $trackingLookupUnavailable = true;
                $trackingError = 'Tracking is temporarily unavailable. Please try again in a few moments.';
            }

            if (!$trackingLookupUnavailable && !$trackedShipment) {
                $trackingError = "We couldn't find a shipment matching {$trackingNumber}. Please check the number and try again.";
            }
        }

        $stats = [
            'total'          => $shipments->count(),
            'order_requests' => \App\Models\SourcingRequest::where('user_id', $user->id)->count(),
            'pending'        => $shipments->where('status', 'pending')->count(),
            'in_transit'     => $shipments->where('status', 'in_transit')->count(),
            'delivered'  => $shipments->whereIn('status', [
                \App\Models\Shipment::STATUS_CONNECTED_TO_CARRIER,
                \App\Models\Shipment::STATUS_DELIVERED,
            ])->count(),
            'exceptions' => $shipments->whereIn('status', ['failed', 'returned'])->count(),
        ];

        $recentShipments = $shipments->take(5);

        $activity = collect(range(6, 0))->map(function (int $daysAgo) use ($shipments) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('M j'),
                'value' => $shipments->filter(fn ($shipment) => $shipment->created_at?->isSameDay($date))->count(),
            ];
        });

        $countries = \App\Models\CountryZone::query()
            ->select('country_code', DB::raw('MAX(country_name) as original_name'))
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->get()
            ->map(function ($c) {
                return (object) [
                    'country_code' => $c->country_code,
                    'country_name' => \App\Helpers\CountryHelper::getCanonicalName($c->country_code)
                ];
            })
            ->filter(fn ($c) => !empty($c->country_name))
            ->sortBy('country_name')
            ->values();

        return view('customer.dashboard', compact(
            'stats',
            'recentShipments',
            'countries',
            'activity',
            'trackingNumber',
            'trackedShipment',
            'trackingError',
            'trackingLookupUnavailable',
        ));
    }

    public function track(Request $request)
    {
        $trackingNumber = trim((string) $request->query('tracking', ''));

        $target = route('customer.dashboard', $trackingNumber !== '' ? ['tracking' => $trackingNumber] : []);

        return redirect($target . '#track');
    }
}
