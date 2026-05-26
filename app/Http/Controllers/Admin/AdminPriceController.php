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

        $price = GamePrice::firstOrNew(['game_id' => $data['game_id'], 'store_id' => $data['store_id']]);
        $price->fill($this->pricePayload($price, [
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'],
            'is_available' => $request->boolean('is_available'),
            'discount_percent' => $data['discount_percent'] ?? 0,
            'external_url' => $data['external_url'] ?? null,
        ]))->save();

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
            $price = GamePrice::firstOrNew(['game_id' => $game->id, 'store_id' => $storeId]);
            $price->fill($this->pricePayload($price, [
                'price' => $priceData['price'] ?? null,
                'currency' => $priceData['currency'] ?? 'RUB',
                'discount_percent' => $priceData['discount_percent'] ?? 0,
                'is_available' => (bool) ($priceData['is_available'] ?? false),
                'external_url' => $priceData['external_url'] ?? null,
            ]))->save();
        }
    }

    private function pricePayload(GamePrice $price, array $data): array
    {
        $oldPrice = $price->exists && $price->price !== null ? (float) $price->price : null;
        $newPrice = $data['price'] !== null && $data['price'] !== '' ? (float) $data['price'] : null;
        $changed = $newPrice !== null && ($oldPrice === null || abs($oldPrice - $newPrice) > 0.009);
        $discount = (int) ($data['discount_percent'] ?? 0);

        if ($discount <= 0 && $newPrice !== null) {
            $referencePrice = $changed ? $oldPrice : ($price->previous_price !== null ? (float) $price->previous_price : null);
            $discount = $this->discountFromPrices($referencePrice, $newPrice);
        }

        return [
            'price' => $newPrice,
            'previous_price' => $changed ? $oldPrice : $price->previous_price,
            'currency' => $data['currency'] ?? 'RUB',
            'discount_percent' => $discount,
            'is_available' => (bool) ($data['is_available'] ?? false),
            'external_url' => $data['external_url'] ?? null,
            'price_changed_at' => $changed ? now() : $price->price_changed_at,
            'updated_at' => now(),
        ];
    }

    private function discountFromPrices(?float $oldPrice, float $newPrice): int
    {
        if (! $oldPrice || $oldPrice <= 0 || $newPrice >= $oldPrice) {
            return 0;
        }

        return max(1, min(99, (int) round((1 - $newPrice / $oldPrice) * 100)));
    }
}
