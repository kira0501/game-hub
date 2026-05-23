<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $appIds = [
            'Cyberpunk 2077' => 1091500,
            'Red Dead Redemption 2' => 1174180,
            'Grand Theft Auto V' => 271590,
            'Elden Ring' => 1245620,
            'The Witcher 3: Wild Hunt' => 292030,
            'Black Myth: Wukong' => 2358720,
            'Detroit: Become Human' => 1222140,
            'DOOM Eternal' => 782330,
            'Dead Space' => 1693980,
            'Dark Souls III' => 374320,
            'God of War' => 1593500,
            'God of War Ragnarök' => 2322010,
            'Half-Life: Alyx' => 546560,
            'Resident Evil Village' => 1196590,
            'Silent Hill 2' => 2124490,
            'Metro 2033 Redux' => 286690,
            'Fallout 4' => 377160,
            'Dying Light' => 239140,
            'Dying Light 2: Stay Human' => 534380,
            'Death Stranding' => 1190460,
            'Death Stranding 2: On the Beach' => 3280350,
            'Sea of Thieves' => 1172620,
            'Mortal Kombat 11' => 976310,
            'Cuphead' => 268910,
            'Life is Strange' => 319630,
            'Little Nightmares II' => 860510,
            'Lies of P' => 1627720,
            'Watch Dogs 2' => 447040,
            'Far Cry 6' => 2369390,
            'Just Cause 3' => 225540,
            'Borderlands 2' => 49520,
            'Borderlands 3' => 397540,
            'Destiny 2' => 1085660,
            'Dead by Daylight' => 381210,
            'Disco Elysium' => 632470,
            'Outlast' => 238320,
            'Poppy Playtime' => 1721470,
            'Need for Speed Unbound' => 1846380,
            'Forza Horizon 6' => 2483190,
        ];

        foreach ($appIds as $title => $appId) {
            DB::table('games')
                ->where('title', $title)
                ->update([
                    'cover' => "https://cdn.akamai.steamstatic.com/steam/apps/{$appId}/library_600x900_2x.jpg",
                    'updated_at' => now(),
                ]);
        }

        if (! Schema::hasColumn('games', 'hero_image') || ! Schema::hasColumn('games', 'carousel_image')) {
            return;
        }

        DB::table('games')
            ->orderBy('id')
            ->chunkById(50, function ($games) {
                foreach ($games as $game) {
                    $firstGalleryImage = DB::table('game_media')
                        ->where('game_id', $game->id)
                        ->where('type', 'image')
                        ->where('role', 'gallery')
                        ->orderBy('sort_order')
                        ->value('url');

                    if (! $firstGalleryImage) {
                        continue;
                    }

                    DB::table('games')
                        ->where('id', $game->id)
                        ->update([
                            'hero_image' => $game->hero_image ?: $firstGalleryImage,
                            'carousel_image' => $game->carousel_image ?: $game->hero_image ?: $firstGalleryImage,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Data repair only. It is intentionally not reverted.
    }
};
