<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShipmentInvoice extends Model
{
    use HasFactory;

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'shipment_id',
        'invoice_number',
        'currency',
        'total_amount',
        'status',
        'due_date',
        'items',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'due_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public static function generateInvoiceNumber(): string
    {
        do {
            $number = 'INV-SH-' . strtoupper(Str::random(5)) . '-' . now()->format('ymd');
        } while (static::where('invoice_number', $number)->exists());

        return $number;
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_UNPAID => 'Unpaid',
            self::STATUS_PAID => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }
}
