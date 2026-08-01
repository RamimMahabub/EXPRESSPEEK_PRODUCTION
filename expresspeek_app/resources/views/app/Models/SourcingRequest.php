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

        'status',
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
            "Hi {$this->customer_name}, this is ExpressPeak regarding your sourcing request {$this->reference_number}. We found your product and would like to discuss the details."
        );
        return "https://wa.me/{$phone}?text={$msg}";
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
}
