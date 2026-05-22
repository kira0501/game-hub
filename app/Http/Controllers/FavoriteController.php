<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        return view('favorites.index', [
            'games' => $request->user()->favoriteGames()->with(['genres', 'prices'])->paginate(12),
        ]);
    }

    public function toggle(Request $request, Game $game): RedirectResponse
    {
        $favorite = Favorite::where('user_id', $request->user()->id)->where('game_id', $game->id)->first();

        if ($favorite) {
            $favorite->delete();
            return back()->with('status', 'Игра удалена из избранного.');
        }

        Favorite::create(['user_id' => $request->user()->id, 'game_id' => $game->id]);

        return back()->with('status', 'Игра добавлена в избранное.');
    }

    public function destroySelected(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'game_ids' => ['required', 'array'],
            'game_ids.*' => ['integer', 'exists:games,id'],
        ]);

        Favorite::where('user_id', $request->user()->id)
            ->whereIn('game_id', $data['game_ids'])
            ->delete();

        return back()->with('status', 'Выбранные игры удалены из избранного.');
    }
}
