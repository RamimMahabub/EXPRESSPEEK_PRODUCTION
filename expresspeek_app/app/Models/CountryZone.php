<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Carrier;

class CountryZone extends Model
{
    protected $fillable = [
        'carrier_id',
        'country_code',
        'country_name',
        'zone',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}
