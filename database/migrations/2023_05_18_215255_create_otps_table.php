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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('model',50);
            $table->unsignedBigInteger('model_id');
            $table->string('reference_id');
            $table->string('country_code', 7)->index();
            $table->string('phone_number',25)->index();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('email')->unique()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('otp_provider')->default(Constant::No);
            $table->string('phone_otp',8);
            $table->string('email_otp',8);
            $table->tinyInteger('action')->nullable();
            $table->boolean('is_used')->default(Constant::No);
            $table->boolean('is_verified')->default(Constant::No);
            $table->timestamp('verified_at')->nullable();
            $table->string('user_agent');
            $table->string('country');
            $table->string('ip');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
