<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 50)->unique();
            $table->string('currency', 10)->default('BDT');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status', 30)->default('unpaid');
            $table->date('due_date')->nullable();
            $table->json('items');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['shipment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_invoices');
    }
};
