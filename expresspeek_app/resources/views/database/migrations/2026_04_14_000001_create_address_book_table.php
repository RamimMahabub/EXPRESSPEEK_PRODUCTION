<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('address_book', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->string('label')->nullable()->comment('Friendly name like "Office", "Home"');
            $table->string('name');
            $table->string('company')->nullable();
            $table->boolean('is_business')->default(false);
            $table->string('country_code', 5)->nullable();
            $table->string('country_name')->nullable();
            $table->text('address');
            $table->string('address2')->nullable();
            $table->string('address3')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_type')->default('Office');
            $table->string('phone_code')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();

            $table->index('agent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address_book');
    }
};
