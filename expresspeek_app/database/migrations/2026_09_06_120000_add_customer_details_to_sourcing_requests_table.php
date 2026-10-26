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
            $table->boolean('customer_is_business')->default(0)->after('customer_name');
            $table->string('customer_company')->nullable()->after('customer_is_business');
            $table->string('customer_country')->nullable()->after('customer_company');
            $table->string('customer_country_code', 10)->nullable()->after('customer_country');
            $table->string('customer_address', 500)->nullable()->after('customer_country_code');
            $table->string('customer_address2', 500)->nullable()->after('customer_address');
            $table->string('customer_address3', 500)->nullable()->after('customer_address2');
            $table->string('customer_city')->nullable()->after('customer_address3');
            $table->string('customer_state')->nullable()->after('customer_city');
            $table->string('customer_postal_code', 50)->nullable()->after('customer_state');
            $table->string('customer_email')->nullable()->after('customer_postal_code');
            $table->string('customer_phone_type', 50)->nullable()->after('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sourcing_requests', function (Blueprint $table) {
            $table->dropColumn([
                'customer_is_business',
                'customer_company',
                'customer_country',
                'customer_country_code',
                'customer_address',
                'customer_address2',
                'customer_address3',
                'customer_city',
                'customer_state',
                'customer_postal_code',
                'customer_email',
                'customer_phone_type',
            ]);
        });
    }
};
