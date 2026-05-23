<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('cover');
            $table->string('carousel_image')->nullable()->after('hero_image');
        });

        Schema::table('game_media', function (Blueprint $table) {
            $table->string('role')->default('gallery')->after('type')->index();
        });

        Schema::table('game_prices', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('game_prices', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });

        Schema::table('game_media', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['hero_image', 'carousel_image']);
        });
    }
};
