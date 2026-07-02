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

    public function index()
    {
        $shipmentStats = $this->shipmentService->getPlatformStats();
        $userStats     = $this->userService->getUserStats();

        $recentShipments = \App\Models\Shipment::with(['sender', 'agent'])
            ->latest()
            ->take(8)
            ->get();

        $newSourcingCount   = \App\Models\SourcingRequest::where('status', \App\Models\SourcingRequest::STATUS_NEW)->count();
        $totalSourcingCount = \App\Models\SourcingRequest::count();

        return view('admin.dashboard', compact('shipmentStats', 'userStats', 'recentShipments', 'newSourcingCount', 'totalSourcingCount'));
    }
}
