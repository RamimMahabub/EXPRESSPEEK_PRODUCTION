<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'carrier_id',
        'zone',
        'country_code',
        'country_name',
        'shipment_type',
        'weight_slab',
        'price',
        'per_kg_rate',
        'rate_type',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}
