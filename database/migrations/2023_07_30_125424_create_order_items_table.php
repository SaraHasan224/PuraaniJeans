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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('product_sku')->nullable();
            $table->string('variant_sku')->nullable();
            $table->unsignedInteger('product_qty');
            $table->float('product_price', 8, 2);
            $table->float('product_discount', 8, 2);
            $table->float('product_sale_price', 8, 2);
            $table->float('product_sub_total', 8, 2);
            $table->string('product_image')->nullable();
            $table->text('product_additional_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
