<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Shipment;

class ShipmentAssignedNotification extends Notification
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
        return [
            'title' => 'New Shipment Assigned',
            'message' => 'You have been assigned to handle shipment: ' . $this->shipment->tracking_number,
            'action_url' => route('agent.shipments.show', $this->shipment->id),
            'icon' => 'user-check',
            'color' => 'blue'
        ];
    }
}
