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
        Schema::create('countries', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('code')->nullable();
                $table->string('currency_code')->nullable();
                $table->string('country_code')->nullable();
                $table->string('phone_number_mask')->nullable();
                $table->tinyInteger('status')->default(Constant::No);
                $table->integer('created_by')->default(0);
                $table->integer('updated_by')->default(0);
                $table->integer('deleted_by')->default(0);
                $table->timestamps();
                $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
