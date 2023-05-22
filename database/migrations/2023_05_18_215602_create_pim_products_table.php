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
        Schema::create('pim_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('closet_id')->index();
            $table->unsignedBigInteger('brand_id')->index();
            $table->string('name')->nullable();
            $table->string('sku',60);
            $table->string('handle')->nullable();
            $table->text('short_description')->nullable();
            $table->text('tags')->nullable();
            $table->string('pim_product_reference')->nullable();
            $table->boolean('has_variants')->default(Constant::No);
            $table->unsignedBigInteger('currency_id')->index();
            $table->string('price')->nullable();
            $table->unsignedBigInteger('max_quantity')->index();
            $table->tinyInteger('weight')->nullable();
            $table->unsignedBigInteger('position')->nullable();
            $table->boolean('status')->default(Constant::No);
            $table->boolean('is_featured')->default(Constant::No);
            $table->unsignedBigInteger('featured_position')->nullable();
            $table->timestamp('featured_at')->nullable();
            $table->unsignedBigInteger('featured_by')->nullable();
            $table->boolean('is_recommended')->default(Constant::No);
            $table->unsignedBigInteger('recommended_position')->nullable();
            $table->timestamp('recommended_at')->nullable();
            $table->unsignedBigInteger('recommended_by')->nullable();
            $table->unsignedBigInteger('rank')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pim_products');
    }
};
