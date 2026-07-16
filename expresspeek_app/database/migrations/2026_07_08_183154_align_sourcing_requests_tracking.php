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
            if (!Schema::hasColumn('sourcing_requests', 'carrier_id')) {
                $table->unsignedBigInteger('carrier_id')->nullable()->after('carrier_name');
                $table->foreign('carrier_id')->references('id')->on('carriers')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('sourcing_requests', 'awb_number')) {
                $table->string('awb_number')->nullable()->after('tracking_number');
            }
        });

        // Backfill existing requests with a random EP tracking number if they don't have one
        $requests = \App\Models\SourcingRequest::whereNull('tracking_number')->orWhere('tracking_number', '')->get();
        foreach ($requests as $req) {
            $number = 'EP' . strtoupper(\Illuminate\Support\Str::random(2)) . now()->format('ymd') . rand(1000, 9999);
            $req->tracking_number = $number;
            $req->save();
        }
    }

    public function down(): void
    {
        Schema::table('sourcing_requests', function (Blueprint $table) {
            $table->dropForeign(['carrier_id']);
            $table->dropColumn(['carrier_id', 'awb_number']);
        });
    }
};
