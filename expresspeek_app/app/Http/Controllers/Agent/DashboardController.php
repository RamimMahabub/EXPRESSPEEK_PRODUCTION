<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\ShipmentService;

class DashboardController extends Controller
{
    public function __construct(private ShipmentService $shipmentService) {}

    public function index()
    {
        $agent     = auth()->user();
        $shipments = $this->shipmentService->getShipmentsForAgent($agent);

        $stats = [
            'total'            => $shipments->count(),
            'pending'          => $shipments->where('status', 'pending')->count(),
            'in_transit'       => $shipments->where('status', 'in_transit')->count(),
            'out_for_delivery' => $shipments->where('status', 'out_for_delivery')->count(),
            'delivered_today'  => $shipments
                ->where('status', 'delivered')
                ->filter(fn ($s) => $s->updated_at->isToday())
                ->count(),
        ];

        return view('agent.dashboard', compact('stats', 'shipments'));
    }
}
