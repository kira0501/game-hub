<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $image = 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/3280350/702c9ed8dc25f26be07539cd5cfb9f08046d210a/ss_702c9ed8dc25f26be07539cd5cfb9f08046d210a.1920x1080.jpg?t=1774022345';

        DB::table('games')
            ->where('slug', 'death-stranding-2-on-the-beach')
            ->update([
                'hero_image' => $image,
                'carousel_image' => $image,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
