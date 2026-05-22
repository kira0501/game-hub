<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminGameRequest;
use App\Models\Game;
use App\Models\Genre;

class AdminGameController extends Controller
{
    public function index()
    {
        return view('admin.games.index', [
            'games' => Game::with('genres')->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.games.form', [
            'game' => new Game(),
            'genres' => Genre::orderBy('name')->get(),
        ]);
    }

    public function store(AdminGameRequest $request)
    {
        $data = $request->validated();
        $game = Game::create($this->gameData($data));
        $game->genres()->sync($data['genres'] ?? []);
        $game->systemRequirement()->create($this->requirementsData($data));

        return redirect()->route('admin.games.index')->with('status', 'Игра создана.');
    }

    public function edit(Game $game)
    {
        return view('admin.games.form', [
            'game' => $game->load('systemRequirement', 'genres'),
            'genres' => Genre::orderBy('name')->get(),
        ]);
    }

    public function update(AdminGameRequest $request, Game $game)
    {
        $data = $request->validated();
        $game->update($this->gameData($data));
        $game->genres()->sync($data['genres'] ?? []);
        $game->systemRequirement()->updateOrCreate(['game_id' => $game->id], $this->requirementsData($data));

        return redirect()->route('admin.games.index')->with('status', 'Игра обновлена.');
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return back()->with('status', 'Игра удалена.');
    }

    private function gameData(array $data): array
    {
        return collect($data)->only([
            'title',
            'slug',
            'description',
            'cover',
            'trailer_url',
            'developer',
            'publisher',
            'release_date',
            'metacritic_score',
            'user_score_avg',
            'controller_support',
        ])->merge([
            'play_features' => $data['play_features'] ?? [],
            'supports_xbox_controller' => (bool) ($data['supports_xbox_controller'] ?? false),
            'supports_playstation_controller' => (bool) ($data['supports_playstation_controller'] ?? false),
            'developer_recommends_controller' => (bool) ($data['developer_recommends_controller'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ])->all();
    }

    private function requirementsData(array $data): array
    {
        return collect($data)->only([
            'cpu_min',
            'cpu_rec',
            'gpu_min',
            'gpu_rec',
            'ram_min',
            'ram_rec',
            'storage_min',
            'storage_rec',
            'os_min',
            'os_rec',
            'directx_min',
            'directx_rec',
        ])->all();
    }
}
