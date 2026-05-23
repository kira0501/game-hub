<?php

use App\Models\Game;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Game::query()
            ->with('media')
            ->where(function ($query) {
                $query->whereNull('hero_image')
                    ->orWhereNull('carousel_image');
            })
            ->chunkById(50, function ($games) {
                foreach ($games as $game) {
                    $firstGalleryImage = $game->media
                        ->where('type', 'image')
                        ->where('role', 'gallery')
                        ->sortBy('sort_order')
                        ->first()?->url;

                    $game->forceFill([
                        'hero_image' => $game->hero_image ?: $firstGalleryImage,
                        'carousel_image' => $game->carousel_image ?: $game->hero_image ?: $firstGalleryImage,
                    ])->save();
                }
            });
    }

    public function down(): void
    {
        // Backfill is intentionally not reverted to avoid deleting admin-selected images.
    }
};
