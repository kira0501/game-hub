<?php

namespace App\Http\Controllers;

use App\Http\Requests\PcConfigRequest;
use App\Models\Game;
use App\Models\PcConfig;
use App\Services\CompatibilityService;
use Illuminate\Http\Request;

class PcConfigController extends Controller
{
    public function index(Request $request)
    {
        return view('pc-check', [
            'configs' => $request->user()?->pcConfigs()->latest()->get() ?? collect(),
            'games' => Game::active()->orderBy('title')->get(),
            'result' => session('compatibility'),
        ]);
    }

    public function store(PcConfigRequest $request)
    {
        $request->user()->pcConfigs()->create($request->validated());

        return back()->with('status', 'Конфигурация ПК сохранена.');
    }

    public function update(PcConfigRequest $request, PcConfig $pcConfig)
    {
        abort_unless($pcConfig->user_id === $request->user()->id, 403);
        $pcConfig->update($request->validated());

        return back()->with('status', 'Конфигурация обновлена.');
    }

    public function destroy(Request $request, PcConfig $pcConfig)
    {
        abort_unless($pcConfig->user_id === $request->user()->id, 403);
        $pcConfig->delete();

        return back()->with('status', 'Конфигурация ПК удалена. Теперь можно создать новую.');
    }

    public function compare(PcConfigRequest $request, CompatibilityService $service)
    {
        $data = $request->validated();
        $request->validate(['game_id' => ['required', 'exists:games,id']]);

        $game = Game::with('systemRequirement')->findOrFail($data['game_id']);
        $result = $service->check($game, $data);

        return back()->with('compatibility', ['game' => $game->title, ...$result]);
    }

    public function quickCheck(Request $request, Game $game, PcConfig $pcConfig, CompatibilityService $service)
    {
        abort_unless($pcConfig->user_id === $request->user()->id, 403);

        $result = $service->check($game->load('systemRequirement'), $pcConfig);

        return response()->json([
            ...$result,
            'title' => match ($result['level']) {
                'recommended' => 'Полностью подходит',
                'min' => 'Подходит, но не полностью',
                default => 'ПК не подходит',
            },
        ]);
    }
}
