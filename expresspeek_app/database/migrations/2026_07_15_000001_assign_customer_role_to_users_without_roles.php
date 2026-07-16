<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Role::firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);

        User::doesntHave('roles')
            ->select('id')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $user->assignRole('customer');
                }
            });
    }

    public function down(): void
    {
        // Existing role assignments are intentionally preserved on rollback.
    }
};
