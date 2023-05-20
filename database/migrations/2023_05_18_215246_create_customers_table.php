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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username');
            $table->string('email')->unique()->index();
            $table->string('country_code', 7)->index();
            $table->string('phone_number',25)->index();
            $table->unsignedBigInteger('country_id')->index();
            $table->tinyInteger('user_type')->index();
            $table->boolean('status')->default(Constant::No);
            $table->boolean('subscription_status')->default(Constant::No);
            $table->string('identifier');
            $table->timestamp('last_login')->nullable();
            $table->unsignedBigInteger('login_attempts');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
