<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('country_zones')->updateOrInsert(
            ['country_code' => 'CO'],
            [
                'country_name' => 'Colombia',
                'zone' => 7,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep existing production data stable on rollback.
    }
};
