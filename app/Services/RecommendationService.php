<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Collection;

class RecommendationService
{
    public function __construct(private CompatibilityService $compatibilityService)
    {
    }

    public function forUser(?User $user, int $limit = 12): Collection
    {
        if (! $user) {
            return $this->fallback($limit);
        }

        $favoriteGameIds = $user->favorites()->pluck('game_id');
        $reviewedGameIds = $user->reviews()->pluck('game_id');
        $excluded = $favoriteGameIds->merge($reviewedGameIds)->unique();

        $favoriteGenres = $this->genreIdsForGames($favoriteGameIds);
        $reviewedGenres = $this->genreIdsForGames($reviewedGameIds);
        $pcConfig = $user->pcConfigs()->latest()->first();

        if ($favoriteGameIds->isEmpty() && $reviewedGameIds->isEmpty()) {
            return $this->fallback($limit);
        }

        $recommendations = Game::query()
            ->active()
            ->with(['genres', 'prices', 'media', 'systemRequirement'])
            ->whereNotIn('id', $excluded)
            ->get()
            ->map(function (Game $game) use ($favoriteGenres, $reviewedGenres, $pcConfig, $user) {
                $score = 0;
                $reasons = [];
                $tags = [];
                $gameGenreIds = $game->genres->pluck('id');

                if ($gameGenreIds->intersect($favoriteGenres)->isNotEmpty()) {
                    $score += 40;
                    $reasons[] = 'Похожа на игры из избранного';
                    $tags[] = 'жанры';
                }

                if ($gameGenreIds->intersect($reviewedGenres)->isNotEmpty()) {
                    $score += 25;
                    $reasons[] = 'Близка к играм, которые вы оценивали';
                    $tags[] = 'отзывы';
                }

                if ($game->user_score_avg >= 8.4) {
                    $score += 20;
                    $reasons[] = 'Высокая оценка игроков';
                    $tags[] = 'рейтинг';
                }

                if ($pcConfig && $game->systemRequirement) {
                    $compatibility = $this->compatibilityService->check($game, $pcConfig);
                    if ($compatibility['compatible']) {
                        $score += $compatibility['level'] === 'recommended' ? 15 : 8;
                        $reasons[] = $compatibility['level'] === 'recommended' ? 'Хорошо подходит под ваш ПК' : 'Должна запуститься на вашем ПК';
                        $tags[] = 'ПК';
                    }
                }

                $bestPrice = $game->prices->where('is_available', true)->whereNotNull('price')->sortBy('price')->first();
                if ($bestPrice && $bestPrice->price <= 1999 && $game->user_score_avg >= 8.0) {
                    $score += 10;
                    $reasons[] = 'Хорошая цена для такой оценки';
                    $tags[] = 'цена';
                }

                $approvedReviews = $game->reviews()->where('status', 'approved')->count();
                if ($approvedReviews >= 3) {
                    $score += 5;
                    $reasons[] = 'Есть активность игроков';
                    $tags[] = 'популярно';
                }

                return [
                    'game' => $game,
                    'score' => $score,
                    'reason' => $this->mainReason($reasons),
                    'tags' => array_values(array_unique($tags)),
                ];
            })
            ->filter(fn ($item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values();

        $this->sync($user, $recommendations);

        return $recommendations->isNotEmpty() ? $recommendations : $this->fallback($limit);
    }

    private function genreIdsForGames(Collection $gameIds): Collection
    {
        return Game::query()
            ->whereIn('id', $gameIds)
            ->with('genres:id')
            ->get()
            ->flatMap(fn (Game $game) => $game->genres->pluck('id'))
            ->unique()
            ->values();
    }

    private function fallback(int $limit): Collection
    {
        return Game::query()
            ->active()
            ->with(['genres', 'prices', 'media'])
            ->orderByDesc('user_score_avg')
            ->orderByDesc('metacritic_score')
            ->latest('release_date')
            ->limit($limit)
            ->get()
            ->map(fn (Game $game) => [
                'game' => $game,
                'score' => (int) round($game->user_score_avg * 10),
                'reason' => 'Популярная игра с высокой оценкой',
                'tags' => ['рейтинг', 'популярно'],
            ]);
    }

    private function mainReason(array $reasons): string
    {
        return array_values(array_unique($reasons))[0] ?? 'Подходит по общему профилю каталога';
    }

    private function sync(User $user, Collection $items): void
    {
        foreach ($items as $item) {
            Recommendation::updateOrCreate(
                ['user_id' => $user->id, 'game_id' => $item['game']->id],
                ['score' => $item['score'], 'reason' => $item['reason']]
            );
        }
    }
}
