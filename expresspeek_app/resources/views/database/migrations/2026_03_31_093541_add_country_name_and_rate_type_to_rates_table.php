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
        Schema::table('rates', function (Blueprint $table) {
            $table->string('country_name', 100)->nullable()->after('country_code');
            $table->string('rate_type', 20)->nullable()->after('per_kg_rate')->comment('e.g., per_0_5_kg, per_kg');
            $table->index(['carrier_id', 'country_name', 'weight_slab']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->dropIndex(['carrier_id', 'country_name', 'weight_slab']);
            $table->dropColumn(['country_name', 'rate_type']);
        });
    }
};
