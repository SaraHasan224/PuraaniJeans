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
        Schema::create('pim_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('parent_id')->index();
            $table->unsignedBigInteger('closet_id')->index();
            $table->text('description')->nullable();
            $table->string('pim_cat_reference')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_full_banner')->default(Constant::No);
            $table->boolean('status')->default(Constant::No);
            $table->boolean('is_default')->default(Constant::No);
            $table->unsignedBigInteger('position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pim_categories');
    }
};
