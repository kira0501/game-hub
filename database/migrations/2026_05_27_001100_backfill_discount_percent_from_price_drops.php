<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('game_prices')
            ->whereNotNull('previous_price')
            ->whereNotNull('price')
            ->whereColumn('price', '<', 'previous_price')
            ->where(function ($query) {
                $query->whereNull('discount_percent')
                    ->orWhere('discount_percent', '<=', 0);
            })
            ->orderBy('id')
            ->chunkById(50, function ($prices) {
                foreach ($prices as $price) {
                    if ((float) $price->previous_price <= 0) {
                        continue;
                    }

                    $discount = max(1, min(99, (int) round((1 - ((float) $price->price / (float) $price->previous_price)) * 100)));

                    DB::table('game_prices')
                        ->where('id', $price->id)
                        ->update(['discount_percent' => $discount]);
                }
            });
    }

    public function down(): void
    {
        //
    }
};
