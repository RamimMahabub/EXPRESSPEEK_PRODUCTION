<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Shipment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $guest = User::firstOrCreate(
            ['email' => 'guest@expresspeek.local'],
            [
                'name' => 'Guest User',
                'password' => Hash::make(Str::random(32))
            ]
        );

        if (method_exists($guest, 'assignRole') && !$guest->hasRole('customer')) {
            $guest->assignRole('customer');
        }

        $ghostUsers = User::where('email', 'like', '%@expresspeek.local')
            ->where('id', '!=', $guest->id)
            ->get();

        foreach ($ghostUsers as $ghostUser) {
            Shipment::where('sender_id', $ghostUser->id)->update(['sender_id' => $guest->id]);
            $ghostUser->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration as this is a data cleanup
    }
};
