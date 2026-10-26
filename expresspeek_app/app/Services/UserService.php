<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new user and assign a role.
     */
    public function createUser(array $data, string $role): User
    {
        $user = User::create([
            'name' => $data['name'],
            'is_business' => $data['is_business'] ?? false,
            'company_name' => $data['company_name'] ?? null,
            'email' => $data['email'],
            'phone_country_code' => $data['phone_country_code'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($role);

        return $user;
    }

    /**
     * Get all users with a specific role.
     */
    public function getUsersByRole(string $role, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return User::with('roles')
            ->role($role)
            ->where('email', 'not like', '%@expresspeek.local')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get platform user statistics.
     */
    public function getUserStats(): array
    {
        $query = User::where('email', 'not like', '%@expresspeek.local');
        
        return [
            'total' => (clone $query)->count(),
            'admins' => (clone $query)->role('admin')->count(),
            'customers' => (clone $query)->role('customer')->count(),
            'agents' => (clone $query)->role('agent')->count(),
        ];
    }

    /**
     * Assign a new role to a user (removes existing roles).
     */
    public function changeRole(User $user, string $role): void
    {
        $user->syncRoles([$role]);
    }
}
