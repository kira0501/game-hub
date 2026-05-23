<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
        ];

        foreach ($appIds as $title => $appId) {
            $image = "https://cdn.akamai.steamstatic.com/steam/apps/{$appId}/capsule_616x353.jpg";

            DB::table('games')
                ->where('title', $title)
                ->update([
                    'hero_image' => $image,
                    'carousel_image' => $image,
                    'updated_at' => now(),
                ]);
        }

        DB::table('games')
            ->where('title', 'Forza Horizon 6')
            ->update([
                'hero_image' => 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2483190/library_hero.jpg',
                'carousel_image' => 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2483190/library_hero.jpg',
                'updated_at' => now(),
            ]);

        DB::table('games')
            ->where('title', 'Death Stranding 2: On the Beach')
            ->update([
                'hero_image' => 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/3280350/9b523fd411eeaefe80f238489745325a1cd2317f/library_capsule_2x.jpg',
                'carousel_image' => 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/3280350/9b523fd411eeaefe80f238489745325a1cd2317f/library_capsule_2x.jpg',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Data repair only. It is intentionally not reverted.
    }
};
