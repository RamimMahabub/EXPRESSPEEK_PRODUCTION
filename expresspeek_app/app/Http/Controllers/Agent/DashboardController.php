<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\ShipmentService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private ShipmentService $shipmentService) {}

    public function index(Request $request)
    {
        $agent     = auth()->user();
        $shipments = $this->shipmentService->getShipmentsForAgent($agent);
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
            'total'            => $shipments->count(),
            'pending'          => $shipments->where('status', 'pending')->count(),
            'in_transit'       => $shipments->where('status', 'in_transit')->count(),
            'out_for_delivery' => $shipments->where('status', 'out_for_delivery')->count(),
            'delivered_today'  => $shipments
                ->whereIn('status', [
                    \App\Models\Shipment::STATUS_CONNECTED_TO_CARRIER,
                    \App\Models\Shipment::STATUS_DELIVERED,
                ])
                ->filter(fn ($s) => $s->updated_at->isToday())
                ->count(),
        ];

        $activity = collect(range(6, 0))->map(function (int $daysAgo) use ($shipments) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('M j'),
                'value' => $shipments->filter(fn ($shipment) => $shipment->created_at?->isSameDay($date))->count(),
            ];
        });

        return view('agent.dashboard', compact(
            'stats',
            'shipments',
            'activity',
            'trackingNumber',
            'trackedShipment',
            'trackingError',
            'trackingLookupUnavailable',
        ));
    }
}
