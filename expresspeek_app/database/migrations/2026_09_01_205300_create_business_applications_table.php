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
        Schema::create('business_applications', function (Blueprint $table) {
            $table->id();
            $table->string('proprietor_name');
            $table->string('nid_no');
            $table->string('company_name');
            $table->string('trade_license')->nullable();
            $table->text('address');
            $table->string('mobile_no');
            $table->string('email');
            $table->string('status')->default('pending'); // pending, reviewed, accepted, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_applications');
    }
};
