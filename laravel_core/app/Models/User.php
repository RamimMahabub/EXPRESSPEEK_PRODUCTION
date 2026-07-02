<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function hotelBookings()
    {
        return $this->hasMany(HotelBooking::class, 'guest_id');
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isPropertyOwner(): bool
    {
        return $this->role === 'property_owner';
    }

    public function isInternalUser(): bool
    {
        return in_array($this->role, [
            'admin',
            'manager',
            'support_agent',
            'ticketing_officer',
            'accounts_officer',
        ], true);
    }

    public function getDashboardRoute(): string
    {
        if ($this->isPropertyOwner()) {
            return 'property-owner.dashboard';
        }

        if ($this->isInternalUser()) {
            return 'admin.dashboard';
        }

        return 'dashboard';
    }
}
