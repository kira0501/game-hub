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
            'prices' => GamePrice::with(['game', 'store'])->latest('updated_at')->paginate(25),
        ]);
    }

    public function create()
    {
        return view('admin.prices.form', [
            'price' => new GamePrice(['currency' => 'RUB', 'is_available' => true]),
            'games' => Game::orderBy('title')->get(),
            'stores' => Store::orderBy('name')->get(),
        ]);
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
                'external_url' => $data['external_url'] ?? null,
                'updated_at' => now(),
            ]
        );

        return redirect()->route('admin.prices.index')->with('status', 'Цена сохранена.');
    }

    public function edit(GamePrice $price)
    {
        return view('admin.prices.form', [
            'price' => $price,
            'games' => Game::orderBy('title')->get(),
            'stores' => Store::orderBy('name')->get(),
        ]);
    }

    public function update(AdminPriceRequest $request, GamePrice $price)
    {
        $data = $request->validated();

        $price->update([
            'game_id' => $data['game_id'],
            'store_id' => $data['store_id'],
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'],
            'is_available' => $request->boolean('is_available'),
            'external_url' => $data['external_url'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.prices.index')->with('status', 'Цена обновлена.');
    }

    public function destroy(GamePrice $price)
    {
        $price->delete();

        return back()->with('status', 'Цена удалена.');
    }
}
