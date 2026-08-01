<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private ShipmentService $shipmentService) {}

    public function index()
    {
        $user      = auth()->user();
        $shipments = $this->shipmentService->getShipmentsForUser($user);

        $stats = [
            'total'      => $shipments->count(),
            'pending'    => $shipments->where('status', 'pending')->count(),
            'in_transit' => $shipments->where('status', 'in_transit')->count(),
            'delivered'  => $shipments->where('status', 'delivered')->count(),
        ];

        $recentShipments = $shipments->take(5);

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

        return view('customer.dashboard', compact('stats', 'recentShipments', 'countries'));
    }

    public function track(Request $request)
    {
        $trackingNumber = $request->query('tracking');
        $shipment       = null;
        $error          = null;

        if ($trackingNumber) {
            $shipment = $this->shipmentService->findByTrackingNumber(trim($trackingNumber));
            if (!$shipment) {
                $error = "No shipment found for tracking number: {$trackingNumber}";
            }
        }

        return view('customer.tracking', compact('shipment', 'trackingNumber', 'error'));
    }
}
