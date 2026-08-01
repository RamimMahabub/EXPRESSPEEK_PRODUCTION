<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Sender full details
            $table->string('sender_name')->nullable()->after('sender_id');
            $table->string('sender_company')->nullable()->after('sender_name');
            $table->boolean('sender_is_business')->default(false)->after('sender_company');
            $table->string('sender_country_code', 5)->nullable()->after('sender_is_business');
            $table->string('sender_country')->nullable()->after('sender_country_code');
            $table->text('sender_address')->nullable()->after('sender_country');
            $table->string('sender_address2')->nullable()->after('sender_address');
            $table->string('sender_address3')->nullable()->after('sender_address2');
            $table->string('sender_postal_code')->nullable()->after('sender_address3');
            $table->string('sender_city')->nullable()->after('sender_postal_code');
            $table->string('sender_state')->nullable()->after('sender_city');
            $table->string('sender_email')->nullable()->after('sender_state');
            $table->string('sender_phone_type')->default('Office')->after('sender_email');
            $table->string('sender_phone_code')->nullable()->after('sender_phone_type');
            $table->string('sender_phone')->nullable()->after('sender_phone_code');

            // Extended receiver fields
            $table->string('receiver_company')->nullable()->after('receiver_name');
            $table->boolean('receiver_is_business')->default(false)->after('receiver_company');
            $table->string('receiver_country_code', 5)->nullable()->after('receiver_country');
            $table->string('receiver_address2')->nullable()->after('receiver_address');
            $table->string('receiver_address3')->nullable()->after('receiver_address2');
            $table->string('receiver_postal_code')->nullable()->after('receiver_address3');
            $table->string('receiver_state')->nullable()->after('receiver_city');
            $table->string('receiver_phone_type')->default('Mobile')->after('receiver_phone');
            $table->string('receiver_phone_code')->nullable()->after('receiver_phone_type');

            // Shipment type & content
            $table->string('shipment_type')->default('non_document')->after('description');
            $table->text('document_description')->nullable()->after('shipment_type');
            $table->json('items')->nullable()->after('document_description');
            $table->json('packages')->nullable()->after('items');
            $table->integer('total_packages')->default(1)->after('packages');
            $table->decimal('total_weight', 8, 2)->default(0)->after('total_packages');

            // Carrier selection
            $table->foreignId('carrier_id')->nullable()->constrained('carriers')->nullOnDelete()->after('agent_id');
            $table->string('carrier_name')->nullable()->after('carrier_id');

            // Document numbers
            $table->string('awb_number')->unique()->nullable()->after('tracking_number');
            $table->string('invoice_number')->nullable()->after('awb_number');
            $table->foreignId('created_by_agent_id')->nullable()->constrained('users')->nullOnDelete()->after('carrier_name');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'sender_name','sender_company','sender_is_business','sender_country_code',
                'sender_country','sender_address','sender_address2','sender_address3',
                'sender_postal_code','sender_city','sender_state','sender_email',
                'sender_phone_type','sender_phone_code','sender_phone',
                'receiver_company','receiver_is_business','receiver_country_code',
                'receiver_address2','receiver_address3','receiver_postal_code',
                'receiver_state','receiver_phone_type','receiver_phone_code',
                'shipment_type','document_description','items','packages',
                'total_packages','total_weight','carrier_id','carrier_name',
                'awb_number','invoice_number','created_by_agent_id',
            ]);
        });
    }
};
