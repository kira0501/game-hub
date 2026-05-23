<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $covers = [
            'Forza Horizon 6' => 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2483190/711e39803402def82a90e1f31578c64744f952f3/library_capsule_2x.jpg',
            'Death Stranding 2: On the Beach' => 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/3280350/9b523fd411eeaefe80f238489745325a1cd2317f/library_capsule_2x.jpg',
        ];

        foreach ($covers as $title => $cover) {
            DB::table('games')
                ->where('title', $title)
                ->update([
                    'cover' => $cover,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Data repair only. It is intentionally not reverted.
    }
};
