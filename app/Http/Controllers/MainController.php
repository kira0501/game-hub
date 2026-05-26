<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Genre;
use App\Models\Review;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MainController extends Controller
{
    public function __invoke(Request $request, RecommendationService $recommendations)
    {
        $baseGameQuery = fn () => Game::active()->with(['genres', 'prices', 'media']);
        $hasDisplayImages = Schema::hasColumn('games', 'carousel_image')
            && Schema::hasColumn('games', 'hero_image');
        $carouselGames = $baseGameQuery()
            ->when($hasDisplayImages, function ($query) {
                $query->where(function ($query) {
                    $query->whereNotNull('carousel_image')
                        ->orWhereNotNull('hero_image')
                        ->orWhereHas('media', fn ($media) => $media->where('type', 'image'));
                });
            }, function ($query) {
                $query->whereHas('media', fn ($media) => $media->where('type', 'image'));
            })
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('home', [
            'carouselGames' => $carouselGames,
            'popularGames' => $baseGameQuery()->orderByDesc('user_score_avg')->limit(8)->get(),
            'newGames' => $baseGameQuery()->latest('release_date')->limit(8)->get(),
            'dealGames' => $baseGameQuery()
                ->whereHas('prices', function ($query) {
                    $query->where('is_available', true)
                        ->whereNotNull('price')
                        ->where(function ($query) {
                            $query->where('discount_percent', '>', 0)
                                ->orWhereColumn('price', '<', 'previous_price')
                                ->orWhere('price', '<=', 1000);
                        });
                })
                ->withMax('prices as best_discount_percent', 'discount_percent')
                ->orderByDesc('best_discount_percent')
                ->orderByDesc('user_score_avg')
                ->limit(4)
                ->get(),
            'recommended' => $recommendations->forUser($request->user(), 8),
            'genres' => Genre::withCount('games')->orderByDesc('games_count')->limit(10)->get(),
            'stats' => [
                'games' => Game::active()->count(),
                'reviews' => Review::where('status', 'approved')->count(),
                'genres' => Genre::count(),
            ],
        ]);
    }
}
