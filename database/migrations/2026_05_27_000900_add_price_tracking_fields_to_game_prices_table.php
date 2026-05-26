<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_prices', function (Blueprint $table) {
            $table->decimal('previous_price', 8, 2)->nullable()->after('price');
            $table->timestamp('price_changed_at')->nullable()->after('discount_percent');
            $table->timestamp('last_checked_at')->nullable()->after('updated_at');
            $table->string('auto_update_error')->nullable()->after('last_checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('game_prices', function (Blueprint $table) {
            $table->dropColumn([
                'previous_price',
                'price_changed_at',
                'last_checked_at',
                'auto_update_error',
            ]);
        });
    }
};
