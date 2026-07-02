<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourcingRequestItem extends Model
{
    protected $fillable = [
        'sourcing_request_id',
        'product_description',
        'product_link',
        'product_image',
    ];

    public function sourcingRequest()
    {
        return $this->belongsTo(SourcingRequest::class);
    }
}
