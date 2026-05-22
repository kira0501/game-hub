<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'gamesCount' => Game::count(),
            'usersCount' => User::count(),
            'reviewsCount' => Review::count(),
            'topGames' => Game::orderByDesc('user_score_avg')->limit(5)->get(),
            'popularGenres' => Genre::withCount('games')->orderByDesc('games_count')->limit(6)->get(),
            'gamesWithoutPrices' => Game::doesntHave('prices')->limit(8)->get(),
            'latestGames' => Game::latest()->limit(6)->get(),
        ]);
    }
}
