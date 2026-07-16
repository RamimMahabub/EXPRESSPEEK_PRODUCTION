<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sourcing_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('sourcing_requests', 'destination_address')) {
                $table->string('destination_address', 500)->nullable()->after('destination_country_code');
                $table->string('destination_city', 100)->nullable()->after('destination_address');
                $table->string('destination_state', 100)->nullable()->after('destination_city');
                $table->string('destination_postal_code', 20)->nullable()->after('destination_state');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sourcing_requests', function (Blueprint $table) {
            $table->dropColumn([
                'destination_address',
                'destination_city',
                'destination_state',
                'destination_postal_code'
            ]);
        });
    }
};
