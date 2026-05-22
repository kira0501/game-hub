<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Game;
use App\Models\GameMedia;
use App\Models\GamePrice;
use App\Models\Genre;
use App\Models\PcConfig;
use App\Models\Recommendation;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Game Hub',
            'email' => 'admin@gamehub.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'avatar' => 'https://api.dicebear.com/9.x/bottts/svg?seed=admin',
        ]);

        $demo = User::create([
            'name' => 'Demo Player',
            'email' => 'user@gamehub.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
            'avatar' => 'https://api.dicebear.com/9.x/bottts/svg?seed=player',
        ]);

        $users = User::factory(14)->create();
        $genres = collect([
            'Action', 'Adventure', 'RPG', 'Open World', 'Shooter', 'Horror', 'Survival',
            'Souls-like', 'Racing', 'Fighting', 'Platformer', 'Indie', 'Narrative',
            'Co-op', 'Multiplayer', 'Sci-Fi', 'Cyberpunk', 'VR', 'Detective', 'Comedy', 'Puzzle',
        ])->mapWithKeys(fn ($name) => [$name => Genre::create(['name' => $name, 'slug' => Str::slug($name)])]);

        $steam = Store::create(['name' => 'Steam', 'slug' => 'steam', 'logo' => 'https://cdn.akamai.steamstatic.com/store/about/social-og.jpg', 'base_url' => 'https://store.steampowered.com']);
        $epic = Store::create(['name' => 'Epic Games', 'slug' => 'epic-games', 'logo' => 'https://cdn2.unrealengine.com/egs-social-1200x630-1200x630-9a3287c7b053.jpg', 'base_url' => 'https://store.epicgames.com']);

        foreach ($this->games() as $index => $data) {
            $game = Game::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'description' => $data['description'],
                'cover' => $this->cover($data['appid'] ?? null, $index),
                'trailer_url' => $this->trailer($data['title']),
                'developer' => $data['developer'],
                'publisher' => $data['publisher'],
                'release_date' => $data['release_date'],
                'metacritic_score' => $data['meta'],
                'user_score_avg' => $data['score'],
                'play_features' => $data['features'],
                'controller_support' => $data['controller'],
                'supports_xbox_controller' => $data['xbox'],
                'supports_playstation_controller' => $data['playstation'],
                'developer_recommends_controller' => $data['recommend_controller'],
                'is_active' => true,
            ]);

            $game->genres()->sync(collect($data['genres'])->map(fn ($name) => $genres[$name]->id));
            $this->seedRequirements($game, $index, $data);
            $this->seedMedia($game, $data, $index);
            $this->seedPrices($game, $steam, $epic, $data);

            foreach ($users->random(4) as $user) {
                Review::factory()->create([
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'rating' => fake()->numberBetween(7, 10),
                    'status' => 'approved',
                ]);
            }
        }

        $catalog = Game::all();
        foreach ($catalog->take(8) as $game) {
            Favorite::firstOrCreate(['user_id' => $demo->id, 'game_id' => $game->id]);
        }

        PcConfig::create([
            'user_id' => $demo->id,
            'title' => 'Домашний игровой ПК',
            'cpu' => 'Intel Core i5-12400',
            'gpu' => 'RTX 3060',
            'ram' => 16,
            'storage' => 512,
            'os' => 'Windows 11',
            'notes' => 'Демонстрационная конфигурация для проверки совместимости.',
        ]);

        foreach ($catalog->skip(8)->take(8) as $game) {
            Recommendation::create([
                'user_id' => $demo->id,
                'game_id' => $game->id,
                'score' => 80,
                'reason' => 'жанры совпадают с избранным, хорошая оценка и подходящая цена',
            ]);
        }

        Favorite::firstOrCreate(['user_id' => $admin->id, 'game_id' => $catalog->first()->id]);
    }

    private function games(): array
    {
        $f = [
            'single' => ['single_player', 'achievements', 'cloud', 'family_sharing'],
            'coop' => ['single_player', 'coop_online', 'achievements', 'cloud', 'remote_play_together'],
            'multi' => ['single_player', 'pvp_online', 'coop_online', 'achievements', 'in_app_purchases', 'cloud'],
            'local' => ['single_player', 'pvp_splitscreen', 'coop_splitscreen', 'shared_splitscreen', 'achievements'],
            'horror' => ['single_player', 'achievements', 'cloud', 'accessibility'],
        ];

        return [
            $this->game('Cyberpunk 2077', 1091500, ['RPG','Open World','Cyberpunk','Action'], 'CD PROJEKT RED', 'CD PROJEKT RED', '2020-12-10', 86, 8.7, 2999, true, 2999, true, $f['single'], 'partial', true, true, false),
            $this->game('Red Dead Redemption 2', 1174180, ['Open World','Adventure','Action'], 'Rockstar Games', 'Rockstar Games', '2019-12-05', 93, 9.1, 3999, true, 3999, false, $f['single'], 'full', true, true, true),
            $this->game('Grand Theft Auto V', 271590, ['Open World','Action','Multiplayer'], 'Rockstar North', 'Rockstar Games', '2015-04-14', 96, 8.8, 1999, true, 1999, true, $f['multi'], 'full', true, true, true),
            $this->game('Elden Ring', 1245620, ['RPG','Souls-like','Open World','Action'], 'FromSoftware', 'Bandai Namco Entertainment', '2022-02-25', 94, 9.2, 3999, true, 3999, false, $f['coop'], 'full', true, true, true),
            $this->game('The Witcher 3: Wild Hunt', 292030, ['RPG','Open World','Adventure'], 'CD PROJEKT RED', 'CD PROJEKT RED', '2015-05-18', 93, 9.3, 1499, true, 1499, true, $f['single'], 'full', true, true, true),
            $this->game('Black Myth: Wukong', 2358720, ['Action','RPG','Souls-like'], 'Game Science', 'Game Science', '2024-08-20', 81, 8.8, 3599, true, 3599, true, $f['single'], 'full', true, true, true),
            $this->game('Detroit: Become Human', 1222140, ['Adventure','Narrative','Sci-Fi'], 'Quantic Dream', 'Quantic Dream', '2020-06-18', 78, 8.5, 1599, true, 1599, true, $f['single'], 'full', true, true, true),
            $this->game('DOOM Eternal', 782330, ['Shooter','Action','Sci-Fi'], 'id Software', 'Bethesda Softworks', '2020-03-20', 88, 8.9, 1999, true, 1999, false, $f['multi'], 'full', true, true, true),
            $this->game('Dead Space', 1693980, ['Horror','Survival','Sci-Fi'], 'Motive', 'Electronic Arts', '2023-01-27', 89, 8.6, 3499, true, 3499, false, $f['horror'], 'full', true, true, true),
            $this->game('Dark Souls III', 374320, ['RPG','Souls-like','Action'], 'FromSoftware', 'Bandai Namco Entertainment', '2016-04-12', 89, 9.0, 2499, true, 2499, false, $f['coop'], 'full', true, true, true),
            $this->game('God of War', 1593500, ['Action','Adventure','RPG'], 'Santa Monica Studio', 'PlayStation Publishing LLC', '2022-01-14', 93, 9.1, 3149, true, 3149, true, $f['single'], 'full', true, true, true),
            $this->game('God of War Ragnarök', 2322010, ['Action','Adventure','RPG'], 'Santa Monica Studio', 'PlayStation Publishing LLC', '2024-09-19', 90, 8.8, 3999, true, 3999, true, $f['single'], 'full', true, true, true),
            $this->game('Half-Life: Alyx', 546560, ['VR','Shooter','Sci-Fi'], 'Valve', 'Valve', '2020-03-23', 93, 9.4, 1085, true, null, false, $f['single'], 'none', false, false, false),
            $this->game('Resident Evil Village', 1196590, ['Horror','Survival','Action'], 'CAPCOM Co., Ltd.', 'CAPCOM Co., Ltd.', '2021-05-07', 84, 8.5, 1999, true, 1999, false, $f['horror'], 'full', true, true, true),
            $this->game('Silent Hill 2', 2124490, ['Horror','Survival','Adventure'], 'Bloober Team SA', 'KONAMI', '2024-10-08', 87, 8.4, 3999, true, 3999, false, $f['horror'], 'full', true, true, true),
            $this->game('Metro 2033 Redux', 286690, ['Shooter','Survival','Horror'], '4A Games', 'Deep Silver', '2014-08-27', 88, 8.7, 599, true, 599, true, $f['single'], 'full', true, true, true),
            $this->game('Fallout 4', 377160, ['RPG','Open World','Shooter'], 'Bethesda Game Studios', 'Bethesda Softworks', '2015-11-10', 84, 8.6, 1399, true, 1399, false, $f['single'], 'full', true, true, true),
            $this->game('Dying Light', 239140, ['Survival','Horror','Co-op'], 'Techland', 'Techland Publishing', '2015-01-26', 74, 8.4, 1199, true, 1199, false, $f['coop'], 'full', true, true, true),
            $this->game('Dying Light 2: Stay Human', 534380, ['Survival','Horror','Open World','Co-op'], 'Techland', 'Techland', '2022-02-04', 76, 8.1, 2499, true, 2499, true, $f['coop'], 'full', true, true, true),
            $this->game('Death Stranding', 1190460, ['Adventure','Open World','Sci-Fi'], 'KOJIMA PRODUCTIONS', '505 Games', '2020-07-14', 86, 8.7, 1999, true, 1999, true, $f['single'], 'full', true, true, true),
            $this->game('Death Stranding 2: On the Beach', 3280350, ['Adventure','Open World','Sci-Fi'], 'KOJIMA PRODUCTIONS', 'Sony Interactive Entertainment', '2025-06-26', 90, 8.8, 3999, true, null, false, $f['single'], 'full', true, true, true),
            $this->game('Sea of Thieves', 1172620, ['Adventure','Open World','Co-op','Multiplayer'], 'Rare Ltd', 'Xbox Game Studios', '2020-06-03', 81, 8.2, 1999, true, 1999, true, $f['multi'], 'full', true, true, true),
            $this->game('Mortal Kombat 11', 976310, ['Fighting','Action','Multiplayer'], 'NetherRealm Studios', 'Warner Bros. Games', '2019-04-23', 82, 8.3, 1499, true, 1499, false, $f['local'], 'full', true, true, true),
            $this->game('Cuphead', 268910, ['Platformer','Indie','Co-op'], 'Studio MDHR', 'Studio MDHR', '2017-09-29', 88, 9.0, 710, true, 710, false, $f['local'], 'full', true, true, true),
            $this->game('Life is Strange', 319630, ['Adventure','Narrative','Indie'], 'DONTNOD Entertainment', 'Square Enix', '2015-01-29', 85, 8.8, 599, true, null, false, $f['single'], 'full', true, true, true),
            $this->game('Little Nightmares II', 860510, ['Horror','Platformer','Adventure'], 'Tarsier Studios', 'Bandai Namco Entertainment', '2021-02-10', 82, 8.4, 1499, true, 1499, false, $f['horror'], 'full', true, true, true),
            $this->game('Lies of P', 1627720, ['Souls-like','RPG','Action'], 'NEOWIZ', 'NEOWIZ', '2023-09-18', 80, 8.7, 2999, true, 2999, false, $f['single'], 'full', true, true, true),
            $this->game('Watch Dogs 2', 447040, ['Open World','Action','Adventure'], 'Ubisoft', 'Ubisoft', '2016-11-28', 82, 8.0, 1999, true, 1999, true, $f['multi'], 'full', true, true, true),
            $this->game('Far Cry 6', 2369390, ['Shooter','Open World','Action'], 'Ubisoft Toronto', 'Ubisoft', '2023-05-11', 74, 7.8, 2499, true, 2499, true, $f['coop'], 'full', true, true, true),
            $this->game('Just Cause 3', 225540, ['Open World','Action','Adventure'], 'Avalanche Studios', 'Square Enix', '2015-11-30', 74, 8.0, 999, true, 999, false, $f['single'], 'full', true, true, true),
            $this->game('Borderlands 2', 49520, ['Shooter','RPG','Co-op'], 'Gearbox Software', '2K', '2012-09-21', 89, 8.9, 999, true, 999, false, $f['coop'], 'full', true, true, true),
            $this->game('Borderlands 3', 397540, ['Shooter','RPG','Co-op'], 'Gearbox Software', '2K', '2020-03-13', 81, 8.2, 1999, true, 1999, true, $f['coop'], 'full', true, true, true),
            $this->game('Destiny 2', 1085660, ['Shooter','Multiplayer','Sci-Fi'], 'Bungie', 'Bungie', '2019-10-01', 83, 8.1, 0, true, 0, true, $f['multi'], 'full', true, true, true),
            $this->game('Dead by Daylight', 381210, ['Horror','Survival','Multiplayer'], 'Behaviour Interactive Inc.', 'Behaviour Interactive Inc.', '2016-06-14', 71, 8.0, 799, true, 799, true, $f['multi'], 'partial', true, true, false),
            $this->game('Disco Elysium', 632470, ['RPG','Detective','Narrative'], 'ZA/UM', 'ZA/UM', '2019-10-15', 91, 9.2, 725, true, 725, true, $f['single'], 'partial', true, true, false),
            $this->game('Outlast', 238320, ['Horror','Survival','Indie'], 'Red Barrels', 'Red Barrels', '2013-09-04', 80, 8.5, 499, true, 499, false, $f['horror'], 'partial', true, true, false),
            $this->game('Poppy Playtime', 1721470, ['Horror','Puzzle','Indie'], 'Mob Entertainment', 'Mob Entertainment', '2021-10-12', 70, 7.8, 0, true, 0, false, $f['horror'], 'partial', true, false, false, ['Puzzle']),
            $this->game('Need for Speed Unbound', 1846380, ['Racing','Action','Open World'], 'Criterion Games', 'Electronic Arts', '2022-12-01', 77, 7.9, 3499, true, 3499, false, $f['multi'], 'full', true, true, true),
            $this->game('Forza Horizon 6', 2483190, ['Racing','Open World','Multiplayer'], 'Playground Games', 'Xbox Game Studios', '2026-05-01', 90, 9.0, 6999, true, null, false, $f['multi'], 'full', true, true, true),
        ];
    }

    private function game(string $title, ?int $appid, array $genres, string $developer, string $publisher, string $release, int $meta, float $score, ?int $steamPrice, bool $steamAvailable, ?int $epicPrice, bool $epicAvailable, array $features, string $controller, bool $xbox, bool $ps, bool $recommendController, array $extraGenres = []): array
    {
        return compact('title', 'appid', 'genres', 'developer', 'publisher') + [
            'genres' => array_values(array_unique([...$genres, ...$extraGenres])),
            'release_date' => $release,
            'meta' => $meta,
            'score' => $score,
            'steam_price' => $steamPrice,
            'steam_available' => $steamAvailable,
            'epic_price' => $epicPrice,
            'epic_available' => $epicAvailable,
            'features' => $features,
            'controller' => $controller,
            'xbox' => $xbox,
            'playstation' => $ps,
            'recommend_controller' => $recommendController,
            'description' => $title.' — реальная игра в каталоге Game Hub. Страница показывает описание, медиа, системные требования, отзывы, поддержку контроллера, цены Steam/Epic и рекомендации похожих игр.',
        ];
    }

    private function cover(?int $appid, int $index): string
    {
        $manualCovers = [
            3280350 => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/3280350/6270c77b0729e2df0a17d660286eeddfd9169386/header.jpg?t=1774022345',
            2483190 => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2483190/27abb1584a118d50d0e3950fd48d557c51981db7/header.jpg?t=1778870245',
        ];

        if ($appid && isset($manualCovers[$appid])) {
            return $manualCovers[$appid];
        }

        if ($appid) {
            return "https://cdn.akamai.steamstatic.com/steam/apps/{$appid}/library_600x900_2x.jpg";
        }

        return 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=80&sig='.$index;
    }

    private function trailer(string $title): string
    {
        return 'https://www.youtube.com/embed?listType=search&list='.urlencode($title.' official trailer');
    }

    private function seedMedia(Game $game, array $data, int $index): void
    {
        $appid = $data['appid'] ?? null;
        $manualMedia = $this->manualMedia($game->slug);

        if ($manualMedia) {
            foreach ($manualMedia['images'] as $order => $url) {
                GameMedia::create(['game_id' => $game->id, 'type' => 'image', 'url' => $url, 'sort_order' => $order + 1]);
            }

            GameMedia::create(['game_id' => $game->id, 'type' => 'video', 'url' => $manualMedia['video'] ?? $this->trailer($game->title), 'sort_order' => 20]);

            return;
        }

        $details = $this->steamDetails($appid);

        if ($details && ! empty($details['screenshots'])) {
            foreach (array_slice($details['screenshots'], 0, 6) as $order => $screenshot) {
                GameMedia::create([
                    'game_id' => $game->id,
                    'type' => 'image',
                    'url' => $screenshot['path_full'] ?? $screenshot['path_thumbnail'],
                    'sort_order' => $order + 1,
                ]);
            }

            $movie = collect($details['movies'] ?? [])->first();
            $movieUrl = data_get($movie, 'mp4.max')
                ?: data_get($movie, 'webm.max')
                ?: data_get($movie, 'mp4.480')
                ?: data_get($movie, 'webm.480')
                ?: data_get($movie, 'hls_h264');

            if ($movieUrl) {
                GameMedia::create(['game_id' => $game->id, 'type' => 'video', 'url' => $movieUrl, 'sort_order' => 20]);
            } else {
                GameMedia::create(['game_id' => $game->id, 'type' => 'video', 'url' => $this->trailer($game->title), 'sort_order' => 20]);
            }

            return;
        }

        GameMedia::create(['game_id' => $game->id, 'type' => 'image', 'url' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=1200&q=80&sig='.$index, 'sort_order' => 1]);
        GameMedia::create(['game_id' => $game->id, 'type' => 'image', 'url' => 'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?auto=format&fit=crop&w=1200&q=80&sig='.$index, 'sort_order' => 2]);
        GameMedia::create(['game_id' => $game->id, 'type' => 'video', 'url' => $this->trailer($game->title), 'sort_order' => 3]);
    }

    private function manualMedia(string $slug): ?array
    {
        return [
            'death-stranding-2-on-the-beach' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/3280350/702c9ed8dc25f26be07539cd5cfb9f08046d210a/ss_702c9ed8dc25f26be07539cd5cfb9f08046d210a.1920x1080.jpg?t=1774022345',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/3280350/9732214efafbe68e6618806556cd448578217a04/ss_9732214efafbe68e6618806556cd448578217a04.1920x1080.jpg?t=1774022345',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/3280350/1a78ae746ca46713e3c2cb2e5c4f197fea72fe50/ss_1a78ae746ca46713e3c2cb2e5c4f197fea72fe50.1920x1080.jpg?t=1774022345',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/3280350/f8c42434fe7d51fdb3f039ca06a99f5f18518a31/ss_f8c42434fe7d51fdb3f039ca06a99f5f18518a31.1920x1080.jpg?t=1774022345',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/3280350/dc15199b7f71ce4279be8002e9e5c508ecde83bc/ss_dc15199b7f71ce4279be8002e9e5c508ecde83bc.1920x1080.jpg?t=1774022345',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/3280350/346a0dac8c589d8df5b208e5574cc75ab3d35fdf/ss_346a0dac8c589d8df5b208e5574cc75ab3d35fdf.1920x1080.jpg?t=1774022345',
                ],
                'video' => 'https://video.akamai.steamstatic.com/store_trailers/3280350/268802671/82382a7dc1240988a7faed880f0c90290e98061b/1773334709/hls_264_master.m3u8?t=1773930519',
            ],
            'forza-horizon-6' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2483190/706eb79418b2a74192de059693ddcbfaa803108a/ss_706eb79418b2a74192de059693ddcbfaa803108a.1920x1080.jpg?t=1778870245',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2483190/383ace7815eaa8f8f5128a58fd8ca7df911d3e14/ss_383ace7815eaa8f8f5128a58fd8ca7df911d3e14.1920x1080.jpg?t=1778870245',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2483190/d4b81f0cafcfba410371e53bd696fb5cc9b434c0/ss_d4b81f0cafcfba410371e53bd696fb5cc9b434c0.1920x1080.jpg?t=1778870245',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2483190/041b9a2854dc647bf54d7d8b36d2f4186ed72c13/ss_041b9a2854dc647bf54d7d8b36d2f4186ed72c13.1920x1080.jpg?t=1778870245',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2483190/945a08124625ffa26ec0077761fcbe19df07dad5/ss_945a08124625ffa26ec0077761fcbe19df07dad5.1920x1080.jpg?t=1778870245',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2483190/0824558c70b2e5ed67fe239d577434929f157a26/ss_0824558c70b2e5ed67fe239d577434929f157a26.1920x1080.jpg?t=1778870245',
                ],
                'video' => 'https://video.akamai.steamstatic.com/store_trailers/2483190/1133501958/842a46e433376224f42832ce35c55f1f85bbe440/1778255437/hls_264_master.m3u8?t=1778259303',
            ],
            'god-of-war' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1593500/ss_6eccc970b5de2943546d93d319be1b5c0618f21b.1920x1080.jpg?t=1763059412',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1593500/ss_f1bff24d3967a21d303d95e11ed892e3d9113057.1920x1080.jpg?t=1763059412',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1593500/ss_3670ba72c7e3e9c3c3225547ef2c1053504e62b8.1920x1080.jpg?t=1763059412',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1593500/ss_93a3ca63aa2cd8c675bbb6430324ee3f2d44b845.1920x1080.jpg?t=1763059412',
                ],
            ],
            'silent-hill-2' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2124490/ss_1fdda21610fa23d0ce20b5c44fab8aebd509c5cb.1920x1080.jpg?t=1744248682',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2124490/ss_b20363454a190d737e5ff8e6410d66f0034bd807.1920x1080.jpg?t=1744248682',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2124490/ss_7ba4f2993853179e3049a7f56d7b690b892f33bf.1920x1080.jpg?t=1744248682',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2124490/ss_002b780c7a34d50f186456adcc87dc6012741f97.1920x1080.jpg?t=1744248682',
                ],
            ],
            'god-of-war-ragnarok' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.1920x1080.jpg?t=1776465233',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_05f27139b15c5410d07cd59b7b52adbdf73e13da.1920x1080.jpg?t=1776465233',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_974a7b998c0c14da7fe52a342cf36c98850a57ac.1920x1080.jpg?t=1776465233',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_78350297511e81f287b4bc361935efbc3016f6db.1920x1080.jpg?t=1776465233',
                ],
            ],
            'far-cry-6' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2369390/ss_195eb286dad05d3b9e56f22eafacce7efe9c9ebf.1920x1080.jpg?t=1758656170',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2369390/ss_65c6467467795423bb959aa2c76ad2659f6553cd.1920x1080.jpg?t=1758656170',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2369390/ss_b0fa07116df319216ac4a4e7855a4c4a1d224bd0.1920x1080.jpg?t=1758656170',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2369390/ss_8bf4118728c0df8340c665329b78e428ed0a7c9f.1920x1080.jpg?t=1758656170',
                ],
            ],
            'dead-space' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1693980/ss_b4b02766216117747640523bb6e258d44053e355.1920x1080.jpg?t=1777396576',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1693980/ss_179cb4f80343d464ea2d7712b5982555271ad707.1920x1080.jpg?t=1777396576',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1693980/ss_06f8cc8ad27e53078b09df03c5c3fb1c75459960.1920x1080.jpg?t=1777396576',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1693980/ss_a7a0bceba764978d54ff0bb7ba0f0aa08487cd05.1920x1080.jpg?t=1777396576',
                ],
            ],
            'the-witcher-3-wild-hunt' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/292030/ss_5710298af2318afd9aa72449ef29ac4a2ef64d8e.1920x1080.jpg?t=1768303991',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/292030/ss_0901e64e9d4b8ebaea8348c194e7a3644d2d832d.1920x1080.jpg?t=1768303991',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/292030/ss_112b1e176c1bd271d8a565eacb6feaf90f240bb2.1920x1080.jpg?t=1768303991',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/292030/ss_d1b73b18cbcd5e9e412c7a1dead3c5cd7303d2ad.1920x1080.jpg?t=1768303991',
                ],
            ],
            'cyberpunk-2077' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1091500/ss_2f649b68d579bf87011487d29bc4ccbfdd97d34f.1920x1080.jpg?t=1769690377',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1091500/ss_0e64170751e1ae20ff8fdb7001a8892fd48260e7.1920x1080.jpg?t=1769690377',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1091500/ss_af2804aa4bf35d4251043744412ce3b359a125ef.1920x1080.jpg?t=1769690377',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1091500/ss_7924f64b6e5d586a80418c9896a1c92881a7905b.1920x1080.jpg?t=1769690377',
                ],
            ],
            'red-dead-redemption-2' => [
                'images' => [
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1174180/ss_66b553f4c209476d3e4ce25fa4714002cc914c4f.1920x1080.jpg?t=1759502961',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1174180/ss_bac60bacbf5da8945103648c08d27d5e202444ca.1920x1080.jpg?t=1759502961',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1174180/ss_668dafe477743f8b50b818d5bbfcec669e9ba93e.1920x1080.jpg?t=1759502961',
                    'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1174180/ss_4ce07ae360b166f0f650e9a895a3b4b7bf15e34f.1920x1080.jpg?t=1759502961',
                ],
            ],
            'doom-eternal' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/782330/ss_4f93a7c5003d49cb32f6c0c6e547452b284580a0.1920x1080.jpg?t=1755109910',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/782330/ss_7e6a2148321c8024285e3924903d8897cac95358.1920x1080.jpg?t=1755109910',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/782330/ss_af3b43c4be0029b52ceefaf55ebe1918e2cb3471.1920x1080.jpg?t=1755109910',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/782330/ss_ebd31ded4723e991446ededa9e65c980f988567d.1920x1080.jpg?t=1755109910',
            ]],
            'resident-evil-village' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1196590/ss_d25704b01be292d1337df4fea0fba2aab322b58a.1920x1080.jpg?t=1776927117',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1196590/ss_8113ec993ec474055c4cdce5ee86f91f7cf6663f.1920x1080.jpg?t=1776927117',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1196590/ss_50283e6df9d2f3f24ff4a1a36a94ae307e21cee8.1920x1080.jpg?t=1776927117',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1196590/ss_363d9c05ee0a974b766938610a3352e7a89b9c92.1920x1080.jpg?t=1776927117',
            ]],
            'fallout-4' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/377160/ss_f7861bd71e6c0c218d8ff69fb1c626aec0d187cf.1920x1080.jpg?t=1764687456',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/377160/ss_910437ac708aed7c028f6e43a6224c633d086b0a.1920x1080.jpg?t=1764687456',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/377160/ss_f649b8e57749f380cca225db5074edbb1e06d7f5.1920x1080.jpg?t=1764687456',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/377160/ss_c310f858e6a7b02ffa21db984afb0dd1b24c1423.1920x1080.jpg?t=1764687456',
            ]],
            'dying-light' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/239140/ss_a3771358b8eb4ea4c8f99c5850711f55b87804de.1920x1080.jpg?t=1776254860',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/239140/ss_1f0dc94f46fa1a953827188188887f6a12911ec2.1920x1080.jpg?t=1776254860',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/239140/ss_488226f013287012e0feaed2fb7799dbe038fd00.1920x1080.jpg?t=1776254860',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/239140/ss_37f8192f3bf85359ddee0b5c2e9a58eaaf6c4026.1920x1080.jpg?t=1776254860',
            ]],
            'dying-light-2-stay-human' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/534380/ss_df6aeb006060f7b26439f4bc7bee8b9e96c80e02.1920x1080.jpg?t=1772550031',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/534380/ss_9ba79964c3878648a1469d263df7fb17fc3d521c.1920x1080.jpg?t=1772550031',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/534380/ss_d7906b3946d4857d28c159e7a1555a003a4426f8.1920x1080.jpg?t=1772550031',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/534380/ss_6b8d4cc1f7d657745cfd7aab941d3be0067dec00.1920x1080.jpg?t=1772550031',
            ]],
            'sea-of-thieves' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1172620/ss_ec623c77d75dfa098c622b547b1ab21ad4cae0a8.1920x1080.jpg?t=1775816492',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1172620/ss_6ea04bdc415c336a195555aec4b97a73a9910fc1.1920x1080.jpg?t=1775816492',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1172620/ss_2144ac860fd64d82cb9cc49680f5087c7bb8fe2f.1920x1080.jpg?t=1775816492',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1172620/ss_4fb90cbac34d2cbe74b86383bda660cd0316b907.1920x1080.jpg?t=1775816492',
            ]],
            'mortal-kombat-11' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/976310/ss_e5cd8debd74027dbfafd9729fc32986a63393333.1920x1080.jpg?t=1775508340',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/976310/ss_4fa4fd2ea1b7ff6c6b699dc9eb717986f80845a4.1920x1080.jpg?t=1775508340',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/976310/ss_aa70f659fe14e3c07033474249096b60c17021b3.1920x1080.jpg?t=1775508340',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/976310/ss_b0e8bcfcebf910a6606a7903d2e23ec589c0c45b.1920x1080.jpg?t=1775508340',
            ]],
            'life-is-strange' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/319630/ss_d74ebed52e05e7c22937e53fcf6c7bf1de70ada1.1920x1080.jpg?t=1772725755',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/319630/ss_f0cbce81ce638fca6fa8154d6b5f0178e67eb87f.1920x1080.jpg?t=1772725755',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/319630/ss_2abb901703c73f9230d0ad42846c29d263825807.1920x1080.jpg?t=1772725755',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/319630/ss_f071f2da3d45953de69f00e05c6e333954ecdf26.1920x1080.jpg?t=1772725755',
            ]],
            'watch-dogs-2' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/447040/ss_8071f719fea2d45baa805449ec550395db700118.1920x1080.jpg?t=1751986887',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/447040/ss_b93d600b2a0372d6b5a5d191b46654ba489819d1.1920x1080.jpg?t=1751986887',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/447040/ss_6eb9108a5ac2f33942d15ebf0801f0e69373d4f8.1920x1080.jpg?t=1751986887',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/447040/ss_3466ea1a9e73594961b9f73fd560f379f7f49870.1920x1080.jpg?t=1751986887',
            ]],
            'borderlands-3' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/397540/ss_9868ee40f39749a4c8222502cf86525ee32c1bef.1920x1080.jpg?t=1750802377',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/397540/ss_f2053d688ec55f2269c47b24313539938bef9064.1920x1080.jpg?t=1750802377',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/397540/ss_624638e46ed590d4bb1835558a5ab0981f7baadd.1920x1080.jpg?t=1750802377',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/397540/ss_3531d9f91265d94fc06f6587eba1ca49f2c423d1.1920x1080.jpg?t=1750802377',
            ]],
            'dead-by-daylight' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/381210/5355ac410e985cac27f04b877280a984be4d28a6/ss_5355ac410e985cac27f04b877280a984be4d28a6.1920x1080.jpg?t=1778880603',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/381210/76776660827b2b6b73afc4a506c1267462cc70ff/ss_76776660827b2b6b73afc4a506c1267462cc70ff.1920x1080.jpg?t=1778880603',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/381210/141a099b39afb31b864e8eda9570c344ad0c70f5/ss_141a099b39afb31b864e8eda9570c344ad0c70f5.1920x1080.jpg?t=1778880603',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/381210/30fa7d276001c8b33a60dc1cd8683c6aae5c20b6/ss_30fa7d276001c8b33a60dc1cd8683c6aae5c20b6.1920x1080.jpg?t=1778880603',
            ]],
            'need-for-speed-unbound' => ['images' => [
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1846380/ss_e588ffa6c4a219f7d45e359d838075329fb75e93.1920x1080.jpg?t=1777394467',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1846380/ss_9363c669c80fec75684c22a2203d6490a176bf6c.1920x1080.jpg?t=1777394467',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1846380/ss_62104cb0a8f10f467f14976d3f042f7a97a1c1f9.1920x1080.jpg?t=1777394467',
                'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1846380/ss_415ed08e9e71eb7417329d47582ac131efa18503.1920x1080.jpg?t=1777394467',
            ]],
        ][$slug] ?? null;
    }

    private function steamDetails(?int $appid): ?array
    {
        static $cache = [];

        if (! $appid) {
            return null;
        }

        if (array_key_exists($appid, $cache)) {
            return $cache[$appid];
        }

        try {
            $response = Http::timeout(8)->retry(1, 250)->get('https://store.steampowered.com/api/appdetails', [
                'appids' => $appid,
                'filters' => 'basic,screenshots,movies',
            ]);

            $json = $response->json();
            $payload = $json[$appid] ?? $json[(string) $appid] ?? null;
            $cache[$appid] = ($payload['success'] ?? false) ? ($payload['data'] ?? null) : null;
        } catch (\Throwable) {
            $cache[$appid] = null;
        }

        return $cache[$appid];
    }

    private function seedPrices(Game $game, Store $steam, Store $epic, array $data): void
    {
        $epicPrice = $data['epic_price'];
        if ($data['epic_available'] && $epicPrice !== null && $data['steam_price'] !== null && (float) $epicPrice === (float) $data['steam_price'] && (float) $epicPrice > 0) {
            $offset = (((crc32($game->slug) % 7) - 3) * 120) ?: 180;
            $epicPrice = max(99, (int) $epicPrice + $offset);
        }

        GamePrice::create([
            'game_id' => $game->id,
            'store_id' => $steam->id,
            'price' => $data['steam_price'],
            'currency' => 'RUB',
            'is_available' => $data['steam_available'],
            'external_url' => $data['appid'] ? 'https://store.steampowered.com/app/'.$data['appid'] : 'https://store.steampowered.com/search/?term='.urlencode($game->title),
            'updated_at' => now(),
        ]);

        GamePrice::create([
            'game_id' => $game->id,
            'store_id' => $epic->id,
            'price' => $epicPrice,
            'currency' => 'RUB',
            'is_available' => $data['epic_available'],
            'external_url' => 'https://store.epicgames.com/browse?q='.urlencode($game->title),
            'updated_at' => now(),
        ]);
    }

    private function seedRequirements(Game $game, int $index, array $data): void
    {
        $heavy = in_array('Open World', $data['genres'], true) || in_array('Racing', $data['genres'], true);
        $vr = in_array('VR', $data['genres'], true);

        $game->systemRequirement()->create([
            'cpu_min' => $vr ? 'Intel Core i5-7500 / Ryzen 5 1600' : 'Intel Core i5-7400 / Ryzen 3 1200',
            'cpu_rec' => $heavy ? 'Intel Core i7-12700K / Ryzen 7 5800X' : 'Intel Core i7-10700 / Ryzen 5 5600',
            'gpu_min' => $vr ? 'GTX 1060 / RX 580' : 'GTX 970 / RX 570',
            'gpu_rec' => $heavy ? 'RTX 3070 / RX 6800 XT' : 'RTX 3060 / RX 6600 XT',
            'ram_min' => $heavy ? 12 : 8,
            'ram_rec' => $heavy ? 16 : 16,
            'storage_min' => 35 + ($index % 5) * 12,
            'storage_rec' => 55 + ($index % 5) * 18,
            'os_min' => 'Windows 10',
            'os_rec' => 'Windows 11',
            'directx_min' => 'DirectX 11',
            'directx_rec' => 'DirectX 12',
        ]);
    }
}
