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
        Schema::table('pim_products', function (Blueprint $table) {
            $table->boolean('free_shipment')->after('max_quantity');
            $table->boolean('enable_world_wide_shipping')->after('free_shipment');
            $table->boolean('shipping_price')->after('enable_world_wide_shipping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pim_products', function (Blueprint $table) {
            $table->dropColumn('free_shipment');
            $table->dropColumn('enable_world_wide_shipping');
            $table->dropColumn('shipping_price');
        });
    }
};
