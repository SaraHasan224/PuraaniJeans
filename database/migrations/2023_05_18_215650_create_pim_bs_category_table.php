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
        Schema::create('pim_bs_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->index();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('icon')->nullable();
            $table->text('image')->nullable();
            $table->tinyInteger('product_count')->default(Constant::No);
            $table->tinyInteger('position')->default(Constant::No);
            $table->boolean('is_featured')->default(Constant::No);
            $table->boolean('status')->default(Constant::No);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pim_bs_categories');
    }
};
