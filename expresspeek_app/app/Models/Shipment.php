<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'invoice_number',
        'sender_id',
        // Sender details
        'sender_name',
        'sender_company',
        'sender_is_business',
        'sender_country_code',
        'sender_country',
        'sender_address',
        'sender_address2',
        'sender_address3',
        'sender_postal_code',
        'sender_city',
        'sender_state',
        'sender_email',
        'sender_phone_type',
        'sender_phone_code',
        'sender_phone',
        // Receiver details
        'receiver_name',
        'receiver_company',
        'receiver_is_business',
        'receiver_email',
        'receiver_phone',
        'receiver_phone_type',
        'receiver_phone_code',
        'receiver_address',
        'receiver_address2',
        'receiver_address3',
        'receiver_postal_code',
        'receiver_city',
        'receiver_state',
        'receiver_country',
        'receiver_country_code',
        // Shipment content
        'shipment_type',
        'document_description',
        'items',
        'packages',
        'total_packages',
        'total_weight',
        // Legacy
        'weight',
        'dimensions',
        'description',
        // Carrier
        'carrier_id',
        'carrier_name',
        'carrier_tracking_number',
        // Status
        'status',
        'agent_id',
        'created_by_agent_id',
        'estimated_delivery',
        'notes',
    ];

    /**
     * Keep the retired legacy identifier out of API and JSON responses.
     */
    protected $hidden = [
        'awb_number',
    ];

    protected function casts(): array
    {
        return [
            'estimated_delivery' => 'date',
            'items'              => 'array',
            'packages'           => 'array',
            'sender_is_business' => 'boolean',
            'receiver_is_business' => 'boolean',
        ];
    }

    /**
     * Present legacy tracking identifiers without their old letter prefix.
     */
    public function getTrackingNumberAttribute($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return self::normalizeTrackingNumber((string) $value);
    }

    public static function normalizeTrackingNumber(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    /**
     * Generate a unique numeric-only tracking number.
     */
    public static function generateTrackingNumber(): string
    {
        do {
            $number = now()->format('ymd') . random_int(1000, 9999);

            $shipmentExists = self::query()
                ->where('tracking_number', $number)
                ->orWhere('tracking_number', 'like', '%' . $number)
                ->exists();

            $sourcingExists = SourcingRequest::query()
                ->where('tracking_number', $number)
                ->orWhere('tracking_number', 'like', '%' . $number)
                ->exists();
        } while ($shipmentExists || $sourcingExists);

        return $number;
    }

    /**
     * Ensure items are always returned as a clean array
     */
    public function getItemsAttribute($value)
    {
        $items = is_string($value) ? json_decode($value, true) : (array) $value;
        // Convert object with numeric keys to indexed array
        if (is_array($items) && !empty($items)) {
            $keys = array_keys($items);
            if (array_reduce($keys, fn($c, $k) => $c && is_numeric($k), true)) {
                $items = array_values($items);
            }
        }
        return $items ?? [];
    }

    /**
     * Ensure packages are always returned as a clean array
     */
    public function getPackagesAttribute($value)
    {
        $packages = is_string($value) ? json_decode($value, true) : (array) $value;
        // Convert object with numeric keys to indexed array
        if (is_array($packages) && !empty($packages)) {
            $keys = array_keys($packages);
            if (array_reduce($keys, fn($c, $k) => $c && is_numeric($k), true)) {
                $packages = array_values($packages);
            }
        }
        return $packages ?? [];
    }

    // Status constants
    const STATUS_PENDING              = 'pending';
    const STATUS_PICKED_UP            = 'picked_up';
    const STATUS_CONNECTED_TO_CARRIER = 'connected_to_carrier';
    const STATUS_IN_TRANSIT           = 'in_transit';
    const STATUS_OUT_FOR_DELIVERY     = 'out_for_delivery';
    const STATUS_DELIVERED            = 'delivered';
    const STATUS_FAILED               = 'failed';
    const STATUS_RETURNED             = 'returned';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING           => 'Pending',
            self::STATUS_PICKED_UP         => 'Picked Up',
            self::STATUS_CONNECTED_TO_CARRIER => 'Connected to the carrier',
            self::STATUS_IN_TRANSIT        => 'In Transit',
            self::STATUS_OUT_FOR_DELIVERY  => 'Out for Delivery',
            self::STATUS_DELIVERED         => 'Delivered',
            self::STATUS_FAILED            => 'Failed Delivery',
            self::STATUS_RETURNED          => 'Returned',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING           => 'yellow',
            self::STATUS_PICKED_UP         => 'blue',
            self::STATUS_CONNECTED_TO_CARRIER => 'green',
            self::STATUS_IN_TRANSIT        => 'indigo',
            self::STATUS_OUT_FOR_DELIVERY  => 'purple',
            self::STATUS_DELIVERED         => 'green',
            self::STATUS_FAILED            => 'red',
            self::STATUS_RETURNED          => 'orange',
            default                        => 'gray',
        };
    }

    /**
     * Statuses displayed by the public progress bar.
     *
     * Connecting a shipment to its carrier completes the ExpressPeek-side
     * journey, so it intentionally has a shorter terminal progress path.
     */
    public function getProgressStepsAttribute(): array
    {
        if ($this->status === self::STATUS_CONNECTED_TO_CARRIER) {
            return [
                self::STATUS_PENDING => 'Booked',
                self::STATUS_PICKED_UP => 'Picked up',
                self::STATUS_CONNECTED_TO_CARRIER => 'Connected to the carrier',
            ];
        }

        return [
            self::STATUS_PENDING => 'Booked',
            self::STATUS_PICKED_UP => 'Picked up',
            self::STATUS_IN_TRANSIT => 'In transit',
            self::STATUS_OUT_FOR_DELIVERY => 'Out for delivery',
            self::STATUS_DELIVERED => 'Delivered',
        ];
    }

    public function isSuccessfulOutcome(): bool
    {
        return in_array($this->status, [
            self::STATUS_CONNECTED_TO_CARRIER,
            self::STATUS_DELIVERED,
        ], true);
    }

    public function getCreationNoteAttribute(): string
    {
        $sender = $this->relationLoaded('sender') ? $this->sender : $this->sender()->first();

        return match (true) {
            $sender?->isAdmin() => 'Shipment created by admin.',
            $sender?->isAgent() => 'Shipment created by agent.',
            $sender?->isCustomer() => 'Shipment created by customer.',
            default => 'Shipment created by guest.',
        };
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function createdByAgent()
    {
        return $this->belongsTo(User::class, 'created_by_agent_id');
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class)->orderByDesc('occurred_at');
    }

    public function invoices()
    {
        return $this->hasMany(ShipmentInvoice::class);
    }

    public function latestEvent()
    {
        return $this->hasOne(TrackingEvent::class)->latestOfMany('occurred_at');
    }

    public function getCarrierTrackingProviderNameAttribute(): string
    {
        if (empty($this->carrier_name)) {
            return 'DHL'; // Default
        }

        $name = strtolower($this->carrier_name);

        if (str_contains($name, 'fedex')) {
            return 'FedEx';
        }
        if (str_contains($name, 'ups')) {
            return 'UPS';
        }
        if (str_contains($name, 'aramex')) {
            return 'Aramex';
        }
        if (str_contains($name, 'ocs')) {
            return 'OCS';
        }
        if (str_contains($name, 'sf')) {
            return 'SF Express';
        }
        if (str_contains($name, 'tge')) {
            return 'Team Global Express';
        }

        return 'DHL'; // Fallback for DHL-Bangladesh, DUBAI-DHL, S-DHL, Singapore-DHL, etc.
    }

    public function getCarrierTrackingUrlAttribute(): string
    {
        if (empty($this->carrier_tracking_number)) {
            return '#';
        }

        $trackingNum = urlencode($this->carrier_tracking_number);
        $provider = $this->carrier_tracking_provider_name;

        return match ($provider) {
            'FedEx' => "https://www.fedex.com/fedextrack/?trknbr={$trackingNum}",
            'UPS' => "https://www.ups.com/track?tracknum={$trackingNum}",
            'Aramex' => "https://www.aramex.com/us/en/track/results?mode=0&ShipmentNumber={$trackingNum}",
            'OCS' => "https://webcsw.ocs.co.jp/csw/ECSWG0201R00003P.do?cwbno={$trackingNum}",
            'SF Express' => "https://www.sf-international.com/sg/en/support/querySupport/waybill?No={$trackingNum}",
            'Team Global Express' => "https://teamglobalexp.com/myparcel?shipmentID={$trackingNum}",
            default => "https://www.dhl.com/bd-en/home/tracking/tracking-express.html?submit=1&tracking-id={$trackingNum}&inputsource=marketingstage",
        };
    }

}
