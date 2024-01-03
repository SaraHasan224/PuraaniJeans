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
            $table->string('model',50)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('country_code', 7)->index()->nullable();
            $table->string('phone_number',25)->index()->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('email')->nullable()->unique()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('otp_provider')->default(Constant::No);
            $table->text('phone_otp')->nullable();
            $table->text('email_otp')->nullable();
            $table->tinyInteger('action')->nullable();
            $table->boolean('is_used')->default(Constant::No);
            $table->boolean('is_verified')->default(Constant::No);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('ip')->nullable();
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
