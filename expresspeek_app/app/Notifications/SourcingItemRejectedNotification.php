<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\SourcingRequestItem;

class SourcingItemRejectedNotification extends Notification
{
    use Queueable;

    public $item;

    public function __construct(SourcingRequestItem $item)
    {
        $this->item = $item;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Sourcing Item Rejected',
            'message' => 'An item in your sourcing request (' . $this->item->sourcingRequest->reference_number . ') was rejected. Reason: ' . str($this->item->rejection_reason)->limit(50),
            'action_url' => route('customer.sourcing-requests.index'),
            'icon' => 'x-circle',
            'color' => 'red'
        ];
    }
}
