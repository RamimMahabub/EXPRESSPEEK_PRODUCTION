<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sourcing_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 20)->unique(); // e.g. SR-000001

            // Customer contact info
            $table->string('customer_name', 191);
            $table->string('whatsapp_number', 30);  // e.g. +8801XXXXXXXXX
            $table->string('whatsapp_country_code', 10)->default('+880'); // phone country dial code

            // Destination
            $table->string('destination_country', 191);
            $table->string('destination_country_code', 10)->nullable();

            // Product details
            $table->text('product_description');
            $table->string('product_link', 1000)->nullable();
            $table->string('product_image', 500)->nullable(); // path to uploaded image

            // Status tracking
            $table->string('status', 30)->default('new');

            // Admin-only fields
            $table->text('admin_notes')->nullable();
            $table->decimal('quoted_price', 12, 2)->nullable();
            $table->string('quoted_currency', 10)->nullable()->default('BDT');

            // Links
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sourcing_requests');
    }
};
