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
        Schema::create('search_text_history', function (Blueprint $table) {
            $table->id();
            $table->string('search_text');
            $table->tinyInteger('referrer_type')->index();
            $table->string('searched_count');
            $table->timestamp('last_searched_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_text_history');
    }
};
