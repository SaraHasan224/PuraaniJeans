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
        Schema::create('customer_product_recently_viewed', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->index()->nullable();
            $table->unsignedBigInteger('product_id')->index()->nullable();
            $table->tinyInteger('referrer_type')->nullable();
            $table->string('viewed_at')->nullable()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_product_recently_viewed');
    }
};
