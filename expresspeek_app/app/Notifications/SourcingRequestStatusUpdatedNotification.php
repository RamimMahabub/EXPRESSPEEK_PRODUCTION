<?php

namespace App\Notifications;

use App\Models\SourcingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SourcingRequestStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public SourcingRequest $sourcingRequest)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Sourcing Request Updated',
            'message' => $this->sourcingRequest->reference_number . ' is now ' . $this->sourcingRequest->status_label . '.',
            'action_url' => $notifiable->isAgent()
                ? route('agent.shipments.index')
                : route('customer.sourcing-requests.show', $this->sourcingRequest),
            'icon' => 'package',
            'color' => match ($this->sourcingRequest->status_color) {
                'red' => 'red',
                'emerald' => 'green',
                'amber', 'orange' => 'orange',
                default => 'violet',
            },
        ];
    }
}
