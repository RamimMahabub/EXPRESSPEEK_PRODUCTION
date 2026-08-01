<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShipmentService;
use App\Services\UserService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private ShipmentService $shipmentService,
        private UserService $userService
    ) {}

    public function index(Request $request)
    {
        $shipmentStats = $this->shipmentService->getPlatformStats();
        $userStats     = $this->userService->getUserStats();
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

        $recentShipments = \App\Models\Shipment::with(['sender', 'agent'])
            ->latest()
            ->take(8)
            ->get();

        $newSourcingCount   = \App\Models\SourcingRequest::where('status', \App\Models\SourcingRequest::STATUS_NEW)->count();
        $totalSourcingCount = \App\Models\SourcingRequest::count();

        $activity = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('M j'),
                'value' => \App\Models\Shipment::whereDate('created_at', $date)->count(),
            ];
        });

        return view('admin.dashboard', compact(
            'shipmentStats',
            'userStats',
            'recentShipments',
            'newSourcingCount',
            'totalSourcingCount',
            'activity',
            'trackingNumber',
            'trackedShipment',
            'trackingError',
            'trackingLookupUnavailable',
        ));
    }

    public function analyticsData(Request $request)
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'carriers',
            'agents',
            'statuses',
            'grouping',
            'record_type',
        ]);
        
        $data = $this->shipmentService->getAnalytics($filters);
        
        return response()->json($data);
    }
}
