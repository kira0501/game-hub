<?php

namespace App\Services;

use App\Models\Game;
use App\Models\PcConfig;

class CompatibilityService
{
    public function check(Game $game, array|PcConfig $config): array
    {
        $requirements = $game->systemRequirement;

        if (! $requirements) {
            return [
                'compatible' => false,
                'level' => 'not_supported',
                'details' => [],
                'conclusion' => 'Для этой игры пока нет системных требований.',
            ];
        }

        $pc = $config instanceof PcConfig ? $config->toArray() : $config;

        $details = [
            'cpu' => $this->compareText($pc['cpu'] ?? '', $requirements->cpu_min, $requirements->cpu_rec, 'CPU'),
            'gpu' => $this->compareText($pc['gpu'] ?? '', $requirements->gpu_min, $requirements->gpu_rec, 'GPU'),
            'ram' => $this->compareNumber((int) ($pc['ram'] ?? 0), $requirements->ram_min, $requirements->ram_rec, 'RAM', 'GB'),
            'storage' => $this->compareNumber((int) ($pc['storage'] ?? 0), $requirements->storage_min, $requirements->storage_rec, 'Storage', 'GB'),
            'os' => $this->compareText($pc['os'] ?? '', $requirements->os_min, $requirements->os_rec, 'OS'),
        ];

        $failedMin = collect($details)->contains(fn ($item) => ! $item['min_pass']);
        $failedRec = collect($details)->contains(fn ($item) => ! $item['recommended_pass']);

        $level = $failedMin ? 'not_supported' : ($failedRec ? 'min' : 'recommended');

        return [
            'compatible' => ! $failedMin,
            'level' => $level,
            'details' => $details,
            'conclusion' => match ($level) {
                'recommended' => 'ПК уверенно подходит под рекомендуемые требования.',
                'min' => 'Игра запустится, но часть параметров ниже рекомендуемых.',
                default => 'ПК не проходит минимальные требования. Посмотрите параметры, отмеченные красным.',
            },
        ];
    }

    private function compareNumber(int $value, int $min, int $rec, string $label, string $unit): array
    {
        return [
            'label' => $label,
            'value' => $value.' '.$unit,
            'minimum' => $min.' '.$unit,
            'recommended' => $rec.' '.$unit,
            'min_pass' => $value >= $min,
            'recommended_pass' => $value >= $rec,
        ];
    }

    private function compareText(string $value, string $min, string $rec, string $label): array
    {
        $score = $this->hardwareScore($value);
        $minScore = $this->requirementScore($min);
        $recScore = $this->requirementScore($rec);

        return [
            'label' => $label,
            'value' => $value ?: 'Не указано',
            'minimum' => $min,
            'recommended' => $rec,
            'min_pass' => $value !== '' && $score >= $minScore,
            'recommended_pass' => $value !== '' && $score >= $recScore,
        ];
    }

    private function requirementScore(string $value): int
    {
        $alternatives = preg_split('/\s+\/\s+|\s+or\s+|\s+или\s+/iu', $value) ?: [];
        $scores = collect($alternatives)
            ->map(fn ($item) => trim($item))
            ->filter()
            ->map(fn ($item) => $this->hardwareScore($item))
            ->filter(fn ($score) => $score > 0);

        return $scores->min() ?: $this->hardwareScore($value);
    }

    private function hardwareScore(string $value): int
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/\b(direct3d|shader|vs|ps|dd3d|d3d)\w*\b/i', ' ', $value) ?? $value;

        preg_match_all('/\d+/', $value, $numbers);
        $score = array_sum(array_map('intval', $numbers[0] ?? []));

        if (str_contains($value, 'rtx')) {
            $score += 4000;
        } elseif (str_contains($value, 'gtx')) {
            $score += 2500;
        } elseif (str_contains($value, 'rx')) {
            $score += 2800;
        }

        if (str_contains($value, 'i9') || str_contains($value, 'ryzen 9')) {
            $score += 900;
        } elseif (str_contains($value, 'i7') || str_contains($value, 'ryzen 7')) {
            $score += 700;
        } elseif (str_contains($value, 'i5') || str_contains($value, 'ryzen 5')) {
            $score += 500;
        } elseif (str_contains($value, 'i3') || str_contains($value, 'ryzen 3')) {
            $score += 300;
        }

        if (str_contains($value, 'windows 11')) {
            $score += 110;
        } elseif (str_contains($value, 'windows 10')) {
            $score += 100;
        }

        return $score;
    }
}
