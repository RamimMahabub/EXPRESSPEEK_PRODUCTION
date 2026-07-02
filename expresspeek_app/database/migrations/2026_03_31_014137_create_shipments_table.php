<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('receiver_name');
            $table->string('receiver_email')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->text('receiver_address');
            $table->string('receiver_city');
            $table->string('receiver_country');
            $table->decimal('weight', 8, 2)->comment('Weight in kg');
            $table->string('dimensions')->nullable()->comment('LxWxH in cm');
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('estimated_delivery')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('sender_id');
            $table->index('agent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
