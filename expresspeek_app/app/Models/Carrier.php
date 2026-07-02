<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $fillable = [
        'name',
        'fuel_surcharge_percent',
        'profit_margin_percent',
        'currency',
    ];

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }
}
