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
        Schema::create('sourcing_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sourcing_request_id')->constrained()->cascadeOnDelete();
            $table->text('product_description');
            $table->text('product_link')->nullable();
            $table->string('product_image')->nullable();
            $table->timestamps();
        });

        // Migrate existing data
        $requests = \Illuminate\Support\Facades\DB::table('sourcing_requests')->get();
        foreach ($requests as $request) {
            \Illuminate\Support\Facades\DB::table('sourcing_request_items')->insert([
                'sourcing_request_id' => $request->id,
                'product_description' => $request->product_description,
                'product_link' => $request->product_link,
                'product_image' => $request->product_image,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ]);
        }

        // Drop old columns
        Schema::table('sourcing_requests', function (Blueprint $table) {
            $table->dropColumn(['product_description', 'product_link', 'product_image']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sourcing_requests', function (Blueprint $table) {
            $table->text('product_description')->nullable();
            $table->text('product_link')->nullable();
            $table->string('product_image')->nullable();
        });

        // Restore data (best effort)
        $items = \Illuminate\Support\Facades\DB::table('sourcing_request_items')->get();
        foreach ($items as $item) {
            \Illuminate\Support\Facades\DB::table('sourcing_requests')
                ->where('id', $item->sourcing_request_id)
                ->update([
                    'product_description' => $item->product_description,
                    'product_link' => $item->product_link,
                    'product_image' => $item->product_image,
                ]);
        }

        Schema::dropIfExists('sourcing_request_items');
    }
};
