<?php

namespace App\Http\Controllers;

use App\Services\ShipmentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct(private ShipmentService $shipmentService)
    {
    }

    public function index()
    {
        $stats = ['total' => 0, 'pending' => 0, 'in_transit' => 0, 'delivered' => 0];
        $recentShipments = collect();

        if (auth()->check() && auth()->user()->isCustomer()) {
            $shipments = $this->shipmentService->getShipmentsForUser(auth()->user());
            $recentShipments = $shipments->take(5);
            $stats = [
                'total' => $shipments->count(),
                'pending' => $shipments->where('status', 'pending')->count(),
                'in_transit' => $shipments->where('status', 'in_transit')->count(),
                'delivered' => $shipments->where('status', 'delivered')->count(),
            ];
        }

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

        return view('home', compact('stats', 'recentShipments', 'countries'));
    }

    public function track(\Illuminate\Http\Request $request)
    {
        $trackingNumber = $request->query('tracking');
        $shipment = null;
        $error = null;

        if ($trackingNumber) {
            $shipment = $this->shipmentService->findByTrackingNumber(trim($trackingNumber));

            if ($shipment) {
                // If admin has assigned an actual carrier tracking number,
                // redirect straight to the carrier's live tracking page.
                if (!empty($shipment->carrier_tracking_number)) {
                    return redirect()->away($shipment->carrier_tracking_url);
                } else {
                    $error = "Tracking number {$trackingNumber} is registered, but carrier tracking has not been assigned yet.";
                    return redirect()->route('home')->with('error', $error);
                }
            } else {
                $error = "No shipment found for tracking number: {$trackingNumber}";
                return redirect()->route('home')->with('error', $error);
            }
        }

        return redirect()->route('home');
    }
}
