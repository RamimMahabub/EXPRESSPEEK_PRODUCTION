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
        return Shipment::with(['trackingEvents', 'sender', 'agent'])
            ->where('tracking_number', $trackingNumber)
            ->orWhere('carrier_tracking_number', $trackingNumber)
            ->first();
    }
}
