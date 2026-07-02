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

    public function quote()
    {
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

        $carrierCount = \App\Models\Carrier::count();
        $countryCount = $countries->count();

        return view('quote', compact('countries', 'carrierCount', 'countryCount'));
    }

    public function track(\Illuminate\Http\Request $request)
    {
        $trackingNumber = trim((string) $request->query('tracking', ''));
        $shipment = null;
        $error = null;
        $lookupUnavailable = false;

        if ($trackingNumber !== '') {
            try {
                $shipment = $this->shipmentService->findByTrackingNumber($trackingNumber);
            } catch (\Throwable $exception) {
                report($exception);
                $lookupUnavailable = true;
                $error = 'Tracking is temporarily unavailable. Please try again in a few moments.';
            }

            if (!$lookupUnavailable && !$shipment) {
                $error = "We couldn't find a shipment matching {$trackingNumber}. Please check the number and try again.";
            }
        }

        return view('tracking', compact('shipment', 'trackingNumber', 'error', 'lookupUnavailable'));
    }
}
