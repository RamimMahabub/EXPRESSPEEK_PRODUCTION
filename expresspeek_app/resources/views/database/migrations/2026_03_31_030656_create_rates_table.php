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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained()->onDelete('cascade');
            $table->integer('zone')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->decimal('weight_slab', 8, 2);
            $table->decimal('price', 15, 2);
            $table->decimal('per_kg_rate', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['carrier_id', 'zone', 'weight_slab']);
            $table->index(['carrier_id', 'country_code', 'weight_slab']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
