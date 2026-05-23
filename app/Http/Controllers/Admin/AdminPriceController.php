<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPriceRequest;
use App\Models\Game;
use App\Models\GamePrice;
use App\Models\Store;

class AdminPriceController extends Controller
{
    public function index()
    {
        return view('admin.prices.index', [
            'games' => Game::with(['prices.store'])->orderBy('title')->paginate(20),
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.prices.index');
    }

    public function store(AdminPriceRequest $request)
    {
        $data = $request->validated();

        GamePrice::updateOrCreate(
            ['game_id' => $data['game_id'], 'store_id' => $data['store_id']],
            [
                'price' => $data['price'] ?? null,
                'currency' => $data['currency'],
                'is_available' => $request->boolean('is_available'),
                'discount_percent' => $data['discount_percent'] ?? 0,
                'external_url' => $data['external_url'] ?? null,
                'updated_at' => now(),
            ]
        );

        return redirect()->route('admin.prices.index')->with('status', 'Цена сохранена.');
    }

    public function edit(GamePrice $price)
    {
        return redirect()->route('admin.prices.games.edit', $price->game);
    }

    public function update(AdminPriceRequest $request, GamePrice $price)
    {
        return redirect()->route('admin.prices.games.edit', $price->game);
    }

    public function destroy(GamePrice $price)
    {
        $price->delete();

        return back()->with('status', 'Цена удалена.');
    }

    public function editGame(Game $game)
    {
        return view('admin.prices.form', [
            'game' => $game->load('prices.store'),
            'stores' => Store::orderBy('name')->get(),
        ]);
    }

    public function updateGame(\Illuminate\Http\Request $request, Game $game)
    {
        $data = $request->validate([
            'prices' => ['array'],
            'prices.*.price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'prices.*.currency' => ['required', 'string', 'max:8'],
            'prices.*.discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'prices.*.is_available' => ['nullable', 'boolean'],
            'prices.*.external_url' => ['nullable', 'url', 'max:500'],
        ]);

        $this->saveStorePrices($game, $data);

        return redirect()->route('admin.prices.index')->with('status', 'Цены обновлены.');
    }

    private function saveStorePrices(Game $game, array $data): void
    {
        foreach ($data['prices'] ?? [] as $storeId => $priceData) {
            GamePrice::updateOrCreate(
                ['game_id' => $game->id, 'store_id' => $storeId],
                [
                    'price' => $priceData['price'] ?? null,
                    'currency' => $priceData['currency'] ?? 'RUB',
                    'discount_percent' => $priceData['discount_percent'] ?? 0,
                    'is_available' => (bool) ($priceData['is_available'] ?? false),
                    'external_url' => $priceData['external_url'] ?? null,
                    'updated_at' => now(),
                ]
            );
        }
    }
}
