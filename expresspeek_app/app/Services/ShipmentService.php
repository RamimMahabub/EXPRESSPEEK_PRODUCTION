<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ShipmentService
{
    /**
     * Create a new shipment with a unique tracking number.
     */
    public function createShipment(array $data): Shipment
    {
        $data['tracking_number'] = $this->generateTrackingNumber();
        $data['status'] = Shipment::STATUS_PENDING;

        $shipment = Shipment::create($data);

        // Log initial tracking event
        $shipment->trackingEvents()->create([
            'location'    => 'Origin Facility',
            'status'      => Shipment::STATUS_PENDING,
            'notes'       => 'Shipment created and pending pickup.',
            'occurred_at' => now(),
        ]);

        return $shipment;
    }

    /**
     * Update shipment status and log a tracking event.
     */
    public function updateStatus(Shipment $shipment, string $status, string $location = '', string $notes = ''): void
    {
        $shipment->update(['status' => $status]);

        $shipment->trackingEvents()->create([
            'location'    => $location ?: 'Facility',
            'status'      => $status,
            'notes'       => $notes ?: Shipment::statuses()[$status] ?? $status,
            'occurred_at' => now(),
        ]);
    }

    /**
     * Get all shipments for a specific user (sender).
     */
    public function getShipmentsForUser(User $user): Collection
    {
        return $user->shipments()
            ->with(['trackingEvents', 'agent'])
            ->latest()
            ->get();
    }

    /**
     * Get all shipments assigned to an agent.
     */
    public function getShipmentsForAgent(User $agent): Collection
    {
        return $agent->assignedShipments()
            ->with(['trackingEvents', 'sender'])
            ->latest()
            ->get();
    }

    /**
     * Get platform-wide statistics (admin).
     */
    public function getPlatformStats(): array
    {
        return [
            'total'       => Shipment::count(),
            'pending'     => Shipment::where('status', Shipment::STATUS_PENDING)->count(),
            'in_transit'  => Shipment::where('status', Shipment::STATUS_IN_TRANSIT)->count(),
            'delivered'   => Shipment::where('status', Shipment::STATUS_DELIVERED)->count(),
            'failed'      => Shipment::where('status', Shipment::STATUS_FAILED)->count(),
        ];
    }

    /**
     * Generate a unique tracking number.
     */
    private function generateTrackingNumber(): string
    {
        do {
            $number = 'EP' . strtoupper(Str::random(2)) . now()->format('ymd') . rand(1000, 9999);
        } while (Shipment::where('tracking_number', $number)->exists());

        return $number;
    }

    /**
     * Find shipment by tracking number (public).
     */
    public function findByTrackingNumber(string $trackingNumber): ?Shipment
    {
        $shipment = Shipment::with(['trackingEvents', 'sender', 'agent'])
            ->where('tracking_number', $trackingNumber)
            ->orWhere('carrier_tracking_number', $trackingNumber)
            ->first();

        if ($shipment) {
            return $shipment;
        }

        $sourcing = \App\Models\SourcingRequest::with('items', 'carrier')->where('tracking_number', $trackingNumber)->first();
        if ($sourcing) {
            $mockShipment = new Shipment([
                'tracking_number' => $sourcing->tracking_number,
                'status' => in_array($sourcing->status, ['completed', 'shipped']) ? 'in_transit' : 'pending',
                'carrier_tracking_number' => $sourcing->awb_number,
                'carrier_name' => $sourcing->carrier ? $sourcing->carrier->name : null,
                'receiver_country' => $sourcing->destination_country,
                'sender_country' => 'Bangladesh',
                'shipment_type' => 'Sourcing Request',
            ]);

            $event = new TrackingEvent([
                'status' => 'pending',
                'location' => 'Origin',
                'notes' => 'Sourcing request tracking started.',
                'occurred_at' => $sourcing->updated_at,
            ]);

            $mockShipment->setRelation('trackingEvents', collect([$event]));
            return $mockShipment;
        }

        return null;
    }

    /**
     * Get aggregated analytics data for dashboard.
     */
    public function getAnalytics(array $filters): array
    {
        $query = Shipment::query();

        if (!empty($filters['start_date'])) {
            $query->whereDate('shipments.created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('shipments.created_at', '<=', $filters['end_date']);
        }
        if (!empty($filters['carriers'])) {
            $query->whereIn('carrier_id', $filters['carriers']);
        }
        if (!empty($filters['agents'])) {
            $query->whereIn('agent_id', $filters['agents']);
        }
        if (!empty($filters['statuses'])) {
            $query->whereIn('status', $filters['statuses']);
        }

        $summaryQuery = clone $query;
        $total = $summaryQuery->count();
        $pending = (clone $query)->where('status', Shipment::STATUS_PENDING)->count();
        $delivered = (clone $query)->where('status', Shipment::STATUS_DELIVERED)->count();
        $returned = (clone $query)->where('status', Shipment::STATUS_RETURNED)->count();
        
        $resolved = $total - $pending;
        $successRate = $resolved > 0 ? round(($delivered / $resolved) * 100, 1) : 0;

        $grouping = $filters['grouping'] ?? 'daily';
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $dateExpr = $driver === 'sqlite' 
            ? "date(shipments.created_at)" 
            : "DATE(shipments.created_at)";

        if ($grouping === 'monthly') {
            $dateExpr = $driver === 'sqlite'
                ? "strftime('%Y-%m', shipments.created_at)"
                : "DATE_FORMAT(shipments.created_at, '%Y-%m')";
        } elseif ($grouping === 'weekly') {
            $dateExpr = $driver === 'sqlite'
                ? "strftime('%Y-%W', shipments.created_at)"
                : "DATE_FORMAT(shipments.created_at, '%x-%v')";
        }
        
        $concatExpr = ($driver === 'sqlite') 
            ? "receiver_city || ', ' || receiver_country" 
            : "CONCAT(receiver_city, ', ', receiver_country)";

        $volumeTrend = (clone $query)
            ->selectRaw("{$dateExpr} as date, COUNT(*) as count")
            ->whereNotNull('shipments.created_at')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $statusDistribution = (clone $query)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $carrierVolume = (clone $query)
            ->leftJoin('carriers', 'shipments.carrier_id', '=', 'carriers.id')
            ->selectRaw("COALESCE(carriers.name, shipments.carrier_name, 'Unknown') as carrier, COUNT(shipments.id) as count")
            ->groupBy('carrier')
            ->orderByDesc('count')
            ->pluck('count', 'carrier');

        $topDestinations = (clone $query)
            ->selectRaw("{$concatExpr} as destination, COUNT(*) as count")
            ->whereNotNull('receiver_city')
            ->where('receiver_city', '!=', '')
            ->groupBy('destination')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'destination');

        $agentPerformance = (clone $query)
            ->leftJoin('users', 'shipments.agent_id', '=', 'users.id')
            ->selectRaw("COALESCE(users.name, 'Unassigned') as agent, COUNT(shipments.id) as count")
            ->groupBy('agent')
            ->orderByDesc('count')
            ->pluck('count', 'agent');

        $outcomeTrendData = (clone $query)
            ->selectRaw("{$dateExpr} as date, status, COUNT(*) as count")
            ->whereIn('status', [Shipment::STATUS_DELIVERED, Shipment::STATUS_RETURNED, Shipment::STATUS_PENDING])
            ->whereNotNull('shipments.created_at')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();
            
        $outcomeTrend = [];
        foreach ($outcomeTrendData as $row) {
            if ($row->date === null) continue;
            if (!isset($outcomeTrend[$row->date])) {
                $outcomeTrend[$row->date] = [
                    Shipment::STATUS_DELIVERED => 0,
                    Shipment::STATUS_RETURNED => 0,
                    Shipment::STATUS_PENDING => 0,
                ];
            }
            $outcomeTrend[$row->date][$row->status] = $row->count;
        }

        return [
            'summary' => [
                'total' => $total,
                'pending' => $pending,
                'delivered' => $delivered,
                'returned' => $returned,
                'successRate' => $successRate,
            ],
            'volumeTrend' => $volumeTrend,
            'statusDistribution' => $statusDistribution,
            'carrierVolume' => $carrierVolume,
            'topDestinations' => $topDestinations,
            'agentPerformance' => $agentPerformance,
            'outcomeTrend' => $outcomeTrend,
        ];
    }
}
