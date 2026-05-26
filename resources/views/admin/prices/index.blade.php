@extends('layouts.admin')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-black">Цены</h1>
        <p class="mt-2 text-sm text-slate-400">Одна строка — одна игра. Steam и Epic редактируются вместе.</p>
    </div>
</div>

<div class="mt-6 grid gap-3">
    @foreach($games as $game)
        @php
            $steam = $game->prices->first(fn ($price) => str_contains(strtolower($price->store->slug), 'steam'));
            $epic = $game->prices->first(fn ($price) => str_contains(strtolower($price->store->slug), 'epic'));
            $formatPrice = function ($price) {
                if (! $price || ! $price->is_available || $price->price === null) {
                    return 'Недоступно';
                }

                $label = number_format((float) $price->price, 0, '.', ' ') . ' ' . $price->currency;

                if ($price->discount_percent) {
                    $label .= ' · -'.$price->discount_percent.'%';
                }

                if ($price->price_dropped) {
                    $label .= ' · цена снизилась';
                }

                return $label;
            };
        @endphp
        <div class="rounded-lg border border-white/10 bg-white/5 p-4">
            <div class="grid gap-4 md:grid-cols-[1fr_220px] md:items-center">
                <div>
                    <h2 class="font-black text-white">{{ $game->title }}</h2>
                    <div class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                        <p class="rounded bg-black/20 px-3 py-2"><span class="text-slate-400">Steam:</span> <b class="text-cyan-200">{{ $formatPrice($steam) }}</b></p>
                        <p class="rounded bg-black/20 px-3 py-2"><span class="text-slate-400">Epic Games:</span> <b class="text-cyan-200">{{ $formatPrice($epic) }}</b></p>
                    </div>
                </div>
                <a class="hub-btn-secondary" href="{{ route('admin.prices.games.edit', $game) }}">Редактировать цены</a>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-6">{{ $games->links() }}</div>
@endsection
