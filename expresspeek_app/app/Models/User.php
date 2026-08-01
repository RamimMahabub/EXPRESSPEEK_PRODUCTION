<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_country_code',
        'phone',
        'avatar_path',
        'password',
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

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Resolve a user's primary application role in a consistent order.
     *
     * Admin access takes precedence for accounts that were accidentally or
     * intentionally assigned more than one role.
     */
    public function primaryRoleName(): ?string
    {
        return match (true) {
            $this->isAdmin() => 'admin',
            $this->isAgent() => 'agent',
            $this->isCustomer() => 'customer',
            default => null,
        };
    }

    public function dashboardRouteName(): string
    {
        return match ($this->primaryRoleName()) {
            'admin' => 'admin.dashboard',
            'agent' => 'agent.dashboard',
            'customer' => 'customer.dashboard',
            default => 'home',
        };
    }

    public function shipmentsRouteName(): string
    {
        return match ($this->primaryRoleName()) {
            'admin' => 'admin.shipments.index',
            'agent' => 'agent.shipments.index',
            'customer' => 'customer.shipments.index',
            default => 'home',
        };
    }

    public function shipmentCreateRouteName(): string
    {
        return match ($this->primaryRoleName()) {
            'admin' => 'admin.shipments.create',
            'agent' => 'agent.shipments.create',
            'customer' => 'customer.shipments.create',
            default => 'home',
        };
    }

    public function addressBookRouteName(): string
    {
        return match ($this->primaryRoleName()) {
            'admin' => 'admin.address-book.page',
            'agent' => 'agent.address-book.page',
            'customer' => 'customer.address-book.page',
            default => 'home',
        };
    }

    /**
     * Convert a country code and subscriber number to a consistent E.164 value.
     */
    public static function normalizePhone(string $countryCode, string $number): string
    {
        $countryDigits = preg_replace('/\D+/', '', $countryCode) ?? '';
        $numberDigits = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with(trim($number), '+')) {
            return '+'.$numberDigits;
        }

        if ($countryDigits !== '' && str_starts_with($numberDigits, $countryDigits)) {
            return '+'.$numberDigits;
        }

        // Bangladesh mobile numbers are commonly entered with the local trunk 0.
        if ($countryDigits === '880' && str_starts_with($numberDigits, '0')) {
            $numberDigits = substr($numberDigits, 1);
        }

        return '+'.$countryDigits.$numberDigits;
    }

    /**
     * Produce safe login candidates for common international and Bangladesh formats.
     *
     * @return list<string>
     */
    public static function phoneLoginCandidates(string $identifier): array
    {
        $digits = preg_replace('/\D+/', '', $identifier) ?? '';

        if ($digits === '') {
            return [];
        }

        $candidates = ['+'.$digits];

        if (str_starts_with($digits, '01')) {
            $candidates[] = '+880'.substr($digits, 1);
        } elseif (str_starts_with($digits, '1') && strlen($digits) === 10) {
            $candidates[] = '+880'.$digits;
        }

        return array_values(array_unique($candidates));
    }

    public function phoneDisplay(): string
    {
        return $this->phone ?: 'Not provided';
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? asset($this->avatar_path) : null;
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function isAgent(): bool
    {
        return $this->hasRole('agent');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'sender_id');
    }

    public function assignedShipments()
    {
        return $this->hasMany(Shipment::class, 'agent_id');
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function addressBook()
    {
        return $this->hasMany(AddressBook::class, 'user_id');
    }
}
