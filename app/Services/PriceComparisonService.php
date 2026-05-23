<?php

namespace App\Services;

use App\Models\Game;
use App\Models\GamePrice;

class PriceComparisonService
{
    public function forGame(Game $game): array
    {
        $prices = $game->prices()->with('store')->get();
        $available = $prices->where('is_available', true)->whereNotNull('price');
        $best = $available->sortBy('price')->first();

        return [
            'rows' => $prices->map(fn (GamePrice $price) => [
                'store' => $price->store->name,
                'logo' => $price->store->logo,
                'price' => $price->price,
                'discount_percent' => $price->discount_percent,
                'currency' => $price->currency,
                'is_available' => $price->is_available,
                'external_url' => $price->external_url,
                'difference' => $best && $price->price ? round($price->price - $best->price, 2) : null,
                'is_best' => $best?->id === $price->id,
            ])->values(),
            'best' => $best,
        ];
    }

    public function table()
    {
        return Game::query()
            ->active()
            ->with(['prices.store', 'genres'])
            ->orderByDesc('user_score_avg')
            ->get()
            ->map(function (Game $game) {
                $comparison = $this->forGame($game);

                return [
                    'game' => $game,
                    'prices' => $comparison['rows'],
                    'best' => $comparison['best'],
                ];
            });
    }
}
