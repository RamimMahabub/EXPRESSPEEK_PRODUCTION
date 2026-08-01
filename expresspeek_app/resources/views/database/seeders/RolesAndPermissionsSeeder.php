<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Shipment permissions
            'view shipments',
            'create shipments',
            'edit shipments',
            'delete shipments',
            'assign shipments',
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            // Report permissions
            'view reports',
            // Tracking permissions
            'update tracking',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'view shipments',
            'create shipments',
        ]);

        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $agentRole->givePermissionTo([
            'view shipments',
            'update tracking',
        ]);

        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@expresspeek.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('admin');

        // Create demo customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@expresspeek.com'],
            [
                'name'     => 'John Customer',
                'password' => bcrypt('password'),
            ]
        );
        $customer->assignRole('customer');

        // Create demo agent
        $agent = User::firstOrCreate(
            ['email' => 'agent@expresspeek.com'],
            [
                'name'     => 'Jane Agent',
                'password' => bcrypt('password'),
            ]
        );
        $agent->assignRole('agent');

        $this->command->info('✅ Roles, permissions, and demo users created successfully.');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['admin',    'admin@expresspeek.com',    'password'],
                ['customer', 'customer@expresspeek.com', 'password'],
                ['agent',    'agent@expresspeek.com',    'password'],
            ]
        );
    }
}
