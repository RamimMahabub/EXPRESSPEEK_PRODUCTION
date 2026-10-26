<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\SourcingRequest;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Support\Collection;

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
            'delivered'   => Shipment::whereIn('status', [
                Shipment::STATUS_CONNECTED_TO_CARRIER,
                Shipment::STATUS_DELIVERED,
            ])->count(),
            'failed'      => Shipment::where('status', Shipment::STATUS_FAILED)->count(),
        ];
    }

    /**
     * Generate a unique tracking number.
     */
    private function generateTrackingNumber(): string
    {
        return Shipment::generateTrackingNumber();
    }

    /**
     * Find shipment by tracking number (public).
     */
    public function findByTrackingNumber(string $trackingNumber): ?Shipment
    {
        $numericTrackingNumber = Shipment::normalizeTrackingNumber($trackingNumber);

        $shipment = Shipment::with(['trackingEvents', 'sender', 'agent'])
            ->where(function ($query) use ($trackingNumber, $numericTrackingNumber) {
                $query->where('tracking_number', $trackingNumber);

                if ($numericTrackingNumber !== '') {
                    $query->orWhere('tracking_number', $numericTrackingNumber)
                        ->orWhere('tracking_number', 'like', '%' . $numericTrackingNumber);
                }
            })
            ->orWhere('carrier_tracking_number', $trackingNumber)
            ->first();

        if ($shipment) {
            return $shipment;
        }

        $sourcing = \App\Models\SourcingRequest::with('items', 'carrier')
            ->where(function ($query) use ($trackingNumber, $numericTrackingNumber) {
                $query->where('tracking_number', $trackingNumber)
                      ->orWhere('reference_number', $trackingNumber);

                if ($numericTrackingNumber !== '') {
                    $query->orWhere('tracking_number', $numericTrackingNumber)
                        ->orWhere('tracking_number', 'like', '%' . $numericTrackingNumber)
                        ->orWhere('reference_number', $numericTrackingNumber)
                        ->orWhere('reference_number', 'like', '%' . $numericTrackingNumber);
                }
            })
            ->first();
        if ($sourcing) {
            $mockShipment = new Shipment([
                'tracking_number' => $sourcing->tracking_number,
                'status' => in_array($sourcing->status, ['completed', 'shipped']) ? 'in_transit' : 'pending',
                'carrier_tracking_number' => $sourcing->carrier_tracking_number,
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
        $recordType = in_array($filters['record_type'] ?? 'shipment', ['shipment', 'sourcing', 'all'], true)
            ? ($filters['record_type'] ?? 'shipment')
            : 'shipment';
        $grouping = in_array($filters['grouping'] ?? 'daily', ['daily', 'weekly', 'monthly'], true)
            ? ($filters['grouping'] ?? 'daily')
            : 'daily';

        $records = collect();

        if (in_array($recordType, ['shipment', 'all'], true)) {
            $shipmentQuery = Shipment::query()->with(['carrier:id,name', 'agent:id,name']);
            $this->applyAnalyticsFilters($shipmentQuery, $filters, 'shipment');

            $records = $records->concat($shipmentQuery->get()->map(function (Shipment $shipment) {
                return [
                    'date' => $shipment->created_at,
                    'status' => $this->normaliseAnalyticsStatus($shipment->status, 'shipment'),
                    'carrier' => $shipment->carrier?->name ?: ($shipment->carrier_name ?: 'Unassigned'),
                    'destination' => $this->formatDestination($shipment->receiver_city, $shipment->receiver_country),
                    'agent' => $shipment->agent?->name ?: 'Unassigned',
                    'source' => 'Standard shipment',
                ];
            }));
        }

        if (in_array($recordType, ['sourcing', 'all'], true)) {
            $sourcingQuery = SourcingRequest::query()->with('carrier:id,name');
            $this->applyAnalyticsFilters($sourcingQuery, $filters, 'sourcing');

            $records = $records->concat($sourcingQuery->get()->map(function (SourcingRequest $request) {
                return [
                    'date' => $request->created_at,
                    'status' => $this->normaliseAnalyticsStatus($request->status, 'sourcing'),
                    'carrier' => $request->carrier?->name ?: ($request->carrier_name ?: 'Unassigned'),
                    'destination' => $this->formatDestination($request->destination_city, $request->destination_country),
                    'agent' => 'Sourcing desk',
                    'source' => 'Sourcing request',
                ];
            }));
        }

        $total = $records->count();
        $pending = $records->where('status', 'pending')->count();
        $inTransit = $records->where('status', 'in_transit')->count();
        $delivered = $records->where('status', 'delivered')->count();
        $returned = $records->where('status', 'returned')->count();

        $completed = $delivered + $returned;
        $successRate = $completed > 0 ? round(($delivered / $completed) * 100, 1) : 0;

        $volumeTrend = $this->countAnalyticsValues($records, fn (array $record) => $this->analyticsDateKey($record['date'], $grouping));
        ksort($volumeTrend);
        $statusDistribution = $this->countAnalyticsValues($records, fn (array $record) => $record['status']);
        $carrierVolume = $this->countAnalyticsValues($records, fn (array $record) => $record['carrier']);
        $topDestinations = $this->countAnalyticsValues($records, fn (array $record) => $record['destination'], 8);
        $agentPerformance = $this->countAnalyticsValues($records, fn (array $record) => $record['agent'], 8);
        $sourceDistribution = $this->countAnalyticsValues($records, fn (array $record) => $record['source']);

        $outcomeTrend = [];
        foreach ($records as $record) {
            $date = $this->analyticsDateKey($record['date'], $grouping);
            $outcomeTrend[$date] ??= [
                'delivered' => 0,
                'in_transit' => 0,
                'returned' => 0,
                'pending' => 0,
            ];
            $outcomeTrend[$date][$record['status']]++;
        }
        ksort($outcomeTrend);

        return [
            'summary' => [
                'total' => $total,
                'pending' => $pending,
                'inTransit' => $inTransit,
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
            'sourceDistribution' => $sourceDistribution,
            'meta' => [
                'recordType' => $recordType,
                'grouping' => $grouping,
                'generatedAt' => now()->toIso8601String(),
            ],
        ];
    }

    private function applyAnalyticsFilters($query, array $filters, string $type): void
    {
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (!empty($filters['carriers'])) {
            $query->whereIn('carrier_id', (array) $filters['carriers']);
        }
        if (!empty($filters['statuses'])) {
            $query->whereIn('status', (array) $filters['statuses']);
        }

        if ($type === 'shipment' && !empty($filters['agents'])) {
            $query->whereIn('agent_id', (array) $filters['agents']);
        } elseif ($type === 'sourcing' && !empty($filters['agents'])) {
            // Sourcing requests are handled by the shared sourcing desk and have no agent_id.
            $query->whereRaw('1 = 0');
        }
    }

    private function normaliseAnalyticsStatus(string $status, string $type): string
    {
        if ($type === 'sourcing') {
            return match ($status) {
                SourcingRequest::STATUS_SHIPPED => 'in_transit',
                SourcingRequest::STATUS_COMPLETED => 'delivered',
                SourcingRequest::STATUS_CANCELLED => 'returned',
                default => 'pending',
            };
        }

        return match ($status) {
            Shipment::STATUS_PICKED_UP,
            Shipment::STATUS_IN_TRANSIT,
            Shipment::STATUS_OUT_FOR_DELIVERY => 'in_transit',
            Shipment::STATUS_CONNECTED_TO_CARRIER,
            Shipment::STATUS_DELIVERED => 'delivered',
            Shipment::STATUS_FAILED,
            Shipment::STATUS_RETURNED => 'returned',
            default => 'pending',
        };
    }

    private function analyticsDateKey($date, string $grouping): string
    {
        if ($grouping === 'monthly') {
            return $date->format('Y-m');
        }

        if ($grouping === 'weekly') {
            return $date->format('o-\\WW');
        }

        return $date->format('Y-m-d');
    }

    private function formatDestination(?string $city, ?string $country): string
    {
        $parts = array_values(array_filter([trim((string) $city), trim((string) $country)]));

        return $parts ? implode(', ', array_unique($parts)) : 'Unknown destination';
    }

    private function countAnalyticsValues(Collection $records, callable $keyResolver, ?int $limit = null): array
    {
        $counts = [];
        foreach ($records as $record) {
            $key = (string) $keyResolver($record);
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        arsort($counts);
        if ($limit !== null) {
            $counts = array_slice($counts, 0, $limit, true);
        }

        return $counts;
    }
}
