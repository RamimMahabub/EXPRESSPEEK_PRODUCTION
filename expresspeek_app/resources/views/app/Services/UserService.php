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
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($role);

        return $user;
    }

    /**
     * Get all users with a specific role.
     */
    public function getUsersByRole(string $role): Collection
    {
        return User::role($role)->latest()->get();
    }

    /**
     * Get platform user statistics.
     */
    public function getUserStats(): array
    {
        return [
            'total'     => User::count(),
            'admins'    => User::role('admin')->count(),
            'customers' => User::role('customer')->count(),
            'agents'    => User::role('agent')->count(),
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
