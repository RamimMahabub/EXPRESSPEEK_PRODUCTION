<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('country_zones', function (Blueprint $table) {
            if (!Schema::hasColumn('country_zones', 'carrier_id')) {
                $table->foreignId('carrier_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('carriers')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('country_zones', function (Blueprint $table) {
            if (Schema::hasColumn('country_zones', 'carrier_id')) {
                $table->dropConstrainedForeignId('carrier_id');
            }
        });
    }
};