<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourcingInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'sourcing_request_id',
        'invoice_number',
        'currency',
        'total_amount',
        'status',
        'due_date',
        'items',
        'notes',
    ];

    protected $casts = [
        'items' => 'array',
        'due_date' => 'date',
    ];

    public function sourcingRequest()
    {
        return $this->belongsTo(SourcingRequest::class);
    }

    public static function generateInvoiceNumber()
    {
        do {
            $number = 'INV-SR-' . strtoupper(\Illuminate\Support\Str::random(5)) . '-' . now()->format('ymd');
        } while (static::where('invoice_number', $number)->exists());
        
        return $number;
    }

    public static function statuses()
    {
        return [
            'unpaid' => 'Unpaid',
            'paid'   => 'Paid',
            'cancelled' => 'Cancelled',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }
}
