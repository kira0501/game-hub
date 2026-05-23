<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Genre;
use App\Services\PriceComparisonService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::query()
            ->active()
            ->with(['genres', 'prices.store', 'media'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.(string) $request->string('q').'%';

                $q->where(function ($query) use ($term) {
                    $query->where('title', 'like', $term)
                        ->orWhere('developer', 'like', $term)
                        ->orWhere('publisher', 'like', $term)
                        ->orWhereHas('genres', fn ($genre) => $genre->where('name', 'like', $term));
                });
            })
            ->when($request->filled('genre'), fn ($q) => $q->whereHas('genres', fn ($g) => $g->where('slug', (string) $request->string('genre'))))
            ->when($request->filled('min_score'), fn ($q) => $q->where('user_score_avg', '>=', (float) $request->min_score))
            ->when($request->filled('max_price'), fn ($q) => $q->whereHas('prices', fn ($p) => $p->where('is_available', true)->where('price', '<=', (float) $request->max_price)));

        match ($request->get('sort')) {
            'new' => $query->latest('release_date'),
            'price' => $query->withMin('prices', 'price')->orderBy('prices_min_price'),
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('user_score_avg'),
        };

        return view('games.index', [
            'games' => $query->paginate(12)->withQueryString(),
            'genres' => Genre::orderBy('name')->get(),
        ]);
    }

    public function suggest(Request $request)
    {
        $term = trim((string) $request->query('q'));

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $like = '%'.$term.'%';

        return Game::query()
            ->active()
            ->with(['genres', 'prices.store'])
            ->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('developer', 'like', $like)
                    ->orWhere('publisher', 'like', $like)
                    ->orWhereHas('genres', fn ($genre) => $genre->where('name', 'like', $like));
            })
            ->orderByDesc('user_score_avg')
            ->limit(8)
            ->get()
            ->map(function (Game $game) {
                $best = $game->prices->where('is_available', true)->whereNotNull('price')->sortBy('price')->first();

                return [
                    'title' => $game->title,
                    'url' => route('games.show', $game->slug),
                    'image' => $game->cover ?: $game->hero_image ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=300&q=80',
                    'meta' => $game->genres->take(2)->pluck('name')->join(' • ') ?: $game->developer,
                    'price' => $best ? ((float) $best->price === 0.0 ? 'Бесплатно' : number_format((float) $best->price, 0, '.', ' ').' '.$best->currency) : 'Нет цены',
                    'discount' => $best && $best->discount_percent ? $best->discount_percent : null,
                ];
            });
    }

    public function show(string $slug, PriceComparisonService $prices)
    {
        $game = Game::query()
            ->active()
            ->with(['genres', 'media', 'systemRequirement', 'reviews.user', 'prices.store'])
            ->where('slug', $slug)
            ->firstOrFail();

        $similar = Game::query()
            ->active()
            ->where('id', '!=', $game->id)
            ->whereHas('genres', fn ($q) => $q->whereIn('genres.id', $game->genres->pluck('id')))
            ->with(['genres', 'prices', 'media'])
            ->limit(6)
            ->get();

        return view('games.show', [
            'game' => $game,
            'similar' => $similar,
            'priceComparison' => $prices->forGame($game),
            'isFavorite' => request()->user()?->favoriteGames()->where('games.id', $game->id)->exists() ?? false,
            'pcConfigs' => request()->user()?->pcConfigs()->latest()->get() ?? collect(),
        ]);
    }

    public function genre(string $slug)
    {
        $genre = Genre::where('slug', $slug)->firstOrFail();

        return view('genres.show', [
            'genre' => $genre,
            'games' => $genre->games()->active()->with(['genres', 'prices', 'media'])->paginate(12),
        ]);
    }
}
