<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressBook extends Model
{
    use HasFactory;

    protected $table = 'address_book';

    protected $fillable = [
        'agent_id',
        'label',
        'name',
        'company',
        'is_business',
        'country_code',
        'country_name',
        'address',
        'address2',
        'address3',
        'postal_code',
        'city',
        'state',
        'email',
        'phone_type',
        'phone_code',
        'phone',
    ];

    protected function casts(): array
    {
        return [
            'is_business' => 'boolean',
        ];
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
