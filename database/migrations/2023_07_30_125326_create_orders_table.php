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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('closet_id');
            $table->string('merchant_order_id', 50);
            $table->string('order_ref');
            $table->string('currency_code');
            $table->string('placement_status');
            $table->string('payment_status');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->float('total_amount', 8, 2);
            $table->float('sub_total_amount', 8, 2);
            $table->float('discount_amount', 8, 2)->nullable();
            $table->text('customer_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
