<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourcingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'customer_name',
        'whatsapp_number',
        'whatsapp_country_code',
        'destination_country',
        'destination_country_code',
        'destination_address',
        'destination_city',
        'destination_state',
        'destination_postal_code',

        'status',
        'tracking_number',
        'awb_number',
        'carrier_id',
        'carrier_name',
        'admin_notes',
        'quoted_price',
        'quoted_currency',
        'user_id',
        'shipment_id',
    ];

    protected function casts(): array
    {
        return [
            'quoted_price' => 'decimal:2',
        ];
    }



    // ─── Status Constants ────────────────────────────────────────────────────────

    const STATUS_NEW             = 'new';
    const STATUS_REVIEWING       = 'reviewing';
    const STATUS_CONTACTED       = 'contacted';
    const STATUS_PAYMENT_PENDING = 'payment_pending';
    const STATUS_SOURCING        = 'sourcing';
    const STATUS_SHIPPED         = 'shipped';
    const STATUS_COMPLETED       = 'completed';
    const STATUS_CANCELLED       = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_NEW             => 'New Request',
            self::STATUS_REVIEWING       => 'Under Review',
            self::STATUS_CONTACTED       => 'Customer Contacted',
            self::STATUS_PAYMENT_PENDING => 'Payment Pending',
            self::STATUS_SOURCING        => 'Sourcing Product',
            self::STATUS_SHIPPED         => 'Shipped',
            self::STATUS_COMPLETED       => 'Completed',
            self::STATUS_CANCELLED       => 'Cancelled',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NEW             => 'blue',
            self::STATUS_REVIEWING       => 'indigo',
            self::STATUS_CONTACTED       => 'purple',
            self::STATUS_PAYMENT_PENDING => 'amber',
            self::STATUS_SOURCING        => 'orange',
            self::STATUS_SHIPPED         => 'cyan',
            self::STATUS_COMPLETED       => 'emerald',
            self::STATUS_CANCELLED       => 'red',
            default                      => 'gray',
        };
    }

    /**
     * Full WhatsApp number: country code + local number
     */
    public function getFullWhatsappAttribute(): string
    {
        $code = ltrim($this->whatsapp_country_code ?? '+880', '+');
        $number = ltrim($this->whatsapp_number, '0');
        return $code . $number;
    }

    /**
     * Click-to-chat WhatsApp link with pre-filled message
     */
    public function getWhatsappLinkAttribute(): string
    {
        $phone = $this->full_whatsapp;
        $msg = urlencode(
            "Hi {$this->customer_name}, this is ExpressPeek regarding your sourcing request {$this->reference_number}. We found your product and would like to discuss the details."
        );
        return "https://wa.me/{$phone}?text={$msg}";
    }

    public function getCarrierTrackingProviderNameAttribute(): string
    {
        if ($this->carrier) {
            return $this->carrier->name;
        }

        if (empty($this->carrier_name)) {
            return 'DHL'; // Default
        }

        $name = strtolower($this->carrier_name);

        if (str_contains($name, 'fedex')) return 'FedEx';
        if (str_contains($name, 'ups')) return 'UPS';
        if (str_contains($name, 'aramex')) return 'Aramex';
        if (str_contains($name, 'ocs')) return 'OCS';
        if (str_contains($name, 'sf')) return 'SF Express';
        if (str_contains($name, 'tge')) return 'Team Global Express';

        return 'DHL';
    }

    public function getCarrierTrackingUrlAttribute(): string
    {
        if (empty($this->awb_number)) {
            return '#';
        }

        $trackingNum = urlencode($this->awb_number);
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

    // ─── Reference Number ────────────────────────────────────────────────────────

    /**
     * Generate a unique sequential reference number like SR-000042
     */
    public static function generateReferenceNumber(): string
    {
        $latest = static::max('id') ?? 0;
        return 'SR-' . str_pad($latest + 1, 6, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function items()
    {
        return $this->hasMany(SourcingRequestItem::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function invoices()
    {
        return $this->hasMany(SourcingInvoice::class);
    }
}
