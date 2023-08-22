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
            $table->boolean('shipment_country')->after('free_shipment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pim_products', function (Blueprint $table) {
            $table->dropColumn('shipment_country');
        });
    }
};
