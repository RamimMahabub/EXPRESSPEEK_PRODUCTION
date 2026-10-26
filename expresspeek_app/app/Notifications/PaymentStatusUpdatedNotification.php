<?php

namespace App\Notifications;

use App\Models\SourcingInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public SourcingInvoice $invoice)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $isAdmin = $notifiable->isAdmin();
        $status = ucfirst($this->invoice->status);

        return [
            'title' => 'Payment ' . $status,
            'message' => 'Payment for ' . $this->invoice->invoice_number . ' is ' . $this->invoice->status . '.',
            'action_url' => $isAdmin
                ? route('admin.sourcing-invoices.index')
                : route('customer.sourcing-invoices.show', $this->invoice),
            'icon' => 'credit-card',
            'color' => $this->invoice->status === 'paid' ? 'green' : 'orange',
        ];
    }
}
