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
        Schema::table('closets', function (Blueprint $table) {
            $table->tinyInteger('is_trending')->default(0)->after('about_closet');
            $table->tinyInteger('trending_position')->nullable()->after('is_trending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('closets', function (Blueprint $table) {
            $table->dropColumn('is_trending');
            $table->dropColumn('trending_position');
        });
    }
};
