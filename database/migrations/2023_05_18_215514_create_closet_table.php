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
        Schema::create('closets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->index();
            $table->string('closet_name')->nullable();
            $table->string('closet_reference')->nullable();
            $table->text('logo')->nullable();
            $table->text('banner')->nullable();
            $table->text('about_closet')->nullable();
            $table->boolean('status')->default(Constant::No);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closet');
    }
};
