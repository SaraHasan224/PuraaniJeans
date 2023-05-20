<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Helpers\Constant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pim_product_variant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('product_variant');
            $table->string('price')->nullable();
            $table->string('sku',60)->nullable();
            $table->unsignedBigInteger('quantity')->nullable();
            $table->string('discount')->nullable();
            $table->tinyInteger('discount_type')->nullable();
            $table->unsignedBigInteger('image_id');
            $table->unsignedBigInteger('position');
            $table->string('short_description')->nullable();
            $table->boolean('status')->default(Constant::No);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pim_product_variant');
    }
};
