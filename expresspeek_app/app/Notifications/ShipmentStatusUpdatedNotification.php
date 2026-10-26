<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Shipment;

class ShipmentStatusUpdatedNotification extends Notification
{
    use Queueable;

    public $shipment;

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $isAgent = $notifiable->isAgent();

        return [
            'title' => 'Shipment Update',
            'message' => 'Shipment ' . $this->shipment->tracking_number . ' is now ' . $this->shipment->status_label . '.',
            'action_url' => $isAgent
                ? route('agent.shipments.edit', $this->shipment)
                : route('tracking.index') . '?tracking_number=' . $this->shipment->tracking_number,
            'icon' => 'truck',
            'color' => match ($this->shipment->status) {
                'failed', 'returned' => 'red',
                'delivered' => 'green',
                'in_transit', 'out_for_delivery' => 'blue',
                default => 'violet',
            },
        ];
    }
}
