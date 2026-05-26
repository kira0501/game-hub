<?php

namespace App\Services;

use App\Models\GamePrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StorePriceUpdateService
{
    public function update(GamePrice $price): array
    {
        $slug = strtolower($price->store->slug);

        return match (true) {
            str_contains($slug, 'steam') => $this->updateSteam($price),
            str_contains($slug, 'epic') => $this->updateEpic($price),
            default => $this->markSkipped($price, 'Автообновление для этого магазина не настроено.'),
        };
    }

    private function updateSteam(GamePrice $price): array
    {
        $appid = $this->steamAppId($price->external_url);

        if (! $appid) {
            return $this->markSkipped($price, 'Не найден Steam appid в ссылке.');
        }

        $response = Http::timeout(10)->retry(1, 300)->get('https://store.steampowered.com/api/appdetails', [
            'appids' => $appid,
            'cc' => 'ru',
            'l' => 'russian',
            'filters' => 'basic,price_overview',
        ]);

        $payload = $response->json((string) $appid);

        if (! ($payload['success'] ?? false)) {
            return $this->markSkipped($price, 'Steam не вернул данные по игре.');
        }

        $data = $payload['data'] ?? [];
        $overview = $data['price_overview'] ?? null;

        if (($data['is_free'] ?? false) === true) {
            return $this->applyPrice($price, 0, 'RUB', 0, true);
        }

        if (! $overview || ! isset($overview['final'])) {
            return $this->markSkipped($price, 'У Steam нет цены для этой игры в регионе RU.');
        }

        return $this->applyPrice(
            $price,
            ((int) $overview['final']) / 100,
            $overview['currency'] ?? 'RUB',
            (int) ($overview['discount_percent'] ?? 0),
            true
        );
    }

    private function updateEpic(GamePrice $price): array
    {
        $response = Http::timeout(12)->retry(1, 300)->post('https://store.epicgames.com/graphql', [
            'query' => <<<'GRAPHQL'
query searchStoreQuery($keywords: String!, $country: String!, $locale: String!, $count: Int!, $start: Int!) {
  Catalog {
    searchStore(keywords: $keywords, country: $country, locale: $locale, count: $count, start: $start, withPrice: true) {
      elements {
        title
        productSlug
        urlSlug
        price(country: $country) {
          totalPrice {
            discountPrice
            originalPrice
            currencyCode
          }
        }
      }
    }
  }
}
GRAPHQL,
            'variables' => [
                'keywords' => $price->game->title,
                'country' => 'RU',
                'locale' => 'ru-RU',
                'count' => 5,
                'start' => 0,
            ],
        ]);

        if (! $response->ok()) {
            return $this->markSkipped($price, 'Epic Games не ответил на запрос цены.');
        }

        $elements = data_get($response->json(), 'data.Catalog.searchStore.elements', []);
        $offer = collect($elements)->first(fn ($item) => $this->sameTitle($item['title'] ?? '', $price->game->title))
            ?? collect($elements)->first();

        $total = data_get($offer, 'price.totalPrice');

        if (! $offer || ! $total || ! isset($total['discountPrice'])) {
            return $this->markSkipped($price, 'Epic Games не вернул цену по поиску.');
        }

        $discountPrice = ((int) $total['discountPrice']) / 100;
        $originalPrice = ((int) ($total['originalPrice'] ?? $total['discountPrice'])) / 100;
        $discount = $originalPrice > 0 && $discountPrice < $originalPrice
            ? (int) round((1 - $discountPrice / $originalPrice) * 100)
            : 0;

        return $this->applyPrice(
            $price,
            $discountPrice,
            $total['currencyCode'] ?? 'RUB',
            $discount,
            true
        );
    }

    private function applyPrice(GamePrice $price, float $newPrice, string $currency, int $discountPercent, bool $isAvailable): array
    {
        $oldPrice = $price->price !== null ? (float) $price->price : null;
        $changed = $oldPrice === null || abs($oldPrice - $newPrice) > 0.009;

        $price->fill([
            'previous_price' => $changed ? $oldPrice : $price->previous_price,
            'price' => $newPrice,
            'currency' => $currency,
            'discount_percent' => $discountPercent,
            'is_available' => $isAvailable,
            'price_changed_at' => $changed ? now() : $price->price_changed_at,
            'updated_at' => now(),
            'last_checked_at' => now(),
            'auto_update_error' => null,
        ])->save();

        return [
            'status' => 'updated',
            'changed' => $changed,
            'dropped' => $oldPrice !== null && $newPrice < $oldPrice,
        ];
    }

    private function markSkipped(GamePrice $price, string $message): array
    {
        $price->forceFill([
            'last_checked_at' => now(),
            'auto_update_error' => $message,
        ])->save();

        return ['status' => 'skipped', 'message' => $message];
    }

    private function steamAppId(?string $url): ?string
    {
        return $url && preg_match('~/app/(\d+)~', $url, $matches) ? $matches[1] : null;
    }

    private function sameTitle(string $candidate, string $title): bool
    {
        return Str::of($candidate)->lower()->squish()->value() === Str::of($title)->lower()->squish()->value();
    }
}
