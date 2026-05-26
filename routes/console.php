<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\GamePrice;
use App\Services\StorePriceUpdateService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('prices:update {--store= : steam, epic или пусто для всех}', function (StorePriceUpdateService $service) {
    $store = strtolower((string) $this->option('store'));
    $query = GamePrice::query()->with(['game', 'store']);

    if ($store !== '') {
        $query->whereHas('store', fn ($q) => $q->where('slug', 'like', '%'.$store.'%'));
    }

    $updated = 0;
    $changed = 0;
    $dropped = 0;
    $skipped = 0;

    $query->orderBy('id')->chunkById(25, function ($prices) use ($service, &$updated, &$changed, &$dropped, &$skipped) {
        foreach ($prices as $price) {
            $result = $service->update($price);

            if ($result['status'] === 'updated') {
                $updated++;
                $changed += (int) ($result['changed'] ?? false);
                $dropped += (int) ($result['dropped'] ?? false);
                $this->line("✓ {$price->game->title} / {$price->store->name}");
            } else {
                $skipped++;
                $this->warn("• {$price->game->title} / {$price->store->name}: {$result['message']}");
            }
        }
    });

    $this->info("Готово: обновлено {$updated}, изменилось {$changed}, цена снизилась {$dropped}, пропущено {$skipped}.");
})->purpose('Update Steam and Epic Games prices')->dailyAt('04:00');
