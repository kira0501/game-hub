<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Genre;
use App\Models\Review;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function __invoke(Request $request, RecommendationService $recommendations)
    {
        return view('home', [
            'popularGames' => Game::active()->with(['genres', 'prices', 'media'])->orderByDesc('user_score_avg')->limit(8)->get(),
            'newGames' => Game::active()->with(['genres', 'prices', 'media'])->latest('release_date')->limit(8)->get(),
            'dealGames' => Game::active()
                ->with(['genres', 'prices', 'media'])
                ->whereHas('prices', fn ($query) => $query->where('is_available', true)->where('price', '<=', 1000))
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
