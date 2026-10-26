<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sourcing_requests', function (Blueprint $table) {
            $table->string('destination_address2', 500)->nullable()->after('destination_address');
            $table->string('destination_address3', 500)->nullable()->after('destination_address2');
        });
    }

    public function down(): void
    {
        Schema::table('sourcing_requests', function (Blueprint $table) {
            $table->dropColumn(['destination_address2', 'destination_address3']);
        });
    }
};
