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
            $table->string('handle');
            $table->text('short_description');
            $table->text('tags');
            $table->string('pim_product_reference');
            $table->boolean('has_variants')->default(Constant::No);
            $table->unsignedBigInteger('currency_id')->index();
            $table->string('price')->nullable();
            $table->unsignedBigInteger('max_quantity')->index();
            $table->tinyInteger('weight');
            $table->unsignedBigInteger('position');
            $table->boolean('status')->default(Constant::No);
            $table->boolean('is_featured')->default(Constant::No);
            $table->unsignedBigInteger('featured_position');
            $table->timestamp('featured_at');
            $table->unsignedBigInteger('featured_by');
            $table->boolean('is_recommended')->default(Constant::No);
            $table->unsignedBigInteger('recommended_position');
            $table->timestamp('recommended_at');
            $table->unsignedBigInteger('recommended_by');
            $table->unsignedBigInteger('rank');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('deleted_by');
            $table->timestamp('recommended_at');
            $table->timestamp('featured_by');
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
