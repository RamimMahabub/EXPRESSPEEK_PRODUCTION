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
            $table->string('shipment_type', 20)->default('non_document')->after('country_code');
            $table->index(['carrier_id', 'shipment_type', 'zone', 'weight_slab'], 'rates_carrier_type_zone_weight_idx');
            $table->index(['carrier_id', 'shipment_type', 'country_code', 'weight_slab'], 'rates_carrier_type_country_weight_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->dropIndex('rates_carrier_type_zone_weight_idx');
            $table->dropIndex('rates_carrier_type_country_weight_idx');
            $table->dropColumn('shipment_type');
        });
    }
};
