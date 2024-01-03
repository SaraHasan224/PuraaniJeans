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
        Schema::create('pim_bs_category_mapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bs_category_id')->index();
            $table->unsignedBigInteger('pim_category_id')->index();
            $table->unsignedBigInteger('mapped_by')->index();
            $table->timestamp('mapped_at');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pim_bs_category_mapping');
    }
};
